<?php

namespace Authentication;

use Psr\Container\ContainerExceptionInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

/**
 * Class Module
 *
 * @package Authentication
 */
class Module implements ConfigProviderInterface
{

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     * @throws ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getApplication();
        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $configArray = $serviceManager->get('ApplicationConfig');

//        var_dump($serviceManager->get('systemLogger'));

        /*
         * 如果没有在配置文件<b>application.config.php</b>中设定<b>authentication_required</b>的值，或是设定为true
         * 则表示需要对使用者进行身份认证
         */
        if (!key_exists('authentication_required', $configArray) || (true == $configArray['authentication_required'])) {
            try {
                $authService = $serviceManager->get('AuthenticationService');
                if (null == $authService) {
                    throw new ServiceNotFoundException('没有定义身份认证服务！！', 100);
                }
                if (!$authService->hasIdentity()) { //用户没有完成身份认证
                    var_dump('用户没有完成身份认证');
//                    $eventManager->attach(MvcEvent::EVENT_ROUTE, function () use ($event) {
//                        //如果当前请求的不是登录页面，才做跳转
//                        $routeName = $event->getRouteMatch()->getMatchedRouteName();
//                        var_dump($routeName);
//                        if (!in_array($routeName, array('authentication', 'authentication/signin', 'authentication/signout'))) {
//                            $url = $event->getRouter()->assemble(array(), array('name' => 'authentication'));
//                            $url .= '?r=' . $event->getRequest()->getRequestUri();
//                            $response = $event->getResponse();
//                            $response->getHeaders()->addHeaderLine('Location', $url);
//                            $response->setStatusCode(302);
//                            $response->sendHeaders();
//                            $event->stopPropagation();
//                        }
//                    }, -100);
//                    return $response;
                } else {
                    var_dump('用户已完成身份认证');
                    var_dump($authService->getIdentity());
                    /*
                     * 如果没有在配置文件<b>application.config.php</b>中设定<b>authorization_required</b>的值，或是设定为true
                     * 则表示需要对使用者进行权限控制
                     */
                    if (!key_exists('authorization_required', $configArray) || (true == $configArray['authorization_required'])) {
                        $eventManager->attach(MvcEvent::EVENT_ROUTE, array($this, "onRoute"), 100);
                    }
                }
            } catch (ServiceNotFoundException $e) {
                echo $e->getMessage();
                $event->setController('application');
//                die("The authentication service is not implemented yet.");
            }
        }
    }

    /**
     * @param MvcEvent $event
     */
    public function onRoute(MvcEvent $event)
    {
        //todo 判断用户是否拥有相应的方问权限
    }
}