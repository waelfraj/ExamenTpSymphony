<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
class GameType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px'],
                'required' => true
            ])
            ->add('type', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px'],
                'required' => true

            ])
            ->add('editeur', TextType::class, [
                'attr' => ['class' => 'form-control', 'style' => 'margin-top: 10px;margin-bottom:10px'],
                'required' => true

            ])
            ->add('image', FileType::class, [
                'attr' => ['style' => 'margin-top: 10px;margin-bottom:10px'],
                'required' => true

            ])
            ->add('valider', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary', 'style' => 'margin-top: 10px']
            ]);

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }


}