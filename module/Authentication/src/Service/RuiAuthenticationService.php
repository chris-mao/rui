<?php

namespace Authentication\Service;

use Zend\Authentication\Adapter;
use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\Ldap as LdapAdapter;

/**
 * Class RuiAuthenticationService
 * @package Authentication\Service
 */
class RuiAuthenticationService
{
    /**
     * 认证类型 - 数据库认证
     */
    const DATABASE_AUTHENTICATION = 'manual';

    /**
     * 认证类型 - LDAP认证
     */
    const LDAP_AUTHENTICATION = 'ldap';

    private $_databaseAdapter;

    /**
     * @var \Zend\Authentication\Adapter\Ldap
     */
    private $_ldapAdapter;

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $_innerAuthenticationService;

    /**
     * TODO: 构造函数中需增加传入可写的数据库适配器，用于LDAP认证成功后向数据库写入用户信息
     */

    /**
     * RuiAuthenticationService constructor.
     * @param Storage\StorageInterface $storage
     * @param CredentialTreatmentAdapter $databaseAdapter
     * @param LdapAdapter $ldapAdapter
     */
    public function __construct(Storage\StorageInterface $storage, CredentialTreatmentAdapter $databaseAdapter, LdapAdapter $ldapAdapter = null)
    {
        if (null !== $databaseAdapter) {
            $this->_databaseAdapter = $databaseAdapter;
        }
        if (null !== $ldapAdapter) {
            $this->_ldapAdapter = $ldapAdapter;
        }
        $this->_innerAuthenticationService = new AuthenticationService($storage);
    }

    /**
     * 先使用默认的数据库认证方式对用户身份进行认证，
     * 如果认证失败，尝试使用LDAP方式认证，并在通过认证后将用户数据写入到数据库中
     *
     * @param $userName
     * @param $credential
     * @return Result
     * @throws Exception\RuntimeException
     * @throws \Exception
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function authenticate($userName, $credential)
    {
        if (($this->_databaseAdapter == null) && ($this->_ldapAdapter == null)) {
            throw new Exception\RuntimeException('未指定用于身份认证的数据库适配器或是LDAP适配器，无法完成身份认证。');
        }

        $authResult = $this->databaseAuthenticate($userName, $credential);
        if (Result::FAILURE_IDENTITY_NOT_FOUND == $authResult->getCode()) {
            //数据库认证失败，尝试LDAP认证
            if ($this->_ldapAdapter != null) {
                $authResult = $this->ldapAuthenticate($userName, $credential);
                if (Result::SUCCESS == $authResult->getCode()) {
                    //LDAP认证成功，将用户信息写入到数据库中
                    //TODO: LDAP认证成功，将用户信息写入到数据库中
                }
            }
        }
        return $authResult;
    }

    /**
     * @param $userName
     * @param $credential
     * @return \Zend\Authentication\Result
     * @throws Exception\RuntimeException
     */
    protected function ldapAuthenticate($userName, $credential)
    {
        $this->_ldapAdapter->setIdentity($userName);
        $this->_ldapAdapter->setCredential($credential);
        return $this->_innerAuthenticationService->authenticate($this->_ldapAdapter);
    }

    /**
     * @param $userName
     * @param $credential
     * @return \Zend\Authentication\Result
     */
    protected function databaseAuthenticate($userName, $credential)
    {
        $this->_databaseAdapter->setIdentity($userName);
        $this->_databaseAdapter->setCredential($credential);
        return $this->_innerAuthenticationService->authenticate($this->_databaseAdapter);
    }

    public function readLDAPAccounts()
    {
        $ldap = $this->_ldapAdapter->getLdap();
        $ldap->bind();
//        $hm = $ldap->getEntry('OU=MITS,OU=CI,OU=staff,OU=xjtlu,DC=xjtlu,DC=edu,DC=cn');
        $filter = "(objectclass=user)";
        $person = $this->_ldapAdapter->getIdentity();
        $filter = "(|(sn=$person*)(cn=$person*))";
        $result = $ldap->search($filter);
        echo '<ul>';
        foreach ($result as $item) {
            echo '<li>' . $item["dn"] . ': ' . $item['cn'][0] . '</li>';
        }
        echo '</ul>';
//        var_dump($hm);

    }

    /**
     * 以下方法是对AuthenticationService暴露出来的方法的封装
     */

    /**
     * Returns the authentication adapter
     *
     * The adapter does not have a default if the storage adapter has not been set.
     *
     * @return Adapter\AdapterInterface|null
     */
    public function getAdapter()
    {
        return $this->_innerAuthenticationService->getAdapter();
    }

    /**
     * Sets the authentication adapter
     *
     * @param  Adapter\AdapterInterface $adapter
     * @return self Provides a fluent interface
     */
    public function setAdapter(Adapter\AdapterInterface $adapter)
    {
        $this->_innerAuthenticationService->setAdapter($adapter);
    }

    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Storage\StorageInterface
     */
    public function getStorage()
    {
        return $this->_innerAuthenticationService->getStorage();
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Storage\StorageInterface $storage
     * @return self Provides a fluent interface
     */
    public function setStorage(Storage\StorageInterface $storage)
    {
        $this->_innerAuthenticationService->setStorage($storage);
    }


    /**
     * Returns true if and only if an identity is available from storage
     *
     * @return bool
     */
    public function hasIdentity()
    {
        return $this->_innerAuthenticationService->hasIdentity();
    }

    /**
     * Returns the identity from storage or null if no identity is available
     *
     * @return mixed|null
     */
    public function getIdentity()
    {
        return $this->_innerAuthenticationService->getIdentity();
    }

    /**
     * Clears the identity from persistent storage
     *
     * @return void
     */
    public function clearIdentity()
    {
        $this->_innerAuthenticationService->clearIdentity();
    }
}