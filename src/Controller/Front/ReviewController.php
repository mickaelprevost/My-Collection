<?php

namespace App\Controller\Front;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Album;
use App\Entity\ReviewLike;
use App\Repository\ReviewLikeRepository;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @Route("/review")
 */
class ReviewController extends AbstractController
{

    /**
     * Modify a review
     * @Route("/edit/{id<\d+>}", name="app_review_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($review->getUser()->getId() === $this->getUser()->getId()){
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        $userId = $review->getUser()->getId();
        }else {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier ce commentaire');
            return $this->redirectToRoute('app_home');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review, true);

            $this->addFlash('success', 'Commentaire mis à jour avec succès.');
            return $this->redirectToRoute('app_user_index', ['id' => $userId], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('front/review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }


    /**
     * Remove a review
     * @Route("/remove/{id<\d+>}", name="app_review_delete", methods={"POST"})
     */
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager, ReviewRepository $reviewRepository): Response
    {
        $userId = $review->getUser()->getId();

        if ($userId === $this->getUser()->getId()){
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Commentaire supprimé avec succès.');

        return $this->redirectToRoute('app_user_index', ['id' => $userId], Response::HTTP_SEE_OTHER);
    }else {
        $this->addFlash('danger', 'Vous n\'êtes pas autorisé à éffectuer cette action.');

        return $this->redirectToRoute('app_home');
    }
    }


    /**
     * Add a review
     * @Route("/new/{id<\d+>}", name="app_review_new", methods={"GET", "POST"})
     */
    public function new(Album $album, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($album === null) {
            throw $this->createNotFoundException('Collection non trouvée.');
        }

        $review = new Review();

        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        $review->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {

            // We link the review to the current album
            $review->setAlbum($album);

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }

        return $this->renderForm('front/review/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * Handle the add/remove of a like on a review
     * @Route("/like/{id<\d+>}", name="app_review_like", methods={"GET", "POST"})
     * @return Response
     */
    public function like(Review $review, EntityManagerInterface $manager, ReviewLikeRepository $reviewLikeRepository): Response
    {
        // we get actual connected user's informations
        $user = $this->getUser();

        // if user is not a connected/registered user, return error message
        if (!$user) return $this->json([
            'code' => 403,
            'message' => "non autorisé"
        ], 403);

        // To avoid many likes by the same user we use a condition that remove the like instead of adding another one
        if ($review->isLikedByUser($user)) {
            $like = $reviewLikeRepository->findOneBy([
                'review' => $review,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $reviewLikeRepository->count(['review' => $review])
            ], 200);
        }

        // If review is not liked already by the user we can add a like on it
        $like = new ReviewLike();
        $like->setReview($review)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $reviewLikeRepository->count(['review' => $review])
        ], 200);
    }
}
