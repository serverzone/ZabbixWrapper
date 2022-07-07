<?php declare(strict_types=1);


namespace ZabbixWrapper\Entity;

/**
 * Zabbix action entity.
 */
class Action extends AbstractEntity
{
    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'action';

    protected static string $entityIdIndex = 'actionid';
}
