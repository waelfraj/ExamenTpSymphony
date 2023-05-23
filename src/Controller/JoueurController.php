<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Joueur;
use App\Entity\Game;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
        $fb = $this->createFormBuilder($joueur)
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('born_at', DateType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('score', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px']
            ])
            ->add('valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-top: 10px']
            ]);


        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($joueur);
            $em->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Joueur ajouté avec success');
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
        $joueur = new Joueur();
        $joueur = $this->getDoctrine()
            ->getRepository(Joueur::class)
            ->find($id);
        if (!$joueur) {
            throw $this->createNotFoundException(
                'No joueur found for id ' . $id
            );
        }
        $fb = $this->createFormBuilder($joueur)
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('born_at', DateType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('score', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('game', EntityType::class, [
                'class' => Game::class,
                'choice_label' => 'titre',
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px']
            ])
            ->add('valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-top: 10px']
            ]);

        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Joueur modifié avec success');
            return $this->redirectToRoute('list_joueurs');
        }
        return $this->render(
            'joueur/ajouter.html.twig',
            ['f' => $form->createView()]
        );
    }


    #[Route('/joueurs/delete/{id}', name: 'delete_joueur')]

    public function delete(Request $request, $id): Response
    {
        $c = $this->getDoctrine()->getRepository(Joueur::class)
            ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                'no Joueur found for id' . $id
            );
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('list_joueurs');
    }
}