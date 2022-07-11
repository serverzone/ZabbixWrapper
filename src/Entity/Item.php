<?php

namespace ZabbixWrapper\Entity;

class Item extends AbstractEntity
{

    protected static string $entityNameIndex = 'name';

    protected static string $entityIdIndex = 'itemid';

    protected static string $zabbixEndpoint = 'item';
}
