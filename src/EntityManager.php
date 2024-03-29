<?php declare(strict_types=1);

namespace ZabbixWrapper;

use ZabbixWrapper\Exception\EntityNotFoundException;
use ZabbixWrapper\Exception\MultipleEntitiesFoundException;

class EntityManager extends AbstractEntityTree implements EntityTree
{

    /**
     * Class constructor.
     *
     * @param \ZabbixApi\ZabbixApi $zabbix
     * @param mixed $data
     */
    public function __construct($zabbix, $data)
    {
        $this->zabbix = $zabbix;
    }

    /*************** Entity objects factories **************/

    protected function buildGetEntityParameters(string $className)
    {
        return null;
    }

    public function getEntity(string $className, ...$parameters)
    {
        return $this->getEntityWithParent($this, $className, ...$parameters);
    }

    public function getEntityWithParent(EntityTree $parent, string $className, ...$parameters)
    {
        $this->checkEntity($className);

        $this->getLoggerWrapper()->debug(sprintf('Getting entity "%s".', $className), [ 'parameters' => $parameters]);

        $entities = $className::fetchEntities($this, ...$parameters);
        if (count($entities) == 0) {
            $this->getLoggerWrapper()->info(sprintf('Entity "%s" not found', $className), [ 'parameters' => $parameters ]);
            throw new EntityNotFoundException('Entity not found.');
        }

        if (count($entities) > 1) {
            throw new MultipleEntitiesFoundException('Multiple entities found.');
        }

        return $className::createInstance($parent, array_shift($entities));
    }

    public function getEntities(string $className, ...$parameters)
    {
        return $this->getEntitiesWithParent($this, $className, ...$parameters);
    }

    public function getEntitiesWithParent(EntityTree $parent, string $className, ...$parameters)
    {
        $this->checkEntity($className);

        $entities = $className::fetchEntities($this, ...$parameters);
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $className::createInstance($parent, $entity);
        }
        return $result;
    }

    public function hasEntity(string $className, ...$parameters): int
    {
        $this->checkEntity($className);

        $count = count($className::fetchEntities($this, ...$parameters));
        $this->getLoggerWrapper()->debug(sprintf("Fetched %d %s entities.", $count, $className), [ 'parameters' => $parameters ]);
        return $count;
    }

    /*************** Dry run ******************************/

    private $dryRun = false;

    public function setDryRun(bool $value = false)
    {
        $this->dryRun = $value;
    }

    public function getDryRun()
    {
        return $this->dryRun;
    }

    /*************** Entity factory ***********************/

    public function createEntity(string $className, array $values)
    {
        $this->checkEntity($className);

        $this->getLoggerWrapper()->debug(sprintf("Creating new %s.", $className), [ 'values' => $values ]);

        if ($this->getDryRun() === true) {
            return;
        }

        $endpoint = $className::getZabbixEndpoint();
        $values = $this->getZabbix()->$endpoint->create($values);

        $entityIdIndex = $className::getEntityIdIndex() . 's';

        if (!isset($values[$entityIdIndex]) || (count($values[$entityIdIndex]) === 0)) {
            throw new \RuntimeException('Entity id index not found.');
        }

        return $this->getEntity($className, [
            $entityIdIndex => array_shift($values[$entityIdIndex])
        ]);
    }

    /*************** Getters ******************************/

    public function getEntityManager(): \ZabbixWrapper\EntityManager
    {
        return $this;
    }

    /*************** Misc *********************************/

    protected static function createInstance($parent, $data)
    {
        throw new \BadMethodCallException();
    }

    private function checkEntity(string $className)
    {
        if (is_subclass_of($className, Entity\Entity::class)) {
            return true;
        }

        throw new \InvalidArgumentException(sprintf('"%s" is not "%s".', $className, Entity\Entity::class));
    }
}
