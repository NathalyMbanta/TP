<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Image;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                // 'required' => true, // pas nécessaire, required vaut true par défaut
                // 'label' => 'Nom', // c'est du visuel, c'est mieux dans les templates
                'attr' => [
                    // 'placeholder' => 'Ex.: David', // c'est du visuel, c'est mieux dans les templates
                    'maxLength' => 100
                ]
            ])
            ->add('abstract', TextareaType::class, [
                'attr' => [
                    'maxLength' => 255
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'maxLength' => 65535
                ]
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 99999,
                    'step' => 1
                ]
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 9999.99,
                    'step' => 0.01
                ]
            ])
            ->add('min_players', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 99,
                    'step' => 1
                ]
            ])
            ->add('max_players', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 1,
                    'step' => 1
                ]
            ])
            ->add('minimum_age', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 21,
                    'step' => 1
                ]
            ])
            ->add('duration', TimeType::class, [
                'required' => false
            ])
            ->add('editor', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxLength' => 45
                ]
            ])
            ->add('theme', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 255
                ]
            ])
            ->add('mecanism', TextType::class, [
                'required' => false,
                'attr' => [
                    'maxlength' => 255
                ]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                // 'multiple' => true, // pour autoriser la sélectionne de plusieurs catégories
                // 'expanded' => true, // affichage sous form de boutons checkbox (ou radio si multiple à false)
            ])
            ->add('img1', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format {{ types }}.'
                    ])
                ]
            ])
            ->add('img2', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format {{ types }}.'
                    ])
                ]
            ])
            ->add('img3', FileType::class, [
                'required' => false,
                'mapped' => false,
                'help' => 'png, jpg, jpeg, jp2 ou webp - 1 Mo maximum',
                'constraints' => [
                    new Image([
                        'maxSize' => '1M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/jp2',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci de sélectionner une image au format {{ types }}.'
                    ])
                ]
            ])
            // ->add('Creer', SubmitType::class) // ajoute un bouton sur tous les formulaires de type Product
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
