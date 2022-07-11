<?php declare(strict_types=1);

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

    private EntityTree $entity;

    private string $signature;

    /** @var array<int|string, mixed> */
    private array $history;

    /**
     * Class constructor.
     *
     * @param EntityTree $entity
     * @param string $signature
     */
    public function __construct(EntityTree $entity, string $signature)
    {
        $this->entity = $entity;
        $this->signature = $signature;
    }

    public function log($level, $message, array $context = [])
    {
        $context['@@EntityHistory'] = $this->getHistory();
        $this->entity->getLogger()->log($level, $message, $context);
    }

    /**
     * Return history.
     *
     * @return array<int|string, mixed>
     */
    public function getHistory(): array
    {
        if (isset($this->history) !== false) {
            return $this->history;
        }

        // Build new history
        $this->history = [];

        $parent = $this->entity->getParent();
        if ($parent !== null) {
            $this->history = $parent->getLoggerWrapper()->getHistory();
        }
        $this->history[] = $this->signature;

        return $this->history;
    }
}
