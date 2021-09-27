<?php

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CanMakeThreadTest extends \PHPUnit\Framework\TestCase
{
    public function testThreadCanBeMade(): void
    {
        $cache = new FilesystemAdapter();
        $cache->clear();

        $thread = \UWebPro\MultiThread\ThreadManager::dispatch(static function () {
            file_put_contents('test-file', 'working!');
        });


        while ($thread->isRunning()) {
            sleep(1);
        }

        $this->assertFileExists(dirname(__DIR__) . '/test-file');

        unlink('test-file');
    }
}