<?php

namespace ZabbixWrapper\Entity;

class Host extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'hostid';

    protected static string $zabbixEndpoint = 'host';

    protected function buildGetEntityParameters(string $className)
    {
        switch ($className) {
            case Macro::class:
            case Template::class:
            case Trigger::class:
            case WebScenario::class:
                return [ 'hostids' => $this->get('hostid') ];
        }

        return false;
    }

    protected function buildCreateEntityValues(string $className)
    {
        switch ($className) {
            case Macro::class:
            case WebScenario::class:
                return [ 'hostid' => $this->get('hostid') ];
        }

        return false;
    }
}
