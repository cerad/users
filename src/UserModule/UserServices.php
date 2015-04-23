<?php
namespace Cerad\Module\UserModule;

class UserServices
{
  public function __construct($container)
  {
    /* ======================================
     * Connections
     */
    $container->set('db_conn_users',function($container)
    {
      return \Cerad\Component\Dbal\ConnectionFactory::create($container->get('db_url_users'));
    });
    
    /* ===========================================
     * Repositories
     */
    $container->set('user_repository',function($container)
    {
      return new UserRepository($container->get('db_conn_users'));
    },'repository');
    
    /* ===========================================
     * Controllers
     */
    $container->set('user_controller',function($container)
    {
      return new UserController($container->get('user_repository'));
    },'controller');
  }
}