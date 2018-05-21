<?php

namespace AppBundle\Service;

use AppBundle\Entity\IntegrationDebit;
use Maknz\Slack\Attachment;
use Maknz\Slack\Client;

class NotificationService
{
    /**
     * @var Client
     */
    private $slackClient;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(Client $slackClient, \Swift_Mailer $mailer)
    {
        $this->slackClient = $slackClient;
        $this->mailer = $mailer;
    }

    public function notifyLostPushbacks($depositsIds)
    {
        $messageSlack = $this->slackClient->createMessage();
        $messageSlack->attach(new Attachment([
            "title" => "pushbacks LOST, retrying all",
            'fallback' => 'Skins4Real pushbacks LOST',
            'color'     => 'danger',
            'text' => 'Pushbacks was lost for deposits with ids: ' . $depositsIds,
            'ts' => time(),
        ]));
        $this->sendToSlack($messageSlack);
    }

    public function notifyByEmail($subject, $from, $to, $message)
    {
        $this->sendEmail($subject, $from, $to, $message);
    }

    private function sendToSlack($message)
    {
        $this->slackClient->sendMessage($message);
    }

    private function sendEmail($subject, $from, $to, $message)
    {
        $messageEmail = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody(
                $message, 'text/html'
            )
        ;

        $this->mailer->send($messageEmail);
    }
}