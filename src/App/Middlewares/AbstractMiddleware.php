<?php

namespace Src\App\Middlewares;

abstract class AbstractMiddleware
{
    private ?AbstractMiddleware $nextHandler = null;

    public function setNext($handler)
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * @param $request
     * @return null
     */
    public function handle($request = null)
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($request);
        }

        return null;
    }
}
