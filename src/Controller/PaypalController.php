<?php

namespace App\Controller;


use App\Entity\PaymentForJobs;
use App\Entity\PaymentForJobsMetadata;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/buypackage", name="buy_package")
     */
    public function buyPackage(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($request->request->get('type') == 'job') {
            /** @var PaymentForJobs $pack */
            $pack = $em->getRepository(PaymentForJobs::class)->find($request->request->get('package_id'));

            $currentUser->addPackageJob($pack);

            $metadata = new PaymentForJobsMetadata();
            $metadata
                ->setUser($currentUser)
                ->setPackage($pack)
                ->setDatePurchase(new \DateTime('now'))
                ->setActive(true)
                ->setCurrentPostCount(0);

            $currentUser->addPaymentForJobsMetadata($metadata);

            $pack->addPaymentForJobsMetadata($metadata);

            $em->persist($metadata);
            $em->flush();

            //TODO  proceder al pago con PayPal

        } else {
            /** @var PaymentForServices $pack */
            $pack = $em->getRepository(PaymentForServices::class)->find($request->request->get('package_id'));

            $currentUser->addPackageService($pack);

            $metadata = new PaymentForServicesMetadata();
            $metadata
                ->setUser($currentUser)
                ->setPackage($pack)
                ->setDatePurchase(new \DateTime('now'))
                ->setActive(true)
                ->setCurrentPostCount(0);

            $currentUser->addPaymentForServicesMetadata($metadata);

            $pack->addPaymentForServicesMetadata($metadata);

            $em->persist($metadata);
            $em->flush();

            //TODO  proceder al pago con PayPal
        }

        return $this->redirectToRoute('dashboard');
    }

    /**
     * @param $packId
     * @param $type
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     * @Route("/buypackage/free/{packId}/{type}", name="buy_package_free")
     */
    public function buyPackageFree($packId, $type)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        if ($type === 'job') {
            /** @var PaymentForJobs $pack */
            $pack = $em->getRepository(PaymentForJobs::class)->find($packId);

            if ($pack === null || $pack->getPrice() !== 0)
                return $this->redirectToRoute('pricing_page', ['type' => $type]);

            $currentUser->addPackageJob($pack);

            $metadata = new PaymentForJobsMetadata();
            $metadata
                ->setUser($currentUser)
                ->setPackage($pack)
                ->setDatePurchase(new \DateTime('now'))
                ->setActive(true)
                ->setCurrentPostCount(0);

            $currentUser->addPaymentForJobsMetadata($metadata);

            $pack->addPaymentForJobsMetadata($metadata);
        } else {
            /** @var PaymentForServices $pack */
            $pack = $em->getRepository(PaymentForServices::class)->find($packId);

            if ($pack === null || $pack->getPrice() !== 0)
                return $this->redirectToRoute('pricing_page', ['type' => $type]);

            $currentUser->addPackageService($pack);

            $metadata = new PaymentForServicesMetadata();
            $metadata
                ->setUser($currentUser)
                ->setPackage($pack)
                ->setDatePurchase(new \DateTime('now'))
                ->setActive(true)
                ->setCurrentPostCount(0);

            $currentUser->addPaymentForServicesMetadata($metadata);

            $pack->addPaymentForServicesMetadata($metadata);
        }

        $em->persist($metadata);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
}
