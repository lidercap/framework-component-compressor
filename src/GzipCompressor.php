<?php

namespace Telesena\Backend\GlobalBundle\Compressor;

/**
 * Class GzipCompressor
 *
 * @package Telesena\Warmup\Compressor
 */
class GzipCompressor extends AbstractCompressor
{
    /**
     * Comprime o conteúdo do arquivo de trabalho e salva no buffer.
     *
     * @param int $level
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function compress($level = 9)
    {
        if ($this->isCompressed()) {
            $message = 'O conteúdo já está comprimido';
            throw new \RuntimeException($message, -1);
        }

        $level = (int)$level;
        if ($level < 1 or $level > 9) {
            $message = sprintf('Level de compressão não suportado: %s', $level);
            throw new \InvalidArgumentException($message, -2);
        }

        $this->buffer = gzencode($this->buffer, $level);

        return $this;
    }

    /**
     * Descomprime o conteúdo do arquivo de trabalho e salva no buffer.
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function decompress()
    {
        if (!$this->isCompressed()) {
            $message = 'O conteúdo já está descomprimido';
            throw new \RuntimeException($message, -1);
        }

        $this->buffer = gzdecode($this->buffer);

        return $this;
    }
}
