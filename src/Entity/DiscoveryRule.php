<?php

namespace ZabbixWrapper\Entity;

use ZabbixWrapper\ZabbixException;

class DiscoveryRule extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'discoveryrule';

    protected function buildGetEntityParameters(string $className) {
        $result = FALSE;

        switch ($className) {
            case GraphPrototype::class:
            case ItemPrototype::class:
            case TriggerPrototype::class:
                $result = [
                    'discoveryids' => $this->get('itemid'),
                    'templated' => true,
                ];
                break;
        }

        return $result;
    }

    protected function buildCreateEntityValues(string $className) {
        $result = FALSE;

        switch ($className) {
            case ItemPrototype::class:
                $result = [
                    'hostid' => $this->get('hostid'),
                    'ruleid' => $this->get('itemid'),
                ];
                break;
        }

        return $result;
    }

}
