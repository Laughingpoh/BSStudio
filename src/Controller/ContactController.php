<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted()&&$form->isValid()) {
            $this->addFlash('notice','Merci de nous avoir contacter. Notre équipe vous contactera dans les meilleurs délais.');
            $mail = new Mail();
            $content="Message de ".$form->get('firstname')->getData()." ".$form->get('lastname')->getData()." (".$form->get('email')->getData().") :";
            $corps=$form->get('content')->getData();
            $mail->send('amin.elmoubachar@iramps.email','BSStudio','Nouvelle demande de contact',$content,$corps);
        }
        return $this->render('contact/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
