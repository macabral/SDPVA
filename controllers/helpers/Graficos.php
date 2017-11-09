<?php

require_once 'processodb.php';
require_once 'parametrosdb.php';
require_once 'dadosderivadosdb.php';
require_once 'alertasdb.php';

class Zend_Controller_Action_Helper_Graficos extends Zend_Controller_Action_Helper_Abstract {


    public function graficos() {
        //// gera gráficos
        system("python2.7 ". APPLICATION_PATH . "/python/barbela3h.py");
	// prognostico
        system("python2.7 ". APPLICATION_PATH . "/python/barbela.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela6h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela12h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/barbela24h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor3h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor6h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor12h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vetor24h.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_proa.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_proa_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_cauda.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_cauda_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_dir.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_dir_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_esq.py");
        system("python2.7 ". APPLICATION_PATH . "/python/var_traves_esq_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_proa.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_proa_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_cauda.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_cauda_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_dir.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_dir_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_esq.py");
        system("python2.7 ". APPLICATION_PATH . "/python/vel_traves_esq_27.py");
        system("python2.7 ". APPLICATION_PATH . "/python/windshear6x1.py");

        
        $this->gravarlog('info', "Gráficos processados", 'Gráficos');
    }

    private function gravarlog($tipo, $msg, $modulo) {
        $_logger = Zend_Registry::get('logger');
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $_logger->setEventItem('username', Zend_Auth::getInstance()->getIdentity()->login);
            $_logger->setEventItem('fkidusu', Zend_Auth::getInstance()->getIdentity()->idusu);
        } else {
            $_logger->setEventItem('username', 'convidado');
            $_logger->setEventItem('fkidusu', 0);
        }
        $_logger->setEventItem('userip', $_SERVER['REMOTE_ADDR']);
        $_logger->setEventItem('modulo', $modulo);

        $msg = substr($msg,0,500);
        if ($tipo == 'info') {
            $_logger->info($msg);
        }
        if ($tipo == 'err') {
            $_logger->err($msg);
        }
        if ($tipo == 'warn') {
            $_logger->warn($msg);
        }
    }
}
