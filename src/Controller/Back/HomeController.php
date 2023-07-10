<?php

namespace App\Controller\Back;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniverseRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    /**
     * Display main page of the backOffice
     * @Route("/back", name="app_back_home", methods={"GET"})
     */
    public function index(
        UniverseRepository $universeRepository,
        CategoryRepository $categoryRepository,
        AlbumRepository $albumRepository,
        UserRepository $userRepository,
        Request $request
    ): Response {
        // Retrieve sorting and direction parameters from the request
        $sortUser = $request->query->get('sortUser', 'username');
        $sortAlbum = $request->query->get('sortAlbum', 'title', 'rating', 'created_at', 'universe');
        $direction = $request->query->get('direction', 'ASC');

        // Fetch users and albums from the repositories with the specified sorting
        $users = $userRepository->findBy([], [$sortUser => $direction]);
        $albums = $albumRepository->findBy([], [$sortAlbum => $direction]);

        // Render the template and pass data to it
        return $this->render('back/index.html.twig', [
            'universes' => $universeRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'albums' => $albums,
            'sortAlbum' => $sortAlbum,
            'users' => $users,
            'sortUser' => $sortUser,
            'direction' => $direction,
        ]);
    }
}
