<?php
namespace Cerad\Module\AuthModule;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

use Doctrine\DBAL\Connection;

class AuthUserProviderDao implements AuthUserProviderInterface
{
  private $db;
  
  public function __construct(Connection $db)
  {
    $this->db = $db;
  }
  public function loadUserByUsername($username)
  {
    $sql = <<<EOT
SELECT 
  id,
  user_name AS userName,
  disp_name AS dispName,
  email,salt,password,roles,
  person_key AS personKey
FROM users
WHERE user_name = ? OR email = ?;
EOT;
    $stmt = $this->db->executeQuery($sql,[$username,$username]);
    $rows = $stmt->fetchAll();
    if (count($rows) != 1) 
    {
      $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
      $ex->setUsername($username);
      throw $ex;
    }
    $user = $rows[0];

    //if (count($user['roles']) < 1) $user['roles'] = ['ROLE_USER'];
    
    return $user;
  }
}