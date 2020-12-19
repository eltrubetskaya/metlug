<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MailService.
 */
class MailService
{
    public const CREATE_INSTALLATION_TEMPLATE = 'email/';
    public const CREATE_COMMENT_TEMPLATE = 'email/new_comment';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var Environment
     */
    protected $templating;

    public function __construct(Session $session, \Swift_Mailer $mailer, string $from, Environment $templating)
    {
        $this->session = $session;
        $this->mailer = $mailer;
        $this->from = $from;
        $this->templating = $templating;
    }

    /**
     * @param $sendTo
     * @param $template
     *
     * @return bool|int
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail($sendTo, string $subject, $template, array $params = [])
    {
        try {
            $message = (new \Swift_Message("The «METLUG» Theatre - " . $subject))
                ->setFrom($this->from)
                ->setTo($sendTo)
                ->setBody($this->renderMail($template . '.html.twig', $params), 'text/plain')
            ;

            return $this->mailer->send($message);
        } catch (\Swift_TransportException $e) {
            //log mail fail

            $this->session->getFlashBag()->add('danger', 'There was a problem sending email to "' . $sendTo . '": ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @param $name
     * @param $params
     *
     * @return string
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderMail($name, $params)
    {
        return $this->templating->render($name, $params);
    }

    /**
     * @return \Swift_Mailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }
}
