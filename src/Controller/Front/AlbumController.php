<?php

namespace App\Controller\Front;


use App\Entity\Album;
use App\Entity\AlbumLike;
use App\Entity\Universe;
use App\Form\AlbumType;
use App\Repository\AlbumLikeRepository;
use App\Repository\AlbumRepository;
use App\Repository\CategoryRepository;
use App\Repository\ReviewRepository;
use App\Repository\UniverseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mime\MimeTypes;

/**
 * @Route("/album")
 */
class AlbumController extends AbstractController
{
    /**
     * Display the albums list
     * @Route("/", name="app_album_index", methods={"GET"})
     */
    public function index(AlbumRepository $albumRepository, UniverseRepository $universeRepository, CategoryRepository $categoryRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $albumRepository->createQueryBuilder('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            21
        );

        return $this->render('front/album/index.html.twig', [
            'albums' => $pagination,
            'universes' => $universeRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Add a new album
     * @Route("/new", name="app_album_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AlbumRepository $albumRepository, 
    SluggerInterface $slugger): Response
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
                }
                // updates the 'pictureFilename' property to store the file name instead of its contents
                $album->setPoster($newFilename);
                $albumRepository->add($album, true);
            } else {
                $albumRepository->add($album, true);
            }

            $this->addFlash('success', 'Collection ajoutée avec succès.');

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }

        return $this->renderForm('front/album/new.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * Display details of an album
     * @Route("/{id<\d+>}", name="app_album_show", methods={"GET"})
     */
    public function show($id, Album $album, ReviewRepository $reviewRepository): Response
    {
        $user = $this->getUser();

        return $this->render('front/album/show.html.twig', [
            'album' => $album,
            'user' => $user,
            'reviews' => $reviewRepository->find($id),
        ]);
    }

    /**
     * Display form to modify an album
     * @Route("/edit/{id<\d+>}", name="app_album_edit", methods={"GET", "POST"})
     */
    public function edit($id, Request $request, Album $album, SluggerInterface $slugger, AlbumRepository $albumRepository): Response
    {
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('poster')->getData();

            /* In order to remove/change the poster we remove the name 
            from database and we unlink the file from pictures directory*/
            if ($album->getPoster() == '' or null) {
                $album->setPoster('');
                $albumRepository->add($album, true);
            } else {
                $image = $album->getPoster();
                unlink("upload/pictures/" . $image);
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

            $this->addFlash('success', 'Collection mise à jour avec succès.');

            return $this->redirectToRoute('app_album_show', ['id' => $album->getId()]);
        }

        return $this->renderForm('front/album/edit.html.twig', [
            'album' => $album,
            'form' => $form,
        ]);
    }

    /**
     * Remove an album
     * @Route("/remove/{id<\d+>}", name="app_album_delete", methods={"POST"})
     */
    public function delete(Request $request, Album $album, AlbumRepository $albumRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $album->getId(), $request->request->get('_token'))) {
            $albumRepository->remove($album, true);
        }

        $this->addFlash('success', 'Collection supprimée avec succès.');

        return $this->redirectToRoute('app_album_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * Display result of the search by keyword
     * @Route("/search/list", name="app_album_list", methods={"GET"})
     */
    public function albumsBySearch(Request $request, AlbumRepository $albumRepository): Response
    {
        $keyword = $request->query->all()['search'];
        $albums = $albumRepository->findBySearch($keyword);

        return $this->render("front/album/albumsBySearch.html.twig", [
            'albums' => $albums,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Display result of the search by universe
     * @Route("/list/{id<\d+>}", name="app_albums_by_universe", methods={"GET"})
     *
     * @return Response
     */
    public function albumsByUniverse($id, AlbumRepository $albumRepository, Universe $universe, UniverseRepository $universeRepository): Response
    {
        $allAlbums = $albumRepository->findAllByUnivers($universe);
        $allUnivers = $universeRepository->findAll();
        $universes = $universeRepository->find($id);

        return $this->render(
            "front/album/albumsByUniverse.html.twig",
            [
                "allAlbumsbyuniverse" => $allAlbums,
                "allUniverse" => $allUnivers,
                "universes" => $universes,
            ]
        );
    }

    /**
     * handle the add/remove of likes 
     * @Route("/like/{id<\d+>}", name="app_album_like", methods={"GET", "POST"})
     * @return Response
     */
    public function like(Album $album, EntityManagerInterface $manager, AlbumLikeRepository $albumLikeRepository): Response
    {
        // we get actual connected user's informations
        $user = $this->getUser();

        // if user is not a connected/registered user, return error message
        if (!$user) return $this->json([
            'code' => 403,
            'message' => "non autorisé"
        ], 403);

        // To avoid many likes by the same user we use a condition that remove the like instead of adding another one
        if ($album->isLikedByUser($user)) {
            $like = $albumLikeRepository->findOneBy([
                'album' => $album,
                'user' => $user
            ]);

            $manager->remove($like);
            $manager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like supprimé',
                'likes' => $albumLikeRepository->count(['album' => $album])
            ], 200);
        }

        // If album is not liked already by the user we can add a like on it
        $like = new AlbumLike();
        $like->setAlbum($album)
            ->setUser($user);

        $manager->persist($like);
        $manager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like ajouté',
            'likes' => $albumLikeRepository->count(['album' => $album])
        ], 200);
    }
}
