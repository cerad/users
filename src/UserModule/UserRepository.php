<?php
namespace Cerad\Module\UserModule;

use Cerad\Component\Dbal\Repository;

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
    $itemId =  $this->insertItem('users',$itemProps);

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
    return $this->insertItem('user_auths',$itemProps);
  }
  /* ==========================================================
   * Find one stuff
   */
  public function findOne($id)
  {
    return $this->findItem('users',$id);
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
      if (strpos($colName,'.') === false) $colName = 'user.' . $colName;
      
      $paramName = str_replace('.','_',$colName);
      
      $qb->andWhere($colName . ' = :' . $paramName);
      
      $qb->setParameter($paramName,$value);
    }
  //echo sprintf("\n%s\n",(string)$qb);
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
}