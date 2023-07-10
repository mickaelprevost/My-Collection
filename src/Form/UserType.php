<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Courriel',
            ])
            ->add('username', TextType::class, [
                'label' => 'Pseudo',
            ])
            ->add('roles', ChoiceType::class, [
                // les choix
                'label' => 'Rôles',
                'choices' => [
                    // label, valeur
                    'Utilisateur' => 'ROLE_USER',
                    'Modérateur' => 'ROLE_MANAGER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                // $roles = array donc choix multiple
                'multiple' => true,
                // checkboxes (1 widget HTML par choix)
                'expanded' => true,
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
            ->add('password', TextType::class, [
                'label' => 'Mot de passe',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => user::class,
        ]);
    }

}
