<?php

namespace Pozitim\AuthClient\Turkcell;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class SessionClientImpl implements SessionClient
{
    protected $apiEndpoint;
    protected $sessionId;
    protected $turkcellEndpoint;
    protected $spId;
    protected $serviceVariantId;
    protected $password;

    /**
     * @var bool
     */
    protected $force = false;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var Listener
     */
    protected $listener;

    /**
     * @return string
     */
    public function getSessionId()
    {
        if ($this->sessionId == null) {
            $this->updateSessionId();
            $this->force = false;
        }
        return $this->sessionId;
    }

    public function reset()
    {
        $this->sessionId = null;
        $this->force = true;
    }

    protected function updateSessionId()
    {
        $object = $this;
        try {
            $this->getGuzzleClient()
                ->postAsync(
                    $this->apiEndpoint . '/v1/turkcell-session',
                    array(
                        'form_params' => array(
                            'endpoint' => $this->turkcellEndpoint,
                            'username' => $this->spId,
                            'password' => $this->password,
                            'variant_id' => $this->serviceVariantId,
                            'force' => intval($this->force)
                        )
                    )
                )
                ->then(
                    function (ResponseInterface $response) use ($object) {
                        if ($this->getListener() instanceof Listener) {
                            $this->getListener()->onSuccess($response);
                        }
                        if (in_array($response->getStatusCode(), array(200, 201))) {
                            $jsonObject = json_decode($response->getBody());
                            $this->sessionId = $jsonObject->sessionId;
                        }
                    },
                    function (\Exception $exception) use ($object) {
                        if ($this->getListener() instanceof Listener) {
                            $this->getListener()->onException($exception);
                        }
                    }
                )->wait();
        } catch (\Exception $exception) {
            if ($this->getListener() instanceof Listener) {
                $this->getListener()->onException($exception);
            }
        }
    }

    /**
     * @param mixed $apiEndpoint
     */
    public function setApiEndpoint($apiEndpoint)
    {
        $this->apiEndpoint = $apiEndpoint;
    }

    /**
     * @param mixed $turkcellEndpoint
     */
    public function setTurkcellEndpoint($turkcellEndpoint)
    {
        $this->turkcellEndpoint = $turkcellEndpoint;
    }

    /**
     * @param mixed $serviceVariantId
     */
    public function setServiceVariantId($serviceVariantId)
    {
        $this->serviceVariantId = $serviceVariantId;
    }

    /**
     * @param mixed $spId
     */
    public function setSpId($spId)
    {
        $this->spId = $spId;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return Listener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @param Listener $listener
     */
    public function setListener(Listener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * @return Client
     */
    protected function getGuzzleClient()
    {
        if ($this->guzzleClient == null) {
            $this->guzzleClient = new Client();
        }
        return $this->guzzleClient;
    }

    /**
     * @return string
     */
    public function getSign()
    {
        return md5($this->turkcellEndpoint . '_' . $this->spId . '_' . $this->password . '_' . $this->serviceVariantId);
    }
}