<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Classe\Cart;
use App\Classe\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/succes/{stripeSessionId}', name: 'app_order_validate')]
    public function index(Cart $cart, $stripeSessionId, EntityManagerInterface $entityManager): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
       if (!$order || $order->getUser() != $this->getUser()) {
           return $this->redirectToRoute('app_home');
       }
        //Vider panier de l'utilisateur
        $cart->remove();
       if ($order->getState()==0) {
           //Modifier l'état du status isPaid 0 to 1
           $order->setState(1);
           $this->entityManager->flush();

           //Envoie d'email commande client
           $mail = new Mail();
           $content = "Bonjour ".$order->getUser()->getFirstname().",";
           $corps = "Nous vous remercions pour votre commande n°<strong>".$order->getReference()."</strong>. Vous pouvez à tout moment visualiser celle-ci dans la section commande de votre compte.";
           $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Commande validée - Belgian Sound Studio',$content,$corps);

       }

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);

    }
}
