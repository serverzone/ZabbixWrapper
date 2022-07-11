<?php declare(strict_types=1);

namespace ZabbixWrapper\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Very simple stdout printer
 *
 */
final class SimpleStd extends AbstractLogger
{

    /**
     * Log message.
     *
     * @param mixed $level
     * @param string $message
     * @param array<string, mixed> $context
     */
    public function log($level, $message, array $context = [])
    {
        $history = $context['@@EntityHistory'] ?? [];
        $callTrace = (count($history) > 0 ? "\n\t" : '' ) . implode("\n\t-> ", $history);
        unset($context['@@EntityHistory']);

        $contextJson = (empty($context) ? '' : json_encode($context));

        echo sprintf("%s: %s %s %s\n\n", date('d.m.Y H:i:s'), $message, $contextJson, $callTrace);
    }
}
