<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Joueur;

class JoueurController extends AbstractController
{

    #[Route('/joueurs/{id}', name: 'show_joueur')]

    public function show($id)
    {
        $joueur = $this->getDoctrine()
            ->getRepository(Joueur::class)
            ->find($id);

        if (!$joueur) {
            throw $this->createNotFoundException(
                'No Joueur found for id ' . $id
            );
        }
        return $this->render('joueur/show.html.twig', [
            'joueur' => $joueur
        ]);
    }

    #[Route('/joueurs', name: 'list_joueurs')]

    public function afficherList(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Joueur::class);
        $joueurs = $repo->findAll();
        return $this->render('joueur/home.html.twig', [
            'joueurs' => $joueurs,
        ]);
    }

    #[Route('joueurs/add/new', name: 'ajouter_joueur')]

    public function ajouter(Request $request)
    {
        $joueur = new Joueur();
        $form = $this->createForm("App\Form\JoueurType", $joueur);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            if (null === $joueur->getScore() || !is_numeric($joueur->getScore())) {
                // Set a default value or handle the error condition
                $joueur->setScore(0);
            }
            $selectedGame = $joueur->getGame();

            // Increase the nbrJoueur value of the associated Game entity
            if ($selectedGame) {
                $nbrJoueur = $selectedGame->getNbrJoueur();
                $selectedGame->setNbrJoueur($nbrJoueur + 1);
            }

            $em->persist($joueur);
            $em->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Joueur ajouter avec success');
            return $this->redirectToRoute('list_joueurs');
        }

        return $this->render(
            'joueur/ajouter.html.twig',
            ['f' => $form->createView()]
        );
    }

    #[Route('joueurs/edit/{id}', name: 'modifier_joueur')]

    public function edit(Request $request, $id)
    {
        $joueur = $this->getDoctrine()
            ->getRepository(Joueur::class)
            ->find($id);

        if (!$joueur) {
            throw $this->createNotFoundException(
                'No joueur found for id ' . $id
            );
        }

        $previousGame = $joueur->getGame(); // Get the previous game before updating

        $form = $this->createForm("App\Form\JoueurType", $joueur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if (null === $joueur->getScore() || !is_numeric($joueur->getScore())) {
                // Set a default value or handle the error condition
                $joueur->setScore(0);
            }

            $newGame = $joueur->getGame(); // Get the updated game

            // Update the nbrJoueur fields accordingly
            if ($previousGame !== $newGame) {
                $previousGame->setNbrJoueur($previousGame->getNbrJoueur() - 1);
                $newGame->setNbrJoueur($newGame->getNbrJoueur() + 1);
            }

            $entityManager->flush();

            $session = new Session();
            $session->getFlashBag()->add('notice', 'Joueur modifier avec succès');
            return $this->redirectToRoute('list_joueurs');
        }

        return $this->render('joueur/ajouter.html.twig', [
            'f' => $form->createView()
        ]);
    }



    #[Route('/joueurs/delete/{id}', name: 'delete_joueur')]

    public function delete(Request $request, $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $joueur = $entityManager->getRepository(Joueur::class)->find($id);

        if (!$joueur) {
            throw $this->createNotFoundException('No Joueur found for id ' . $id);
        }

        // Get the associated Game entity
        $game = $joueur->getGame();

        // Decrease the nbrJoueur value of the associated Game entity
        if ($game) {
            $nbrJoueur = $game->getNbrJoueur();
            $game->setNbrJoueur($nbrJoueur - 1);
        }
        // Remove the Joueur entity
        $entityManager->remove($joueur);
        $entityManager->flush();
        $session = new Session();
        $session->getFlashBag()->add('notice', 'Joueur modifier avec succès');
        return $this->redirectToRoute('list_joueurs');
    }
}