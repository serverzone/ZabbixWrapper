<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

/**
 * Zabbix maintenance entity.
 */
class Maintenance extends AbstractEntity
{
    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'maintenance';

    protected static string $entityIdIndex = 'maintenanceid';
}
