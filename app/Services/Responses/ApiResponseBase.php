<?php
namespace App\Services\Responses;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;

abstract class ApiResponseBase implements ResponseInterface
{
    private $responseCode;

    /**
     * ApiError constructor.
     * @param int|null $responseCode
     */
    public function __construct(int $responseCode = ResponseCodes::HTTP_INTERNAL_SERVER_ERROR)
    {
        $this->setResponseCode($responseCode);
    }

    /**
     * @param $messages
     * @return \Illuminate\Http\Response
     */
    public function getResponse($messages): Response
    {
        return Response($this->getContent($messages), $this->getResponseCode());
    }

    /**
     * getResponseCode
     *
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * setResponseCode
     *
     * @param $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }
}
