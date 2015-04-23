<?php

namespace Cerad\Module\UserModule;

use Cerad\Component\HttpMessage\Request;

use Cerad\Component\DependencyInjection\Container;

class UserApp
{
  protected $container;
  
  public function __construct()
  {
    $this->container = $container = new Container();
    new UserParameters($container);
    new UserServices  ($container);
    new UserRoutes    ($container);
    
    $container->set('route_matcher',function($container)
    {
      $routes = [];
      $tags = $container->getTags('route');
      foreach($tags as $tag)
      {
        $serviceId = $tag['service_id'];
        $service   = $container->get($serviceId);
        $routes[$serviceId] = $service;
      }
      return new \Cerad\Component\HttpRouting\UrlMatcher
      (
        $routes,
        $container->get('request_context')
      );
    });
    
  }
  public function getContainer() { return $this->container; }
  
  public function handle(Request $request)
  {
    $requestContext = [ 'method' => $request->getMethod()];
    
    $this->container->set('request_context',$requestContext);
    
    $matcher = $this->container->get('route_matcher');
    $match   = $matcher->match($request->getRoutePath());
    if (!$match) return null; //die ('No match for ' . $request->getRoutePath());
    
    $request->setAttributes($match);
    $action   = $request->getAttribute('_action');
    $response = $action($request);
    
    return $response;
  }
}