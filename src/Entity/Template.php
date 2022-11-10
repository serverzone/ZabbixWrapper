<?php

namespace ZabbixWrapper\Entity;

class Template extends AbstractEntity
{

    protected static string $entityNameIndex = 'host';

    protected static string $entityIdIndex = 'templateid';

    protected static string $zabbixEndpoint = 'template';

    protected function buildGetEntityParameters(string $className)
    {
        switch ($className) {
            case Macro::class:
                return [ 'hostids' => [ $this->get('templateid') ]];
            case DiscoveryRule::class:
            case Item::class:
            case Trigger::class:
            case Graph::class:
            case WebScenario::class:
                return [ 'templateids' => [ $this->get('templateid') ]];
        }

        return false;
    }

    protected function buildCreateEntityValues(string $className)
    {
        switch ($className) {
            case DiscoveryRule::class:
            case Graph::class:
            case Item::class:
            case Macro::class:
            case WebScenario::class:
                return [
                    'hostid' => $this->get('templateid'),
                ];
        }

        return false;
    }
}
