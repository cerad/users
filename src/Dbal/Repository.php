<?php
namespace Cerad\Component\Dbal;

class Repository
{
  protected $dbConn;
  
  protected $c2p = [];
  protected $p2c = [];
  
  protected $joins = [];
  
  protected $tableSelects    = [];
  protected $tableDefaults   = [];
  protected $tablePrimaryKey = [];
  
  public function __construct($dbConn)
  {
    $this->dbConn = $dbConn;    
  }
  protected function loadTable($name,$aliasArg = null)
  {
    // Strip traoiling s if no alias provided
    $alias = ($aliasArg !== null) ? $aliasArg : substr($aliasArg,0,strlen($aliasArg)-1);
    
    $sm = $this->dbConn->getSchemaManager();
    
    $table = $sm->listTableDetails($name);
    
    $primaryKeyCols = $table->getPrimaryKey()->getColumns();
    $primaryKey = implode(',',$primaryKeyCols);
    $this->tablePrimaryKey[$name] = $primaryKey;
    
    $cols = $table->getColumns();
    
    $selects  = [];
    $defaults = [];
    foreach($cols as $colName => $col)
    {
      $colNameParts = explode('_',$colName);
      for($i = 1, $ii = count($colNameParts); $i < $ii; $i++)
      {
        $colNameParts[$i] = ucfirst($colNameParts[$i]);
      }
      $propName = implode(null,$colNameParts);
      
      if (count($colNameParts)) 
      {
        $this->c2p[ $colName] = $propName;
        $this->p2c[$propName] =  $colName;
      }
      $selects[] = sprintf('%s.%s AS %s__%s',$alias,$colName,$alias,$propName);
      
      $defaults[$colName] = $col->getDefault();
    }
    $this->tableSelects[$name . '.' . $alias] = $selects;
    
    $this->tableDefaults[$name] = $defaults;
  }
  protected function getTableSelects($name,$alias)
  {
    $key = $name . '.' . $alias;
    
    if (!isset($this->tableSelects[$key])) 
    {
      $this->loadTable($name,$alias);
    }
    return $this->tableSelects[$key];
  }
  protected function getTableDefaults($name)
  {
    if (!isset($this->tableDefaults[$name])) 
    {
      $this->loadTable($name);
    }
    return $this->tableDefaults[$name];
  }
  protected function getTablePrimaryKey($name)
  {
    if (!isset($this->tablePrimaryKey[$name])) 
    {
      $this->loadTable($name);
    }
    return $this->tablePrimaryKey[$name];
  }
  protected function extractItem($prefix,$row)
  {
    $item = [];
    $prefixLen = strlen($prefix);
    foreach($row as $key => $value)
    {
      if (substr($key,0,$prefixLen) === $prefix)
      {
        $item[substr($key,$prefixLen)] = $value;
      }
    }
    return $item;
  }
  /* =================================================
   * Just o mess around with
   */
  public function test()
  {
    $qb = $this->dbConn->createQueryBuilder();
    
    $qb->addSelect(['user_name AS userName','disp_name']);
    $qb->addSelect(['password AS user.password']);
    $qb->from('users','user');
    
    die($qb);
  }
}