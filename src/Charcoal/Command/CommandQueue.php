<?php

namespace Charcoal\Command;

use Charcoal\Model\AbstractModel;
use Charcoal\Object\AuthorableInterface;
use Charcoal\Object\AuthorableTrait;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

class CommandQueue extends AbstractModel implements
    AuthorableInterface
{
    use AuthorableTrait;

    /**
     * StructureProperty
     * Gets additional argument to execute the command.
     * Something like
     * { objType: 'my/type', objId: 'myId' }
     *
     * @var mixed
     */
    protected $arguments;

    /**
     * Whether the item has been processed.
     *
     * @var bool
     */
    protected $processed;

    /**
     * When the item should be processed.
     *
     * The date/time at which this queue item job should be ran.
     * If NULL, 0, or a past date/time, then it should be performed immediately.
     *
     * @var DateTimeInterface|null $processingDate
     */
    protected $processingDate;

    /**
     * When the item was processed.
     *
     * @var DateTimeInterface|null $processedDate
     */
    protected $processedDate;

    /**
     * DateTime when the command was issued
     *
     * @var DateTimeInterface|null $issuedDate
     */
    protected $issuedDate;

    /**
     * Command class / path
     *
     * @var string
     */
    protected $command;

    /**
     * CommandQueue constructor.
     *
     * @param array|null $data
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        if (is_callable([$this, 'defaultData'])) {
            $this->setData($this->defaultData());
        }
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param mixed $arguments
     * @return CommandQueue
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return bool
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * @param bool $processed
     * @return CommandQueue
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getProcessingDate()
    {
        return $this->processingDate;
    }

    /**
     * @param DateTimeInterface|string|null $timestamp
     * @return CommandQueue
     */
    public function setProcessingDate($timestamp)
    {
        if ($timestamp === null) {
            $this->processingDate = null;
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

        $this->processingDate = $timestamp;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getProcessedDate()
    {
        return $this->processedDate;
    }

    /**
     * @param DateTimeInterface|string|null $timestamp
     * @return CommandQueue
     */
    public function setProcessedDate($timestamp)
    {
        if ($timestamp === null) {
            $this->processedDate = null;
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

        $this->processedDate = $timestamp;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * @param DateTimeInterface|string|null $issuedDate
     * @return CommandQueue
     */
    public function setIssuedDate($timestamp)
    {
        if ($timestamp === null) {
            $this->issuedDate = null;
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

        $this->issuedDate = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return CommandQueue
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * @return bool
     */
    public function preSave()
    {
        $this->setIssuedDate('now');

        // Processing date is used to define when the script will be processed
        // If nothing is defined, it is set as 'now', for immediate procedure
        if (!$this->getProcessingDate()) {
            $this->setProcessingDate('now');
        }
        return parent::preSave();
    }
}
