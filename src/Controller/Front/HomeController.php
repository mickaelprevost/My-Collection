<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\SignUpType;
use App\Repository\AlbumLikeRepository;
use App\Repository\UserRepository;
use App\Repository\AlbumRepository;
use App\Repository\CategoryRepository;
use App\Repository\CollectibleRepository;
use App\Repository\ReviewRepository;
use App\Repository\UniverseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class HomeController extends AbstractController
{
    /**
     * Default page
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(UniverseRepository $universeRepository, CategoryRepository $categoryRepository, AlbumLikeRepository $albumLikeRepository, 
    AlbumRepository $albumRepository, CollectibleRepository $collectibleRepository, ReviewRepository $reviewRepository, UserRepository $userRepository, Request $request): Response
    {
        // call of the function to get the albums list ordered by numbers of likes (DESC)
        $topAlbums = $albumLikeRepository->findAllOrderedByNbersOfLikes();
        // call of the function to get the albums list ordered by creation date (DESC)
        $lastAlbums = $albumRepository->findAllOrderedByCreationDate();
        return $this->render('front/home/index.html.twig', [
            'universes' => $universeRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'albums' => $albumRepository->findAll(),
            'collectibles' => $collectibleRepository->findAll(),
            'reviews' => $reviewRepository->findAll(),
            'topAlbums' => $topAlbums,
            'lastAlbums' => $lastAlbums,
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * Display the signup form
     * @Route("/signup", name="app_signup", methods={"GET", "POST"})
     */
    public function signup(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Before saving th user we get the plaintext password and we encrypt it
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);
            $userRepository->add($user, true);

           
            return $this->redirectToRoute('login', [], Response::HTTP_FOUND);
        }

        return $this->renderForm('front/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
