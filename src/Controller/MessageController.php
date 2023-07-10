<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
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
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // we define that the actual connected user is the sender of the message
            $message->setSender($this->getUser());
            $entityManager->persist($message);
            $entityManager->flush();

            // success message for the user
            $this->addFlash("message", "message envoyé avec succès.");
            return $this->redirectToRoute("app_messagerie_index");
        }
        return $this->render('message/send.html.twig', [
            "form" => $form->createView(),
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
    public function read($id, MessageRepository $messageRepository): Response
    {
        $messages = $messageRepository->find($id);
        // after checking the message content we set the Isread property at true so it no longer appear in bold style
        $messages->setIsRead(true);
        $messageRepository->add($messages, true);

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
}
