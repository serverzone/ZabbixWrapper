<?php

namespace ZabbixWrapper\Entity;

class Trigger extends AbstractEntity
{
    protected static string $entityNameIndex = 'description';

    protected static string $entityIdIndex = 'triggerid';

    protected static string $zabbixEndpoint = 'trigger';
}
