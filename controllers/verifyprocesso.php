<?php
    // verifica se o SDPVA processou os dados recebidos da localidade

    defined('APP_ROOT') || define('APP_ROOT', "C:\wamp64\www\SDPVA\\");
    $xpath = array(
        ".",
        APP_ROOT . 'library\\',
        APP_ROOT . 'application\models',
        APP_ROOT . 'application\configs'
    );
    set_include_path(implode(PATH_SEPARATOR, $xpath));
    date_default_timezone_set('Brazil/East');
    require_once('Zend/Loader/Autoloader.php');
    $autoloader = Zend_Loader_Autoloader::getInstance();

    $config = new Zend_Config_Ini("application.ini", "linux");
    $host = $config->resources->db->params->host;
    $user = $config->resources->db->params->username;
    $pass = $config->resources->db->params->password;
    $dbname = $config->resources->db->params->dbname;
    $db = Zend_Db::factory('Pdo_Mysql', array(
                'host' => $host,
                'username' => $user,
                'password' => $pass,
                'dbname' => $dbname
    ));
    Zend_Db_Table_Abstract::setDefaultAdapter($db);

    require_once("processamentodb.php");

    $db = new processamentodb();
    $dtproc = $db->getData();
    print "Data do ultimo processamento: ".$dtproc."\n";
    
    $dt = new Zend_Date();
    $minutes = $dt->toString("mm");

    if ($minutes<15) {
        $minutes = "00:00";
    } else {
        if ($minutes>14 and $minutes<30) {
            $minutes = "15:00";
        } else {
            if ($minutes>29 and $minutes<45) {
                $minutes = "30:00";
            } else {
                $minutes = "45:00";
            }
        }
    }

    $dt = $dt->toString("YYYY-MM-dd HH:".$minutes);

    print "Data para processamento: " . $dt;
    if ($dtproc < $dt) {
        // solicita o arquivo XML da localidade e executa o processamento
        echo "Processando arquivos";
        //$url = "http://10.32.48.63:8080/SDPVA/services/getgeral";
        //$hc = fopen($url,'r');
        //$data = stream_get_contents($hc);
        //$date = new Zend_Date();
        //$nomearq = $date->toString("YYMMdd") . '.mnd';
        //$arq = APP_XML . 'SBRJ/mnd/' . $nomearq;
        //$file = fopen($arq, 'w');
        //fwrite($file, $data);
        //fclose($file);
        //
        //$this->_helper->Myhelper->processalocal($local,$arq);
        //
        //// processa solicitação de dados da EMS para a RNA
        //$fim = date(substr($xml['datahora'], 0, 4). substr($xml['datahora'], 5, 2). substr($xml['datahora'], 8, 2). substr($xml['datahora'], 11, 2) . substr($xml['datahora'], 14, 2)."00");
        //$this->_helper->getEMS->getems(1, "http://10.136.8.69:33349", $fim);
        //$this->_helper->Myhelper->entradaRNA();
    }
   

?>