<?php

namespace Lidercap\Component\Compressor;

/**
 * Class CompressorAware
 *
 * @package Lidercap\Component\Compressor
 */
trait CompressorAware
{
    /**
     * @var CompressorInterface
     */
    protected $compressor;

    /**
     * @codeCoverageIgnore
     *
     * @param CompressorInterface $compressor
     */
    public function setCompressor(CompressorInterface $compressor)
    {
        $this->compressor = $compressor;
    }
}
