<?php


namespace MJErwin\Clockwork;

use MJErwin\Clockwork\Exception\InvalidMessageException;
use MJErwin\Clockwork\Exception\ClockworkException;

/**
 * Class Message
 * @package MJErwin\Clockwork
 * @author Matthew Erwin <matthew.j.erwin@me.com>
 * www.matthewerwin.co.uk
 */
class Message
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $from_name;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->from_name;
    }

    /**
     * @param string $from_name
     *
     * @return $this
     */
    public function setFromName($from_name)
    {
        $this->from_name = $from_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    protected function validate()
    {
        if (!$this->getNumber())
        {
            throw new InvalidMessageException('No number set in Message');
        }

        if (!$this->getContent())
        {
            throw new InvalidMessageException('No content set in Message');
        }
    }

    protected function isValid()
    {
        try
        {
            $this->validate();
        } catch(ClockworkException $e)
        {
            return false;
        }

        return true;
    }

}