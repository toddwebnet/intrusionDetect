<?php

namespace App\Services\Api;

class MailgunnerApi extends BaseApi
{
    private $token;

    public function __construct($username, $password)
    {
        parent::__construct(env('MAILGUNNER_API_URl'));
        $this->setToken($username, $password);
    }

    private function setToken($username, $password)
    {
        $endpoint = '/token/get';
        $params = [
            'username' => $username,
            'password' => $password
        ];
        $method = 'POST';

        $response = $this->call($method, $endpoint, $params);

        try {

            $this->token = json_decode($this->getResponseContent($response))->token;
        } catch (\Exception $e) {
            $this->token = null;
        }

        if ($this->token === null || $this->token == '') {
            throw new \Exception('Unable to get token from Mailgunner Api');
        }
    }

    public function sendMail($to, $from, $subject, $body){
        $options = ['headers' => ['token' => $this->token]];
        $method = "POST";
        $endPoint = "/send-mail";

        $params = [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
            'body' => $body
        ];
        $response = $this->call($method, $endPoint, $params, $options);
        return $this->getResponseContent($response);
    }
    public function getDone()
    {
        $options = ['headers' => ['token' => $this->token]];
        $method = "GET";
        $endPoint = "/done";
        $params = [];
        $response = $this->call($method, $endPoint, $params, $options);
        return $this->getResponseContent($response);

    }

}
