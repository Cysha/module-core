<?php namespace Cysha\Modules\Core\Controllers;

use Illuminate\Support\Facades\Response;
use Dingo\Api\Routing\Controller;

class BaseApiController extends Controller
{


    /**
     * Alias method for sending an error back
     *
     * @param string  $message
     * @param integer $status  HTTP Status Code
     */
    public function sendResponse($message = 'ok', $status = 200, $data = array())
    {
        $reply = array(
            'status'  => $status,
            'message' => $message,
        );

        if (!empty($data)) {
            $reply['data'] = $data;
        }

        // return $this->response->array($reply)->setStatusCode($status);
        return Response::json($reply, $status);
    }

    /**
     * Alias method for sending an error back
     *
     * @param string  $message
     * @param integer $status  HTTP Status Code
     */
    public function sendError($message, $status = 500)
    {
        return $this->sendResponse($message, $status);
    }

    /**
     * Alias method for sending an error back
     *
     * @param string  $message
     * @param integer $status  HTTP Status Code
     */
    public function sendOK($message, $status = 200)
    {
        return $this->sendResponse($message, $status);
    }

    /**
     * On missing method, throw an error
     *
     * @param array $parameters
     */
    public function missingMethod($parameters = array())
    {
        return $this->sendError('Invalid Method');
    }
}
