<?php

namespace App\Controller;


use App\Entity\PaymentForJobs;
use App\Entity\PaymentForJobsMetadata;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use App\Service\PaymentForJobsMetadataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{

    /** @var PaymentForJobsMetadataService  */
    private $pfjmService;

    /**
     * PaypalController constructor.
     * @param PaymentForJobsMetadataService $pfjmService
     */
    public function __construct(PaymentForJobsMetadataService $pfjmService)
    {
        $this->pfjmService = $pfjmService;
    }


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
     *  @Route("/test", name="test")
     */
    public function test(){
        $freePurshases = $this->pfjmService->checkFreePack($this->getUser());

        if (count($freePurshases) > 0)
        {
            /** @var PaymentForJobsMetadata $meta */
            $meta =  $freePurshases[0];
            $intervalo = new \DateInterval('P1M');
            $fecha = $meta->getDatePurchase();

            $fecha->add($intervalo);

            if ($fecha > new \DateTime('now'))
            {
                echo 'OK';
            }
        }
        die;
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
        $freePurshases = $this->pfjmService->checkFreePack($this->getUser());

        if (count($freePurshases) > 0)
        {
            /** @var PaymentForJobsMetadata $meta */
            $meta =  $freePurshases[0];
            $intervalo = new \DateInterval('P1M');
            $fecha = $meta->getDatePurchase();

            $fecha->add($intervalo);

            if ($fecha > new \DateTime('now'))
            {
                $this->addFlash('error','Solo puede adquirir este paquete una vez cada 30 días');
                return $this->redirectToRoute('pricing_page', ['type' => $type]);
            }
        }

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
