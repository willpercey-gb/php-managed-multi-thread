<?php

namespace UWebPro\MultiThread;

use Opis\Closure\SerializableClosure;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Uid\Uuid;

class ThreadManager
{
    public const THREAD_PREFIX = 'thread-';

    public static function make(\Closure $closure): ManagedThread
    {
        $cache = new FilesystemAdapter();
        $cache->get(
            $key = Uuid::v4(),
            function (CacheItemInterface $item) use ($closure, $key) {
                $item->expiresAfter(1 << 16);

                SerializableClosure::setSecretKey($key);

                return serialize(
                    new SerializableClosure(
                        \Closure::bind(
                            $closure,
                            null,
                            ThreadManager::class
                        )
                    )
                );
            }
        );

        return new ManagedThread($key);
    }

    public static function dispatch(\Closure $closure): ManagedThread
    {
        $thread = static::make($closure);

        $thread->enableOutput();
        $thread->start();

        return $thread;
    }
}