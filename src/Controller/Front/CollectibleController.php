<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Entity\Collectible;
use App\Form\CollectibleType;
use App\Repository\AlbumRepository;
use App\Repository\CategoryRepository;
use App\Repository\CollectibleRepository;
use App\Repository\UniverseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

class CollectibleController extends AbstractController
{

    /**
     * @Route("/collectible", name="app_collectible_index", methods={"GET"})
     */
    public function index(CollectibleRepository $collectibleRepository, UniverseRepository $universeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $collectibleRepository->createQueryBuilder('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            21
        );

        $user = $this->getUser();

        return $this->render('front/collectible/index.html.twig', [
            'collectibles' => $pagination,
            'universes' => $universeRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'user' => $user
        ]);
    }

    /**
     * Création de collectible
     * @Route("/collectible/new/{id<\d+>}", name="app_collectible_new", requirements={"id"="\d+"}, methods={"GET", "POST"})
     *
     * @return Response
     */
    public function showReview($id, Request $request, AlbumRepository $albumRepository, CollectibleRepository $collectibleRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {

        $album = $albumRepository->find($id);

        $collectible = new Collectible();
        $form = $this->createForm(CollectibleType::class, $collectible);
        $form->handleRequest($request);
        $pictureFile = $form->get('picture')->getData();
        $collectible->setUserId($this->getUser());



        if ($form->isSubmitted() && $form->isValid()) {

            $collectible->addAlbum($album);

            // The 'picture' field is not required so the file must be processed only when a file is uploaded
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'pictureFilename' property to store the file name instead of its contents

                $collectible->setPicture($newFilename);

                $collectibleRepository->add($collectible, true);
            }
            $entityManager->persist($collectible);
            $entityManager->flush();

            $this->addFlash('success', 'Collectible ajouté avec succès.');

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }
        return $this->renderForm("front/collectible/new.html.twig", [
            "album" => $album,
            "form" => $form,
        ]);
    }

    /**
     * Modify a collectible
     * @Route("/collectible/edit/{id<\d+>}", name="app_collectible_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Collectible $collectible = null, CollectibleRepository $collectibleRepository, SluggerInterface $slugger): Response
    {

        if ($collectible === null) {
            throw $this->createNotFoundException('Objet non trouvé.');
        }

        $form = $this->createForm(CollectibleType::class, $collectible);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $pictureFile = $form->get('picture')->getData();

            // In order to remove/change the picture we remove the name from database and we unlink the file from pictures directory
            if ($collectible->getPicture() !== null) {
                $chemin = $collectible->getPicture();
                unlink("upload/pictures/" . $chemin);
                $collectible->setPicture(null);
                $collectibleRepository->add($collectible, true);
            }


            //The 'picture' field is not required so the file must be processed only when a file is uploaded
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                // Move the file to the directory where pictures are stored
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'pictureFilename' property to store the file name
                // instead of its contents

                $collectible->setPicture($newFilename);

                $collectibleRepository->add($collectible, true);
            }

            $this->addFlash('success', 'Collectible mis à jour avec succès.');

            return $this->redirectToRoute('app_collectible_show', ['id' => $collectible->getId()]);
        }

        return $this->renderForm('front/collectible/edit.html.twig', [
            'collectible' => $collectible,
            'form' => $form,
        ]);
    }

    /**
     * Remove a collectible
     * @Route("/collectible/remove/{id<\d+>}", name="app_collectible_delete", methods={"POST"})
     */
    public function delete(Request $request, Collectible $collectible, EntityManagerInterface $entityManager, CollectibleRepository $collectibleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collectible->getId(), $request->request->get('_token'))) {
            $collectibleRepository->remove($collectible, true);
            // Condition to avoid unlink error if there is no picture file linked to the collectible
            if ($collectible->getPicture() !== null) {
                $chemin = $collectible->getPicture();
                unlink("upload/pictures/" . $chemin);
                $entityManager->flush();
            }
        }
        $this->addFlash('success', 'Collectible supprimé avec succès.');

        return $this->redirectToRoute('app_collectible_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Display details of a collectible
     * @Route("/collectible/{id<\d+>}", name="app_collectible_show", methods={"GET"})
     */
    public function show($id, CollectibleRepository $collectibleRepository, AlbumRepository $albumRepository, UniverseRepository $universeRepository): Response
    {
        $collectible = $collectibleRepository->find($id);
        $album = $albumRepository->find($id);
        $universe = $universeRepository->find($id);
        $user = $this->getUser();


        return $this->renderForm('front/collectible/show.html.twig', [
            'collectible' => $collectible,
            'album' => $album,
            'universeId' => $universe,
            'user' => $user


        ]);
    }

    /**
     * Display result of the search by keyword
     * @Route("/collectible/search/list", name="app_collectible_list", methods={"GET"})
     */
    public function list(Request $request, CollectibleRepository $collectibleRepository, AlbumRepository $albumRepository): Response
    {
        $keyword = $request->query->all()['search'];
        $collectibles = $collectibleRepository->findBySearch($keyword);
        $user = $this->getUser();

        return $this->render("front/collectible/collectiblesBySearch.html.twig", [
            'collectibles' => $collectibles,
            'keyword' => $keyword,
            'user' => $user
        ]);
    }

    /**
     * Display result of the search by category
     * @Route("/collectible/list/{id<\d+>}", name="app_collectibles_by_category", methods={"GET"})
     *
     * @return Response
     */
    public function collectiblesByCategory($id, CollectibleRepository $collectibleRepository, Category $category, CategoryRepository $categoryRepository): Response
    {
        $allCollectibles = $collectibleRepository->findAllByCategory($category);
        $allCategories = $categoryRepository->findAll();
        $categorie = $categoryRepository->find($id);
        $user = $this->getUser();

        return $this->render(
            "front/collectible/collectiblesByCategory.html.twig",
            [
                "Collectibles" => $allCollectibles,
                "Categories" => $allCategories,
                "categorie" => $categorie,
                'user' => $user
            ]
        );
    }
}
