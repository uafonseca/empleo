<?php

namespace App\Controller;

use App\Entity\Payment;
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
        return $this->render('paypal/index.html.twig', [
            'controller_name' => 'PaypalController',
        ]);
    }
	/**
	 * @Route("/buypackage", name="buy_package")
	 */
	public function buyPackage(Request $request){
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$user =  $em->getRepository(User::class)->find($currentUser->getId());
		$pack = $em->getRepository(Payment::class)->find($request->get('package_id'));
		if($user->getPackage() == null)
		{
			$user->setNumPosts($pack->getAnouncementsNumberMax());
		}else{
			if($user->getNumPosts()>0){
				$user->setNumPosts($user->getNumPosts() + $pack->getAnouncementsNumberMax());
			}
		}
		$user->setPackage($pack);
		$user->setDateOfPurchase(new \DateTime());
		$em->flush();
		return $this->redirectToRoute('dashboard');
	}
	/**
	 * @Route("/buypackage/free/{packId}", name="buy_package_free")
	 */
	public function buyPackageFree($packId){
		$currentUser= $this->get('security.token_storage')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$pack = $em->getRepository(Payment::class)->find($packId);
		$currentUser->setPackage($pack);
		$currentUser->setDateOfPurchase(new \DateTime());
		$em->flush();
		return $this->redirectToRoute('dashboard');
	}
}
