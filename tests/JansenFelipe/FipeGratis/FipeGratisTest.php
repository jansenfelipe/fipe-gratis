<?php

namespace JansenFelipe\FipeGratis;

use PHPUnit_Framework_TestCase;

class FipeGratisTest extends PHPUnit_Framework_TestCase {

    public function testGetMarcas() {

        $tabelas = FipeGratis::getTabelas(FipeGratis::CARRO);
        $codigoTabela = $tabelas[0]['codigo'];

        $marcas = FipeGratis::getMarcas(FipeGratis::CARRO, $codigoTabela);
        $codigoMarca = $marcas[0]['codigo'];

        $modelos = FipeGratis::getModelos(FipeGratis::CARRO, $codigoTabela, $codigoMarca);
        $codigoModelo = $modelos[0]['codigo'];

        $anos = FipeGratis::getAnos(FipeGratis::CARRO, $codigoTabela, $codigoMarca, $codigoModelo);
        $codigoAno = $anos[0]['codigo'];

        $precoMedio = FipeGratis::getPrecoMedio(FipeGratis::CARRO, $codigoTabela, $codigoMarca, $codigoModelo, $codigoAno);

        $this->assertEquals(count($tabelas) > 0, true);
        $this->assertEquals(count($marcas) > 0, true);
        $this->assertEquals(count($modelos) > 0, true);
        $this->assertEquals(count($anos) > 0, true);
        $this->assertEquals(is_float($precoMedio), true);
    }

}
