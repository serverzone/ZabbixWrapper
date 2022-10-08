<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

/**
 * Zabbix Web scenario entity.
 */
class WebScenario extends AbstractEntity
{
    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'httptest';

    protected static string $entityIdIndex = 'httptestid';
}
