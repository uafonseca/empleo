<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Service\NotificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{

    /** @var NotificationService  */
    private $notificationService;

    /**
     * NotificationController constructor.
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/notification_list", name="notification_list", options={"expose" = true})
     */
    public function showAll()
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $this->notificationService->orderByDate($this->getUser())
        ]);
    }

    /**
     * @param Notification $notification
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/remove/{id}", name="notification_remove", options={"expose" = true})
     */
    public function delete(Notification $notification)
    {
        $this->notificationService->remove($notification);
        $this->addFlash('success', 'Notificación eliminada');
        return $this->redirectToRoute('notification_list');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/delete/all", name="notification_remove_all", options={"expose" = true})
     */
    public function removeAll()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Notification $notification */
        foreach ($this->getUser()->getNotifications() as $notification) {
            $em->remove($notification);
            $em->flush();
        }
        $this->addFlash('success', 'Notificaciones eliminadas');
        return $this->redirectToRoute('notification_list');
    }

    /**
     * @param NotificationRepository $notificationRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="notification_index", options={"expose" = true})
     */
    public function getNotifications(NotificationRepository $notificationRepository)
    {
        $user = $this->getUser();

        return $this->render('notification/modal.html.twig', [
            'notifications' => $notificationRepository->findBy(
                [
                    'user' => $user,
                    'active' => true,
                ],
                [
                    'date' => 'DESC',
                ],
                5
            )
        ]);
    }



    /**
     * Marca todas las notificaciones como leidas
     *
     * @return Response
     * 
     * @Route("/mark_all_as_read", name="mark_all_as_read", options={"expose" = true})
     */
    public function readAll(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $notifications = $em->getRepository(Notification::class)->findBy([
            'active' => true
        ]);

        /** @var Notification $n */
        foreach ($notifications as $n)
            $n->setActive(false);

        $em->flush();

        return new JsonResponse([
            'type' => 'success',
        ]);
    }
}
