<?php

namespace ZabbixWrapper;

class ZabbixException extends \Exception
{
}

class EntityNotFoundException extends ZabbixException
{
}

class MultipleEntitiesFoundException extends ZabbixException
{
}
