<?php

namespace App\Mailer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Templating\EngineInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var UploaderHelper
     */
    protected $uploader;

    /**
     * @var RequestStack
     */
    protected $request_stack;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var Security
     */
    protected $security;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param UrlGeneratorInterface $router
     * @param EngineInterface $templating
     * @param Security $security
     * @param UploaderHelper $vich_uploader
     * @param RequestStack $request_stack
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        EngineInterface $templating,
        Security $security,
        UploaderHelper $vich_uploader,
        RequestStack $request_stack,
        EntityManagerInterface $entityManager
    ) {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->templating = $templating;
        $this->security = $security;
        $this->uploader = $vich_uploader;
        $this->request_stack = $request_stack;
        $this->entityManager = $entityManager;
    }



    /**
     * @param string $renderedTemplate
     * @param array $fromEmail
     * @param string $toEmail
     * @param string $CcEmail
     * @param string $documento_url
     */
    public function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail, $CcEmail, $documento_url = null): void
    {
        $dominio = $this->request_stack->getCurrentRequest()->getSchemeAndHttpHost();

        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = (new \Swift_Message($subject))
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setCc($CcEmail)
            ->attach(\Swift_Attachment::fromPath($dominio . $documento_url))
            ->setBody($renderedTemplate, 'text/html');

        $this->mailer->send($message);
    }

}
