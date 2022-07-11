<?php

namespace ZabbixWrapper;

use Psr\Log;

interface EntityTree
{

    /**
     * Returns parent or null if we are at root
     *
     */
    public function getParent();

    /**
     * Returns entity
     *
     */
    public function getEntity(string $className, ...$parameters);

    /**
     * Returns entity or DummyFluent if entity doesn't exist
     *
     */
    public function fluentEntity(string $className, ...$parameters);

    /**
     * Returns array of entities
     *
     */
    public function getEntities(string $className, ...$parameters);

    /**
     * Returns number of matching entities
     *
     */
    public function hasEntity(string $className, ...$parameters);

    /**
     * Creates new entity
     *
     */
    public function createEntity(string $className, array $value);
}
