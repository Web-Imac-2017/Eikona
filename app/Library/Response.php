<?php
/**
 * Response handle the creation of JSON reponse outward of the API
 */

/**
 * HTML status codes details :
    100: 'Continue';
    101: 'Switching Protocols';
    200: 'OK';
    201: 'Created';
    202: 'Accepted';
    203: 'Non-Authoritative Information';
    204: 'No Content';
    205: 'Reset Content';
    206: 'Partial Content';
    300: 'Multiple Choices';
    301: 'Moved Permanently';
    302: 'Moved Temporarily';
    303: 'See Other';
    304: 'Not Modified';
    305: 'Use Proxy';
    400: 'Bad Request';
    401: 'Unauthorized';
    402: 'Payment Required';
    403: 'Forbidden';
    404: 'Not Found';
    405: 'Method Not Allowed';
    406: 'Not Acceptable';
    407: 'Proxy Authentication Required';
    408: 'Request Time-out';
    409: 'Conflict';
    410: 'Gone';
    411: 'Length Required';
    412: 'Precondition Failed';
    413: 'Request Entity Too Large';
    414: 'Request-URI Too Large';
    415: 'Unsupported Media Type';
    500: 'Internal Server Error';
    501: 'Not Implemented';
    502: 'Bad Gateway';
    503: 'Service Unavailable';
    504: 'Gateway Time-out';
    505: 'HTTP Version not supported';
 */

class Response
{
    private $data       = [];
    private $status     = "success";
    private $message    = "";
    private $code       = 200;

    private $allowedCodes = [
        100, 101, 200, 201, 202, 203, 204, 205, 206, 300, 301, 302, 303, 304, 305, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 500, 501, 502, 503, 504, 505
    ];



    /**
     * Set response has success
     *
     * @param String $msg Message to bind to the response
     */
    public function setSuccess($code = 200, $msg = "")
    {
        $this->status = "success";
        $this->message = Sanitize::string($msg, true);
        $this->setCode($code);

        return $this;
    }

    /**
     * Set response has error
     *
     * @param String $msg Message to bind to the response
     */
    public function setFailure($code = 404, $msg = "An error occured. Please try again.")
    {
        $this->status = "error";
        $this->message = Sanitize::string($msg, true);
        $this->setCode($code);

        return $this;
    }

    /**
     * Set response HTML status code
     *
     * @param String $msg Message to bind to the response
     */
    public function setCode($code)
    {
        if(in_array($code, $this->allowedCodes))
        {
            $this->code = $code;
        }

        return $this;
    }

    /**
     * Attach a nex value to the data array
     *
     * @param String $vName Name of the new value
     * @param $value Content of the new value
     */
    public function bindValue($vName, $value)
    {
        $this->data[$vName] = $value;

        return $this;
    }

    /**
     * Remove a value from the data array
     *
     * @param String $vName Name of the value to be removed
     */
    public function unlinkValue($vName)
    {
        unset($data);

        return $this;
    }

    /**
     * Send the response
     */
    public function send()
    {
        $json = [
            "code" => $this->code,
            "status" => $this->status,
            "message" => $this->message,
            "data" => $this->data
        ];

        header('Content-Type: application/json');
        http_response_code($this->code);

        echo json_encode($json);
    }

    /**
     * Send the response
     */
    public static function read($controller, $method, ...$args)
    {
        $controller .= "Controller";

        //Get response
        ob_start();

        call_user_func_array(array(new $controller, $method), $args);

        $response = str_replace('\\', '', ob_get_clean());

        //Reset header
        header('Content-Type: text/html');
        http_response_code(200);

        return json_decode($response, true);
    }
}
