<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le titre est obligatoire.'])
                ]
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('videoId', TextType::class, [
                'required' => false,
                'label' => 'ID de la Vidéo',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => 'L\'ID de la vidéo ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Playlist',
                'attr' => ['class' => 'form-select']
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'label' => 'Catégories',
                'attr' => ['class' => 'form-select']
            ])
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de Publication',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}