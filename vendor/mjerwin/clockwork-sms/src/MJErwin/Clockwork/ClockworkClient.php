<?php


namespace MJErwin\Clockwork;


use MJErwin\Clockwork\Exception\ClockworkException;
use MJErwin\Clockwork\Exception\ClockworkResponseException;
use Curl\Curl;
use DOMDocument;

/**
 * Class ClockworkClient
 * @package MJErwin\Clockwork
 * @author Matthew Erwin <matthew.j.erwin@me.com>
 * www.matthewerwin.co.uk
 *
 */
class ClockworkClient
{
    const REQUEST_URI = 'https://api.clockworksms.com/xml/send.aspx';
    const REQUEST_URI_BALANCE = 'https://api.clockworksms.com/xml/balance';
    /**
     *
     */
    const INVALID_CHAR_ACTION_RETURN_ERROR = 1;
    /**
     *
     */
    const INVALID_CHAR_ACTION_REMOVE_CHARS = 2;
    /**
     *
     */
    const INVALID_CHAR_ACTION_REPLACE_CHARS = 3;

    /**
     * @var string
     */
    protected $api_key;

    /**
     * @var int
     */
    protected $invalid_char_action = self::INVALID_CHAR_ACTION_REMOVE_CHARS;

    /**
     * @var bool
     */
    protected $truncate_enabled = true;

    function __construct($api_key)
    {
        $this->setApiKey($api_key);
    }


    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param string $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @return int
     */
    public function getInvalidCharAction()
    {
        return $this->invalid_char_action;
    }

    /**
     * @param $invalid_char_action
     *
     * @throws ClockworkException
     */
    public function setInvalidCharAction($invalid_char_action)
    {
        $allowed_values = [
            self::INVALID_CHAR_ACTION_RETURN_ERROR,
            self::INVALID_CHAR_ACTION_REMOVE_CHARS,
            self::INVALID_CHAR_ACTION_REPLACE_CHARS
        ];

        if (!in_array($invalid_char_action, $allowed_values))
        {
            throw new ClockworkException('InvalidCharAction must be one of the following values: ' . implode(', ', $allowed_values));
        }

        $this->invalid_char_action = $invalid_char_action;
    }

    /**
     * @return boolean
     */
    public function isTruncateEnabled()
    {
        return $this->truncate_enabled;
    }

    /**
     * @param boolean $truncate_enabled
     *
     * @return $this
     */
    public function setTruncateEnabled($truncate_enabled)
    {
        $this->truncate_enabled = $truncate_enabled;

        return $this;
    }

    /**
     * @param Message $message
     *
     * @return MessageResponse
     */
    public function sendMessage(Message $message)
    {
        $xml = $this->getMessageRequestXML($message);

        $response = $this->makeRequest(self::REQUEST_URI, $xml->saveXml());

        $response_string = $response->raw_response;

        $response = new MessageResponse($response_string);

        return $response;
    }

    /**
     * @param Message $message
     *
     * @return DOMDocument
     */
    protected function getMessageRequestXML(Message $message)
    {
        $data = [
            'Key' => $this->getApiKey(),
            'SMS' => [
                'To' => $message->getNumber(),
                'Content' => $message->getContent(),
                'From' => $message->getFromName(),
                'Truncate' => $this->isTruncateEnabled() ? 1 : 0,
                'InvalidCharAction' => $this->getInvalidCharAction(),
            ],
        ];

        $xml = $this->generateXMLFromArray('Message', $data);

        return $xml;
    }

    public function getBalance()
    {
        $data = [
            'Key' => $this->getApiKey(),
        ];

        $xml = $this->generateXMLFromArray('Balance', $data);

        $response = $this->makeRequest(self::REQUEST_URI_BALANCE, $xml->saveXml());

        $response_string = $response->raw_response;

        $response = new DOMDocument();
        $response->loadXML($response_string);

        $error_number = null;
        $balance = null;
        $error_description = null;

        foreach($response->documentElement->childNodes as $doc_child)
        {
            switch($doc_child->nodeName)
            {
                case "Balance":
                    $balance = number_format(floatval($doc_child->nodeValue), 2);
                    break;
                case "ErrNo":
                    $error_number = $doc_child->nodeValue;
                    break;
                case "ErrDesc":
                    $error_description = $doc_child->nodeValue;
                    break;
                default:
                    break;
            }
        }

        if ($error_number)
        {
            throw new ClockworkResponseException('Clockwork API request responded with the following error: ' . '"' . $error_description . '"', $error_number);
        }

        return $balance;
    }

    /**
     * @param string $parent_element_name
     * @param array  $data
     *
     * @return DOMDocument
     */
    protected function generateXMLFromArray($parent_element_name, $data)
    {
        $xml = new DOMDocument();

        $parent_element = $xml->createElement($parent_element_name);

        foreach($data as $key => $value)
        {
            if (is_array($value))
            {
                $child_element = $xml->createElement($key);

                foreach($value as $sub_key => $sub_value)
                {
                    $element = $xml->createElement($sub_key, $sub_value);

                    $child_element->appendChild($element);
                }

                $parent_element->appendChild($child_element);
            }
            else
            {
                $element = $xml->createElement($key, $value);

                $parent_element->appendChild($element);
            }
        }

        $xml->appendChild($parent_element);

        return $xml;
    }

    /**
     * @param $uri
     * @param $data
     *
     * @return Curl
     * @throws ClockworkResponseException
     */
    public function makeRequest($uri, $data)
    {
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'text/xml');
        $curl->post($uri, $data);

        if ($curl->http_status_code !== 200)
        {
            throw new ClockworkResponseException('Clockwork API request responded with a HTTP status code of ' . $curl->http_status_code);
        }

        return $curl;
    }


}