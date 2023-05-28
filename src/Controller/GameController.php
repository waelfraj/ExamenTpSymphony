<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Game;
use App\Entity\Image;
use App\Entity\Joueur;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class GameController extends AbstractController
{


    #[Route('/games/{id}', name: 'show_game')]

    public function show($id, Request $request)
    {
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $listJoueurs = $em->getRepository(Joueur::class)->findBy(['Game' => $game]);
        $publicPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/games/';
        if (!$game) {
            throw $this->createNotFoundException(
                'No game found for id : ' . $id
            );
        }
        return $this->render('game/show.html.twig', [
            'listejoueurs' => $listJoueurs,
            'game' => $game,
            'publicPath' => $publicPath
        ]);
    }

    #[Route('/games/', name: 'list_games')]

    public function afficherList(Request $request)
    {
        $publicPath = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . '/uploads/games/';
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Game::class);
        $games = $repo->findAll();
        return $this->render('game/home.html.twig', [
            'games' => $games,
            'publicPath' => $publicPath
        ]);
    }

    #[Route('/games/add/new', name: 'ajouter_game')]

    public function add(Request $request)
    {
        $publicPath = "uploads/games/";
        $game = new Game();
        $form = $this->createForm("App\Form\GameType", $game);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /*
             * @var UploadFile $image
             */
            $image = $form->get('image')->getData();
            $em = $this->getDoctrine()->getManager();

            if ($image) {
                $imageName = $game->getTitre() . '.' . $image->guessExtension();
                $image->move($publicPath, $imageName);
                $game->setImage($imageName);
            }
            if (null === $game->getNbrJoueur() || !is_numeric($game->getNbrJoueur())) {
                // Set a default value or handle the error condition
                $game->setNbrJoueur(0);
            }
            $em->persist($game);
            $em->flush();
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Game ajouté avec success');
            return $this->redirectToRoute('list_games');
        }

        return $this->render(
            'game/ajouter.html.twig',
            [
                'f' => $form->createView(),
                'button_label' => 'Ajouter',
            ]
        );
    }

    #[Route('/games/edit/{id}', name: 'edit_game')]

    public function edit(Request $request, $id)
    {
        $publicPath = "uploads/games/";

        $game = new Game();
        $game = $this->getDoctrine()
            ->getRepository(Game::class)
            ->find($id);
        if (!$game) {
            throw $this->createNotFoundException(
                'No game found for id ' . $id
            );
        }

        $originalImage = $game->getImage(); // Store the original image filename

        $fb = $this->createFormBuilder($game)
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('type', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('nbrJoueur', NumberType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])

            ->add('editeur', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px']
            ])
            ->add('image', FileType::class, [
                'attr' => ['style' => 'margin-top: 10px;margin-bottom:10px'],
                'data_class' => null,
                'required'   => false,
            ])

            ->add('valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-top: 10px']
            ]); // générer le formulaire à partir du FormBuilder
        $form = $fb->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
    
            if ($image) {
                $imageName = $game->getTitre() . '.' . $image->guessExtension();
                $image->move($publicPath, $imageName);
                $game->setImage($imageName);
            } else {
                $game->setImage($originalImage); // Preserve the original image if no new image is selected
            }

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
    
            $session = new Session();
            $session->getFlashBag()->add('notice', 'Game modifié avec succès');
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
        $entityManager = $this->getDoctrine()->getManager();
        $game = $entityManager->getRepository(Game::class)->find($id);
        if (!$game) {
            throw $this->createNotFoundException('No game found for id ' . $id);
        }
        $joueurs = $entityManager->getRepository(Joueur::class)->findBy(['Game' => $game]);
        foreach ($joueurs as $joueur) {
            $entityManager->remove($joueur);
        }
        $entityManager->remove($game);
        $entityManager->flush();
        return $this->redirectToRoute('list_games');
    }

}