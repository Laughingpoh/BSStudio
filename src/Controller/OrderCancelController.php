<?php

namespace App\Controller;

use App\Entity\Order;
use App\Classe\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/commande/erreur/{stripeSessionId}', name: 'app_order_cancel')]
    public function index($stripeSessionId): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //Envoie email pour lui indiquer echec de paiement / commande incomplète
        //Envoie d'email commande client
        $mail = new Mail();
        $content = "Bonjour ".$order->getUser()->getFirstname().",";
        $corps = "Votre commande n°<strong>".$order->getReference()."</strong> n'a pas été validée.</br> Veuillez réesayer ";
        $corps = "Nous avons le regret de vous informer que votre paiement pour la commande n°<strong>".$order->getReference()."</strong> n'a pas abouti.</br></br>Veuillez utiliser un autre mode de paiement ou réessayer plus tard.";
        $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Commande incomplète - Belgian Sound Studio',$content,$corps);
        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
