<?php

namespace ZabbixWrapper\Entity;

class HostGroup extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'groupid';

    protected static string $zabbixEndpoint = 'hostgroup';
}
