<?php

/*
 * CarregaDadosDerivados.php
 * Carrega o último arquivo processado do servidor definido em APP_URL
 * 
 * CTCEA/COPPETEC
 * Marco Aurélio de Souza Cabral
 * Marcus 
 * 
 * Dezembro de 2015
 */
echo "Inciando processamento...\n";

defined('APP_LIBRARY') || define('APP_LIBRARY', 'C:/www/SDPVA/library/Zend');
define('CONFIG_FILE', 'c:/www/SDPVA/application/configs/application.ini');
define('XML_FILE', 'c:/www/SDPVA/public/xml/201601051900.xml');


set_include_path(implode(PATH_SEPARATOR, array(APP_LIBRARY, get_include_path())));

require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

echo "configurando acesso banco de dados...\n";

// configura acesso ao banco de dados
$config = new Zend_Config_Ini(CONFIG_FILE, 'windows');
$host   = $config->resources->db->params->host;
$user   = $config->resources->db->params->username;
$pass   = $config->resources->db->params->password;
$dbname = $config->resources->db->params->dbname;
$db     = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => $host,
            'username' => $user,
            'password' => $pass,
            'dbname'   => $dbname
        ));
Zend_Db_Table_Abstract::setDefaultAdapter($db);

echo "acessando servico...\n";


$xmlstr = file_get_contents(XML_FILE);

if (!$xmlstr == '') {

    $xml = SimpleXML_load_string($xmlstr);

    $local = "'" . $xml['local'] . "'";
    $dthr  = substr($xml['datahora'], 0, 10) . " " . substr($xml['datahora'], 11, 8);
    $tipo = $xml['tipo'];
    
    echo "data: " . $dthr . "\n";

    // verifica se o arquivo já foi processado
    $sql = "select sodar_atual_data from dados_derivados where sodar_atual_data = '" . $dthr . "'";
    $row = $db->fetchRow($sql);

    // se não for encontrado insere os dados do arquivo xml na tabela dados_derivados
    if (is_null($row['sodar_atual_data']) and $tipo == "G") {

        echo "salvando arquivo xml...\n";
        $nomearq = substr($dthr, 0, 4) . substr($dthr, 5, 2) . substr($dthr, 8, 2) . substr($dthr, 11, 2) . substr($dthr, 14, 2) . ".xml";
        $arq     = XML_FILE . $xml['local'] . "/diagnostico/".$xml['pista']."/_" . $nomearq;
        $file    = fopen($arq, "w");
        fwrite($file, $xmlstr);
        fclose($file);
        
        echo "inserindo no banco de dados...\n";
        $sql = "insert into dados_derivados (sodar_atual_z,alinhado,traves,proa,cauda,traves_esq,traves_dir,local,sodar_atual_data) values ";
        foreach ($xml->children() as $obs) {
            $sql .= '(' . $obs->altura . ',' . $obs->u . ',' . $obs->v . ',' . $obs->proa . ',' . $obs->cauda . ',' . $obs->traves_esq . ',' . $obs->traves_dir . ',' . $local . ',"' . $dthr . '"),';
        }
        $sql = substr($sql, 0, strlen($sql) - 1);
        $db->query($sql);
        
        echo "atualiza tabela de processamento\n";
        $sql = "update processamento set datahora = '" . $dthr . "', nomearqxml_d = '" . $nomearq . "'";
        $db->query($sql);

        echo "fim de processamento...\n";
    } else {
        echo "arquivo ja processado!\n";
    }
} else {
    echo "nao foi possivel processar arquivo xml.\n";
}







