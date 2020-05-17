<?php
namespace App\Services\Responses;


use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseCodes;

class ApiError extends ApiResponseBase
{


    /**
     * @param $messages
     * @return string
     */
    public function getContent($messages): ?string
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        // Build error objects
        $errors = [];
        foreach ($messages as $i => $message) {
            if (is_array($message)) {
                $errors[] = (object)$message;
            } elseif (is_string($message)) {
                $errors[] = (object)[
                    'field' => '',
                    'message' => $message,
                ];
            }
        }

        return json_encode((object)[
            'errors' => $errors
        ]);
    }


    /**
     * handleException
     * simplify calling api exception with exception object
     * @param \Exception $e
     * @return \Illuminate\Http\Response
     */
    public static function handleException(\Exception $e)
    {
        $code = 0;
        if (method_exists($e, 'getCode')) {
            $code = $e->getCode();
        }
        if ($code == 0) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        return (new self($code))->getResponse([$e->getMessage()]);
    }

    /**
     * return response for an error
     *
     * @param $messages
     * @param int $code
     * @return Response
     */
    public static function handleErrors($messages, $code = Response::HTTP_BAD_REQUEST)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        return (new self($code))->getResponse($messages);
    }

}
