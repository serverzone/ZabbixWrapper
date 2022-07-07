# Zabbix wrapper

**Please, be advised the library is far away from complete.**

Feel free to send us merge request.

## A quick example

```php
<?php

use ZabbixWrapper\Entity as ZE;

require __DIR__ . '/vendor/autoload.php';

$zabbix = new ZabbixApi\ZabbixApi('http://localhost/api_jsonrpc.php', ['user' => 'Admin', 'password' => 'zabbix'], ['verify' => false]);
$manager = new ZabbixWrapper\EntityManager($zabbix);

// Delete Ethernet speed change trigger if exists
$manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')
    ->getEntity(ZE\DiscoveryRule::class, 'Network interface discovery');
    ->fluentEntity(ZE\TriggerPrototype::class, 'Interface {#IFNAME}: Ethernet has changed to lower speed than it was before')
    ->delete();

// Update AGENT.NODATA_TIMEOUT to 5 minutes (default is 30 minutes)
$manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')
    ->getEntity(ZE\Macro::class, '{$AGENT.NODATA_TIMEOUT}')->update(['value' => '5m']);
```

## Purpose

We wanted to migrated our old zabbix server to a fresh installation, so we can use new and updated templates. We also wanted to keep track of changes we do to the templates. You know - default 30 minutes for server outage trigger is too long for production environment.

Zabbix provides us with an amaizing API and (freeeed/zabbix)[https://packagist.org/packages/freeeed/zabbix] is extremely lightweight library to acess zabbix api in PHP.

However after several migration scripts we started to struggle with very complex code, which was repeating itself.

Therefore we were looking for a method, which will help us to express our changes in human readable format. Such us:

```
Template('Linux agent')
    ->Discovery('Linux by Zabbix agent active')
    ->TriggerPrototype('{#FSNAME}: Disk space is critically low')
    ->delete()
```

## Installation

TODO: `composer require`

## Usage

**Create an instance**

```
<?php

require __DIR__ . '/vendor/autoload.php';

use ZabbixWrapper\Entity as ZE;

$zabbix = new ZabbixApi\ZabbixApi('http://localhost/zabbix/api_jsonrpc.php', ['user' => 'Admin', 'password' => 'zabbix'], ['verify' => false]);
$manager = new ZabbixWrapper\EntityManager($zabbix);
```

**List of all templates**

```php
$templates = $manager->getEntities(ZE\Template::class);
foreach ($templates as $template) {
    echo $template->__toString() . "\n";
}
```

**More complex example with chaining**

```php
$itemPrototype = $manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')
    ->getEntity(ZE\DiscoveryRule::class, 'Mounted filesystem discovery')
    ->getEntity(ZE\ItemPrototype::class, '{#FSNAME}: Free space');
```

**Custom filters other than the name**

 *  Please, follow (zabbix api documentation)[https://www.zabbix.com/documentation/current/en/manual/api/reference] to pass correct parameters

```php
$template->getEntitiy(ZE\Template::class, [ 'templateids' => 12345 ]);
$template->getEntitiy(ZE\Template::class, [ 'filter' => [ 'uuid' => 'dad8d6c6-21c7-4ff2-a9ad-987ba8e9de84' ] ]);
```

## Entity methods

**getEntity**

  * Returns one entity
  * Throws an exception if no entity is found
  * Thorws an exception if multiple entities are found

```php
$template = $manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')
```

**getEntities**

  * Returns an array of found entities

```php
$templates = $manager->getEntities(ZE\Template::class);
```

**fluentEntity**

  * Returns entity or DummyFluent, which si an object consuming all commands and returing itself
  * Throws an exception if multiple entities are found
  * Use case: Item is supposed to be deleted no matter if it exists or not, so we run the script mulitple times

```php
$template = $manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')
    ->fluentEntity(ZE\Item::class, 'Checksum of /etc/passwd')
    ->delete();
```

**hasEntities**

  * Returns count of found entities

```php
echo $manager->hasEntities(ZE\Template::class)) . " have been found in zabbix.";
```

**create**

  * Creates new entity in zabbix

TODO

```php
```

**delete**

  * Deletes an entity in zabbix

```php
# Let's delete all graphs
foreach ($manager->getEntites(ZE\Graph::class as $graph) {
    $graph->delete();
}
```

  * Updates an entity in zabbix

**update**

```php
# Let's rename all graphs
foreach ($manager->getEntites(ZE\Graph::class as $graph) {
    $graph->update([
        'name' => 'Prefix: ' . $graph->get('name')
    ]);
}
```

## Accessing entity values

**get**

 *  Returns specific parameter's value

```php
$manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')->get('name');
```

**dump**

 *  Returns array with all parameters

```php
$manager->getEntity(ZE\Template::class, 'Linux by Zabbix agent active')->dump();
```

## Development

This library is still under development, but we use it in our production environment, so API should not change much.

**Issues**

 *  Please provide us with code example if possible

**Merge requests**

 *  All merge requests are welcome
