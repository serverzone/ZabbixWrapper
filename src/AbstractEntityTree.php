<?php

namespace ZabbixWrapper;

use Psr\Log;

abstract class AbstractEntityTree implements EntityTree
{

    public function __toString()
    {
        return '';
    }

    /*************** Parent *******************************/

    protected $parent = null;

    public function getParent()
    {
        return $this->parent;
    }

    /*************** Zabbix *******************************/

    protected $zabbix;

    /**
     * Returns ZabbixApi\ZabbixApi instance we use in current tree
     *
     */
    public function getZabbix()
    {
        return $this->zabbix;
    }

    /*************** Logger *******************************/

    use Log\LoggerAwareTrait;

    /**
     * Return current logger or NullLogger it none found.
     *
     */
    public function getLogger()
    {
        if (isset($this->logger) === false) {
            $parent = $this->getParent();
            $this->logger = ($parent !== null ? $parent->getLogger() : new Log\NullLogger());
        }

        return $this->logger;
    }

    protected $loggerWrapper;

    public function getLoggerWrapper()
    {
        if (isset($this->loggerWrapper) === false) {
            $signature = $this->__toString();
            $signature = static::class . ($signature ? "('" . $signature . "')" : '');
            $this->loggerWrapper = new Logger\EntityHistoryContextWrapper($this, $signature);
        }
        return $this->loggerWrapper;
    }

    /*************** EntityManager ************************/

    protected EntityManager $entityManager;

    /**
     * Returns EntityManagers
     *
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /*************** Entities factory *********************/

    /**
     * Returns filtering parameters for getEntity, getEntities, hasEntity
     *
     */
    abstract protected function buildGetEntityParameters(string $className);

    abstract public function getEntity(string $className, ...$parameters);

    public function fluentEntity(string $className, ...$parameters)
    {
        try {
            return $this->getEntity($className, ...$parameters);
        } catch (EntityNotFoundException $e) {
            return new Entity\DummyFluent($this);
        }
    }

    abstract public function getEntities(string $className, ...$parameters);

    /**
     * Creates new Entity instance
     *
     * We protected creating new instances out of EntityTree this way
     *
     */
    protected static function createInstance($parent, $data)
    {
        return new static($parent, $data);
    }
}
