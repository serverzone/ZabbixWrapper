<?php declare(strict_types=1);


namespace ZabbixWrapper\Entity;

/**
 * Zabbix media type entity.
 */
class MediaType extends AbstractEntity
{
    protected static string $entityNameIndex = 'name';

    protected static string $zabbixEndpoint = 'mediaType';

    protected static string $entityIdIndex = 'mediatypeid';
}
