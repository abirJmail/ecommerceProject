<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Header;
use App\Form\RegisterType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Loader\Configurator\mailer;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    
    // private EmailVerifier $emailVerifier;
    private $entityManager;
    
    public function __construct(private MailerInterface $mailer,EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        // $this->emailVerifier = $emailVerifier;
        
    }

    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $encoder,MailerInterface $mailer): Response
    {
        $headers = $this->entityManager->getRepository(Header::class)->findAll();
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $notification = null;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            //    dd($user);
            
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());


            if (!$search_email) {
                  // encode the plain password
            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
   

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // // generate a signed url and email it to the user
            $email = (new Email())
            // ->from('joaillerie turqueza@france.com')
            ->from('hello@example.com')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Bienvenue sur La Boutique Joaillerie Turqurza')
            ->text('Bienvenue sur La Boutique Joaillerie Turqurza')
            ->html('<p>Bonjour<br/>Bienvenue sur notre boutique en France.<br><br/>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam expedita fugiat ipsa magnam mollitia optio voluptas! Alias, aliquid dicta ducimus exercitationem facilis, incidunt magni, minus natus nihil odio quos sunt?</p>');

        $mailer->send($email);

            // $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            return $this->redirectToRoute('app_home');
        } else {
            $notification = "L'email que vous avez renseigné existe déjà.";
        }
        }


        return $this->render('register/index.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form->createView(),
            'notification' => $notification,
            'headers' => $headers
        ]);
    }

}
