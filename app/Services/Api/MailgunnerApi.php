<?php

namespace App\Services\Api;

class MailgunnerApi extends BaseApi
{
    private $token;

    public function __construct($username, $password)
    {
        parent::__construct(env('MAILGUNNER_API_URl'));
        if ($username != 'notoken') {
            $this->setToken($username, $password);
        }
    }

    public static function ping()
    {
        try {
            $endpoint = '/';
            $method = 'GET';
            $obj = new BaseApi(env('MAILGUNNER_API_URl'));

            $response = $obj->call($method, $endpoint);
        } catch (\Exception $e) {
            return false;
        }

        $data = json_decode($obj->getResponseContent($response));
        if ($data->message = 'pong') {
            return true;
        }
        return false;
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

    public function sendMail($to, $from, $subject, $body)
    {
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
