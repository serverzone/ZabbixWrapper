<?php

namespace ZabbixWrapper\Entity;

use ZabbixWrapper;

interface Entity
{

    /**
     * Returns raw data from zabbix
     *
     * TODO: Mozna presunout do EntityTree a v Entitymanageru udelat vyjimku - cilem je, aby nebyla public
     */
    public static function fetchEntities(ZabbixWrapper\EntityTree $parent, ...$parameters);

    /**
     * Returns zabbix endpoint
     *
     */
    public static function getZabbixEndpoint();

}
