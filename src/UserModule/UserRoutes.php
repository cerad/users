<?php
namespace Cerad\Module\UserModule;

class UserRoutes
{
  public function __construct($container)
  {
    $usersRouteAction = function($request) use ($container)
    {
      $controller = $container->get('user_controller');
      $id = $request->getAttribute('id');
      switch($request->getMethod())
      {
        case 'GET':
          return $id !== null
            ? $controller->getOneAction($request,$id)
            : $controller->searchAction($request);
          
        case 'POST':   return $controller->postAction  ($request);
        case 'PUT':    return $controller->putAction   ($request,$id);
        case 'DELETE': return $controller->deleteAction($request,$id);
      }
    };
    $usersRouteMatch = function($path, $context = null) use($usersRouteAction)
    {
      $params = [
        'id'      => null,
        '_action' =>  $usersRouteAction,
      //'_roles'  => ['ROLE_ASSIGNOR']
      ];
      if ($path === '/users') 
      {
      //if (!in_array($context['method'],['GET','POST'])) return false;

        return $params;
      }
      $matches = [];
        
      if (!preg_match('#^/users/(\d+$)#', $path, $matches)) return false;

      $params['id'] = $matches[1]; // No typecast, ussf id's are 16 digits long
        
      return $params;
    };
    $usersRouteService = function() use ($usersRouteMatch)
    {
      return $usersRouteMatch;
    };
    $container->set('users_route',$usersRouteService,'route');
  }
}