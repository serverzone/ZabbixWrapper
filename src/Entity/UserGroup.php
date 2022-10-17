<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

/**
 * Zabbix user entity.
 */
class UserGroup extends AbstractEntity
{
    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'usergroup';

    protected static string $entityIdIndex = 'usrgrpid';
}
