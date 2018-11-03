<?php

namespace Authentication\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\Storage\Session;
use Zend\Authentication\Adapter\Ldap as LdapAdapter;
use Authentication\Service\RuiAuthenticationService;
use Zend\Ldap\Ldap;

/**
 * 读取全局配置文件<b>Glob.php</b>中的数据库适配器服务组件
 * 并指写用于身份认证的数据库表及相关字段信息
 *
 * Class AuthenticationServiceFactory
 * @package Authentication\Factory
 */
class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
//            $writeAdapter = $container->get('Application\Db\WriteAdapter');
            $readOnlyAdapter = $container->get('Application\Db\ReadOnlyAdapter');
            $databaseAdapter = new CredentialTreatmentAdapter($readOnlyAdapter, 'vw_auth_user', 'user_name', 'user_psd', "MD5(CONCAT('staticSalt', ?, salt))");

            $ldapAdapter = new LdapAdapter();
            $config = $container->get('config');
            if (isset($config['ldap'])) {
                $ldapAdapter->setLdap(new Ldap($config['ldap']));
            }
            return new RuiAuthenticationService(new Session('rui_1124'), $databaseAdapter, $ldapAdapter);
        } catch (NotFoundExceptionInterface $e) {
            throw new ServiceNotFoundException('系统中尚未定义数据库适配器', 0, $e);
        } catch (ContainerExceptionInterface $e) {
            die($e->getMessage());
        }
    }
}