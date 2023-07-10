<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortByAlbumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortField', ChoiceType::class,[
                'choices' => [
                     'les plus récents' => 'created_at_asc',
                     'les plus anciens' => 'created_at_desc',
                     'De A à Z' => 'title_asc',
                     'De Z à A' => 'title_desc',
                     'Par Univers' => 'universe_asc',
                     'Les mieux notés' => 'rating_asc',
                     'Les moins bien notés' => 'rating_desc',
                    ],
                    'label' => 'Champ de tri',
                    'placeholder' => 'Sélectionnez une option',
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
