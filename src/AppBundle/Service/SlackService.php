<?php

namespace AppBundle\Service;

use CL\Slack\Exception\SlackException;
use CL\Slack\Payload\ChatPostMessagePayload;
use CL\Slack\Transport\ApiClient;
use Monolog\Logger;

class SlackService
{
    private $noticeChannel;
    private $slackClient;
    private $alertsChannel;
    private $payload;
    private $logger;
    const SLACK_USERNAME    = 'Skins4Real';

    public function __construct(ApiClient $slackClient, Logger $logger, $noticeChannel, $alertsChannel)
    {
        $this->logger           = $logger;
        $this->slackClient      = $slackClient;
        $this->noticeChannel    = $noticeChannel;
        $this->alertsChannel    = $alertsChannel;
        $this->payload          = new ChatPostMessagePayload();
    }

    public function sendNotice(string $text)
    {
        try {
            $this->payload->setText($text);
            $this->payload->setUsername(self::SLACK_USERNAME);
            $this->payload->setIconEmoji(':rage:');
            $this->payload->setChannel($this->noticeChannel);
            $this->slackClient->send($this->payload);
        } catch(SlackException $exception) {
            $this->logger->crit(__CLASS__ . ' slack exception', [$exception->getMessage()]);
        } catch (\Exception $exception) {
            $this->logger->crit(__CLASS__ . ' exception ', [$exception->getMessage()]);
        }
    }
}