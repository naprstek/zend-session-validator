<?php

namespace Zend\Session\Validator;

use Zend\Http\PhpEnvironment\RemoteAddress;
use Zend\Session\Validator\ValidatorInterface as SessionValidator;

class OwnSession implements SessionValidator
{
    /**
     * Internal data.
     *
     * @var string
     */
    protected $data;

    /**
     * Whether to use proxy addresses or not.
     *
     * As default this setting is disabled - IP address is mostly needed to increase
     * security. HTTP_* are not reliable since can easily be spoofed. It can be enabled
     * just for more flexibility, but if user uses proxy to connect to trusted services
     * it's his/her own risk, only reliable field for IP address is $_SERVER['REMOTE_ADDR'].
     *
     * @var bool
     */
    protected static $useProxy = false;

    /**
     * List of trusted proxy IP addresses
     *
     * @var array
     */
    protected static $trustedProxies = [];

    /**
     * HTTP header to introspect for proxies
     *
     * @var string
     */
    protected static $proxyHeader = 'HTTP_X_FORWARDED_FOR';

    /**
     * Constructor
     * get the current user session ID and store it in the session as 'valid data'
     *
     * @param null|string $data
     */
    public function __construct($data = null)
    {
        if (empty($data)) {
            $data = $this->createData();
        }
        $this->data = $data;
    }

    /**
     * Creates data for further comparation
     */
    protected function createData()
    {
        return session_id() . '|' . $this->getIpAddress();
    }

    /**
     * isValid() - this method will determine if the current session ID matches the
     * ID we stored when we initialized this variable.
     *
     * @return bool
     */
    public function isValid()
    {
        $data = $this->createData();
        return ($data === $this->getData());
    }

    /**
     * Changes proxy handling setting.
     *
     * This must be static method, since validators are recovered automatically
     * at session read, so this is the only way to switch setting.
     *
     * @param bool  $useProxy Whether to check also proxied IP addresses.
     * @return void
     */
    public static function setUseProxy($useProxy = true)
    {
        static::$useProxy = $useProxy;
    }

    /**
     * Checks proxy handling setting.
     *
     * @return bool Current setting value.
     */
    public static function getUseProxy()
    {
        return static::$useProxy;
    }

    /**
     * Set list of trusted proxy addresses
     *
     * @param  array $trustedProxies
     * @return void
     */
    public static function setTrustedProxies(array $trustedProxies)
    {
        static::$trustedProxies = $trustedProxies;
    }

    /**
     * Set the header to introspect for proxy IPs
     *
     * @param  string $header
     * @return void
     */
    public static function setProxyHeader($header = 'X-Forwarded-For')
    {
        static::$proxyHeader = $header;
    }

    /**
     * Returns client IP address.
     *
     * @return string IP address.
     */
    protected function getIpAddress()
    {
        $remoteAddress = new RemoteAddress();
        $remoteAddress->setUseProxy(static::$useProxy);
        $remoteAddress->setTrustedProxies(static::$trustedProxies);
        $remoteAddress->setProxyHeader(static::$proxyHeader);
        return $remoteAddress->getIpAddress();
    }

    /**
     * Retrieve token for validating call
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return validator name
     *
     * @return string
     */
    public function getName()
    {
        return __CLASS__;
    }
}
