<?php
	
	namespace App\Controller;
	
	use App\Entity\Resume;
	use App\Entity\User;
	use App\Form\UserType;
	use FOS\UserBundle\Event\GetResponseUserEvent;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
	use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
	
	class SecurityController extends Controller
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
		 * @Route("/login_success", name="login_success")
		 */
		public function afterLogin()
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			$is_admin = in_array('ROLE_ADMIN', $user->getRoles());
			if ($is_admin) {
				return $this->redirectToRoute('dashboard');
			} else {
				return $this->redirectToRoute('job_list');
			}
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
					$plain_password = $user->getPlainPassword();
					if ($user->getCandidate()) {
						$user->addRole("ROLE_USER");
						$resume = new Resume();
						$resume->setUser($user);
						$user->setResume($resume);
					} elseif ($user->getEmployer()) {
						$user->addRole("ROLE_ADMIN");
					}
					$user->setVerificated(false);
					$user->setSecret(rand(10000, 99999));
//                $user->setImage('/_files_'.$user->getId().'/'.$user->getImage());
					$entityManager->persist($user);
					$entityManager->flush();
					$message = (new \Swift_Message('Bienvenido a emplear.com'))
						->setFrom('emplearecuador@gmail.com')
						->setBody(
							$this->renderView(
								'mail/register.html.twig',
								[
									'user' => $user,
									'plain_password' => $plain_password,
								]
							),
							'text/html'
						)
						->setTo($user->getEmail());
					$mailer->send($message);
					$token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
					$this->get('security.token_storage')->setToken($token);
					$this->get('session')->set('_security_main', serialize($token));
					if (null === $response = $event->getResponse()) {
						$url = $this->generateUrl('dashboard_edit');
						$response = new RedirectResponse($url);
					}
					
					return $response;
				}
				if (null !== $response = $event->getResponse()) {
					return $response;
				}
			}
			
			return $this->render(
				'/security/register.html.twig',
				array(
					'form' => $form->createView(),
					'form2' => $form2->createView(),
				)
			);
		}
	}
