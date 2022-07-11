<?php declare(strict_types=1);

namespace ZabbixWrapper\Entity;

use ZabbixWrapper;
use ZabbixWrapper\Factory;

abstract class AbstractEntity extends ZabbixWrapper\AbstractEntityTree implements Entity, ZabbixWrapper\EntityTree
{

    /** @var string $entityNameIndex Default entity name - please follow to filterBuild documentation */
    protected static string $entityNameIndex;

    /** @var string $entityIdIndex Unique entity id - please follow update() and delete() methods */
    protected static string $entityIdIndex;

    /** @var string $zabbixEndpoint Variable used for zabbix calls: $zabbix->$zabbixEndpoint->get(); */
    protected static string $zabbixEndpoint;

    /** @var array<mixed> $entity Contains entity from zabbix api */
    protected array $entity;

    /**
     * Return entity id index.
     *
     * @return string
     */
    public static function getEntityIdIndex(): string
    {
        return static::$entityIdIndex;
    }

    /**
     * Protected constructor - use getEntity or getEntities factories
     *
     * @param \ZabbixWrapper\EntityTree $parent Parent containing zabbix api and other stugg
     * @param array<mixed> $entity Array with entity data
     */
    public function __construct($parent, $entity)
    {
        $this->parent = $parent;
        $this->entityManager = $parent->getEntityManager();
        $this->zabbix = $parent->getZabbix();

        $this->entity = $entity;

        $this->getLoggerWrapper()->debug(sprintf('Entity "%s"(\'%s\') has been created.', static::class, $this->__toString()));
    }


    /*************** Entity factories *********************/

    /**
     * Returns array of entities from zabbix backend
     *
     */
    public static function fetchEntities(ZabbixWrapper\EntityTree $parent, ...$parameters)
    {
        $endpoint = static::$zabbixEndpoint;
        return $parent->getZabbix()->$endpoint->get(static::buildZabbixParameters(...$parameters));
    }

    public static function getZabbixEndpoint()
    {
        return static::$zabbixEndpoint;
    }

    /*************** Entity data getters ******************/

    /**
     * Returns an entity element
     *
     */
    public function get($index)
    {
        if (isset($this->entity[$index]) === false) {
            throw new \InvalidArgumentException(sprintf("Undefined index '%s' of entity '%s'", $index, static::class));
        }
        return $this->entity[$index];
    }

    /**
     * Returns whole entity
     *
     */
    public function dump()
    {
        return $this->entity;
    }

    /*************** Related entities getters *************/

    /**
     * Returns array with entity overrides
     *  - this method is used to automatically inject parent's filters
     *  - e.g. Template->DiscoveryRule->DiscoveryGraph;
     *
     * Returns:
     *  - FALSE: If call is not supported
     *  - array with entity overrides
     */
    protected function buildGetEntityParameters(string $className)
    {
        return false;
    }

    public function getEntity(string $className, ...$parameters)
    {
        $overrides = $this->buildGetEntityParameters($className);
        if ($overrides === false) {
            throw new \InvalidArgumentException(sprintf('Creating "%s" from "%s" is not supported.', $className, static::class));
        }
        $parameters[] = $overrides;

        return $this->entityManager->getEntityWithParent($this, $className, ...$parameters);
    }

    public function getEntities(string $className, ...$parameters)
    {
        $overrides = $this->buildGetEntityParameters($className);
        if ($overrides === false) {
            throw new \InvalidArgumentException(sprintf('Creating "%s" from "%s" is not supported.', $className, static::class));
        }
        $parameters[] = $overrides;

        return $this->entityManager->getEntitiesWithParent($this, $className, ...$parameters);
    }

    public function hasEntity(string $className, ...$parameters)
    {
        $overrides = $this->buildGetEntityParameters($className);
        if ($overrides === false) {
            throw new \InvalidArgumentException(sprintf('Creating "%s" from "%s" is not supported.', $className, static::class));
        }
        $parameters[] = $overrides;

        return $this->entityManager->hasEntity($className, ...$parameters);
    }

    /*************** Related entites factory **************/

    /**
     * Returns array with entity overrides
     *  - this method is used to automatically inject parents values
     *  - e.g. Template->DiscoveryRule->DiscoveryGraph;
     *
     * Returns:
     *  - FALSE: If call is not supported
     *  - array with entity overrides
     */
    protected function buildCreateEntityValues(string $className)
    {
        return false;
    }

    public function createEntity(string $className, array $values)
    {
        $overrides = $this->buildCreateEntityValues($className);
        if ($overrides === false) {
            throw new \InvalidArgumentException(sprintf('Creating "%s" from "%s" is not supported.', $className, static::class));
        }

        $values = array_merge($values, $overrides);

        $this->getLoggerWrapper()->debug(sprintf('Creating entity "%s"', static::class), ['values' => $values]);

        if ($this->getEntityManager()->getDryRun() === true) {
            return;
        }

        return $this->entityManager->createEntity($className, $values);
    }

    /*************** Entity delete and update *************/

    /**
     * Updates entity
     *
     */
    public function update(array $values)
    {
        if (isset(static::$entityIdIndex) === false) {
            throw new \InvalidArgumentException(sprintf('Static variable $entityIdIndex is not set. "%s" doesn\'t support update.', get_class($this)));
        }

        $id = $this->get(static::$entityIdIndex);
        $values[static::$entityIdIndex] = $id;

        $this->getLoggerWrapper()->debug(sprintf('Updating entity [%d] "%s"', $id, $this->__toString()), ['values' => $values]);

        if ($this->getEntityManager()->getDryRun() === true) {
            return;
        }

        $endpoint = static::$zabbixEndpoint;
        $this->getZabbix()->$endpoint->update($values);
    }

    public function delete()
    {
        if (isset(static::$entityIdIndex) === false) {
            throw new \InvalidArgumentException(sprintf('Static variable $entityIdIndex is not set. "%s" doesn\'t support delete.', get_class($this)));
        }

        $id = $this->get(static::$entityIdIndex);
        $this->getLoggerWrapper()->debug(sprintf('Deleting entity [%d] "%s".', $id, $this->__toString()));

        if ($this->getEntityManager()->getDryRun() === true) {
            return;
        }

        $endpoint = static::$zabbixEndpoint;
        $this->getZabbix()->$endpoint->delete([
            static::$entityIdIndex => $id,
        ]);
    }

    /*************** Helpers ******************************/

    /**
     * Helper function to build zabbix api parameters with filters.
     * - You can pass as many arguments as you need, all will be mergeded in to one array
     * - Arguments are deep merged one by one, later argument overrides previous
     * - If string is found, it is translated to [ 'filter' => [ $entityNameIndex => $value ] ]
     *
     * usage:
     *  - static::buildZabbixParameters('Linux agent', [ 'filter' => [ 'hostid' => 1111 ]]) results to [ 'filter' => [ 'name' => 'Linux agent', 'hostid' => 1111 ]]
     */
    protected static function buildZabbixParameters(...$parameters)
    {
        $result = [];
        foreach ($parameters as $row) {
            if (is_null($row)) {
                continue;
            }

            // Strings are converted to Entity names filters
            if (is_string($row)) {
                if (isset(static::$entityNameIndex) === false) {
                    throw new \InvalidArgumentException('Static variable $entityNameIndex is not set');
                }

                $row = ['filter' => [static::$entityNameIndex => $row]];
            }

            // Merge the results
            $result = array_merge_recursive($result, $row);
        }

        return $result;
    }

    public function __toString()
    {
        return $this->get(static::$entityNameIndex);
    }
}
