<?php

namespace JansenFelipe\FipeGratis;

use Exception;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Classe com metodos estáticos para realizar as consultas
 *
 * @license MIT
 * @package JansenFelipe\FipeGratis
 */
class FipeGratis {

    const CARRO = "p=51";
    const MOTO = "v=m&p=52";
    const CAMINHAO = "v=c&p=53";

    private static $__COOKIE = null;
    private static $__PARAM_EVENTARGUMENT = null;
    private static $__PARAM_VIEWSTATE = null;
    private static $__PARAM_VIEWSTATEGENERATOR = null;
    private static $__PARAM_EVENTVALIDATION = null;

    /**
     * Método para retornar o tipo a partir de uma string
     *
     * @throws Exception
     * @return array
     */
    public static function getTipoByString($string) {
        $class = new ReflectionClass('JansenFelipe\FipeGratis\FipeGratis');
        return $class->getConstant($string);
    }
    
    /**
     * Método para buscar as tabelas de referencia
     *
     * @throws Exception
     * @return array
     */
    public static function getTabelas() {
        $client = new Client();
        $crawler = $client->request('GET', "http://www.fipe.org.br/web/indices/veiculos/default.aspx");

        $tabelas = $crawler->filter('#ddlTabelaReferencia > option')->each(function (Crawler $node) {
            return array('codigo' => $node->attr('value'), 'tabela' => $node->text());
        });

        return $tabelas;
    }

    /**
     * Método para buscar as marcas existentes de acordo com o tipo informado
     *
     * @param  string $tipo
     * @param  int $tabelaReferencia
     * @throws Exception
     * @return array
     */
    public static function getMarcas($tipo = null, $tabelaReferencia = null) {
        FipeGratis::setParams($tipo);

        /*
         * Verificando se exite $tabelaReferencia
         */
        $validate = array_filter(FipeGratis::getTabelas(), function($tables) use ($tabelaReferencia) {
            return ($tables['codigo'] == $tabelaReferencia);
        });

        if (empty($validate))
            throw new Exception("A tabela de referencia $tabelaReferencia não existe");


        /*
         * Montando parametros
         */
        $paramters = array(
            'ScriptManager1' => 'UdtMarca|ddlMarca',
            '__ASYNCPOST' => true,
            '__EVENTTARGET' => 'ddlMarca',
            '__LASTFOCUS' => '',
            '__EVENTARGUMENT' => FipeGratis::$__PARAM_EVENTARGUMENT,
            '__VIEWSTATE' => FipeGratis::$__PARAM_VIEWSTATE,
            '__VIEWSTATEGENERATOR' => FipeGratis::$__PARAM_VIEWSTATEGENERATOR,
            '__EVENTVALIDATION' => FipeGratis::$__PARAM_EVENTVALIDATION,
            'ddlAnoValor' => 0,
            'ddlMarca' => 0,
            'ddlModelo' => 0,
            'ddlTabelaReferencia' => $tabelaReferencia,
            'txtCodFipe' => ''
        );

        /*
         * Consultando modelos
         */
        $ch = curl_init("http://www.fipe.org.br/web/indices/veiculos/default.aspx?$tipo");
        $options = array(
            CURLOPT_COOKIEJAR => 'cookiejar',
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate",
                "Referer: http://www.fipe.org.br/web/indices/veiculos/default.aspx?$tipo",
                "Cookie: " . FipeGratis::$__COOKIE . "",
                "Host: www.fipe.org.br",
                "Connection: keep-alive",
                "X-MicrosoftAjax: Delta=true"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($paramters),
            CURLOPT_FOLLOWLOCATION => 1
        );

        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $explode = explode('|', $html);

        $key = array_search('__EVENTARGUMENT', $explode);
        FipeGratis::$__PARAM_EVENTARGUMENT = $explode[$key + 1];

        $key = array_search('__VIEWSTATE', $explode);
        FipeGratis::$__PARAM_VIEWSTATE = $explode[$key + 1];

        $key = array_search('__VIEWSTATEGENERATOR', $explode);
        FipeGratis::$__PARAM_VIEWSTATEGENERATOR = $explode[$key + 1];

        $key = array_search('__EVENTVALIDATION', $explode);
        FipeGratis::$__PARAM_EVENTVALIDATION = $explode[$key + 1];

        $crawler = new Crawler($html);
        $options = $crawler->filter('#ddlMarca > option');

        $marcas = array();

        foreach ($options as $node) {
            if ($node->getAttribute('value') != '0')
                $marcas[] = array('codigo' => $node->getAttribute('value'), 'marca' => $node->nodeValue);
        }

        return $marcas;
    }

    /**
     * Metodo para buscar os modelos existentes de acordo com o tipo, tabela e marca informado
     *
     * @param  string $tipo
     * @param  int $tabelaReferencia
     * @param  int $codMarca
     * @throws Exception
     * @return array
     */
    public static function getModelos($tipo = null, $tabelaReferencia = null, $codMarca = null) {

        /*
         * Verificando se exite $codMarca
         */
        $validate = array_filter(FipeGratis::getMarcas($tipo, $tabelaReferencia), function($tables) use ($codMarca) {
            return ($tables['codigo'] == $codMarca);
        });

        if (empty($validate))
            throw new Exception("A marca $codMarca não existe");

        /*
         * Montando parametros
         */
        $paramters = array(
            'ScriptManager1' => 'UdtMarca|ddlMarca',
            '__ASYNCPOST' => true,
            '__EVENTTARGET' => 'ddlMarca',
            '__LASTFOCUS' => '',
            '__EVENTARGUMENT' => FipeGratis::$__PARAM_EVENTARGUMENT,
            '__VIEWSTATE' => FipeGratis::$__PARAM_VIEWSTATE,
            '__VIEWSTATEGENERATOR' => FipeGratis::$__PARAM_VIEWSTATEGENERATOR,
            '__EVENTVALIDATION' => FipeGratis::$__PARAM_EVENTVALIDATION,
            'ddlAnoValor' => 0,
            'ddlMarca' => $codMarca,
            'ddlModelo' => 0,
            'ddlTabelaReferencia' => $tabelaReferencia,
            'txtCodFipe' => ''
        );


        /*
         * Consultando modelos
         */
        $ch = curl_init("http://www.fipe.org.br/web/indices/veiculos/default.aspx?$tipo");
        $options = array(
            CURLOPT_COOKIEJAR => 'cookiejar',
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate",
                "Referer: http://www.fipe.org.br/web/indices/veiculos/default.aspx?$tipo",
                "Cookie: " . FipeGratis::$__COOKIE . "",
                "Host: www.fipe.org.br",
                "Connection: keep-alive",
                "X-MicrosoftAjax: Delta=true"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($paramters),
            CURLOPT_FOLLOWLOCATION => 1
        );

        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $explode = explode('|', $html);

        $key = array_search('__EVENTARGUMENT', $explode);
        FipeGratis::$__PARAM_EVENTARGUMENT = $explode[$key + 1];

        $key = array_search('__VIEWSTATE', $explode);
        FipeGratis::$__PARAM_VIEWSTATE = $explode[$key + 1];

        $key = array_search('__VIEWSTATEGENERATOR', $explode);
        FipeGratis::$__PARAM_VIEWSTATEGENERATOR = $explode[$key + 1];

        $key = array_search('__EVENTVALIDATION', $explode);
        FipeGratis::$__PARAM_EVENTVALIDATION = $explode[$key + 1];


        $crawler = new Crawler($html);
        $options = $crawler->filter('#ddlModelo > option');

        $modelos = array();

        foreach ($options as $node) {
            if ($node->getAttribute('value') != '0')
                $modelos[] = array('codigo' => $node->getAttribute('value'), 'modelo' => $node->nodeValue);
        }

        return $modelos;
    }

    /**
     * Metodo para buscar os anos existentes de acordo com o tipo, tabela, marca e modelo informado
     *
     * @param  string $tipo
     * @param  int $codMarca
     * @param  int $codModelo
     * @throws Exception
     * @return array
     */
    public static function getAnos($tipo = null, $tabelaReferencia = null, $codMarca = null, $codModelo = null) {

        /*
         * Verificando se exite $codModelo
         */
        $validate = array_filter(FipeGratis::getModelos($tipo, $tabelaReferencia, $codMarca), function($tables) use ($codModelo) {
            return ($tables['codigo'] == $codModelo);
        });

        if (empty($validate))
            throw new Exception("O modelo $codModelo não existe");

        /*
         * Montando parametros
         */
        $paramters = array(
            'ScriptManager1' => 'updModelo|ddlModelo',
            '__ASYNCPOST' => true,
            '__EVENTTARGET' => 'ddlModelo',
            '__LASTFOCUS' => '',
            '__EVENTARGUMENT' => FipeGratis::$__PARAM_EVENTARGUMENT,
            '__VIEWSTATE' => FipeGratis::$__PARAM_VIEWSTATE,
            '__VIEWSTATEGENERATOR' => FipeGratis::$__PARAM_VIEWSTATEGENERATOR,
            '__EVENTVALIDATION' => FipeGratis::$__PARAM_EVENTVALIDATION,
            'ddlAnoValor' => 0,
            'ddlMarca' => $codMarca,
            'ddlModelo' => $codModelo,
            'ddlTabelaReferencia' => $tabelaReferencia,
            'txtCodFipe' => ''
        );

        /*
         * Consultando anos
         */
        $ch = curl_init("http://www.fipe.org.br/web/indices/veiculos/default.aspx?azxp=1&$tipo");
        $options = array(
            CURLOPT_COOKIEJAR => 'cookiejar',
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate",
                "Referer: http://www.fipe.org.br/web/indices/veiculos/default.aspx?azxp=1&$tipo",
                "Cookie: " . FipeGratis::$__COOKIE . "",
                "Host: www.fipe.org.br",
                "Connection: keep-alive",
                "X-MicrosoftAjax: Delta=true"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($paramters),
            CURLOPT_FOLLOWLOCATION => 1
        );

        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $explode = explode('|', $html);

        $key = array_search('__EVENTARGUMENT', $explode);
        FipeGratis::$__PARAM_EVENTARGUMENT = $explode[$key + 1];

        $key = array_search('__VIEWSTATE', $explode);
        FipeGratis::$__PARAM_VIEWSTATE = $explode[$key + 1];

        $key = array_search('__VIEWSTATEGENERATOR', $explode);
        FipeGratis::$__PARAM_VIEWSTATEGENERATOR = $explode[$key + 1];

        $key = array_search('__EVENTVALIDATION', $explode);
        FipeGratis::$__PARAM_EVENTVALIDATION = $explode[$key + 1];

        $crawler = new Crawler($html);
        $options = $crawler->filter('#ddlAnoValor > option');

        $anos = array();

        foreach ($options as $node) {
            if ($node->getAttribute('value') != '0')
                $anos[] = array('codigo' => $node->getAttribute('value'), 'ano' => $node->nodeValue);
        }

        return $anos;
    }

    /**
     * Metodo para buscar o preço médio de acordo com o tipo, tabela, marca, modelo e ano informado
     *
     * @param  string $tipo
     * @param  int $codMarca
     * @param  int $codModelo
     * @throws Exception
     * @return array
     */
    public static function getPrecoMedio($tipo = null, $tabelaReferencia = null, $codMarca = null, $codModelo = null, $codAno = null) {

        /*
         * Verificando se exite $codAno
         */
        $validate = array_filter(FipeGratis::getAnos($tipo, $tabelaReferencia, $codMarca, $codModelo), function($tables) use ($codAno) {
            return ($tables['codigo'] == $codAno);
        });

        if (empty($validate))
            throw new Exception("O ano $codAno não existe");

        /*
         * Montando parametros
         */
        $paramters = array(
            'ScriptManager1' => 'updAnoValor|ddlAnoValor',
            '__ASYNCPOST' => true,
            '__EVENTTARGET' => 'ddlAnoValor',
            '__LASTFOCUS' => '',
            '__EVENTARGUMENT' => FipeGratis::$__PARAM_EVENTARGUMENT,
            '__VIEWSTATE' => FipeGratis::$__PARAM_VIEWSTATE,
            '__VIEWSTATEGENERATOR' => FipeGratis::$__PARAM_VIEWSTATEGENERATOR,
            '__EVENTVALIDATION' => FipeGratis::$__PARAM_EVENTVALIDATION,
            'ddlAnoValor' => $codAno,
            'ddlMarca' => $codMarca,
            'ddlModelo' => $codModelo,
            'ddlTabelaReferencia' => $tabelaReferencia,
            'txtCodFipe' => ''
        );


        /*
         * Consultando anos
         */
        $ch = curl_init("http://www.fipe.org.br/web/indices/veiculos/default.aspx?azxp=1&$tipo");
        $options = array(
            CURLOPT_COOKIEJAR => 'cookiejar',
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate",
                "Referer: http://www.fipe.org.br/web/indices/veiculos/default.aspx?azxp=1&$tipo",
                "Cookie: " . FipeGratis::$__COOKIE . "",
                "Host: www.fipe.org.br",
                "Connection: keep-alive",
                "X-MicrosoftAjax: Delta=true"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($paramters),
            CURLOPT_FOLLOWLOCATION => 1
        );

        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler($html);
        $preco_medio = $crawler->filter('#lblValor')->text();

        $valor = trim(str_replace('R$', '', $preco_medio));

        if (strlen($valor) <= 3)
            return floatval(str_replace(',', '.', $valor));
        else {
            $valor = str_replace('.', '', $valor);
            return floatval(str_replace(',', '.', $valor));
        }
    }

    /**
     * Metodo para validar o tipo informado
     *
     * @param  string $tipo
     * @throws Exception
     * @return void
     */
    private static function validaTipo($tipo = null) {
        if ($tipo != FipeGratis::CARRO && $tipo != FipeGratis::MOTO && $tipo != FipeGratis::CAMINHAO)
            throw new Exception("O tipo informado é inválido");
    }

    /**
     * Metodo para capturar o cookie
     *
     * @param  string $tipo
     * @throws Exception
     * @return void
     */
    private static function setCookie($tipo = null) {
        FipeGratis::validaTipo($tipo);

        $client = new Client();
        $client->request('GET', "http://www.fipe.org.br/web/index.asp?$tipo&aspx=/web/indices/veiculos/default.aspx");

        $response = $client->getResponse();
        $headers = $response->getHeaders();

        FipeGratis::$__COOKIE = $headers['Set-Cookie'][0];
    }

    /**
     * Metodo para capturar os PARAM's
     *
     * @param  string $tipo
     * @throws Exception
     * @return void
     */
    private static function setParams($tipo = null) {
        FipeGratis::setCookie($tipo);

        $ch = curl_init("http://www.fipe.org.br/web/indices/veiculos/default.aspx?azxp=1&$tipo");
        $options = array(
            CURLOPT_COOKIEJAR => 'cookiejar',
            CURLOPT_HTTPHEADER => array(
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:32.0) Gecko/20100101 Firefox/32.0",
                "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3",
                "Accept-Encoding: gzip, deflate",
                "Referer: http://www.fipe.org.br/web/index.asp?$tipo&aspx=/web/indices/veiculos/default.aspx",
                "Cookie: " . FipeGratis::$__COOKIE . "",
                "Connection: keep-alive"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 1
        );

        curl_setopt_array($ch, $options);
        $html = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler($html);

        FipeGratis::$__PARAM_EVENTARGUMENT = $crawler->filter('#__EVENTARGUMENT')->attr('value');
        FipeGratis::$__PARAM_EVENTVALIDATION = $crawler->filter('#__EVENTVALIDATION')->attr('value');
        FipeGratis::$__PARAM_VIEWSTATE = $crawler->filter('#__VIEWSTATE')->attr('value');
        FipeGratis::$__PARAM_VIEWSTATEGENERATOR = $crawler->filter('#__VIEWSTATEGENERATOR')->attr('value');
    }

}
