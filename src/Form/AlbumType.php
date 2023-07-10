<?php

namespace App\Form;

use App\Entity\Universe;
use App\Entity\Album;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;

class AlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la collection',
                'attr' => [
                    // le placeholder est un attribut HTML
                    'placeholder' => 'ex. Cartes Pokemon',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'=>'Description',
            ])
            ->add('poster', FileType::class, [
                'label' => 'image(jpeg, png file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Merci d\'utiliser un fichier png,jpeg,jpg,webp valide',
                    ])
            ]
            ])
            ->add('universeId', EntityType::class, [
                'class' => Universe::class,
                'label' => 'univers',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Album::class,
        ]);
    }
}
