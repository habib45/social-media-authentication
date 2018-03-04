<?php


namespace MJErwin\Clockwork;

use DOMDocument;

/**
 * Class MessageResponse
 * @package MJErwin\Clockwork
 * @author Matthew Erwin <matthew.j.erwin@me.com>
 * www.matthewerwin.co.uk
 * */
class MessageResponse
{

    /**
     * @var string
     */
    protected $to;

    /**
     * @var string
     */
    protected $message_id;

    /**
     * @var int
     */
    protected $error_code;

    /**
     * @var string
     */
    protected $error_description;

    /**
     * @param string $request_response_string
     *
     */
    function __construct($request_response_string)
    {
        $response = new DOMDocument();
        $response->loadXML($request_response_string);

        foreach($response->documentElement->childNodes as $doc_child)
        {
            switch($doc_child->nodeName)
            {
                case "SMS_Resp":
                    foreach($doc_child->childNodes as $node_child)
                    {
                        switch($node_child->nodeName)
                        {
                            case "To":
                                $this->setTo($node_child->nodeValue);
                                break;
                            case "MessageID":
                                $this->setMessageId($node_child->nodeValue);
                                break;
                            case "ErrNo":
                                $this->setErrorCode($node_child->nodeValue);
                                break;
                            case "ErrDesc":
                                $this->setErrorDescription($node_child->nodeValue);
                                break;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->error_code;
    }

    /**
     * @param int $error_code
     */
    public function setErrorCode($error_code)
    {
        $this->error_code = $error_code;
    }

    /**
     * @return string
     */
    public function getErrorDescription()
    {
        return $this->error_description;
    }

    /**
     * @param string $error_description
     */
    public function setErrorDescription($error_description)
    {
        $this->error_description = $error_description;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * @param string $message_id
     */
    public function setMessageId($message_id)
    {
        $this->message_id = $message_id;
    }

    /**
     * @return string
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->getErrorCode() ? true : false;
    }


}