<?php

require_once 'sodaratualdb.php';
require_once 'dadosderivadosdb.php';
require_once 'processodb.php';

class Zend_Controller_Action_Helper_Dadosderivados extends Zend_Controller_Action_Helper_Abstract {

    public function dadosderivados() {

        $processamentodb = new processodb();
        $dtData = $processamentodb->getProcessamento();
        $dtA = $dtData['datahora'];
        $data = new Zend_Date($dtA);
        $dt = $data->get("YYYYMMddHHmm");

        $local = 1;

        $sodar_atualdb = new sodaratualdb();

        try {
            $derivados1 = $sodar_atualdb->getDerivados1($dtA);
            foreach ($derivados1 as $linha) {
                $data = $linha['data'];
                $z = $linha['z'];
                $dir = $linha['dir'];
                $speed = $linha['speed'];

                ##Orientaçao da pista de esquerda para a direita

                ## Proa>=10 cauda>=2  traves>= 5

                $alinhado_real = number_format(cos(deg2rad($dir-70))*$speed/0.51444, 2);
                if($alinhado_real<0)
                {
                    $proa = number_format($alinhado_real*-1, 2);
                    $cauda = 0;
                }
                else
                {
                    $cauda = number_format($alinhado_real, 2);
                    $proa = 0;
                }

                $traves_real = number_format(sin(deg2rad($dir-70))*$speed/0.51444, 2);
                if($traves_real<0)
                {
                    $traves_esq = number_format($traves_real*-1, 2);
                    $traves_dir = 0;
                }
                else
                {
                    $traves_dir = number_format($traves_real, 2);
                    $traves_esq = 0;
                }
                try {
                    $sql = "INSERT INTO dados_derivados VALUES($alinhado_real, $traves_real, $cauda, $proa, $traves_dir, $traves_esq, '$data', $z, $local )";
                    $sodar_atualdb->gravar($sql);
                } catch (Exception $e) {
                    $this->gravarlog('err', utf8_encode($e->getMessage()),'Dados Derivados');
                }
            }
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()),'Dados Derivados');
        }
        $this->gravarlog('info', 'Processou Dados Derivados','Dados Derivados');
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