<?php

namespace Lidercap\Component\Compressor;

/**
 * Class AbstractCompressor
 *
 * @package Lidercap\Component\Compressor
 */
abstract class AbstractCompressor implements CompressorInterface
{
    /**
     * @var string
     */
    protected $from;

    /**
     * @var mixed
     */
    protected $buffer;

    /**
     * Informa o caminho para o arquivo de trabalho.
     *
     * @param string $from Caminho para o arquivo.
     *
     * @throws \RuntimeException
     */
    public function __construct($from)
    {
        if (!file_exists($from)) {
            $message = sprintf('Arquivo não encontrado ou sem permissão: %s', $from);
            throw new \RuntimeException($message, -1);
        }

        $this->from   = (string)$from;
        $this->buffer = file_get_contents($this->from);
    }

    /**
     * Verifica se o arquivo de trabalho já está compactado.
     *
     * A verificação é feita com base no conteúdo do arquivo,
     * não na sua extensão.
     *
     * @return bool
     */
    public function isCompressed()
    {
        return (0 === mb_strpos($this->buffer, "\x1f" . "\x8b" . "\x08"));
    }

    /**
     * Salva o conteúdo do buffer num arquivo físico.
     *
     * A extensão do arquivo deve ser informada.
     *
     * @param string $to       Caminho para salvar o arquivo.
     * @param bool   $truncate Se ativado, exclui o arquivo original
     *
     * @throws \RuntimeException
     */
    public function saveAs($to, $truncate = false)
    {
        $directory = dirname($to);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($truncate) {
            @unlink($this->from);
        }

        file_put_contents($to, $this->buffer);
    }

    /**
     * Recupera o conteúdo do buffer.
     *
     * @return mixed
     */
    public function getContents()
    {
        return $this->buffer;
    }
}
