<?php

namespace Charcoal\Command\Logger\Model;

use Charcoal\Model\AbstractModel;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

/**
 * Class Log
 * Used Monolog/Logger property definition
 *
 * @package Charcoal\Command\Logger\Model
 */
class Log extends AbstractModel
{
    /**
     * @var DateTime
     */
    protected $datetime;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array|null
     */
    protected $context;

    /**
     * @var mixed
     */
    protected $level;

    /**
     * @var string
     */
    protected $levelName;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var array
     */
    protected $extra;


    /**
     * @return DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set when the object was created.
     *
     * @param  DateTimeInterface|string|null $timestamp The timestamp at object's creation.
     *                                                  NULL is accepted and instances of DateTimeInterface are
     *                                                  recommended; any other value will be converted (if possible)
     *                                                  into one.
     * @throws InvalidArgumentException If the timestamp is invalid.
     * @return self
     */
    public function setDatetime($timestamp)
    {
        if ($timestamp === null) {
            $this->datetime = null;
            return $this;
        }

        if (is_string($timestamp)) {
            try {
                $timestamp = new DateTime($timestamp);
            } catch (Exception $e) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid timestamp: %s',
                    $e->getMessage()
                ), 0, $e);
            }
        }

        if (!$timestamp instanceof DateTimeInterface) {
            throw new InvalidArgumentException(
                'Invalid timestamp value. Must be a date/time string or a DateTime object.'
            );
        }

        $this->datetime = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param array|null $context
     * @return Log
     */
    public function setContext(?array $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param string $levelName
     * @return Log
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;
        return $this;
    }

    /**
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param string $channel
     * @return Log
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array|null $extra
     * @return Log
     */
    public function setExtra(?array $extra)
    {
        $this->extra = $extra;
        return $this;
    }
}
