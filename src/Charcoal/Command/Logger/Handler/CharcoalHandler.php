<?php

namespace Charcoal\Command\Logger\Handler;

use Charcoal\Model\ModelInterface;
use Monolog\Handler\AbstractProcessingHandler;

class CharcoalHandler extends AbstractProcessingHandler
{
    /**
     * @var ModelInterface|null
     */
    protected $loggerProto;

    /**
     * CharcoalHandler constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        // Options and defaults
        $opts   = $data['options'];
        $config = $data['config'];

        // Abstract processing handler required options
        $level  = isset($opts['level']) ? $opts['level'] : $config['level'];
        $bubble = isset($opts['bubble']) ? $opts['bubble'] : null;

        // Proto class
        $class     = isset($opts['model']) ? $opts['model'] : $config['model'];
        $container = $data['container'];

        $proto = $container['model/factory']->create($class);
        $this->setLoggerProto($proto);

        $format = $opts['formatter'];

        foreach ($format as $formatterClass => $opts) {
            $formatter = $container['logger/formatter/factory']->create($formatterClass, $opts);
            $this->setFormatter($formatter);
        }

        parent::__construct($level, $bubble);
    }

    /**
     * @return ModelInterface|null
     */
    public function loggerProto()
    {
        return $this->loggerProto;
    }

    /**
     * @param ModelInterface|null $loggerProto
     * @return CharcoalHandler
     */
    public function setLoggerProto(?ModelInterface $loggerProto)
    {
        $this->loggerProto = $loggerProto;
        return $this;
    }

    /**
     * @param array $record
     */
    public function write(array $record)
    {
        // Default formatter for monolog is the LineFormatter
        if (!is_array($record['formatted'])) {
            $this->loggerProto()->setData($record)->save();
        } else {
            $this->loggerProto()->setData($record['formatted'])->save();
        }
    }
}
