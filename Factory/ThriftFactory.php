<?php

namespace Overblog\ThriftBundle\Factory;

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
     * Load Thrift cache Files
     */
    protected function loadFiles($service)
    {
        $path = $this->cacheDir .
                DIRECTORY_SEPARATOR .
                str_replace('\\', DIRECTORY_SEPARATOR, $this->services[$service]['namespace']) .
                DIRECTORY_SEPARATOR;

        require_once($path . $this->services[$service]['definition'] . '.php');
        require_once($path . 'Types.php');
    }

    /**
     * Return an instance of a Thrift Model Class
     * @param string $service
     * @param string $classe
     * @param mixed $param
     * @return Object
     */
    public function getInstance($service, $classe, $param = null)
    {
        $this->loadFiles($service);

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
        $this->loadFiles($service);

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
        $this->loadFiles($service);

        $classe = sprintf('%s\%sClient', $this->services[$service]['namespace'], $this->services[$service]['definition']);

        return new $classe($protocol);
    }
}