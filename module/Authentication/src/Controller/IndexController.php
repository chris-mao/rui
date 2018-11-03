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

        try {
            $authResult = $this->_authService->authenticate($userName, $password);
            if (Result::SUCCESS != $authResult->getCode()) {
                $messages = $authResult->getMessages();
                return array('messages' => $messages);
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
        } catch (ExceptionInterface $e) {
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