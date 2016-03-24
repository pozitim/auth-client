<?php

namespace Pozitim\AuthClient\Turkcell;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
                        $this->getLogger()->info('Auth Response', array('body' => $response->getBody()->__toString()));
                        if (in_array($response->getStatusCode(), array(200, 201))) {
                            $jsonObject = json_decode($response->getBody());
                            $this->sessionId = $jsonObject->sessionId;
                        }
                    },
                    function (\Exception $exception) use ($object) {
                        $object->getLogger()->error($exception);
                    }
                )->wait();
        } catch (\Exception $exception) {
            $this->getLogger()->error($exception);
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
     * @return LoggerInterface
     */
    protected function getLogger()
    {
        return $this->logger;
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