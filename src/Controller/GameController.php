<?php

namespace App\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Joueur;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class GameController extends AbstractController
{
    #[Route('/games/add', name: 'app_game')]
    public function index(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $game = new Game();
        $game->setTitre("cs go");
        $game->setType("shooters");
        $game->setNbrJoueur(10);
        $game->setEditeur("wael");
        $image = new Image();
        $image->setUrl('https://www.timeslifestyle.net/wp-content/uploads/2021/07/CS-GO.jpg');
        $image->setAlt('cs go image');
        $game->setImage($image);

        $entityManager->persist($game);

        $entityManager->flush();
        return $this->render('game/index.html.twig', [
            'id' => $game->getId(),
        ]);
    }

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

    #[Route('/games/supp/{id}', name: 'delete_game')]

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