<?php

namespace UWebPro\MultiThread;

use Symfony\Component\Process\Process;

class ManagedThread extends Process
{
    public function __construct(private string $name)
    {
        parent::__construct([PHP_BINARY, dirname(__DIR__) . '/bin/thread ' . $this->name]);
    }

    public function getThreadName(): string
    {
        return $this->name;
    }

    public function dispatch()
    {
        $this->start();
        return $this;
    }

}