<?php

namespace Cerad\Archive;

use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Component\DependencyInjection\Container;

class ArchiveServices
{
  public function __construct(Container $container)
  {
    /* ======================================
     * Connections
     */
    $container->set('db_conn_ng2012',function(Container $container)
    {
      return ConnectionFactory::create($container->get('db_url_ng2012'));
    });
    $container->set('db_conn_ng2014',function(Container $container)
    {
      return ConnectionFactory::create($container->get('db_url_ng2014'));
    });
    $container->set('db_conn_tourns',function(Container $container)
    {
      return ConnectionFactory::create($container->get('db_url_tourns'));
    });
    /* ===========================================
     * Commands
     */
    $container->set('unload_ng2012_command',function(Container $container)
    {
      return new UnloadNG2012Command($container->get('db_conn_ng2012'));
    },'command');
    $container->set('unload_ng2014_command',function(Container $container)
    {
      return new UnloadNG2014Command($container->get('db_conn_ng2014'));
    },'command');
    $container->set('unload_tourns_command',function(Container $container)
    {
      return new UnloadTournsCommand($container->get('db_conn_tourns'));
    },'command');
  }
}