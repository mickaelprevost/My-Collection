<?php

namespace App\Controller\Back;

use App\Entity\Album;
use App\Form\AlbumType;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/back/album")
 */
class AlbumController extends AbstractController
{
    /**
     * Display albums list
     * @Route("/", name="app_back_album_index", methods={"GET"})
     */
    public function index(AlbumRepository $albumRepository): Response
    {
        return $this->render('back/album/index.html.twig', [
            'albums' => $albumRepository->findAll(),
        ]);
    }

    /**
     * Create a new album
     * @Route("/new", name="app_back_album_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AlbumRepository $albumRepository, SluggerInterface $slugger): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);
        $album->setUserId($this->getUser());
        $pictureFile = $form->get('poster')->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            // If a picture is uploaded
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

                $album->setPoster($newFilename);
                $albumRepository->add($album, true);
            } else {
                $albumRepository->add($album, true);
            }

            return $this->redirectToRoute('app_back_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * Display content of an album
     * @Route("/{id<\d+>}", name="app_back_album_show", methods={"GET"})
     */
    public function show(Album $album): Response
    {
        return $this->render('back/album/show.html.twig', [
            'album' => $album,
        ]);
    }

    /**
     * Display form to modify an album
     * @Route("/edit/{id<\d+>}", name="app_back_album_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Album $album, AlbumRepository $albumRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('poster')->getData();

            /* In order to remove/change the poster we remove the name from database 
            and we unlink the file from pictures directory */
            if ($album->getPoster() !== '') {
                $chemin = $album->getPoster();
                unlink("upload/pictures/" . $chemin);
                $album->setPoster('');
                $albumRepository->add($album, true);
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

                $album->setPoster($newFilename);

                $albumRepository->add($album, true);
            }

            $this->addFlash('success', 'collection mise à jour.');
            return $this->redirectToRoute('app_back_album_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * Remove an album
     * @Route("/remove/{id<\d+>}", name="app_back_album_delete", methods={"POST"})
     */
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $album->getId(), $request->request->get('_token'))) {
            $albumRepository->remove($album, true);
        }

        return $this->redirectToRoute('app_back_album_index', [], Response::HTTP_SEE_OTHER);
    }
}
