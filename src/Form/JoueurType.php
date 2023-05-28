<?php

namespace App\Form;

use App\Entity\Game;
use App\Entity\Joueur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JoueurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-top: 10px;margin-bottom:10px',
                ],
                'label' => 'Nom',
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-top: 10px;margin-bottom:10px',
                ],
                'label' => 'Email',
            ])

            ->add('born_at', DateType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-top: 10px;margin-bottom:10px',
                ],
                'label' => 'Date naissance',
                'widget' => 'single_text',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}