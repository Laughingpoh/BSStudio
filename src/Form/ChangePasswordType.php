<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'disabled' => true,
                'label' => 'Mon adresse mail'
            ])
            ->add('firstname', TextType::class,[
                'disabled' => true,
                'label' => 'Mon prénom'
            ])
            ->add('lastname', TextType::class,[
                'disabled' => true,
                'label' => 'Mon nom'
            ])
            ->add('old_password',PasswordType::class,[
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe actuel'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'type' =>PasswordType::class,
                'mapped' => false,
                'invalid_message'=>'La confirmation du mot de passe a echouée',
                'required' => true ,
                'label'=>'Nouveau mot de passe',
                'first_options' => [
                    'label'=>'Nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Saisissez votre nouveau mot de passe'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du nouveau mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmez votre nouveau mot de passe'
                    ]
                ]
            ])

            ->add('submit', SubmitType::class,[
                'label'=>"Mettre à jour",
                'attr' => [
                    'class' => 'btn btn-sm btn-info btn-block mt-3'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
