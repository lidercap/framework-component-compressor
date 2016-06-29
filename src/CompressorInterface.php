<?php

namespace Lidercap\Component\Compressor;

/**
 * Interface para compressores.
 */
interface CompressorInterface
{
    /**
     * Informa o caminho para o arquivo de trabalho.
     *
     * @param string $from
     *
     * @throws \RuntimeException
     */
    public function __construct($from);

    /**
     * Verifica se o arquivo de trabalho já está compactado.
     *
     * A verificação é feita com base no conteúdo do arquivo,
     * não na sua extensão.
     *
     * @return bool
     */
    public function isCompressed();

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
    public function compress($level = 9);

    /**
     * Descomprime o conteúdo do arquivo de trabalho e salva no buffer.
     *
     * @throws \RuntimeException
     *
     * @return $this
     */
    public function decompress();

    /**
     * Salva o conteúdo do do buffer num arquivo físico.
     *
     * A extensão do arquivo deve ser informada.
     *
     * @param string $to       Caminho para salvar o arquivo.
     * @param bool   $truncate Se ativado, exclui o arquivo original
     *
     * @throws \RuntimeException
     */
    public function saveAs($to, $truncate = false);

    /**
     * Recupera o conteúdo do buffer.
     *
     * @return mixed
     */
    public function getContents();
}
