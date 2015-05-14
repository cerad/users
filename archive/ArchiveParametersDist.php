<?php

namespace Cerad\Archive;

use Cerad\Component\DependencyInjection\Container;

class ArchiveParametersDist
{
  public function __construct(Container $container)
  {
    $container->set('db_url_ng2012','mysql://user:pass@localhost/ng2012');
    $container->set('db_url_ng2014','mysql://user:pass@localhost/ng2014');
    $container->set('db_url_tourns','mysql://user:pass@localhost/tourns');
    $container->set('db_url_users' ,'mysql://user:pass@localhost/users' );
  }
}