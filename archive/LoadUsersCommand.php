<?php

namespace Cerad\Archive;

use Symfony\Component\Console\Command\Command;
//  Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;
use Doctrine\DBAL\Connection;

class LoadUsersCommand extends Command
{
  protected $dbConn;
  
  public function __construct(Connection $dbConn)
  {
    parent::__construct();
    
    $this->dbConn = $dbConn;
  }
  protected function configure()
  {
    $this
      ->setName('load:users')
      ->setDescription('Load Users Database');
  }
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $dbName   = $this->dbConn->getDatabase();
    $userName = $this->dbConn->getUsername();

    shell_exec(sprintf('mysql --login-path=%s -e "DROP DATABASE IF EXISTS %s"',$userName,$dbName));
    shell_exec(sprintf('mysql --login-path=%s -e "CREATE DATABASE %s"',        $userName,$dbName));
    shell_exec(sprintf('mysql --login-path=%s %s < %s',$userName,$dbName,__DIR__ . '/../config/schema.sql'));

    $this->load('data/personsNG2014.yml');
    $this->load('data/personsTourns.yml');
  }
  protected function doesUserExist($user)
  {
    $sql = <<<EOT
SELECT
  user.id        AS id,
  user.user_name AS userName,
  user.disp_name AS dispName,
  user.email     AS email
FROM  users AS user
WHERE user.user_name = ? OR user.email = ?
EOT;
    $stmt = $this->dbConn->executeQuery($sql,[$user['userName'],$user['email']]);
    $rows = $stmt->fetchAll();
    return count($rows) ? true : false;
  }
  protected function load($fileName)
  {
    $persons = Yaml::parse(file_get_contents($fileName));
    echo sprintf("File %s Count %d\n",$fileName,count($persons));

    foreach($persons as $person)
    {
      $user = $person['user'];
      $userx = [
        'user_name'  => $user['userName'],
        'disp_name'  => $user['dispName'],
        'email'      => $user['email'],
        'password'   => $user['password'],
        'salt'       => $user['salt'],
        'roles'      => count($user['roles']) ? implode(',',$user['roles']) : 'ROLE_USER',
        'person_key' => $person['key'],
      ];
      if ($this->doesUserExist($user)) {
        echo sprintf("Existing user %s %s\n",$user['userName'],$user['dispName']);
      }
      else {
        $this->dbConn->insert('users',$userx);
      }

      //print_r($userx); die();
    }
  }
}