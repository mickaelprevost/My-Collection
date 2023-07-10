<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/back/user")
 */
class UserController extends AbstractController
{
    /**
     * Display the users list
     * @Route("/", name="app_back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            // Retrieves all users from the UserRepository
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
 * Add a new user
 * @Route("/new", name="app_back_user_new", methods={"GET", "POST"})
 */
public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
{
    // Create a new User instance
    $user = new User();

    // Create a form using the UserType form type and bind it to the $user object
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);


    // Retrieve the uploaded picture file from the form
    $pictureFile = $form->get('picture')->getData();

    // Check if the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Check if a picture file is uploaded
        if ($pictureFile) {
            // Generate a safe filename for the picture
            $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

            // Move the uploaded picture file to the designated directory
            try {
                $pictureFile->move(
                    $this->getParameter('usersPics'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle exception if something goes wrong during file upload
                // ...
            }

            // Update the 'picture' property of the user entity with the new filename
            $user->setPicture($newFilename);

            // Hash the plain text password and update the user entity
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);

            // Add the user entity to the repository and persist the changes
            $userRepository->add($user, true);
        } else {
            // If no picture file is uploaded, only hash the password and update the user entity
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);
            $userRepository->add($user, true);

        }

        // Redirect to the user index page
        return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    }


    // Render the new form template with the user object and the form
    return $this->renderForm('back/user/new.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

  /**
 * Modify a user
 * @Route("/edit/{id<\d+>}", name="app_back_user_edit", methods={"GET", "POST"})
 */
public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger): Response
{
    // Check if the user object is null, and if so, throw an exception
    if ($user === null) {
        throw $this->createNotFoundException('Objet non trouvé.');
    }

    // Create a form using the UserType form type and bind it to the $user object
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    // Check if the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Retrieve the uploaded picture file from the form
        $pictureFile = $form->get('picture')->getData();

        // Check if the user has an existing picture
        if ($user->getPicture() !== null) {
            // If yes, remove the existing picture file from the server
            $chemin = $user->getPicture();
            unlink("upload/usersPics/" . $chemin);
            $user->setPicture(null);
            // Update the user entity in the repository
            $userRepository->add($user, true);
        }

        // Check if a new picture file is uploaded
        if ($pictureFile) {
            // Generate a safe filename for the picture
            $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

            // Move the uploaded picture file to the designated directory
            try {
                $pictureFile->move(
                    $this->getParameter('usersPics'),
                    $newFilename
                );
            } catch (FileException $e) {
                // Handle exception if something goes wrong during file upload
                // ...
            }

            // Update the 'picture' property of the user entity with the new filename
            $user->setPicture($newFilename);

            // Update the user entity in the repository
            $userRepository->add($user, true);
        } else {
            // If no new picture file is uploaded, update the user entity without modifying the picture
            $userRepository->add($user, true);
        }

        // Set a flash message to indicate successful user update
        $this->addFlash('success', 'User mis à jour.');

        // Redirect to the user index page
        return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    }

    // Render the edit form template with the user object and the form
    return $this->renderForm('back/user/edit.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

    /**
 * @Route("/{id<\d+>}", name="app_back_user_show", methods={"GET"})
 */
public function show(User $user): Response
{
    // Render the show template with the user object
    return $this->render('back/user/show.html.twig', [
        'user' => $user,
    ]);
}

    /**
 * @Route("/remove/{id<\d+>}", name="app_back_user_delete", methods={"POST"})
 */
public function delete(Request $request, User $user, UserRepository $userRepository): Response
{
    // Check if the CSRF token is valid
    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
        // Remove the user from the UserRepository
        $userRepository->remove($user, true);
    }

    // Redirect to the user index page after deletion
    return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
}
}
