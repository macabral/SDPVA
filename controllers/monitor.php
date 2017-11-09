<?php
    defined('APP_ROOT') || define('APP_ROOT', "/var/www/html/SDPVA/");
    $xpath = array(
        ".",
        APP_ROOT . 'library',
        APP_ROOT . 'application/models',
        APP_ROOT . 'application/configs',
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

    require_once 'processamentodb.php';
    
    $data = new Zend_Date();
    $datahj = $data->toString("YYYY-MM-dd HH:mm");

    $processodb = new processamentodb();
    $datap = $processodb->getProcessamento();

    $datap = new Zend_Date($datap['datahora']);
    $datap1 = $datap->toString("YYYY-MM-dd HH:mm");

    $datahj = date_create($datahj);
    $datap = date_create($datap1);

    $diff = date_diff($datahj, $datap);

    if($diff->h > 0 || $diff->i>20) {
        $sql = "insert into log (lvl,msg,modulo) values ('err','Ausencia de dados SODAR desde ". $datap1 . "','SODAR')";
        $db->query($sql);
	$command = "vboxmanage controlvm Win7 reset";
        system($command);

    } else {
        $sql = "insert into log (lvl,msg,modulo) values ('info','Monitor executado.','SODAR')";
        $db->query($sql);
    }

    // roda rna
?>
<script type="text/javascript">
	location = "https://localhost/SDPVA/services/procems";
</script>