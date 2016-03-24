<?php

namespace Pozitim\AuthClient\Turkcell;

interface SessionClient
{
    /**
     * @return string
     */
    public function getSessionId();

    public function reset();

    /**
     * @return string
     */
    public function getSign();
}