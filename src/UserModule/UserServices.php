<?php

namespace Cerad\Module\UserModule;

class UserServices
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
    $container->set('db_conn_users',function($container) use($newDbConn)
    {
      return $newDbConn($container->get('db_url_users'));
    });
    /* ===========================================
     * Repositories
     */
    $container->set('user_repository',function($container)
    {
      return new UserRepository($container->get('db_conn_users'));
    },'repository');
  }
}