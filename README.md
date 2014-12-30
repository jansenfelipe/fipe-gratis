# FIPE Grátis
[![Travis](https://travis-ci.org/jansenfelipe/fipe-gratis.svg?branch=1.0)](https://travis-ci.org/jansenfelipe/fipe-gratis)

Com esse pacote você poderá consultar dados atualizados da tabela FIPE.

### Como usar

Adicione no seu arquivo `composer.json` o seguinte registro na chave `require`

    "jansenfelipe/fipe-gratis": "1.0.*@dev"

Execute

    $ composer update
    
Adicione o autoload.php do composer no seu arquivo PHP.

    require_once 'vendor/autoload.php';  

Para realizar uma consulta de `precoMedio()`, você vai precisar saber os códigos de Tipo, Tabela, Marca, Modelo e Ano.

    $precoMedio = FipeGratis::getPrecoMedio(FipeGratis::CARRO, $codigoTabela, $codigoMarca, $codigoModelo, $codigoAno);

###### Tipos

Os tipos disponíveis são: Carro, Moto e Caminhão. Seus códigos já estão disponíveis em constantes na classe FipeGratis.
    
    FipeGratis::CARRO
    FipeGratis::MOTO
    FipeGratis::CAMINHAO
    
###### Tabelas

As tabelas são os meses de referência. Para saber os códigos das tabelas, basta chamar o método `getTabelas()` passando o Tipo desejado:

    $tabelas = FipeGratis::getTabelas(FipeGratis::CARRO);

###### Marcas

Para saber os códigos das marcas, basta chamar o método `getMarcas()` passando os parâmetros:

    $marcas = FipeGratis::getMarcas(FipeGratis::CARRO, $codigoTabela);

###### Modelos

Para saber os códigos dos modelos, basta chamar o método `getModelos()` passando os parâmetros:

    $modelos = FipeGratis::getModelos(FipeGratis::CARRO, $codigoTabela, $codigoMarca);

###### Anos

Para saber os códigos dos anos, basta chamar o método `getAnos()` passando os parâmetros:

    $anos = FipeGratis::getAnos(FipeGratis::CARRO, $codigoTabela, $codigoMarca, $codigoModelo);

### Frameworks

##### (Laravel)

Abra seu arquivo `config/app.php` e adicione `'JansenFelipe\FipeGratis\FipeGratisServiceProvider'` ao final do array `$providers`

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'JansenFelipe\FipeGratis\FipeGratisServiceProvider',
    ),

Adicione também `'FipeGratis' => 'JansenFelipe\FipeGratis\Facade'` no final do array `$aliases`

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'FipeGratis'    => 'JansenFelipe\FipeGratis\Facade',

    ),