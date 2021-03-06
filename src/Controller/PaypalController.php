<?php

namespace App\Controller;

use App\constants;
use App\Entity\Notification;
use App\Entity\PaymentForJobs;
use App\Entity\PaymentForJobsMetadata;
use App\Entity\PaymentForServices;
use App\Entity\PaymentForServicesMetadata;
use App\Entity\User;
use App\Service\PaymentForJobsMetadataService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PaypalController extends AbstractController
{

    /** @var PaymentForJobsMetadataService  */
    private $pfjmService;

    /** @var SessionInterface  */
    private $session;

    /**
     * PaypalController constructor.
     * @param PaymentForJobsMetadataService $pfjmService
     * @param SessionInterface $session
     */
    public function __construct(PaymentForJobsMetadataService $pfjmService, SessionInterface $session)
    {
        $this->pfjmService = $pfjmService;
        $this->session = $session;
    }

    /**
     * @param $type
     * @return RedirectResponse
     * @Route("/cancell/{type}", name="pago_cancelado")
     */
    public function pagoCancelado($type)
    {
        $this->session->set('_error_', 'Ha ocurrido un error al procesar su solicitud');

        return $this->redirectToRoute('pricing_page', ['type' => $type]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     * @Route("/buypackage/{type}/{uuid}", name="buy_package")
     */
    public function buyPackage(Request $request, $type, $uuid)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if ($type == 'job') {
            /** @var PaymentForJobs $pack */
            $pack = $em->getRepository(PaymentForJobs::class)->findOneBy(array('uuid' => $uuid));

            $currentUser->addPackageJob($pack);

            $transaccion = $request->query->get('transaccion');

            $metadata = new PaymentForJobsMetadata();
            $metadata
                ->setUser($currentUser)
                ->setPackage($pack)
                ->setDatePurchase(new \DateTime('now'))
                ->setActive(true)
                ->setTransaccion($transaccion)
                ->setCurrentPostCount(0);

            $currentUser->addPaymentForJobsMetadata($metadata);

            $pack->addPaymentForJobsMetadata($metadata);

            $em->persist($metadata);


            $notification = new Notification();
            $notification->setDate(new \DateTime());
            $notification->setType(constants::NOTIFICATION_PAYMENT_SUCCESS);
            $notification->setContext("Pago efectuado satisfactoriamente");
            $notification->setUser($currentUser);
            $notification->setActive(true);
            $em->persist($notification);


            $em->flush();
        } else {
            /** @var PaymentForServices $pack */
            $pack = $em->getRepository(PaymentForServices::class)->findOneBy(array('uuid' => $uuid));

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
    public function test()
    {
        $freePurshases = $this->pfjmService->checkFreePack($this->getUser());

        if (count($freePurshases) > 0) {
            /** @var PaymentForJobsMetadata $meta */
            $meta =  $freePurshases[0];
            $intervalo = new \DateInterval('P1M');
            $fecha = $meta->getDatePurchase();

            $fecha->add($intervalo);

            if ($fecha > new \DateTime('now')) {
                echo 'OK';
            }
        }
        die;
    }

    /**
     * @param $packId
     * @param $type
     * @return RedirectResponse
     * @throws Exception
     * @Route("/buypackage/free/{packId}/{type}", name="buy_package_free")
     */
    public function buyPackageFree($packId, $type)
    {
        $freePurshases = $this->pfjmService->checkFreePack($this->getUser());

        if (count($freePurshases) > 0) {
            /** @var PaymentForJobsMetadata $meta */
            $meta =  $freePurshases[0];
            $intervalo = new \DateInterval('P1M');
            $fecha = $meta->getDatePurchase();

            $fecha->add($intervalo);

            if ($fecha > new \DateTime('now')) {

                $this->session->set('_error', 'Solo puede adquirir este paquete una vez cada 30 d??as');

                return $this->redirectToRoute('pricing_page', ['type' => $type]);
            }
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        if ($type === 'job') {
            /** @var PaymentForJobs $pack */
            $pack = $em->getRepository(PaymentForJobs::class)->find($packId);

            if ($pack === null || $pack->getPrice() !== 0  /*|| $currentUser->getBuyFreePackJob()*/){
        
                return $this->redirectToRoute('pricing_page', ['type' => $type]);
            }

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

            if ($pack === null || $pack->getPrice() !== 0 || $currentUser->getBuyFreePackService())
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