<?php

namespace ZabbixWrapper\Entity;

use ZabbixWrapper;

class DummyFluent implements Entity
{

    protected $parent;

    public function __construct(ZabbixWrapper\EntityTree $parent)
    {
        $this->parent = $parent; // Potrebujeme ho vubec?
    }

    public function __call(string $name, array $arguments)
    {
//        $this->logCall($name, $arguments);
        return $this;
    }

    public static function getZabbixEndpoint()
    {
        throw new \InvalidStateException('This is not supported. Seems like a rare case. Feel free to report use case.');
    }

    public static function fetchEntities(ZabbixWrapper\EntityTree $parent, ...$parameters)
    {
        return [];
    }
}
