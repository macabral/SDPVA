<?php

require_once 'sodaratualdb.php';
require_once 'processodb.php';
require_once 'dadosderivadosdb.php';

class Zend_Controller_Action_Helper_Procsodar extends Zend_Controller_Action_Helper_Abstract {

    public function procsodar($local,$dthr) {
        // esta função tem por finalidade ler o arquivo XML dos dados do SODAR
        // gravar no banco de dados, processar os dados derivados e
        // gerar os gráficos do diagnóstico e arquivos XML do diagnóstico

        $nomearq = $dthr. ".xml";
        $arq = APP_ROOT . '/public/xml/' . $local . '/geral/' . $nomearq;

        // lê arquivo xml recebido da localidade
        if (file_exists($arq)) {
            $xml = simplexml_load_file($arq);
            $this->gravarlog('info', 'Processando arquivo [' . $arq . ']', 'Proc SODAR');
        } else {
            $this->gravarlog('err', 'Arquivo não encontrado [' . $arq . ']', 'Proc SODAR');
            return false;
        }

        if ($local == "SBGR") {
            $local = 1;
            $locald = "SBGR";
        }
        // formata a data do processamento
        $dtx =  substr($dthr,0,4) . "-" . substr($dthr,4,2) . "-" . substr($dthr,6,2) . " " . substr($dthr,8,2)  . ":" . substr($dthr,10,2);
        $dt = "'" . $dtx . "'";

        // monta comando SQL
        $sql = 'INSERT INTO sodar_atual VALUES ' ;
        foreach ($xml->Obs as $obs){

            $sql .="(DEFAULT, $dt, ";
            $sql .= $obs->z . ', ';
            $sql .= $obs->speed . ', ';
            $sql .= $obs->dir . ', ';
            $sql .= $obs->U . ', ';
            $sql .= $obs->V . ', ';
            $sql .= $obs->W . ', ';
            $sql .= $obs->sigU . ', ';
            $sql .= $obs->sigU_r . ', ';
            $sql .= $obs->sigV . ', ';
            $sql .= $obs->sigV_r . ', ';
            $sql .= $obs->sigW . ', ';
            $sql .= $obs->speed_ass . ', ';
            $sql .= $obs->dir_ass . ', ';
            $sql .= $obs->U_ass . ', ';
            $sql .= $obs->V_ass . ', ';
            $sql .= $obs->W_ass . ', ';
            $sql .= $obs->sigU_ass . ', ';
            $sql .= $obs->sigU_r_ass . ', ';
            $sql .= $obs->sigV_ass . ', ';
            $sql .= $obs->sigV_r_ass . ', ';
            $sql .= $obs->sigW_ass . ', ';
            $sql .= $obs->shear . ', ';
            $sql .= $obs->shearDir . ', ';
            $sql .= $obs->sigSpeed . ', ';
            $sql .= $obs->sigLat . ', ';
            $sql .= $obs->sigPhi . ', ';
            $sql .= $obs->sigTheta . ', ';
            $sql .= $obs->TI . ', ';
            $sql .= $obs->PGz . ', ';
            $sql .= $obs->TKE . ', ';
            $sql .= $obs->EDR . ', ';
            $sql .= $obs->bck_raw . ', ';
            $sql .= $obs->bck . ', ';
            $sql .= $obs->bck_ID . ', ';
            $sql .= $obs->CT2 . ', ';
            $sql .= $obs->error . ', ';
            $sql .= $obs->PG . ', ';
            $sql .= $obs->h_range . ', ';
            $sql .= $obs->h_inv . ', ';
            $sql .= $obs->h_mixing . ', ';
            $sql .= $obs->H . ', ';
            $sql .= $obs->u_estrela . ', ';
            $sql .= $obs->L_estrela . ', ';
            $sql .= "'" . $local . "'";
            $sql .= '), ';
        }
        $sql = rtrim($sql, ', ');

        // salva registros do arquivo xml no banco de dados - tabela: sodar_atual
        $sodar_atualdb = new sodaratualdb();
        try {
            $sodar_atualdb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()), 'Proc SODAR');
        }

        // atualiza dados derivados
        $sql = "UPDATE sodar_atual s SET U = ROUND(s.speed*SIN(RADIANS(s.dir)),2), V= ROUND(s.speed*COS(RADIANS(s.dir)),2) WHERE s.data=$dt and s.speed<99.99";
        try {
            $sodar_atualdb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()),'Proc SODAR');
        }
        try {
            $derivados1 = $sodar_atualdb->getDerivados1($dt);
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
                $sql = "INSERT INTO dados_derivados VALUES($alinhado_real, $traves_real, $cauda, $proa, $traves_dir, $traves_esq, '$data', $z, '$local' )";
                $sodar_atualdb->gravar($sql);
            }
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()),'Proc SODAR');
        }
        
        // atualiza a data de processamento - tabela: processamento
        $processadb = new processodb();
        $sql = "update processamento set datahora = " . $dt .   ", nomearqxml_d = '". $nomearq . "' where local = '" . $locald . "'";
        try {
            $processadb->gravar($sql);
        } catch (Exception $e) {
            $this->gravarlog('err', utf8_encode($e->getMessage()),'Proc SODAR');
        }
        
        // // gera arquivos XML
        system("python2.7 ". APPLICATION_PATH . "/python/xml09/xml.py");
        system("python2.7 ". APPLICATION_PATH . "/python/xml27/xml.py");
                
        return true;
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
