<?php namespace Johnny\Logger;
/**
 * Class LoggerFactory - Description
 *
 * @package Johnny\Logger
 * @author Johnny <Johnny.joyful@gmail.com>
 */
use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Johnny\Tools\Directory;
class LoggerFactory
{
    /**
     * loggers
     * @var array
     */
    protected $logger=[];
    /**
     * config array
     * @var array
     */
    private $_config=[];

    /**
     * the __construct method set config
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->_config   = $config;
    }

    /**
     * get config
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * get default logger config name
     *
     * @return mixed
     */
    public function getDefaultLoggerConfig()
    {
        return $this->getConfig()['default'];
    }

    /**
     * create new logger by config name
     *
     * @param $name
     * @return Logger
     */
    protected function createNewLoggerByConfig($name)
    {
        $config = $this->getConfig();
        $file   = $config['logs'][$name];
        $path   = $config['root'].DIRECTORY_SEPARATOR.$file;

        Directory::mkdir(dirname($path));

        $logger = new MonologLogger($name);
        $logger->pushHandler(new StreamHandler($path));

        return $logger;
    }

    /**
     * has logger
     *
     * @param $name
     * @return bool
     */
    public function hasLogger($name)
    {
        if (isset($this->logger[$name]))
        {
            return true;
        }

        if (isset($this->_config['logs'][$name]))
        {
            return true;
        }

        return false;
    }

    /**
     * set logger
     *
     * @param $name
     */
    public function setLogger($name, $logger)
    {
        $this->logger[$name]    = $logger;
    }

    /**
     * get logger
     *
     * @param null $name
     * @return mixed
     */
    public function getLogger($name=null)
    {
        $name   = ($name) ? $name : $this->getDefaultLoggerConfig();

        if (!isset($this->logger[$name]))
        {
            $logger = $this->createNewLoggerByConfig($name);

            $this->setLogger($name, $logger);
        }

        return $this->logger[$name];
    }

    /**
     * rewrite __call method
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if ($this->hasLogger($name))
        {
            $instance = $this->getLogger($name);
            if(!empty($args))
            {
                return call_user_func_array(array($instance, 'addRecord'), $args);
            }
            return $instance;
        }

        $instance   = $this->getLogger();

        return call_user_func_array(array($instance, $name), $args);
    }
}