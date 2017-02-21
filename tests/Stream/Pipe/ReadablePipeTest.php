<?php
declare (strict_types = 1);

namespace Async\Stream\Pipe;

use PHPUnit\Framework\TestCase;

class ReadablePipeTest extends TestCase
{
    const UNIX_SOCKET_PATHNAME = __DIR__ . '/../../../run/readable-pipe-test.unix';
    /**
     * @var resource
     */
    private $readNativePipe;

    /**
     * @var resource
     */
    private $writeNativePipe;

    /**
     * @var ReadablePipe
     */
    private $readablePipe;

    public function setUp()
    {
        posix_mkfifo(self::UNIX_SOCKET_PATHNAME, 0644);

        $this->assertTrue(file_exists(self::UNIX_SOCKET_PATHNAME));

        $this->readNativePipe = fopen(self::UNIX_SOCKET_PATHNAME, 'r+');
        $this->writeNativePipe = fopen(self::UNIX_SOCKET_PATHNAME, 'w');

        $this->readablePipe = new ReadablePipe(\Async\loop()->nativeHandler(), $this->readNativePipe);

    }

    public function tearDown()
    {
        fclose($this->readNativePipe);
        fclose($this->writeNativePipe);

        @unlink(self::UNIX_SOCKET_PATHNAME);
    }

    /**
     * @test
     */
    public function _()
    {
        $this->write($expectedData = 'hello worlds');
        $actualData = null;

        $actualData = fread($this->readNativePipe, strlen($expectedData));

//        $this->readablePipe->read()->then(static function (string $data) use (&$actualData) {
//            $actualData = $data;
//        })->then(function () {
//            $this->readablePipe->close();
//        });
//
//        \Async\loop()->run();

        $this->assertEquals($expectedData, $actualData);
    }

    private function write(string $data)
    {
        fwrite($this->writeNativePipe, $data);
    }
}
