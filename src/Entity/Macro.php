<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

/**
 * Zabbix user entity.
 */
class Macro extends AbstractEntity
{
    protected static string $entityNameIndex = 'macro';

    protected static string $zabbixEndpoint = 'usermacro';

    protected static string $entityIdIndex = 'hostmacroid';
}
