<?php

namespace ZabbixWrapper\Entity;

class ItemPrototype extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'itemid';

    protected static string $zabbixEndpoint = 'itemprototype';
}
