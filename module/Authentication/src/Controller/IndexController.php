<?php

namespace Authentication\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
class IndexController extends AbstractActionController {

    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * 用户登录
     *
     * @return ViewModel
     */
    public function signinAction() {
        return new ViewModel();
    }

    /**
     * 用户注销
     *
     * @return ViewModel
     */
    public function signoutAction() {
        return new ViewModel();
    }

}