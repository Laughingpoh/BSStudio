<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Classe\Mail;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request); //formulaire ecoute la requete

        if ($form->isSubmitted() && $form->isValid() ) {
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if (!$search_email) {
                $password = $encoder->hashPassword($user,$user->getPassword());
                $user->setPassword($password);
                //dd($password);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "Inscription validée";

                $mail = new Mail();
                $content = "Bonjour ".$user->getFirstname().",";
                $corps = "Merci pour votre inscription à notre plateforme. Vous pouvez désormais effectuer des transactions sur notre plateforme.</br>	
Vous pouvez consulter dès à présent consulter nos offres, vos commandes ainsi que d'autres informations concernant votre compte dans votre Dashboard.";
                $mail->send($user->getEmail(),$user->getFirstname(),'Inscription - sound studio',$content,$corps);

            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }

            //$errors = $form->getErrors();

        }
        return $this->render('register/index.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
