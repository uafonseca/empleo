<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
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
                10
            )
        ]);

    }
}
