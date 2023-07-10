<?php

namespace App\Controller\Back;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/review")
 */
class ReviewController extends AbstractController
{
    /**
     * Display the reviews list
     * @Route("/", name="app_back_review_index", methods={"GET"})
     */
    public function index(ReviewRepository $reviewRepository): Response
    {
        return $this->render('back/review/index.html.twig', [
            'reviews' => $reviewRepository->findAll(),
        ]);
    }

    /**
     * Add a new review
     * @Route("/new", name="app_back_review_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ReviewRepository $reviewRepository): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review, true);

            return $this->redirectToRoute('app_back_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    /**
     * Show details of a review
     * @Route("/{id<\d+>}", name="app_back_review_show", methods={"GET"})
     */
    public function show(Review $review): Response
    {
        return $this->render('back/review/show.html.twig', [
            'review' => $review,
        ]);
    }

    /**
     * Modify a review
     * @Route("/edit/{id<\d+>}", name="app_back_review_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        $review->updatedTimestamps();

        if ($form->isSubmitted() && $form->isValid()) {
            $reviewRepository->add($review, true);

            $this->addFlash('success', 'avis mis à jour.');

            return $this->redirectToRoute('app_back_review_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/review/edit.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }

    /**
     * Remove a review
     * @Route("/remove/{id<\d+>}", name="app_back_review_delete", methods={"POST"})
     */
    public function delete(Request $request, Review $review, ReviewRepository $reviewRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
            $reviewRepository->remove($review, true);
        }

        return $this->redirectToRoute('app_back_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
