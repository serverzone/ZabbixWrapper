<?php

namespace ZabbixWrapper\Entity;

class Graph extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'graphid';

    protected static string $zabbixEndpoint = 'graph';
}
