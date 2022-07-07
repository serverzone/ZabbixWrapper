<?php

namespace ZabbixWrapper\Logger;

use Psr\Log\AbstractLogger;
use ZabbixWrapper\EntityTree;

/**
 * This is a logger wrapper, so we can store history of calls.
 * e.g. EntityManager()->Template(Linux)->DiscoveryRule('Block devices')->ItemPrototype('Latency')
 *
 */
class EntityHistoryContextWrapper extends AbstractLogger
{

    private $entity;

    private $signature;

    private $history;

    public function __construct(EntityTree $entity, $signature)
    {
        $this->entity = $entity;
        $this->signature = $signature;
    }

    public function log($level, $message, array $context = [])
    {
        $context['@@EntityHistory'] = $this->getHistory();
        $this->entity->getLogger()->log($level, $message, $context);
    }

    public function getHistory() {
        if (isset($this->history) !== FALSE) {
            return $this->history;
        }

        // Build new history
        $this->history = [];

        $parent = $this->entity->getParent();
        if ($parent !== NULL) {
            $this->history = $parent->getLoggerWrapper()->getHistory();
        }
        $this->history[] = $this->signature;

        return $this->history;
    }
}
