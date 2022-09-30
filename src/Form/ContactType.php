<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('firstname',TextType::Class,[
                'label'=>false,
                'constraints'=> new Length(2,3,15,
                    minMessage: 'Votre prénom doit contenir plus de 3 caractères',
                    maxMessage: 'Votre prénom doit contenir moins de 15 caractères'),
                'attr'=> [
                    'placeholder' => 'Prénom'
                ]
            ])//input
            ->add('lastname',TextType::Class,[
                'label'=>false,
                'constraints'=> new Length(2,3,15,
                    minMessage: 'Votre nom doit contenir plus de 3 caractères',
                    maxMessage: 'Votre nom doit contenir moins de 15 caractères'),
                'attr'=> [
                    'placeholder' => 'Nom de famille'
                ]
            ])//input
            ->add('email',EmailType::class,[
                'label'=>false,
                'constraints'=> new Length(2,3,40,
                    minMessage: 'Votre adresse mail doit contenir plus de 3 caractères',
                    maxMessage: 'Votre adresse mail doit contenir moins de 40 caractères'),
                'attr' => [
                    'placeholder' => 'Adresse mail'
                ]
            ])//input
            ->add('content', TextareaType::class,[
                'label' => false,
                'attr' => [
                    'placeholder' => "Contenu du message"
                ]
            ])
            ->add('submit', SubmitType::class,[
                'label'=>"Envoyer",
                'attr' => [
                    'class' => 'btn btn-lg btn-dark btn-block mt-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //Passage du User OrderController vers OrderType
        ]);
    }
}
