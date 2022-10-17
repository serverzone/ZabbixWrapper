<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

/**
 * Zabbix user entity.
 */
class User extends AbstractEntity
{
    protected static string $entityNameIndex = 'username';

    protected static string $zabbixEndpoint = 'user';

    protected static string $entityIdIndex = 'userid';
}
