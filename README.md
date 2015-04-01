# FIPE Grátis
[![Travis](https://travis-ci.org/jansenfelipe/fipe-gratis.svg?branch=1.0)](https://travis-ci.org/jansenfelipe/fipe-gratis)
[![Latest Stable Version](http://img.shields.io/packagist/v/jansenfelipe/fipe-gratis.svg?style=flat)](https://packagist.org/packages/jansenfelipe/fipe-gratis)
[![Total Downloads](http://img.shields.io/packagist/dt/jansenfelipe/fipe-gratis.svg?style=flat)](https://packagist.org/packages/jansenfelipe/fipe-gratis)
[![License](http://img.shields.io/packagist/l/jansenfelipe/fipe-gratis.svg?style=flat)](https://packagist.org/packages/jansenfelipe/fipe-gratis)

Com esse pacote você poderá consultar dados atualizados da tabela FIPE.

### Como usar

Adicione a library

    $ composer require jansenfelipe/fipe-gratis
    
Adicione o autoload.php do composer no seu arquivo PHP.

    require_once 'vendor/autoload.php';  

Agora chame o método `precoMedio()`

     getPrecoMedio($tipo, $tabelaReferencia, $codMarca, $codModelo, $codAno);

##### $tipo

Os tipos disponíveis são: Carro, Moto e Caminhão. Seus códigos já estão disponíveis em constantes na classe FipeGratis.
    
    FipeGratis::CARRO
    FipeGratis::MOTO
    FipeGratis::CAMINHAO
    
##### $tabelaReferencia

As tabelas são os meses de referência. Para saber os códigos das tabelas, basta chamar o método `getTabelas()` passando o Tipo desejado:

    $tabelas = FipeGratis::getTabelas(FipeGratis::CARRO);

##### $codMarca

Para saber os códigos das marcas, basta chamar o método `getMarcas()` passando os parâmetros:

    $marcas = FipeGratis::getMarcas(FipeGratis::CARRO, $codigoTabela);

##### $codModelo

Para saber os códigos dos modelos, basta chamar o método `getModelos()` passando os parâmetros:

    $modelos = FipeGratis::getModelos(FipeGratis::CARRO, $codigoTabela, $codigoMarca);

##### $codAno

Para saber os códigos dos anos, basta chamar o método `getAnos()` passando os parâmetros:

    $anos = FipeGratis::getAnos(FipeGratis::CARRO, $codigoTabela, $codigoMarca, $codigoModelo);
