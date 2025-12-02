<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MessagerieController extends AbstractController
{
    #[Route('/messagerie/{id}', name: 'messagerie_conversation')]
    public function conversation(
        UtilisateurRepository $userRepo,
        MessageRepository $messageRepo,
        Request $request,
        EntityManagerInterface $em,
        int $id
    ): Response {
        $receveur = $userRepo->find($id);
        $user = $this->getUser();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setRelation($user);
            $message->setReceveur($receveur);
            $message->setDateEnvoi(new \DateTime());
            $message->setLuOuNon(false);

            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('messagerie_conversation', ['id' => $id]);
        }

        $messages = $messageRepo->findConversation($user, $receveur);
        $unreadMessages = $messageRepo->findUnreadMessages($receveur, $user); // ceux que je reçois

        foreach ($unreadMessages as $msg) {
            $msg->setLuOuNon(true);
        }
        $em->flush();
        return $this->render('messagerie/conversation.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
            'receveur' => $receveur
        ]);
    }
    #[Route('/messagerie', name: 'messagerie_index')]
    public function index(
        MessageRepository $messageRepo,
        UtilisateurRepository $userRepo
    ): Response {
        $user = $this->getUser();

        // Conversations déjà existantes
        $conversations = $messageRepo->findConversationsForUser($user);
        

        // Tous les utilisateurs sauf moi
        $utilisateurs = $userRepo->findAllExcept($user);

        return $this->render('messagerie/index.html.twig', [
            'conversations' => $conversations,
            'utilisateurs' => $utilisateurs,
            
        ]);
    }
    #[Route('/messagerie/{id}/modifier', name: 'message_modifier')]
    public function modifierMessage(
        Message $message,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('messagerie_conversation', [
                'id' => $message->getReceveur()->getId()
            ]);
        }

        return $this->render('messagerie/modifier.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
        ]);
    }

    #[Route('/messagerie/{id}/supprimer', name: 'message_supprimer', methods: ['POST'])]
    public function supprimerMessage(
        Request $request,
        Message $message,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('delete-message' . $message->getId(), $request->request->get('_token'))) {
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('messagerie_conversation', [
            'id' => $message->getReceveur()->getId()
        ]);
    }


}

