<?php

namespace Charcoal\Command;

use Charcoal\Model\ModelFactoryTrait;
use Pimple\Container;
use Psr\Log\LoggerInterface;

abstract class AbstractCommand implements CommandInterface
{
    use ModelFactoryTrait;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var mixed
     */
    protected $arguments;

    /**
     * @var array|null
     */
    protected $results;

    /**
     * @var boolean
     */
    protected $success;
    /**
     * AbstractCommand constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setDependencies($data['container']);
    }

    /**
     * @param array     $arguments
     * @return mixed|void
     */
    public function __invoke($arguments = [])
    {
        $this->setArguments($arguments);
        $this->execute();
        $this->log();
    }

    /**
     * @param Container $container
     */
    public function setDependencies(Container $container)
    {
        $this->setModelFactory($container['model/factory']);
        $this->setLogger($container['command/logger']);
    }

    /**
     * This is where the command code goes.
     *
     * @return mixed
     */
    abstract function execute();

    /**
     * @return mixed
     */
    protected function logger()
    {
        return $this->logger;
    }

    /**
     * @param mixed $logger
     * @return AbstractCommand
     */
    protected function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param mixed $arguments
     * @return AbstractCommand
     */
    protected function setArguments($arguments)
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return array|null
     */
    protected function getResults()
    {
        return $this->results;
    }

    /**
     * @param array|null $results
     * @return AbstractCommand
     */
    protected function setResults($results)
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @return bool
     */
    protected function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param bool $success
     * @return AbstractCommand
     */
    protected function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Log procedure
     */
    protected function log()
    {
        $data = [
            'arguments' => $this->getArguments(),
            'success' => $this->getSuccess(),
            'results' => $this->getResults(),
            'command' => get_called_class()
        ];
        $this->logger()->debug(get_called_class(), $data);
    }
}
