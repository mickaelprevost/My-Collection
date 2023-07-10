<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Review;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // on donne null en second argument
            // si on souhaite que Symfony continue de deviner les types
                // ->add('username', EntityType::class, [
                //     'class' => User::class,
                //     'label' => 'Pseudo',
                // ])
                // ->add('email', EmailType::class, [
                //     'label' => 'Courriel',
                // ])
            ->add('comment', TextareaType::class, [
                'label' => 'Commentaire',
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Avis',
                'choices' => [
                    'Excellent' => 5,
                    'Très bon' => 4,
                    'Bon' => 3,
                    'Peut mieux faire' => 2,
                    'A éviter' => 1,
                ],
                'placeholder' => 'Votre choix...',
                // par défaut, multiple est false => choix unique
                'multiple' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
