<?php
require_once "emswinddb.php";
require_once "emsptudb.php";

class Zend_Controller_Action_Helper_GetEMS extends Zend_Controller_Action_Helper_Abstract {
    protected $_db;

    public function getems($local, $dthr) {
		$url1 = "https://192.168.1.40:33349";

		$local = 1;
		$fim = $dthr;

		$log = 'Log de solicitação de dados da EMS ' . chr(13) . chr(13) . 'Solicitando dados WIND ' . chr(13) . chr(13);

		$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		// verifica a última observação gravada no banco de dados
		$emswinddb = new emswinddb();
		$dt = $emswinddb->lastwind($local); // formato YYYY-MM-DD HH:MM
		if (empty($dt)) {
                        $inicio = substr($fim,0,8) . "T000001";
		} else {
			$inicio = substr($dt,0,4) . substr($dt,5,2) . substr($dt,8,2) . "T" . substr($dt,11,2)  . substr($dt,14,2) . "01";
		}
		$url = $url1 . "/ems/wind?from=" . $inicio. "Z&to=" . substr($fim,0,8) . "T" . substr($fim,8,6) . "59Z";
		$log .=  $url . chr(13) ;
		try {
			$xml = simplexml_load_file($url);
		} catch (Exception $e) {
			$log .= utf8_encode($e->getCode() . ': ' . $e->getMessage()) . chr(13);
		}
		if(!empty($xml)) {
			foreach ($xml->Measurements as $obs){
				$sql = "INSERT INTO ems_wind VALUES" ;
				$sql .="(DEFAULT, $local,' ";
				$sql .= str_replace('T', ' ', $obs->timestamp) . "', ";
				$sql .= "'".$obs->runway . "', ";
				$sql .= $obs->wsins . ", ";
				$sql .= $obs->ws2a . ", ";
				$sql .= $obs->ws2m . ", ";
				$sql .= $obs->ws2x . ", ";
				$sql .= $obs->ws10a . ", ";
				$sql .= $obs->ws10m . ", ";
				$sql .= $obs->ws10x . ", ";
				$sql .= $obs->wdins . ", ";
				$sql .= $obs->wd2a . ", ";
				$sql .= $obs->wd2m . ", ";
				$sql .= $obs->wd2x . ", ";
				$sql .= $obs->wd10a . ", ";
				$sql .= $obs->wd10m . ", ";
				$sql .= $obs->wd10x . ", ";
				$sql .= $obs->wdvar;
				$sql .= "), ";
				$sql = rtrim($sql, ", ");
				try {
					$this->_db->query($sql);
				} catch (Exception $e) {
					$log .= utf8_encode($e->getCode() . ': ' . $e->getMessage()) . chr(13);
				}
				$sql = '';
			}
		} else {
			$log .= "Não houve retorno de dados." . chr(13);
		}
		// Le dados da PTU
		$log .= chr(13) . chr(13) . 'Solicitando dados PTU ' . chr(13) . chr(13);
		// verifica a última observação gravada no banco de dados
		$emsptudb = new emsptudb();
		$dt = $emsptudb->lastptu($local);
        $url = $url1 . "/ems/ptu?from=" . $inicio . "Z&to=" . substr($fim,0,8) . "T" . substr($fim,8,6) . "59Z";
        $log .=  $url . chr(13) ;
		try {
			$xml = simplexml_load_file($url);
		} catch (Exception $e) {
			$log .= utf8_encode($e->getCode() . ': ' . $e->getMessage()) . chr(13);
		}
        if(!empty($xml)) {
			foreach ($xml->Measurements as $obs){
				$sql = "INSERT INTO ems_ptu VALUES" ;
				$sql .="(DEFAULT, $local,' ";
				$sql .= str_replace('T', ' ', $obs->timestamp) . "', ";
				$sql .= "'".$obs->runway . "', ";
				$sql .= $obs->pa_ins . ", ";
				$sql .= $obs->pa_qnh . ", ";
				$sql .= $obs->pa_qfe . ", ";
				$sql .= $obs->th_tt . ", ";
				$sql .= $obs->th_uu . ", ";
				$sql .= $obs->th_td . ", ";
				$sql .= $obs->th_tt_2 . ", ";
				$sql .= $obs->th_uu_2 . ", ";
				$sql .= $obs->th_td_2 . ", ";
				$sql .= $obs->ground_t . ", ";
				$sql .= $obs->ground_t_2;
				$sql .= ") ";
				$sql = rtrim($sql, ", ");
				try {
					$this->_db->query($sql);
				} catch (Exception $e) {
					$log .= utf8_encode($e->getCode() . ': ' . $e->getMessage()) . chr(13);
				}
				$sql = '';
			}
		} else {
			$log .= "Não houve retorno de dados." . chr(13);
		}

		$this->gravarlog('info', $log, 'EMS');

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
