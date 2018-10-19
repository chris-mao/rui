<?php

namespace Authentication\Adapter;

use Zend\Ldap\Exception\LdapException;
use Zend\Authentication\Result as AuthenticationResult;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;

/**
 * 实现用户身份认证，支持数据库认证和LDAP认证
 *
 * Class RuiAuthenticationAdapter
 * @package Authentication\Adapter
 */
class RuiAuthenticationAdapter extends CredentialTreatmentAdapter
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
     * 静态盐值
     */
    const STATIC_SALT = 'staticSalt';

    /**
     * @var \Zend\Ldap\
     */
    private $_ldap;

    /**
     * This method is called to attempt an authentication. Previous to this
     * call, this adapter would have already been configured with all
     * necessary information to successfully connect to a database table and
     * attempt to find a record matching the provided identity.
     *
     * @return AuthenticationResult
     * @throws \Exception
     */
    public function authenticate()
    {
        $authResult = parent::authenticate();
        switch ($authResult->getCode()) {
            case AuthenticationResult::SUCCESS == $authResult->getCode():
                $columnsToReturn = array('user_id', 'auth_type', 'user_name', 'salt');
                $returnedObj = $this->getResultRowObject($columnsToReturn);
                if ($returnedObj->auth_type == self::LDAP_AUTHENTICATION) { //更新用户密码，保持数据库中的密码与LDAP密码的一致性
                    $this->updateAccountFromLDAP($returnedObj->user_id);
                }
                break;
            case AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND:
                /*
                 * 数据库中找不到用户身份时尝试以下操作：
                 * 1. 使用LDAP认证
                 * 2. 认证通过后，在数据加中创建用户信息
                 */
                try {
                    if ($this->LdapAuthenticate() && $this->createAccountFromLdap()) {
                        $authResult = parent::authenticate();
                    }
                } catch (LdapException $e) {
                    if ($e->getCode() == LdapException::LDAP_X_EXTENSION_NOT_LOADED) {
                        throw new \Exception('the LDAP Extension not loaded yet.');
                    }
                    return new AuthenticationResult(AuthenticationResult::FAILURE_CREDENTIAL_INVALID, $this->getIdentity(), array(sprintf('The AD account [%s] does not exists or the credential is not correct.', $this->getIdentity())));
                }
                break;
            default:
                return $authResult;

        }

        return $authResult;
    }

    /**
     * 生成动态加密盐值
     *
     * @return string
     */
    private function generateDynamicSalt()
    {
        $result = '';
        for ($i = 0; $i < 50; $i++) {
            $result .= chr(rand(48, 125));
        }
        return $result;
    }

    /**
     * 将ldap用户密码更新到数据库中，保持数据库中的密码与LDAP密码的一致性
     *
     * @param $user_id
     */
    protected function updateAccountFromLDAP($user_id)
    {
    }

    /**
     * 使用LDAP进行用户身份认证，认证成功返回<code>true</code>，否则返回<code>false</code>
     */
    protected function LdapAuthenticate()
    {
        $options = array(
            'host' => 'ldap://dc01.xjtlu.edu.cn:389',
            'username' => 'CN=zibing.mao,DC=xjtlu,DC=edu,DC=cn',
            'password' => 'Copeland@',
            'accountDomainName' => 'xjtlu.edu.cn',
            'baseDn' => 'OU=staff,OU=xjtlu,DC=xjtlu,DC=edu,DC=cn',
        );

        if (!isset($this->_ldap)) {
            $this->_ldap = new Ldap($options);
        }
        if ($this->_ldap->bind($this->getIdentity(), $this->getCredential())) {
            return true;
        }
        return false;
    }

    /**
     * 根据用户AD信息创建新帐户，并返回用户id，创建失败返回<code>null</code>
     *
     * @return mixed
     */
    protected function createAccountFromLdap()
    {
        $sql = sprintf("UPDATE auth_user SET user_psd = MD5(CONCAT('%s', '%s', salt)) WHERE user_id = %d", self::STATIC_SALT, $this->getCredential(), $userId);
        $result = $this->zendDb->query($sql, Adapter::QUERY_MODE_EXECUTE);
        return $result->getAffectedRows();
    }


}