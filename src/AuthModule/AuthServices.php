<?php
namespace Cerad\Module\AuthModule;

use Cerad\Component\Jwt\JwtCoder;
use Cerad\Component\Dbal\ConnectionFactory;
use Cerad\Component\DependencyInjection\Container;

class AuthServices
{
  public function __construct(Container $container)
  {
    // Users
    $users = 
    [
      'ahundiak@gmail.com' 
        => ['password' => 'zzz','roles' => 'ROLE_ADMIN','person_name' => 'Art gmail Hundiak'],
      'ahundiak' => ['password' => 'zzz',      'roles' => 'ROLE_ADMIN',    'person_name' => 'Art Hundiak'],
      'sra'      => ['password' => 'sra',      'roles' => 'ROLE_SRA',      'person_name' => 'Pat Miller'],
      'assignor' => ['password' => 'assignor', 'roles' => 'ROLE_ASSIGNOR', 'person_name' => 'Andy Dye'],
      'user'     => ['password' => 'user',     'roles' => 'ROLE_USER',     'person_name' => 'Bill Steely'],
    ];
    $container->set('auth_users_data',$users);
    $container->set('auth_user_provider_in_memory',function(Container $container)
    {
      return new AuthUserProviderInMemory($container->get('auth_users_data'));
    });
    $container->set('auth_user_password_encoder_plain_text',function()
    {
      return new AuthUserPasswordEncoderPlainText();
    });
    // Roles
    $hierarchy = 
    [
        'ROLE_USER'        => [],
        'ROLE_ASSIGNOR'    => ['ROLE_USER'],
        'ROLE_SRA'         => ['ROLE_ASSIGNOR'],
        'ROLE_ADMIN'       => ['ROLE_USER','ROLE_ASSIGNOR','ROLE_SRA'],
        'ROLE_SUPER_ADMIN' => ['ROLE_ADMIN'],
    ];
    $container->set('auth_role_hierarchy_data',$hierarchy);
    $container->set('auth_role_hierarchy',function(Container $container)
    {
      return new AuthRoleHierarchy($container->get('auth_role_hierarchy_data'));
    });
    $container->set('jwt_coder',function(Container $container)
    {
      return new JwtCoder($container->get('secret'));
    });
    $container->set('auth_token_listener',function(Container $container)
    {
      return new AuthTokenListener
      (
        $container->get('auth_role_hierarchy'),
        $container->get('jwt_coder')
      );
    },'event_listener');
    
    $container->set('auth_token_controller',function(Container $container)
    {
      return new AuthTokenController
      (
        $container->get('jwt_coder'),
        $container->get('auth_user_provider_dao'),
        $container->get('auth_user_password_encoder_dao')
      );
    });
    $container->set('database_connection_users',function(Container $container)
    {
      return ConnectionFactory::create($container->get('db_url_users'));
    });    
    $container->set('auth_user_provider_dao',function(Container $container)
    {
      return new AuthUserProviderDao
      (
        $container->get('database_connection_users')
      );
    });
    $container->set('auth_user_password_encoder_dao',function(Container $container)
    {
      return new AuthUserPasswordEncoderDao
      (
        $container->get('cerad_user_master_password')
      );
    });
  }
}