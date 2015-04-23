#!/usr/bin/env php
<?php
// application.php
namespace Cerad\Archive;

require __DIR__ . '/../vendor/autoload.php';

use Cerad\Component\DependencyInjection\Container;

use Symfony\Component\Console\Application;

$container = new Container();

new ArchiveParameters($container);
new ArchiveServices  ($container);

$application = new Application();

$tags = $container->getTags('command');
foreach($tags as $tag)
{
  $application->add($container->get($tag['service_id']));
}
$application->run();