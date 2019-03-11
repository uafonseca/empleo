<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Entity\User;
use App\Form\UserType;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, \Swift_Mailer $mailer)
    {
        $user = new User();
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(UserType::class, $user);
        $form2 = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $form2->handleRequest($request);
        if ($form->isSubmitted() || $form2->isSubmitted()) {
            if ($form->isValid() || $form2->isValid()) {
                if ($user->getCandidate()) {
                    $user->addRole("ROLE_USER");
                    $resume = new Resume();
                    $resume->setUser($user);
                    $user->setResume($resume);
                } elseif ($user->getEmployer()) {
                    $user->addRole("ROLE_ADMIN");
                }
                $entityManager->persist($user);
                $entityManager->flush();
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('homepage');
                    $response = new RedirectResponse($url);
                }
                $message = (new \Swift_Message('Bienvenido a emplear.com'))
                    ->setFrom('emplearecuador@gmail.com')
                    ->setBody($this->renderView(
                        'mail/register.html.twig',
                        ['user' => $user]
                    ),
                        'text/html'
                    )
                    ->setTo($user->getEmail());
                $mailer->send($message);
                return $response;
            }
            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        return $this->render('/security/register.html.twig', array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ));
    }
}
