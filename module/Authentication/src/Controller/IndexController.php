<?php

namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Exception\ServiceNotFoundException;

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
     * @var \Zend\Authentication\AuthenticationService
     */
    private $_authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->_authService = $authService;
    }

    /**
     * @return ViewModel
     * @throws ServiceNotFoundException
     */
    public function signinAction()
    {
//        $this->redirect()->toRoute('home');
//        $this->_authService->hasIdentity();
        $this->layout('layout/signin');
        return new ViewModel();
    }

    /**
     * 用户注销
     *
     * @return ViewModel
     */
    public function signoutAction()
    {
        return new ViewModel();
    }

}