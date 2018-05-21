<?php
namespace AppBundle\Service;

use GuzzleHttp\Client;

class CaptchaService
{
    private $url = "https://www.google.com/recaptcha/api/siteverify";

    public function __construct()
    {

    }

    public function verify($captcha, $secret)
    {
        $httpClient = new Client();
        $result = json_decode($httpClient->post($this->url, [
            'form_params'   => [
                'secret'    => $secret,
                'response'  => $captcha,
            ]
        ])->getBody()->getContents(), true);

        return $result['success'] ?? false;
    }

}
