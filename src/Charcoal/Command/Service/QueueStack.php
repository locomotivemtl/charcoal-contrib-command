<?php

namespace Charcoal\Command\Service;

use Charcoal\Command\CommandQueue;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Model\ModelInterface;
use InvalidArgumentException;

/**
 * Class QueueStack
 *
 * @package Charcoal\Command\Service
 */
class QueueStack
{
    use ModelFactoryTrait;

    /**
     * QueueStack constructor.
     *
     * @param array $data
     * @return self
     */
    public function __construct(array $data)
    {
        $this->setModelFactory($data['model/factory']);
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

        $queue = $this->modelFactory()->create(CommandQueue::class);
        $queue->setData($data);
        $queue->save();

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
