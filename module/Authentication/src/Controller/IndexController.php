<?php

namespace Authentication\Controller;

use Authentication\Service\RuiAuthenticationService;
use Zend\Authentication\Adapter\Exception\ExceptionInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Authentication\Result;

/**
 * 用户身份认证
 *
 * @package index
 *
 * @author Chris Mao(Zibing) <chris.mao.zb@163.com>
 *
 * @version 1.0
 *
 */
class IndexController extends AbstractActionController
{

    /**
     * @var \Authentication\Service\RuiAuthenticationService
     */
    private $_authService;

    public function __construct(RuiAuthenticationService $authService)
    {
        $this->_authService = $authService;
    }

    /**
     * @return ViewModel
     * @throws \Exception
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function signinAction()
    {
        $this->layout('layout/signin');

        if (!$this->getRequest()->isPost()) { // 如果不是POST操作，不执行任何动作
            return new ViewModel();
        }

        $userName = ltrim(rtrim($this->params()->fromPost('login_name')));
        $password = ltrim(rtrim($this->params()->fromPost('password')));
        $authType = strtolower('manual');

        try {
            switch ($authType) {
                case RuiAuthenticationService::DATABASE_AUTHENTICATION:
                    $authResult = $this->_authService->databaseAuthenticate($userName, $password);
                    break;
                case RuiAuthenticationService::LDAP_AUTHENTICATION:
                    $authResult = $this->_authService->ldapAuthenticate($userName, $password);
                    break;
                default:
                    $authResult = $this->_authService->databaseAuthenticate($userName, $password);
            }
        } catch (ExceptionInterface $e) {
        }

        if (Result::SUCCESS == $authResult->getCode()) { //身份认证成功
            $columnsToReturn = array('user_id', 'auth_type', 'user_name', 'nick_name', 'navigation');
            $returnedObj = $this->_authService->getResultRowObject($columnsToReturn);
            //todo 将认证后的用户身份转为实体对象后再存储到session中
//            $user = $this->permissionUserFactory($returnedObj);
            try {
                $this->_authService->getStorage()->write($returnedObj);
            } catch (ExceptionInterface $e) {
                throw new \Exception('Not Assign the default storage!!');
            }

            //提取来源地址，登录成功后转到来源页面
            $referer = $this->params()->fromHeader('Referer');
            $queryStr = $referer->uri()->getQuery();
            $url = substr($queryStr, 2);
            if (empty($url)) {
                return $this->redirect()->toRoute('home');
            } else {
                return $this->redirect()->toUrl($url);
            }
        } elseif (Result::FAILURE_IDENTITY_NOT_FOUND == $authResult->getCode()) { //使用数据库进行身份认证失败，尝试使用LDAP认证
            //
        } else { //身份认证失败，返回错误信息
            $messages = $authResult->getMessages();
//            var_dump($authResult);
        }

        return new ViewModel();
    }

    /**
     * 用户注销
     *
     * @return ViewModel
     */
    public function signoutAction()
    {
        if ($this->_authService->hasIdentity()) {
            $this->_authService->clearIdentity();
        }
        return new ViewModel();
    }

}