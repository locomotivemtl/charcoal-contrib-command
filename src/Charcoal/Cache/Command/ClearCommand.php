<?php

namespace Charcoal\Cache\Command;

use Charcoal\Cache\CachePoolAwareTrait;
use Charcoal\Command\AbstractCommand;
use Pimple\Container;

/**
 * Cache clearer command
 *
 * @package Charcoal\Cache\Queue
 */
class ClearCommand extends AbstractCommand
{
    use CachePoolAwareTrait;

    /**
     * @param array $arguments
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->setCachePool($container['cache']);
    }

    /**
     * @return mixed|void
     */
    public function execute()
    {
        $success = $this->cachePool()->clear();
        $this->setSuccess($success);
    }
}
