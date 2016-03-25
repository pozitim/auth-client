<?php

namespace Pozitim\AuthClient\Turkcell;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class PsrLoggerListenerImpl implements Listener
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param ResponseInterface $response
     */
    public function onSuccess(ResponseInterface $response)
    {
        $this->logger->info('Auth Response', array('body' => $response->getBody()->__toString()));
    }

    /**
     * @param \Exception $exception
     */
    public function onException(\Exception $exception)
    {
        $this->logger->error($exception);
    }
}
