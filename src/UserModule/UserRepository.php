<?php
namespace Cerad\Module\UserModule;

class UserRepository extends Repository
{
  protected $joins =
  [
    'auths' => 
    [
      'from'    => 'user',
      'table'   => 'user_auths',
      'alias'   => 'auth',
      'on'      => 'auth.user_id = user.id',
      'selects' => 'userAuthSelects',
    ],
  ];
  public function insertUser($itemProps)
  {
    $tableName = 'users';
    
    $itemx = $this->getTableDefaults($tableName);
    
    $primaryKey = $this->getTablePrimaryKey($tableName);
    
    unset($itemx[$primaryKey]);
    
    $c2p = $this->c2p;
    $item = [];
    foreach(array_keys($itemx) as $colName)
    {
      $propName = isset($c2p[$colName]) ? $c2p[$colName] : $colName;
      
      if (array_key_exists($propName,$itemProps))
      {
        $item[$colName] = $itemProps[$propName];
      }
    }
    $this->dbConn->insert($tableName,$item);

    $itemId = $this->dbConn->lastInsertId();
    
    if (isset($itemProps['auths']))
    {
      foreach($itemProps['auths'] as $auth)
      {
        $auth['userId'] = $itemId;
        $this->insertUserAuth($auth);
      }
    }
    return $itemId;
  }
  public function insertUserAuth($itemProps)
  {
    $cols = $this->getTableDefaults('user_auths');
    unset($cols['id']);
    
    $item = [];
    $c2p = $this->c2p;
    foreach(array_keys($cols) as $colName)
    {
      $propName = isset($c2p[$colName]) ? $c2p[$colName] : $colName;
      
      if (array_key_exists($propName,$itemProps))
      {
        $item[$colName] = $itemProps[$propName];
      }
    }
    $this->dbConn->insert('user_auths',$item);

    return $this->dbConn->lastInsertId();   
  }
  /* ==========================================================
   * Find one stuff
   */
  public function findOne($id)
  {
    $items = $this->findBy(['user.id' => $id]);
    
    return count($items) === 1 ? $items[0] : null;
  }
  public function findOneWithAuths($id)
  {
    $items = $this->findBy(['user.id' => $id],['auths']);
    
    return count($items) === 1 ? $items[0] : null;
  }
  /* ===========================================================
   * Find all
   */
  public function findAll($joins = [])
  {
    return $this->findBy([],$joins);   
  }
  /* ===========================================================
   * Generic
   * 'id' => value
   * joins = auths
   */
  public function findBy($criteria = [], $joins = [])
  {
    $qb = $this->dbConn->createQueryBuilder();
    $qb->select($this->getTableSelects('users','user'));
    $qb->from('users','user');
    
    foreach($joins as $join)
    {
      $joinx = isset($this->joins[$join]) ? $this->joins[$join] : null;
      
      if (!$joinx) die('Invalid join ' . $join);
      
      $qb->addSelect($this->getTableSelects($joinx['table'],$joinx['alias']));
      
      $qb->leftJoin($joinx['from'],$joinx['table'],$joinx['alias'],$joinx['on']);
    }
    foreach($criteria as $colName => $value)
    {
      $paramName = str_replace('.','_',$colName);
      $qb->andWhere($colName . ' = :' . $paramName);
      $qb->setParameter($paramName,$value);
    }
    $rows = $qb->execute()->fetchAll();
    
    $users = [];
    foreach($rows as $row)
    {
      $userId = $row['user__id'];
      if (isset($users[$userId])) $user = $users[$userId];
      else                        $user = $this->extractItem('user__',$row);
      
      $auth = $this->extractItem('auth__',$row);
      
      if (count($auth)) $user['auths'][] = $auth;
      
      $users[$userId] = $user;
    }
    return array_values($users);
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