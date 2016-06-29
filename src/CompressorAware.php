<?php

namespace Telesena\Backend\GlobalBundle\Compressor;

/**
 * Class CompressorAware
 *
 * @package Telesena\Backend\GlobalBundle\Compressor
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
