<?php

namespace Cerad\Archive;

error_reporting(E_ALL);

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UnloadNG2012Command extends Command
{
  protected $dbConn;
  
  public function __construct($dbConn)
  {
    parent::__construct();
    
    $this->dbConn = $dbConn;
  }
  protected function configure()
  {
    $this
      ->setName('unload:ng2012')
      ->setDescription('Unlod NG2012 Database');
  }
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $output->writeln('Unload ng2012'); 
    
    $sql = <<<EOT
SELECT 
  account.user_name AS username,
  account.user_pass AS userpass,
  person.first_name AS nameFirst,
  person.last_name  AS nameLast,
  person.nick_name  AS nameNick,
  person.email      AS email,
  person.cell_phone AS phone,
  person.gender     AS gender,
  person.dob        AS dob,
  person_reg.reg_type AS fedRole,
  person_reg.reg_key  AS fedKey,
  person_reg.org_id   AS orgKey,
  person_reg.datax    AS regData
FROM account
LEFT JOIN person ON person.id = account.person_id
LEFT JOIN person_reg ON person_reg.person_id = person.id
EOT;
    $stmt = $this->dbConn->executeQuery($sql);
    $rows = $stmt->fetchAll();
    
    $output->writeln(sprintf('User Count: %d',count($rows))); 
    
    $row = $rows[0];
    $row['regData'] = unserialize($row['regData']);
    
    print_r($row);
  }
}