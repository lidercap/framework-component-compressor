COMPONENT COMPRESSOR
====================

Compressão de arquivos via PHP.

Instalação
----------

É recomendado instalar **framework-component-compressor** através do [composer](http://getcomposer.org).

```
{
    "require": {
        "lidercap/framework-component-compressor": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:lidercap/framework-component-compressor.git"
        }
    ]
}
```

GzipCompressor
--------------

Compressor de arquivos Gzip(.gz).

#### Compress: comprime um arquivo

```php
<?php

$gzip = new GzipCompressor('/tmp/file.txt');
$gzip->compress($level); // level default: 9

// Opção 1: obtendo o buffer comprimido.
$buffer = $gzip->getContents();

// Opção 2: salvando o novo arquivo.
$gzip->saveAs('/tmp/file.txt.gz');

```

Desenvolvimento e Testes
------------------------

Dependências:

 * PHP 5.5.x ou superior
 * Composer
 * Git
 * Make

Para rodar a suite de testes, você deve instalar as dependências externas do projeto e então rodar o PHPUnit.

    $ make install
    $ make test    (sem relatório de coverage)
    $ make testdox (com relatório de coverage)

Responsáveis técnicos
---------------------

 * **André Sabino: <asabino@lidercap.com.br>**
 * **Fernando Villaça: <fvillaca@lidercap.com.br>**
 * **Leonardo Thibes: <lthibes@lidercap.com.br>**
