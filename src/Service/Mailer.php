<?php

/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 8/19/19
 * Time: 3:13 p.m.
 */

namespace App\Service;

use App\Entity\Alert;
use App\Entity\ContactMessage;
use App\Entity\Job;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Swift_Mailer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;


/**
 * Class Mailer
 * @package App\Estudiantes\Service
 */
class Mailer
{

    /** @var RequestStack */
    private $request_stack;

    /** @var UploaderHelper */
    private $vich_uploader;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Environment  */
    private $templating;

    /** @var Swift_Mailer */
    private $mailer;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var EventDispatcherInterface */
    private $event_dispatcher;

    /** @var TokenStorageInterface */
    private $token;

    public function __construct(
        Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        UploaderHelper $vich_uploader,
        RequestStack $request_stack,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $event_dispatcher,
        TokenStorageInterface $token,
        Environment $templating
    ) {
        $this->request_stack = $request_stack;
        $this->vich_uploader = $vich_uploader;
        $this->entityManager = $entityManager;
        $this->templating =  $templating;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->event_dispatcher = $event_dispatcher;
        $this->token = $token;
    }

    /**
     * @param $subject
     * @param $renderedTemplate
     * @param $fromEmail
     * @param $toEmail
     * @param $attachment
     */
    public function sendEmailMessage($subject, $renderedTemplate, $fromEmail, $toEmail, $attachment): void
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setReadReceiptTo(['benditotrabajoecuador@gmail.com'])
            ->setBody($renderedTemplate, 'text/html');
        if ($attachment) {
            $dominio = $this->request_stack->getCurrentRequest()->getSchemeAndHttpHost();

            $message->attach(\Swift_Attachment::fromPath($dominio . $attachment));
        }
        $this->mailer->send($message);
    }

    /**
     * @param Notification $notification
     */
    public function sendNotification(Notification $notification)
    {
        $subject = 'Se ha registrado una nueva notificaci??n';
        $this->sendEmailMessage($subject, $this->notificationTemplate($notification), 'benditotrabajoecuador@gmail.com', $notification->getUser()->getEmail(), false);
    }

    /**
     * @param Notification $notification
     * @return string
     */
    public function notificationTemplate(Notification $notification)
    {
        return $this->templating->render('mail/notification.html.twig', [
            'notification' => $notification
        ]);
    }

    /**
     * @param ContactMessage $message
     */
    public function sendEmailCandidate(ContactMessage $message)
    {
        $subject = 'Nuevo mensaje de ' . $message->getCreator()->getName();
        $this->sendEmailMessage($subject, $this->candidateEmailTemplate($message), 'benditotrabajoecuador@gmail.com', $message->getDestinatario()->getEmail(), false);
    }

    /**
     * @param ContactMessage $message
     * @return string
     */
    public function candidateEmailTemplate(ContactMessage $message)
    {
        return $this->templating->render('mail/privateEmail.html.twig', [
            'message' => $message
        ]);
    }



    public function alertNotification(Job $job)
    {
        $emails = $this->extractEmails($this->entityManager->getRepository(Alert::class)->getValidAlerts());
        
        $template = $this->templating->render('mail/alert.html.twig', [
            'job' => $job
        ]);
        $subject = 'Nueva oferta que quiz??s pueda interesarte ';
        $this->sendEmailMessage($subject, $template, 'benditotrabajoecuador@gmail.com', $emails, false);

    }

    /**
     * Undocumented function
     *
     * @param array $alerts
     * @return array
     */
    public function extractEmails(array $alerts):array{
        $emails = [];
        /** @var Alert $alert */
        foreach ($alerts as $alert) {
            if(filter_var($alert->getEmail(), FILTER_VALIDATE_EMAIL))
                $emails[]=$alert->getEmail();
        }
        return $emails;
    }
}
