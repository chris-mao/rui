<?php

namespace Authentication\Service;

use Zend\Authentication\Adapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\Authentication\Adapter\Ldap as LdapAdapter;

/**
 * Class RuiAuthenticationService
 * @package Authentication\Service
 */
class RuiAuthenticationService extends AuthenticationService
{
    /**
     * 认证类型
     */
    const DATABASE_AUTHENTICATION = 'manual';

    /**
     * 认证类型
     */
    const LDAP_AUTHENTICATION = 'ldap';

    /**
     * @var \Zend\Authentication\Adapter\Ldap
     */
    private $_ldapAdapter;

    /**
     * RuiAuthenticationService constructor.
     * @param Storage\StorageInterface $storage
     * @param CredentialTreatmentAdapter $databaseAdapter
     * @param LdapAdapter $ldapAdapter
     */
    public function __construct(Storage\StorageInterface $storage, CredentialTreatmentAdapter $databaseAdapter, LdapAdapter $ldapAdapter = null)
    {
        parent::__construct($storage, $databaseAdapter);
        $this->_ldapAdapter = $ldapAdapter;
    }

    /**
     * @param $userName
     * @param $credential
     * @return \Zend\Authentication\Result
     * @throws Adapter\Exception\ExceptionInterface
     */
    public function ldapAuthenticate($userName, $credential)
    {
        //todo: 实现AD身份认证

        $options = array(
            'host' => 'ldap://dc01.xjtlu.edu.cn:389',
            'username' => 'CN=zibing.mao,DC=xjtlu,DC=edu,DC=cn',
            'password' => 'Copeland@',
            'accountDomainName' => 'xjtlu.edu.cn',
            'baseDn' => 'OU=staff,OU=xjtlu,DC=xjtlu,DC=edu,DC=cn',
        );

        return $this->_ldapAdapter->authenticate();
    }

    /**
     * @param $userName
     * @param $credential
     * @return \Zend\Authentication\Result
     */
    public function databaseAuthenticate($userName, $credential)
    {
        $this->getAdapter()->setIdentity($userName);
        $this->getAdapter()->setCredential($credential);
        return $this->authenticate();
    }

    public function getResultRowObject($columnsToReturn)
    {
        var_dump($this->getAdapter()->getResultRowObject());
        return $this->getAdapter()->getResultRowObject($columnsToReturn);
    }
}