<?php

namespace App\Form;

use App\Entity\Album;
use App\Entity\Category;
use App\Entity\Collectible;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CollectibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'objet',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'desciption',
            ])
            ->add('releaseDate', DateType::class, [
                'label' => 'Date de sortie',
                'years' => range(date('Y') + 2, 1900),
                ])
            ->add('picture', FileType::class, [
                'label' => 'image(jpeg, png file)',
                'mapped' => false,
                'required' => false,

                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Merci d\'utiliser un fichier png,jpeg valide',
                    ])
                ],
            ])
            ->add('poster', UrlType::class, [
                'label' => 'Affiche',
            ])
            ->add('Categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Collectible::class,
        ]);
    }
}
