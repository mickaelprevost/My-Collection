<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MessageController extends AbstractController
{
    /**
     * @Route("/messagerie", name="app_messagerie_index", methods={"GET"})
     */
    public function index(MessageRepository $messageRepository): Response
    {
        // we get the id of the actual connected user
        $userId = $this->getUser()->getId();
        // we get the number of active messages(non deleted) related to that user
        $messageSent = $messageRepository->findAllMessageActive($userId);
        $messageReceived = $messageRepository->findAllMessageActive2($userId);

        return $this->render('message/index.html.twig', [
            'messageReceived' => $messageReceived,
            'messageSent' => $messageSent
        ]);
    }

    /**
     * Display the form to send a message
     * @Route("/message/send", name="app_message_send", methods={"GET", "POST"})
     */
    public function send(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $contact = $this->getUser()->getContact();



        if ($request->getMethod() === 'POST'){
            $message = new Message();
            foreach($request->request->get('liste') as $liste){
            $recipient = $userRepository->findOneBy(['username' => $liste]);
            $message->setRecipient($recipient);
            }
            $message->setTitle($request->request->get('titre'));
            $message->setContent($request->request->get('message'));
            $message->setSender($this->getUser());
            $entityManager->persist($message);
            $entityManager->flush();
        }

        return $this->render('message/send.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * Display the list of received messages
     * @Route("/message/received", name="app_message_received", methods={"GET"})
     */
    public function received(MessageRepository $messageRepository): Response
    {
        // we get the id of the actual connected user
        $userId = $this->getUser()->getId();
        // we get the number of active messages(non deleted) related to that user
        $messageReceived = $messageRepository->findAllMessageActive2($userId);

        return $this->render('message/received.html.twig', [
            'messageReceived' => $messageReceived,
        ]);
    }

    /**
     * Display the list of sent messages
     * @Route("/message/sent", name="app_message_sent", methods={"GET"})
     */
    public function sent(MessageRepository $messageRepository): Response
    {
        // we get the id of the actual connected user
        $userId = $this->getUser()->getId();
        // we get the number of active messages(non deleted) related to that user
        $messageSent = $messageRepository->findAllMessageActive($userId);

        return $this->render('message/sent.html.twig', [
            'messageSent' => $messageSent,
        ]);
    }

    /**
     * Display the content of a message
     * @Route("/message/read/{id<\d+>}", name="app_message_read", methods={"GET"})
     */
    public function read($id, MessageRepository $messageRepository, EntityManagerInterface $entityManager): Response
    {
        $messages = $messageRepository->find($id);
        if ($messages->getRecipient() == $this->getUser() or $messages->getSender() == $this->getUser()){
        // after checking the message content we set the Isread property at true so it no longer appear in bold style
        $messages->setIsRead(true);
        $messageRepository->add($messages, true);

        if ($messages->getTitle() == 'Un utilisateur souhaite intéragir avec vous' && $messages->isIsRead() == true){
            $this->getUser()->setFriendReady(true);
            $messages->getSender()->setFriendReady(true);
            $entityManager->flush();
        }}else {
            $this->addFlash("danger", "Désolé, ce n'est pas possible");
            return $this->redirectToRoute("app_messagerie_index");
        }

        return $this->render('message/read.html.twig', [
            "message" => $messages
        ]);
    }

    /**
     * Delete a message
     * @Route("/message/delete/{id<\d+>}", name="app_message_delete", methods={"POST"})
     */
    public function delete($id, MessageRepository $messageRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $message = $messageRepository->find($id);

        $submittedToken = $request->request->get('token');

        // 'delete-message' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-message', $submittedToken)) {

            // If the actual user is the sender we set the active property to false. It stay in the database until the receiver delete it also.
            if ($message->getSender() == $this->getUser()) {
                $message->setActive(false);
                $entityManager->persist($message);
                // When the message is also deleted on the recipient side (property active2 set to false), we can delete the message from the database.
                if ($message->isActive2() === false) {
                    $entityManager->remove($message);
                }
                $entityManager->flush();
            }

            // If the actual user is the recipient we set the active2 property to false. It stay in the database until the sender delete it also.
            if ($message->getRecipient() == $this->getUser()) {
                $message->setActive2(false);
                if($message->getTitle() == 'Un utilisateur souhaite intéragir avec vous'){
                $this->getUser()->setFriendReady(false);
                $message->getSender()->setFriendReady(false);
                }
                $entityManager->persist($message);
                // When the message is also deleted on the sender side (property active set to false), we can delete the message from the database.
                if ($message->isActive() === false) {
                    $entityManager->remove($message);
                }
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('app_message_received', [
            "message" => $message
        ]);
    }

    /**
     * @Route("/contact/request/{id<\d+>}", name="app_contact_new", methods={"GET", "POST"})
     */
    public function invite($id, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($id > '4'){
            if ($id != $this->getUser()->getId()){
        $message = new Message();
        $message->setSender($this->getUser());
        $message->setRecipient($user);
        $message->setTitle('Un utilisateur souhaite intéragir avec vous');
        $message->setContent("L'utilisateur " . $message->getSender()->getUsername() . " souhaite vous ajouter à ses contacts, êtes-vous d'accord? <a href='/contact/validate/" . $this->getUser()->getId(). "' class='btn btn-primary'>accepter</a> <a href='/contact/reject/" . $this->getUser()->getId(). "' class='btn btn-primary'>refuser</a>");
        $message->setActive(false);
        $entityManager->persist($message);
        $entityManager->flush();
        $this->addFlash("success", "demande d'ajout aux contacts envoyée à " . $message->getRecipient($user)->getUsername() . ".");
        return $this->redirectToRoute("app_messagerie_index");
        } else {
            $this->addFlash("danger", "Vous ne pouvez pas vous ajouter vous-même à votre liste de contacts");
            return $this->redirectToRoute("app_messagerie_index");
        }} else {
            $this->addFlash("danger", "Désolé une erreur s'est produite");
            return $this->redirectToRoute("app_messagerie_index");
        }
    }

    /**
     * @Route("/contact/validate/{id<\d+>}", name="app_contact_validate", methods={"GET", "POST"})
     */
    public function validate($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($id > '4'){
            if ($this->getUser()->isFriendReady()== true && $user->isFriendReady() == true){
        $user->addContact($this->getUser());
        $this->getUser()->addContact($user);
        $user->setFriendReady(false);
        $this->getUser()->setFriendReady(false);
        $entityManager->flush();

        $this->addFlash("success", "Le nouveau contact a bien été ajouté à votre liste.");
        return $this->redirectToRoute("app_messagerie_index");
        } else {
            $this->addFlash("danger", "Désolé une erreur s'est produite");
            return $this->redirectToRoute("app_messagerie_index");
        }} 
    }

    /**
     * @Route("/contact/reject/{id<\d+>}", name="app_contact_reject", methods={"GET", "POST"})
     */
    public function reject($id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($id > '4'){
            if ($this->getUser()->isFriendReady()== true && $user->isFriendReady() == true){
        $user->setFriendReady(false);
        $this->getUser()->setFriendReady(false);
        $entityManager->flush();

        $this->addFlash("success", "La demande a bien été rejetée.");
        return $this->redirectToRoute("app_messagerie_index");
        } else {
            $this->addFlash("danger", "Désolé une erreur s'est produite");
            return $this->redirectToRoute("app_messagerie_index");
        }} 
    }
}
