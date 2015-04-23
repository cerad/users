<?php

namespace Cerad\Archive;

class ArchiveServices
{
  public function __construct($container)
  {
    $newDbConn = function($dbUrl)
    {
      $config = new \Doctrine\DBAL\Configuration();
      $connParams = 
      [
        'url' => $dbUrl,
        'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false],
      ];
      return \Doctrine\DBAL\DriverManager::getConnection($connParams, $config);
    };
    /* ======================================
     * Connections
     */
    $container->set('db_conn_ng2012',function($container) use($newDbConn)
    {
      return $newDbConn($container->get('db_url_ng2012'));
    });
    $container->set('db_conn_ng2014',function($container) use($newDbConn)
    {
      return $newDbConn($container->get('db_url_ng2014'));
    });
    $container->set('db_conn_tourns',function($container) use($newDbConn)
    {
      return $newDbConn($container->get('db_url_tourns'));
    });
    /* ===========================================
     * Commands
     */
    $container->set('unload_ng2012_command',function($container)
    {
      return new UnloadNG2012Command($container->get('db_conn_ng2012'));
    },'command');
    $container->set('unload_ng2014_command',function($container)
    {
      return new UnloadNG2014Command($container->get('db_conn_ng2014'));
    },'command');
    $container->set('unload_tourns_command',function($container)
    {
      return new UnloadTournsCommand($container->get('db_conn_tourns'));
    },'command');
  }
}