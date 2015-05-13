<?php
namespace Cerad\Module\UserModule;

use Cerad\Component\DependencyInjection\Container;

use Cerad\Component\Dbal\ConnectionFactory;

class UserServices
{
  public function __construct(Container $container)
  {
    /* ======================================
     * Connections
     */
    $container->set('db_conn_users',function(Container $container)
    {
      return ConnectionFactory::create($container->get('db_url_users'));
    });

    /* ===========================================
     * Repositories
     */
    $container->set('user_repository',function(Container $container)
    {
      return new UserRepository($container->get('db_conn_users'));
    },'repository');
    
    /* ===========================================
     * Controllers
     */
    $container->set('user_controller',function(Container $container)
    {
      return new UserController($container->get('user_repository'));
    },'controller');
  }
}