<?php

namespace Cerad\Archive;

error_reporting(E_ALL);

use Symfony\Component\Console\Command\Command;
//  Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
//  Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;
use Doctrine\DBAL\Connection;

class UnloadNG2014Command extends Command
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
      ->setName('unload:ng2014')
      ->setDescription('Unload NG2014 Database');
  }
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln('Unload ng2014'); 
    
    $sql = <<<EOT
SELECT 
  user.username     AS username,
  user.email        AS email,
  user.salt         AS salt,
  user.password     AS password,
  user.account_name AS name,
  user.roles        AS roles,
      
  person.guid      AS personKey,
  person.name_full AS personNameFull,
  person.email     AS personEmail,
  
  fed.fed              AS fedName,
  fed.fed_role         AS fedRole,
  fed.fed_key          AS fedKey,
  fed.fed_key_verified AS fedKeyVerified,
  fed.org_key          AS fedOrgKey,
  fed.org_key_verified AS fedOrgKeyVerified,
  fed.mem_year         AS fedMemYear,
  fed.person_verified  AS fedPersonVerified,
      
  cert.role           AS certRole,
  cert.role_date      AS certRoleDate,
  cert.badge          AS certBadge,
  cert.badge_date     AS certBadgeDate,
  cert.badge_verified AS certBadgeVerified,
  cert.badge_user     AS certBadgeUser,
  cert.upgrading      AS certUpgrading,
  cert.org_key        AS certOrgKey,
  cert.mem_year       AS certMemYear

FROM users AS user
LEFT JOIN persons          AS person ON person.guid = user.person_guid
      
LEFT JOIN person_feds      AS fed    ON fed.person_id = person.id
      
LEFT JOIN person_fed_certs AS cert   ON cert.person_fed_id = fed.id
      
EOT;
    $stmt = $this->dbConn->executeQuery($sql);
    $rows = $stmt->fetchAll();
    
    //$output->writeln(sprintf('User Count: %d',count($rows)));
    
    $persons = [];
    foreach($rows as $row)
    {
      $personKey = $row['personKey'];
      if (!$personKey)
      {
        echo sprintf("Missing person guid\n");
      }
      if (isset($persons[$personKey])) $person = $persons[$personKey];
      else
      {
        $person = 
        [
          'key'      => $personKey,
          // Record 323 had a non UTF-8 character in it, need to filter the input
          'nameFull' => mb_convert_encoding($row['personNameFull'],'UTF-8','UTF-8'),
          'email'    => $row['personEmail'],
          'user'     => [],
          'feds'     => [],
        ];
        $user = 
        [
          'userName' => mb_convert_encoding($row['username'],'UTF-8','UTF-8'),
          'dispName' => mb_convert_encoding($row['name'],'UTF-8','UTF-8'),
          'email'    => $row['email'],
          'salt'     => $row['salt'],
          'password' => $row['password'],
          'roles'    => array_values(unserialize($row['roles'])),
        ];
        $person['user'] = $user;
      }
      /* ==================================================
       * Extract federation object
       */
      $fedName = $row['fedName'];
      if (isset($person['feds'][$fedName])) $fed = $person['feds'][$fedName];
      else
      {
        $fed = 
        [
          'name'           => $fedName,
          'role'           => $row['fedRole'],
          'key'            => $row['fedKey'],
          'keyVerified'    => $row['fedKeyVerified'],
          'orgKey'         => $row['fedOrgKey'],
          'orgKeyVerified' => $row['fedOrgKeyVerified'],
          'memYear'        => $row['fedMemYear'],
          'personVerified' => $row['fedPersonVerified'],
          'certs'          => [],
        ];
      }
      $certRole = $row['certRole'];
      $cert = 
      [
        'role'          => $certRole,
        'roleDate'      => $row['certRoleDate'],
        'badge'         => $row['certBadge'],
        'badgeDate'     => $row['certBadgeDate'],
        'badgeVerified' => $row['certBadgeVerified'],
        'badgeUser'     => $row['certBadgeUser'],
        'upgrading'     => $row['certUpgrading'],
        'orgKey'        => $row['certOrgKey'],
        'memYear'       => $row['certMemYear'],
      ];
      $fed['certs'][$certRole] = $cert;
      $person['feds'][$fedName] = $fed;

      if (count($persons) < 32300) $persons[$personKey] = $person;
    }
    file_put_contents('data/personsNG2014.yml',Yaml::dump(array_values($persons),10,2));

    $output->writeln(sprintf('Person Count: %d',count($persons)));

    //$personsValues = array_values($persons);
    //print_r($personsValues[0]);
  }
}