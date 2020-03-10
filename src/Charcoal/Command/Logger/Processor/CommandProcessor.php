<?php

namespace Charcoal\Command\Logger\Processor;

use Monolog\Processor\ProcessorInterface;

class CommandProcessor implements ProcessorInterface
{
    /**
     * CommandProcessor constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data)
    {
        // Additional options
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record)
    {
        $context = $record['context'];
        return array_merge($record, $context);
    }
}
