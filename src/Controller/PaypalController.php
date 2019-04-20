<?php
	
	namespace App\Controller;
	
	use App\Entity\Payment;
	use App\Entity\PaymentForJobs;
	use App\Entity\PaymentForServices;
	use App\Entity\User;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Validator\Constraints\DateTime;
	
	class PaypalController extends AbstractController
	{
		/**
		 * @Route("/paypal", name="paypal")
		 */
		public function index()
		{
			return $this->render(
				'paypal/index.html.twig',
				[
					'controller_name' => 'PaypalController',
				]
			);
		}
		
		/**
		 * @Route("/buypackage", name="buy_package")
		 */
		public function buyPackage(Request $request)
		{
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$em = $this->getDoctrine()->getManager();
			$user = $em->getRepository(User::class)->find($currentUser->getId());
			if ($request->request->get('type') == 'job') {
				$pack = $em->getRepository(PaymentForJobs::class)->find($request->request->get('package_id'));
				if ($user->getPackage() == null) {
					$user->setNumPosts($pack->getAnouncementsNumberMax());
				} else {
					if ($user->getNumPosts() > 0) {
						$user->setNumPosts($user->getNumPosts() + $pack->getAnouncementsNumberMax());
					}
				}
				$user->setPackage($pack);
				$user->setDateOfPurchase(new \DateTime());
			} else {
				$pack = $em->getRepository(PaymentForServices::class)->find($request->request->get('package_id'));
				if ($user->getPackageServices() == null) {
					$user->setNumPostsServices($pack->getAnouncementsNumberMax());
					
				} else {
					if ($user->getNumPosts() > 0) {
						$user->setNumPosts($user->getNumPosts() + $pack->getAnouncementsNumberMax());
					}
				}
				$user->setPackageServices($pack);
				$user->setDateOfPurchaseService(new \DateTime());
			}
			$em->flush();
			
			return $this->redirectToRoute('dashboard');
		}
		
		/**
		 * @Route("/buypackage/free/{packId}/{type}", name="buy_package_free")
		 */
		public function buyPackageFree($packId, $type)
		{
			$currentUser = $this->get('security.token_storage')->getToken()->getUser();
			$em = $this->getDoctrine()->getManager();
			if ('job' == $type) {
				$pack = $em->getRepository(PaymentForJobs::class)->find($packId);
				if ($currentUser->getPackage() == null) {
					$currentUser->setNumPosts($pack->getAnouncementsNumberMax());
				} else {
					if ($currentUser->getNumPosts() > 0) {
						$currentUser->setNumPosts($currentUser->getNumPosts() + $pack->getAnouncementsNumberMax());
					}
				}
				$currentUser->setPackage($pack);
				$currentUser->setDateOfPurchase(new \DateTime());
			} else {
				$pack = $em->getRepository(PaymentForServices::class)->find($packId);
				if ($currentUser->getPackageServices() == null) {
					$currentUser->setNumPostsServices($pack->getAnouncementsNumberMax());
				} else {
					if ($currentUser->getNumPostsServices() > 0) {
						$currentUser->setNumPostsServices(
							$currentUser->getPackageServices() + $pack->getAnouncementsNumberMax()
						);
					}
				}
				$currentUser->setPackageServices($pack);
				$currentUser->setDateOfPurchaseService(new \DateTime());
			}
			$em->flush();
			
			return $this->redirectToRoute('dashboard');
		}
	}
