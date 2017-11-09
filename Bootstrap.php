<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public static function _initView() {
        $view = new Zend_View ( );
        $view->setEncoding('UTF-8');

        // adicionando helpers
        $view->addHelperPath(APPLICATION_HELPER, 'Helpers');
        $view->addHelperPath('zendx', 'Zendx');
        $view->addHelperPath('zendx/jQuery/view/helper', 'Zendx_jQuery_view_helper');
        $view->addHelperPath('Noumenal/View/Helper', 'Noumenal_View_Helper');

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        Zend_Controller_Action_HelperBroker::addPath(APPLICATION_HELPER);

        Zend_Layout::startMvc(
                array('layoutPath' => APPLICATION_LAYOUT,
                    'layout' => 'layout'));
    }

    protected function _initSessions() {
        $config = new Zend_Config_Ini(CONFIG_FILE, APPLICATION_ENV);
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
        $config = array(
            'name' => 'session',
            'primary' => 'id',
            'modifiedColumn' => 'modified',
            'dataColumn' => 'data',
            'lifetimeColumn' => 'lifetime'
        );
        Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
        Zend_Session::start();
    }

    public static function _initLog() {
        $config = new Zend_Config_Ini(CONFIG_FILE, APPLICATION_ENV);
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
        $db->query("SET NAMES 'utf8'");

        $log = new Zend_Log();
        $columnMapping = array(
            'lvl' => 'priorityName',
            'msg' => 'message',
            'username' => 'username',
            'userip' => 'userip',
            'modulo' => 'modulo',
            'fkidusu' => 'fkidusu');

        $writer = new Zend_Log_Writer_Db($db, 'log', $columnMapping);
        $log->addWriter($writer);
        Zend_Registry::set('logger', $log);
    }

}
