<?php

require_once 'sodaratualdb.php';
require_once 'processodb.php';
require_once 'dadosderivadosdb.php';

class Zend_Controller_Action_Helper_Readmnd extends Zend_Controller_Action_Helper_Abstract {

    public function readmnd() {

        $data = new Zend_Date();
        $data1 = $data->toString("YYMMdd");
        $hr = $data->get(Zend_Date::HOUR);
        $min = $data->get(Zend_Date::MINUTE);
        if ($hr == 0 and $min < 15) {
            $data = $data->subDay(1);
            $data1 = $data->toString("YYMMdd");
        }
        $file = APP_APRUN . $data1 . ".mnd";
        if (file_exists($file)) {

            $sodar_atualdb = new sodaratualdb();

            $mnd= file($file);
            $clenf = count($mnd);
            $cleni = $clenf - 55;
            $local = 1; // SBGR
            $locald = "SBGR";
            for ($i=$cleni; $i<$clenf; $i++){
                $txt = rtrim($mnd[$i]);
                if (substr($txt,0,1)=="#") {
                    $txt = '';
                }
                //data
                if ($i == $cleni) {
                    $dt =  substr($txt,0,19);
                    $dth = "'" . $dt  ."'";
                    $txt = '';
                }
                // #PG  h_range  h_inv  h_mixing       H     u*      L*
                if ($i == $cleni+2) {
                    $row = preg_split("/[\s]+/",$txt);
                    $pg = $row[1];
                    $h_range = $row[2];
                    $h_inv = $row[3];
                    $h_mixing = $row[4];
                    $H = $row[5];
                    $u_ = $row[6];
                    $L_ = $row[7];
                    $txt = '';
                }
                if (!empty($txt) || $txt <> '' ){
                    $row = preg_split("/[\s]+/",$txt);
                    //print_r($row) . "<\p>" ;
                    // gravar dados na tabela sodar_atual
                    $sql = "INSERT INTO `sdpva`.`sodar_atual`
                        (`data`,`z`,`speed`,`dir`,`U_geo`,`V_geo`,`U`,`V`,`W`,`sigU`,`sigU_r`,`sigV`,`sigV_r`,`sigW`,`speed_ass`,`dir_ass`,
                        `U_ass`,`V_ass`,`W_ass`,`sigU_ass`,`sigU_r_ass`,`sigV_ass`,`sigV_r_ass`,`sigW_ass`,`shear`,
                        `shearDir`,`sigSpeed`,`sigLat`,`sigPhi`,`sigTheta`,`TI`,`PGz`,`TKE`,`EDR`,`bck_raw`,`bck`,
                        `bck_ID`,`CT2`,`error`,`PG`,`h_range`,`h_inv`,`h_mixing`,`H`,`u_estrela`,`L_estrela`,`local`)
                        VALUES ( $dth";
                    for ($j=1; $j<count($row); $j++) {
                        $sql .= "," . $row[$j];
                    }
                    $sql .= ",$pg,$h_range,$h_inv,$h_mixing,$H,$u_,$L_,1)";

                    try {
                        $sodar_atualdb->gravar($sql);
                    } catch (Exception $e) {
                        $this->gravarlog('err', utf8_encode($e->getMessage()),'Read mnd');
                    }
                }
            }

            // gera arquivos XML
            system("python2.7 ". APPLICATION_PATH . "/python/xml09/xml.py");
            system("python2.7 ". APPLICATION_PATH . "/python/xml27/xml.py");

            // atualiza a data de processamento - tabela: processamento
            $nomexml =  substr($dt,0,4) . substr($dt,5,2) .  substr($dt,8,2) . substr($dt,11,2) . substr($dt,14,2) . '.xml';
            $processadb = new processodb();
            $sql = "update processamento set datahora = " . $dth .   ", nomearqxml_d= '" . $nomexml . "' where local = '" . $locald . "'";
            try {
                $processadb->gravar($sql);
            } catch (Exception $e) {
                $this->gravarlog('err', utf8_encode($e->getMessage()),'Read mnd');
            }



            $this->gravarlog('info', 'Processou MND' . $dth,'Read mnd');

        } else {
            $this->gravarlog('err', 'Arquivo não existe! ' . $file,'Read mnd');
        }


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
