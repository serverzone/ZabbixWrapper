<?php

namespace ZabbixWrapper\Entity;

class TriggerPrototype extends AbstractEntity
{

    protected static string $entityNameIndex = 'description';

    protected static string $entityIdIndex = 'triggerid';

    protected static string $zabbixEndpoint = 'triggerprototype';

}
