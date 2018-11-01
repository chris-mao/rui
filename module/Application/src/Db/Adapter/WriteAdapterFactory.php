<?php

namespace Application\Db\Adapter;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterServiceFactory;

class WriteAdapterFactory extends AdapterServiceFactory
{
    /**
     * Create db adapter service
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options
     * @return Adapter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        return new Adapter($config['db']['adapters']['Application\Db\WriteAdapter']);
    }
}