<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//AbstractType comme tout les form
//Pas besoin de le lier a une entité, si j'utilise la console, il me propose de le lier
//à une entité
class SearchType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche...',
                    'class' => 'form-control-sm'
                ]
            ])
        ->add('categories', EntityType::class, [
            'label' => false,
            'required' => false,
            'class' => Category::class,
            'multiple' => true,
            'expanded' => true
        ])
        ->add('submit', SubmitType::class,[
            'label' => 'filtrer',
            'attr' => [
                'class' => 'btn-block btn-dark'
            ]
        ]);

    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'crsf_protection' => false //pas de besoin de cryptage, donc je peux le désactiver
        ]);
    }
    //Permet de ne pas retourner le tableau avec le prefix search dans l'url
    public function getBlockPrefix()
    {
        return '';
    }

}


?>