<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Joueur;


class GameController extends AbstractController
{


    #[Route('/games/{id}', name: 'show_game')]

    public function show($id)
    {
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $listJoueurs = $em->getRepository(Joueur::class)->findBy(['Game' => $game]);

        if (!$game) {
            throw $this->createNotFoundException(
                'No game found for id ' . $id
            );
        }
        return $this->render('game/show.html.twig', [
            'listejoueurs' => $listJoueurs,
            'game' => $game
        ]);
    }

    #[Route('/games/', name: 'list_games')]

    public function afficherList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Game::class);
        $games = $repo->findAll();
        return $this->render('game/home.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/games/add/new', name: 'ajouter_game')]

    public function add(Request $request)
    {
        $game = new Game();
        $form = $this->createForm("App\Form\GameType", $game);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($game);
            $em->flush();
            return $this->redirectToRoute('list_games');
        }
        return $this->render(
            'game/ajouter.html.twig',
            ['f' => $form->createView()]
        );

    }

    #[Route('/games/edit/{id}', name: 'edit_game')]

    public function edit(Request $request, $id)
    {
        $game = new Game();
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find($id);
        if (!$game) {
            throw $this->createNotFoundException(
                'No game found for id ' . $id
            );
        }
        $fb = $this->createFormBuilder($game)
            ->add('titre', TextType::class)
            ->add('type', TextType::class)
            ->add('nbr_joueur', TextType::class)
            ->add('editeur', TextType::class)
            ->add('Valider', SubmitType::class);
        // générer le formulaire à partir du FormBuilder
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('list_games');
        }
        return $this->render(
            'game/ajouter.html.twig',
            ['f' => $form->createView()]
        );
    }


    #[Route('/games/delete/{id}', name: 'delete_game')]

    public function delete(Request $request, $id): Response
    {
        $c = $this->getDoctrine()->getRepository(Game::class)
            ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                'no game found for id' . $id
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('list_games');
    }

}