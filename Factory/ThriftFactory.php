<?php

namespace Overblog\ThriftBundle\Factory;

use Overblog\ThriftBundle\ClassLoader\ThriftLoader;
use Overblog\ThriftBundle\ClassLoader\ApcThriftLoader;

/**
 * Thrift factory
 *
 * @author Xavier HAUSHERR
 */

class ThriftFactory
{
    protected $cacheDir;
    protected $services;
    protected $debug;

    /**
     * Inject dependencies
     * @param string $cacheDir
     * @param boolean $debug
     */
    public function __construct($cacheDir, Array $services, $debug = false)
    {
        $this->cacheDir = $cacheDir;
        $this->services = $services;
        $this->debug = $debug;
    }

    /**
     * Initialize loader
     * @param array $namespaces
     */
    public function initLoader(Array $namespaces)
    {
        if(false === $this->debug)
        {
            $loader = new ApcThriftLoader('thrift');
        }
        else
        {
            $loader = new ThriftLoader();
        }

        $loader->registerNamespaces($namespaces);
        $loader->register();
    }

    /**
     * Return an instance of a Thrift Model Class
     *
     * @note => We keep this method for compatibily reason and to be user
     *          that auloader is correctly start
     *
     * @param string $service
     * @param string $classe
     * @param mixed $param
     * @return Object
     */
    public function getInstance($classe, $param = null)
    {
        if(is_null($param))
        {
            return new $classe();
        }
        else
        {
            return new $classe($param);
        }
    }

    /**
     * Return a processor instance
     * @param string $service
     * @param mixed $handler
     * @return Object
     */
    public function getProcessorInstance($service, $handler)
    {
        $classe = sprintf('%s\%sProcessor', $this->services[$service]['namespace'], $this->services[$service]['definition']);

        return new $classe($handler);
    }

    /**
     * Return a client instance
     * @param string $service
     * @param Thrift\Protocol\TProtocol $transport
     * @return Object
     */
    public function getClientInstance($service, $protocol)
    {
        $classe = sprintf('%s\%sClient', $this->services[$service]['namespace'], $this->services[$service]['definition']);

        return new $classe($protocol);
    }
}