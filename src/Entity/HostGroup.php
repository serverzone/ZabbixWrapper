<?php

namespace ZabbixWrapper\Entity;

class HostGroup extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'groupid';

    protected static string $zabbixEndpoint = 'hostgroup';

    protected function buildGetEntityParameters(string $className)
    {
        switch ($className) {
            case Host::class:
                return [ 'groupids' => [ $this->get('groupid') ]];
        }

        return false;
    }
}
