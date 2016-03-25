<?php

namespace Pozitim\AuthClient\Turkcell;

use Psr\Http\Message\ResponseInterface;

interface Listener
{
    /**
     * @param $response
     */
    public function onSuccess(ResponseInterface $response);

    /**
     * @param \Exception $exception
     */
    public function onException(\Exception $exception);
}
