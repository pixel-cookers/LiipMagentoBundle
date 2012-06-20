<?php

namespace Liip\MagentoBundle\SessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class MagentoSessionStorage extends NativeSessionStorage
{
    static protected $sessionIdRegenerated = false;
    static protected $sessionStarted = false;

    /**
     * @var Mage_Core_Model_Session_Abstract
     */
    private $session;

    public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
    {
        if (isset($option['session_namespace'])) {
            $sessionNamespace = $option('session_namespace');
        } else {
            $sessionNamespace = 'frontend';
        }

        if (isset($options['cookie_path'])) {
            \Mage::app()->getStore()->setConfig(\Mage_Core_Model_Cookie::XML_PATH_COOKIE_PATH, $options['cookie_path']);
        }

        parent::__construct($options, $handler, $metaBag);


        $this->session = \Mage::getSingleton('core/session',
                array('name' => $sessionNamespace));


    }

    /**
     * {@inheritDoc}
     */
    public function start()
    {
        if (self::$sessionStarted) {
            return;
        }

        // start Magento session
        $this->session->start();

        self::$sessionStarted = true;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if (!self::$sessionStarted) {
            throw new \RuntimeException(
                    'The session must be started before reading its ID');
        }

        $this->session->getSessionId();
    }

    /**
     * {@inheritDoc}
     */
    public function read($key, $default = null)
    {
        return $this->session->getDataSetDefault($key, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        $retval = $this->session->getDataSetDefault($key, null);

        $this->session->unsetData($key);

        return $retval;
    }

    /**
     * {@inheritDoc}
     */
    public function write($key, $data)
    {
        $this->session->setData($key, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function regenerate($destroy = false, $lifetime = null)
    {
        if (self::$sessionIdRegenerated) {
            return true;
        }

        $this->session->regenerateSessionId();

        self::$sessionIdRegenerated = true;

        return true;
    }

    /**
     *
     */
    public function getName()
    {
        return $this->session->getSessionName();

    }

    /**
     * @param unknown_type $name
     */
    public function setName($name)
    {
        $this->session->setSesionName($name);
    }

    /**
     * @param unknown_type $id
     */
    public function setId($id)
    {
        $this->session->setSessionId($id);
    }


}
