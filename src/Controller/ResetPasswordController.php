<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Classe\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{

    private $entityManager;

    /**
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        $YOUR_DOMAIN = 'http://46.101.60.72';
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if($request->get('email')) {
          $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

          if($user) {
              //Demande de reset password (user, token et date création)
              $reset_password = new ResetPassword();
              $reset_password->setUser($user);
              $reset_password->setToken(uniqid());
              $reset_password->setCreatedAt(new \DateTimeImmutable());
              $this->entityManager->persist($reset_password);
              $this->entityManager->flush();

              // Envoie mail utilisateur avec lien de réinitialisation MDP
              $content = "Bonjour ".$user->getFullName();
              $url = $this->generateUrl('app_update_password',[
                  'token' => $reset_password->getToken()
              ]);
              dd($url);
              $corps="Vous avez récemment demandé la réinitialisation du mot de passe de votre identifiant. <br/>Pour terminer le processus, veuillez <a href='".$YOUR_DOMAIN.$url."'>cliquer ici</a>.";
              $mail = new Mail();
              $mail->send($user->getEmail(), $user->getFullName(),'Réinitialisation de mot de passe - Belgian Sound Studio',$content,$corps);
              $emailHash = $user->hideEmailAddress($user->getEmail());
              //$this->addFlash('notice',"Un e-mail a été envoyé à votre adresse ".$emailHash.". Suivez les instructions fournies pour réinitialiser votre mot de passe.");
              $this->addFlash('notice', [
                  'first' => 'Un e-mail a été envoyé à votre adresse ',
                  'email' => $emailHash,
                  'second' => '. Suivez les instructions fournies pour réinitialiser votre mot de passe.'
              ]);

          } else {
              $textEror = "Cette adresse mail est inconnue. Veuillez réessayer.";
              $this->addFlash('notice2',$textEror);
          }
        }

        return $this->render('reset_password/index.html.twig');
    }
    #[Route('/modification-mot-de-passe/{token}', name: 'app_update_password')]
    public function update(Request $request, $token, UserPasswordHasherInterface $encoder): Response
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('app_reset_password');
        }

        //Vérifier si createdAt == now - 3h
        $now = new \DateTimeImmutable();
        if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour')) {
            //Token expiré
            $this->addFlash('notice','Demande de mot de passe expirée.');
            return $this->redirectToRoute( 'app_reset_password');
        }

        //Vue avec mot de passe et confirmation mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $new_pwd = $form->get('new_password')->getData();
            //dd($new_pwd);

            //encodage mot de passe
            $password = $encoder->hashPassword($reset_password->getUser(),$new_pwd);
            $reset_password->getUser()->setPassword($password);
            //flush DB
            $this->entityManager->flush();
            //redirection vers page connexion
            $this->addFlash('notice','Votre mot de passe a bien été mise à jour.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/update.html.twig',[
            'form' => $form->createView()
        ]);

    }
}
