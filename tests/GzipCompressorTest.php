<?php

namespace Telesena\Backend\GlobalBundle\Tests\Compressor;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Telesena\Backend\GlobalBundle\Compressor\GzipCompressor;

/**
 * Class GzipCompressorTest
 *
 * @package Telesena\Backend\GlobalBundle\Tests\Compressor
 */
class GzipCompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    protected $directory;

    public function setUp()
    {
        $this->directory = vfsStream::setup('root');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Arquivo não encontrado ou sem permissão: vfs://root/non-existent-file.txt
     * @expectedExceptionCode -1
     */
    public function testFileNotExists()
    {
        $from = $this->directory->url() . '/non-existent-file.txt';
        new GzipCompressor($from);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Arquivo não encontrado ou sem permissão: vfs://root/non-readable-file.txt
     * @expectedExceptionCode -1
     */
    public function testFileNotReadable()
    {
        $from = $this->directory->url() . '/non-readable-file.txt';
        chmod($from, 0000);
        new GzipCompressor($from);
    }

    public function testUncompressedBuffer()
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $this->assertEquals($content, $gzip->getContents());
    }

    /**
     * @return array
     */
    public function providerInvalidLevels()
    {
        return [
            [0],
            [10],
            [-1],
            [false],
            [null],
            [''],
            [' '],
            ['a'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Level de compressão não suportado:
     * @expectedExceptionCode -2
     *
     * @dataProvider providerInvalidLevels
     *
     * @param mixed $invalidLevel
     */
    public function testCompressInvalidLevel($invalidLevel)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($invalidLevel);
    }

    /**
     * @return array
     */
    public function providerValidLevels()
    {
        return [
            [1],
            [2],
            [3],
            [4],
            [5],
            [6],
            [7],
            [8],
            [9],
        ];
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testCompressSuccess($level)
    {
        $unCompressed = 'Random file content ' . rand(1, 100);
        $from         = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $unCompressed);

        $gzip = new GzipCompressor($from);
        $this->assertInstanceOf(GzipCompressor::class, $gzip->compress($level));

        $compressed = $gzip->getContents();
        $this->assertEquals($unCompressed, gzdecode($compressed));
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testDecompressSuccess($level)
    {
        $content    = 'Random file content ' . rand(1, 100);
        $compressed = gzencode($content, $level);
        $from       = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $compressed);

        $gzip = new GzipCompressor($from);
        $this->assertInstanceOf(GzipCompressor::class, $gzip->decompress());

        $decompressed = $gzip->getContents();
        $this->assertEquals($content, $decompressed);
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testIsCompressed($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $this->assertFalse($gzip->isCompressed());

        $gzip->compress($level);
        $this->assertTrue($gzip->isCompressed());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage O conteúdo já está comprimido
     * @expectedExceptionCode -1
     *
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testAlreadyCompressed($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level);
        $gzip->compress($level);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage O conteúdo já está descomprimido
     * @expectedExceptionCode -1
     *
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testAlreadyDecompressed1($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->decompress($level);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage O conteúdo já está descomprimido
     * @expectedExceptionCode -1
     *
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testAlreadyDecompressed2($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level);
        $gzip->decompress($level);
        $gzip->decompress($level);
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testCompressAndSaveAsNoTruncate($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level)
            ->saveAs($to);

        $this->assertFileExists($to);
        $this->assertEquals(
            file_get_contents($from),
            gzdecode(file_get_contents($to))
        );
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testCompressAndSaveAsTruncate($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level)
            ->saveAs($to, true);

        $this->assertFileExists($to);
        $this->assertFileNotExists($from);
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testCompressAndSaveAsNoTruncateSubdir($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/subdir/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level)
            ->saveAs($to);

        $this->assertFileExists($from);
        $this->assertFileExists(dirname($to));
        $this->assertFileExists($to);

        $this->assertEquals(
            file_get_contents($from),
            gzdecode(file_get_contents($to))
        );
    }

    /**
     * @dataProvider providerValidLevels
     *
     * @param int $level
     */
    public function testCompressAndSaveAsTruncateSubdir($level)
    {
        $content = 'Random file content ' . rand(1, 100);
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/subdir/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->compress($level)
            ->saveAs($to, true);

        $this->assertFileNotExists($from);
        $this->assertFileExists(dirname($to));
        $this->assertFileExists($to);
    }

    public function testDecompressAndSaveAsNoTruncate()
    {
        $content = gzencode('Random file content ' . rand(1, 100));
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->decompress()
            ->saveAs($to);

        $this->assertFileExists($to);
        $this->assertEquals(
            gzdecode(file_get_contents($from)),
            file_get_contents($to)
        );
    }

    public function testDecompressAndSaveAsTruncate()
    {
        $content = gzencode('Random file content ' . rand(1, 100));
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->decompress()
            ->saveAs($to, true);

        $this->assertFileExists($to);
        $this->assertFileNotExists($from);
        $this->assertEquals(
            gzdecode($content),
            file_get_contents($to)
        );
    }

    public function testDecompressAndSaveAsNoTruncateSubdir()
    {
        $content = gzencode('Random file content ' . rand(1, 100));
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/subdir/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->decompress()
            ->saveAs($to);

        $this->assertFileExists($to);
        $this->assertFileExists(dirname($to));
        $this->assertEquals(
            gzdecode(file_get_contents($from)),
            file_get_contents($to)
        );
    }

    public function testDecompressAndSaveAsTruncateSubdir()
    {
        $content = gzencode('Random file content ' . rand(1, 100));
        $from    = $this->directory->url() . '/testfile.txt';
        $to      = $this->directory->url() . '/subdir/testfile.txt.gz';
        file_put_contents($from, $content);

        $gzip = new GzipCompressor($from);
        $gzip->decompress()
            ->saveAs($to, true);

        $this->assertFileExists($to);
        $this->assertFileExists(dirname($to));
        $this->assertFileNotExists($from);
        $this->assertEquals(
            gzdecode($content),
            file_get_contents($to)
        );
    }
}
