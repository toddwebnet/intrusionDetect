<?php
namespace App\Services\Responses;

use Illuminate\Http\Response;

interface ResponseInterface
{
    /**
     * Builds the message from the given hash and returns a json string
     * @param $message
     * @return null|string
     */
    public function getContent($message): ?string;

    /**
     * Returns resposne code associated with message
     * @return int
     */
    public function getResponseCode(): int;

    /**
     * Returns the built http response
     * @param array $message
     * @return \Illuminate\Http\Response
     */
    public function getResponse($message): Response;

}
