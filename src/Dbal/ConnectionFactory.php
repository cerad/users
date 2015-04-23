<?php
namespace Cerad\Component\Dbal;

class ConnectionFactory
{
  static function create($dbUrl)
  {
    $config = new \Doctrine\DBAL\Configuration();
    $connParams = 
    [
      'url' => $dbUrl,
      'driverOptions' => [\PDO::ATTR_EMULATE_PREPARES => false],
    ];
    return \Doctrine\DBAL\DriverManager::getConnection($connParams, $config);    
  }
}