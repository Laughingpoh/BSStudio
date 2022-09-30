<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Classe\Mail;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{

    private $entityManager;
    private $adminUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator) {

        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        //Ajouter un paramètre custom pour easyadmin
        $updateTraitement = Action::new('updateTraitement','En cours de traitement','fas fa-clock')->linkToCrudAction('updateTraitement');
        $updateTraite = Action::new('updateTraite','Traité','fas fa-check-circle')->linkToCrudAction('updateTraite');

        return $actions
            ->add('detail',$updateTraitement)
            ->add('detail',$updateTraite)
            ->add('index','detail');
    }

    public function updateTraitement(AdminContext $context) {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice',"
        <div class='alert alert-success' role='alert'>
            La commande <u>".$order->getReference()."</u> est <strong>en cours de traitement.</strong>
        </div>
");
        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction('index')
        ->generateUrl();

        //Envoyer mail ?
        //$mail = new Mail();
        //$mail->send($order->getUser()->getEmail(),);

        return $this->redirect($url);
    }
    public function updateTraite(AdminContext $context) {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice',"
        <div class='alert alert-success' role='alert'>
            La commande <u>".$order->getReference()."</u> a été  <strong>traitée.</strong>
        </div>
");
        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        //Envoyer mail ?
        //$mail = new Mail();
        //$mail->send($order->getUser()->getEmail(),);

        return $this->redirect($url);
    }

    //TRIER les commandes en mettant l'id le plus grand au début
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt','Date de la commande'),
            TextField::new('user.getFullName','Utilisateur'),
            TextEditorField::new('delivery','Adresse')->formatValue(function ($value) { return $value; })->onlyOnDetail(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName','Délais de traitement'),
            MoneyField::new('carrierPrice','Frais de traitement')->setCurrency('EUR'),
            //BooleanField::new('isPaid', 'Payée'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'En cours de traitement' => 2,
                'Traité' => 3
            ]),
            ArrayField::new('orderDetails', 'Services achetés ')->hideOnIndex()
            //HideOnindex permet de pas l'afficher sur le dashboard des produits
        ];
    }

}
