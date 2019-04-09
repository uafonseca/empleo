<?php
	
	namespace App\Controller;
	
	use App\Entity\Payment;
	use App\Entity\User;
	use App\Form\PaymentType;
	use App\Repository\PaymentRepository;
	use Symfony\Bundle\FrameworkBundle\Controller\Controller;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
	
	/**
	 * @Route("/payment")
	 * @IsGranted("ROLE_SUPER_ADMIN")
	 */
	class PaymentController extends Controller
	{
		/**
		 * @Route("/", name="payment_index", methods={"GET"})
		 */
		public function index(PaymentRepository $paymentRepository): Response
		{
			return $this->render(
				'payment/index.html.twig',
				array(
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
					'payments' => $paymentRepository->findAll(),
				)
			);
		}
		
		/**
		 * @Route("/new", name="payment_new", methods={"GET","POST"})
		 */
		public function new(Request $request): Response
		{
			$payment = new Payment();
			$form = $this->createForm(PaymentType::class, $payment);
			$form->handleRequest($request);
			
			if ($form->isSubmitted() && $form->isValid()) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($payment);
				$entityManager->flush();
				
				return $this->redirectToRoute('payment_index');
			}
			
			return $this->render(
				'payment/new.html.twig',
				[
					'payment' => $payment,
					'form' => $form->createView(),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				]
			);
		}
		
		/**
		 * @Route("/{id}", name="payment_show", methods={"GET"})
		 */
		public function show(Payment $payment): Response
		{
			return $this->render(
				'payment/show.html.twig',
				[
					'payment' => $payment,
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				]
			);
		}
		
		/**
		 * @Route("/{id}/edit", name="payment_edit", methods={"GET","POST"})
		 */
		public function edit(Request $request, Payment $payment): Response
		{
			$form = $this->createForm(PaymentType::class, $payment);
			$form->handleRequest($request);
			
			if ($form->isSubmitted() && $form->isValid()) {
				$this->getDoctrine()->getManager()->flush();
				
				return $this->redirectToRoute(
					'payment_index',
					[
						'id' => $payment->getId(),
					]
				);
			}
			
			return $this->render(
				'payment/edit.html.twig',
				[
					'payment' => $payment,
					'form' => $form->createView(),
					'notifications' => $this->container->get('app.service.helper')->loadNotifications(),
				]
			);
		}
		
		/**
		 * @Route("/{id}", name="payment_delete", methods={"DELETE"})
		 */
		public function delete(Request $request, Payment $payment): Response
		{
			if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
				$entityManager = $this->getDoctrine()->getManager();
				$users = $entityManager->getRepository(User::class)->findAll();
				foreach ($users as $user) {
					if ($user->getPackage() != null && $user->getPackage()->getId() == $payment->getId()) {
						$user->setPackage(null);
					}
				}
				$entityManager->remove($payment);
				$entityManager->flush();
			}
			
			return $this->redirectToRoute('payment_index');
		}
	}
