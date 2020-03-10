<?php

namespace Charcoal\Command\Service;

use Charcoal\Command\CommandQueue;
use Charcoal\Model\ModelInterface;
use InvalidArgumentException;

/**
 * Class QueueStack
 *
 * @package Charcoal\Command\Service
 */
class QueueStack
{
    /**
     * @var ModelInterface
     */
    protected $proto;

    /**
     * QueueStack constructor.
     *
     * @param array $data
     * @return self
     */
    public function __construct(array $data)
    {
        $proto = $data['model/factory']->create(CommandQueue::class);
        $this->setProto($proto);
        return $this;
    }

    /**
     * @return ModelInterface
     */
    protected function getProto()
    {
        return $this->proto;
    }

    /**
     * @param ModelInterface $proto
     * @return self
     */
    protected function setProto(ModelInterface $proto)
    {
        $this->proto = $proto;
        return $this;
    }

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'arguments'      => [],
            'processingDate' => 'now'
        ];
    }

    /**
     * @param array $data
     */
    public function enqueue(array $data = []): QueueStack
    {
        if (!$this->validateData($data)) {
            throw new InvalidArgumentException('Invalid queue data');
        }

        $data = array_merge($this->defaults(), $data);

        $this->getProto()->setData($data);
        $this->getProto()->save();

        return $this;
    }

    /**
     * @param $data
     * @return bool
     */
    protected function validateData($data)
    {
        if (!isset($data['command'])) {
            return false;
        }

        return true;
    }
}
