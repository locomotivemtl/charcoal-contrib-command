<?php

namespace Charcoal\Command\Logger\Formatter;

use Monolog\Formatter\FormatterInterface;

class CommandFormatter implements FormatterInterface
{
    /**
     * CommandFormatter constructor.
     *
     * @param array|null $data
     */
    public function __construct(?array $data = [])
    {
        // Additional options
    }

    /**
     * @param array $records
     * @return array|mixed
     */
    public function formatBatch(array $records)
    {
        $out = [];
        foreach ($records as $record) {
            $out[] = $this->format($record);
        }

        return $out;
    }

    /**
     * @param array $record
     * @return array|mixed
     */
    public function format(array $record)
    {
        return array_merge($record, $record['context']);
    }
}
