<?php

namespace Charcoal\Command\Logger\Handler;

use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Model\ModelInterface;
use Monolog\Handler\AbstractProcessingHandler;

class CharcoalHandler extends AbstractProcessingHandler
{
    use ModelFactoryTrait;

    /**
     * @var ModelInterface|null
     */
    protected $loggerProtoClass;

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

        $this->setModelFactory($container['model/factory']);
        $this->setLoggerProtoClass($class);

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
    public function loggerProtoClass()
    {
        return $this->loggerProtoClass;
    }

    /**
     * @param string|null $loggerProto
     * @return CharcoalHandler
     */
    public function setLoggerProtoClass($loggerProtoClass)
    {
        $this->loggerProtoClass = $loggerProtoClass;
        return $this;
    }

    /**
     * @param array $record
     */
    public function write(array $record)
    {
        // Default formatter for monolog is the LineFormatter
        $proto = $this->modelFactory()->create($this->loggerProtoClass());
        if (!is_array($record['formatted'])) {
            $proto->setData($record)->save();
        } else {
            $proto->setData($record['formatted'])->save();
        }
    }
}
