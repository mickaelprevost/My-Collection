<?php

namespace App\Controller\Back;

use App\Entity\Collectible;
use App\Form\CollectibleType;
use App\Repository\CollectibleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CollectibleController extends AbstractController
{
    /**
     * Display the collectibles list
     * @Route("/back/collectible", name="app_back_collectible_index", methods={"GET"})
     */
    public function index(CollectibleRepository $collectibleRepository): Response
    {
        return $this->render('back/Collectible/index.html.twig', [
            'collectibles' => $collectibleRepository->findAll(),
        ]);
    }

    /**
     * Add a new collectible
     * @Route("back/collectible/new", name="app_back_collectible_new", methods={"GET", "POST"})
     */
    public function addCollectible(Request $request, CollectibleRepository $collectibleRepository, SluggerInterface $slugger): Response
    {
        $collectible = new Collectible();
        $form = $this->createForm(CollectibleType::class, $collectible);
        $form->handleRequest($request);
        $collectible->setUserId($this->getUser());
        $pictureFile = $form->get('picture')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

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
            } else {

                // Even if no file is uploaded the collectible is added to the database
                $collectibleRepository->add($collectible, true);
            }

            $this->addFlash('success', 'Collectible ajouté.');

            return $this->redirectToRoute('app_back_collectible_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/Collectible/new.html.twig', [
            'collectible' => $collectible,
            'form' => $form,
        ]);
    }

    /**
     * Display form to modify a collectible
     * @Route("/back/collectible/edit/{id<\d+>}", name="app_back_collectible_edit", methods={"GET", "POST"})
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


            // this condition is needed because the 'picture' field is not required
            // so the file must be processed only when a file is uploaded
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

            $this->addFlash('success', 'objet mis à jour.');

            return $this->redirectToRoute('app_back_collectible_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/Collectible/edit.html.twig', [
            'collectible' => $collectible,
            'form' => $form,
        ]);
    }

    /**
     * Delete a collectible
     * @Route("/back/collectible/remove/{id<\d+>}", name="app_back_collectible_delete", methods={"POST"})
     */
    public function delete(Request $request, Collectible $collectible, EntityManagerInterface $entityManager, CollectibleRepository $collectibleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collectible->getId(), $request->request->get('_token'))) {
            $collectibleRepository->remove($collectible, true);
            if ($collectible->getPicture() !== null) {
                $chemin = $collectible->getPicture();
                unlink("upload/pictures/" . $chemin);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_back_collectible_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Display details of a collectible
     * @Route("/back/collectible/{id<\d+>}", name="app_back_collectible_show", methods={"GET"})
     */
    public function show($id, CollectibleRepository $collectibleRepository): Response
    {
        $collectible = $collectibleRepository->find($id);

        return $this->renderForm('back/Collectible/show.html.twig', [
            'collectible' => $collectible
        ]);
    }
}
