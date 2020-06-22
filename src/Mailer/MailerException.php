<?php


namespace App\Mailer;

class MailerException
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * Contructor MailerException.
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param $exception
     */
    public function sendRedisErrorEmailMessage($exception)
    {
        $this->sendEmailMessage($exception, ['baboon.cortex@gmail.com' => 'Error Redis'], 'roberto910907@gmail.com');
    }

    /**
     * @param string $exception
     * @param array $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($exception, $fromEmail, $toEmail)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Aviso Servicio Caído')
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($exception, 'text/html');

        $this->mailer->send($message);
    }
}
