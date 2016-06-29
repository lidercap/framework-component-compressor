Nome do Projeto
===============

Aqui vai uma pequena descrição do projeto, falando um pouco sobre seu propósito e os problemas que ele se propõe a resolver.

Instalação
----------

É recomendado instalar [nome-do-projeto] através do [composer](http://getcomposer.org).

```
{
    "require": {
        "lidercap/framework-[nome-do-projeto]": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@bitbucket.org:lidercap/framework-[nome-do-projeto].git"
        }
    ]
}
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

NOME DA FUNÇÃO
--------------

Descrição da função.

```php
<?php

// Coloque aqui exemplos de uso

```

Responsáveis técnicos
---------------------

 * **André Sabino: <asabino@lidercap.com.br>**
 * **Fernando Villaça: <fvillaca@lidercap.com.br>**
 * **Leonardo Thibes: <lthibes@lidercap.com.br>**
