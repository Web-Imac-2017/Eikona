<?php
/**
 * Response handle the creation of JSON reponse outward of the API
 */

class Response
{
    private $data       = [];
    private $status     = "";
    private $message    = "";



    /**
     * Set response has success
     *
     * @param String $msg Message to bind to the response
     */
    public function setSuccess($msg = "")
    {
        $this->status = "success";
        $this->message = $msg;

        return $this;
    }

    /**
     * Set response has error
     *
     * @param String $msg Message to bind to the response
     */
    public function setFailure($msg = "An error occured. Please try again.")
    {
        $this->status = "error";
        $this->message = $msg;

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
            "status" => $this->status,
            "message" => $this->message,
            "data" => $this->data
        ];

        header('Content-Type: application/json');
        echo json_encode($json);
    }
}
