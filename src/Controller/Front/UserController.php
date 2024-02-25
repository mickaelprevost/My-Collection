<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserTypeFront;
use App\Repository\UserRepository;
use App\Repository\CollectibleRepository;
use App\Service\FavoritesManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * Private profil page of an user
     * @Route("/{id<\d+>}", name="app_user_index", methods={"GET", "PATCH"})
     */
    public function index($id, UserRepository $userRepository, CollectibleRepository $collectibleRepository, Request $request): Response

    {
        $user = $this->getuser();
        $userId = $this->getuser()->getId();
        $userCollectibles = $collectibleRepository->findBy(['userId' => $userId]);
        $favoritesList = [];
        foreach ($userCollectibles as $collectibles) {
          if ($collectibles->isFavorite() == '1') {
          $favoritesList[] = $collectibles;
          }
        }

        if ($request->isMethod('PATCH')) {

            // Get JSON into request
            $json = $request->getContent();
            
            // Decode JSON to array
            $summaryJSON = json_decode($json, true);
            
            if ($this->isCsrfTokenValid('bioEdit' . $user->getId(), $summaryJSON["token"])) {

                $user->setSummary($summaryJSON["summary"]);

                $userRepository->add($user, true);
                
                return $this->json([
                    'code' => 200,
                    'message' => 'OK',
                ], 200);
            } else {
                return $this->json([
                    'code' => 400,
                    'message' => 'Error',
                ], 400);
            }
        }

        return $this->render('front/user/private.html.twig', [
            'user' => $user,
            'favorisList' => $favoritesList,
        ]);
    }

    /**
     * Add a new user
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new($id, Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // When the user create his account we make sure his password is hashed before being stored
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);

            $userRepository->add($user, true);

            $this->addFlash('success', 'Compte créé avec succès.');

            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'user' => $userRepository->find($id),
        ]);
    }

    /**
     * Display public profile page of a user
     * @Route("/profil/{id<\d+>}", name="app_user_show", methods={"GET"})
     */
    public function show($id, User $user, UserRepository $userRepository, CollectibleRepository $collectibleRepository): Response
    {
        $user = $userRepository->find($id);
        $userCollectibles = $collectibleRepository->findBy(['userId' => $user]);
        $favoritesList = [];
        foreach ($userCollectibles as $collectibles) {
          if ($collectibles->isFavorite() == '1') {
          $favoritesList[] = $collectibles;
          }
        }
        return $this->render('front/user/public.html.twig', [
            'user' => $user,
            'favorisList' => $favoritesList,
        ]);
    }

   /**
    * Modify a user
    * @Route("/edit/{id}", name="app_user_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
    */
public function edit(Request $request, User $user, UserRepository $userRepository, SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher): Response
{
    if ($user === null) {
        throw $this->createNotFoundException('Objet non trouvé.');
    }

    if ($user->getId() === $this->getUser()->getId()){
    $form = $this->createForm(UserTypeFront::class, $user);
    $form->handleRequest($request);
    } else {
        $this->addFlash("success", "Vous n'êtes pas autorisé à modifier ce profil");
        return $this->redirectToRoute("app_home");
    }

    if ($form->isSubmitted() && $form->isValid()) {

        
        $pictureFile = $form->get('picture')->getData();

        if ($user->getPicture() !== null) {
            $chemin = $user->getPicture();
            unlink("upload/usersPics/" . $chemin);
            $user->setPicture(null);
            $userRepository->add($user, true);
        }
        

        // The 'picture' field is not required so the file must be processed only when a file is uploaded
    if ($pictureFile) {
    $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
    // this is needed to safely include the file name as part of the URL
    $safeFilename = $slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

    // Move the file to the directory where pictures are stored
    try {
        $pictureFile->move(
            $this->getParameter('usersPics'),
            $newFilename
        );
    } catch (FileException $e) {
        // ... handle exception if something happens during file upload
    }

    // updates the 'pictureFilename' property to store the file name
    // instead of its contents
    
            $user->setPicture($newFilename);

            // When the user edit his personnal informations we make sure his password is hashed before being stored
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);

            $userRepository->add($user, true);
        }else {
            // the hashing process happen even outside of the condition
            $plainTextPassword = $user->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainTextPassword);
            $user->setPassword($hashedPassword);
            $userRepository->add($user, true);
        }

        return $this->redirectToRoute('app_user_index', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('front/user/edit.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

     /**
     * Display result of a search by keyword
     * @Route("/list", name="app_user_list", methods={"GET"})
     */
    public function findBySearch(Request $request,UserRepository $userRepository): Response
    {
        $keyword = $request->query->all()['search'];
        $users = $userRepository->findBySearch($keyword);

        return $this->render("front/user/usersBySearch.html.twig", [
            'users' => $users,
            'keyword' => $keyword,
        ]);
    }

     /**
     * Display list of contacts
     * @Route("/contacts", name="app_contacts_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        $contacts = $this->getUser()->getContact();
        return $this->render('front/user/contacts.html.twig', [
            'contacts' => $contacts
        ]);
      
    }

    /**
     * Delete a contact
     * @Route("/contact/delete/{id<\d+>}", name="app_contact_delete", methods={"POST"})
     */
    public function delete($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $contact = $userRepository->findOneBy(['id' => $id]);

        $submittedToken = $request->request->get('contact_token');

        if ($this->isCsrfTokenValid('delete-contact', $submittedToken)) {
                $this->getUser()->removeContact($contact);
                $contact->removeContact($this->getUser());
                $entityManager->flush();
            }

        return $this->redirectToRoute('app_contacts_list');
    }
}