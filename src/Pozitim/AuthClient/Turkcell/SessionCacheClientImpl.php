<?php

namespace Pozitim\AuthClient\Turkcell;

class SessionCacheClientImpl implements SessionClient
{
    protected $sessionId;

    /**
     * @var SessionClient
     */
    protected $sessionClient;

    /**
     * @var \Memcached
     */
    protected $memcached;

    /**
     * @param SessionClient $sessionClient
     * @param \Memcached $memcached
     */
    public function __construct(SessionClient $sessionClient, \Memcached $memcached)
    {
        $this->sessionClient = $sessionClient;
        $this->memcached = $memcached;
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        if ($this->sessionId == null) {
            $this->sessionId = trim($this->getMemcached()->get($this->getSign()));
            if (strlen($this->sessionId) == 0) {
                $this->sessionId = $this->getSessionClient()->getSessionId();
                $this->getMemcached()->set($this->getSign(), $this->sessionId);
            };
        }
        return $this->sessionId;
    }

    public function reset()
    {
        $this->sessionId = null;
        $this->getMemcached()->delete($this->getSign(), 0);
        $this->getSessionClient()->reset();
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return $this->getSessionClient()->getSign();
    }

    /**
     * @return SessionClient
     */
    protected function getSessionClient()
    {
        return $this->sessionClient;
    }

    /**
     * @return \Memcached
     */
    protected function getMemcached()
    {
        return $this->memcached;
    }
}
