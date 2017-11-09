<?php
require_once 'sodaratualdb.php';
require_once 'processodb.php';
require_once 'ensodardb.php';
require_once 'enemsdb.php';
require_once 'emswinddb.php';
require_once 'entradarnadb.php';

class Zend_Controller_Action_Helper_Entradarna extends Zend_Controller_Action_Helper_Abstract {

	protected $_db;

    public function entradarna($local,$dt) {
        // processa dados para gerar o arquivo de entrada da RNA
		// Pegando a ultima data processada

		$this->_db = Zend_Db_Table::getDefaultAdapter();

		$processodb = new processodb();
		$result_data = $processodb->getProcData();

 		$datahr = substr($dt,0,4) . "-" . substr($dt,4,2) . "-" . substr($dt,6,2) . " " . substr($dt,8,2) . ":" . substr($dt,10,2);

		//$datahr = $result_data['datahora'];
		$sem = new DateTime($datahr);
		$semana = $sem->format('W');

		$hora = (int) substr($result_data['hora'],0,2);

		// SODAR
		$sodaratualdb = new sodaratualdb();
		$result = $sodaratualdb->getData($datahr);

		if (empty($result)) {
			// quando n�o achar dados SODAR usar dados default
			$result = $sodaratualdb->getData("9999-12-31 00:00");
		}

		// ensemble
		$ensodar = new ensodardb();
		$enData = $ensodar->getensemble($semana,$hora);


		       
        $log = 'Geração dos arquivos de entrada para a RNA' . chr(13);

        $ct = 0;

		foreach($result as $d) {

			// flag para determinar se vai utilizar o ensemble por ausência de valor ou por estar fora dos limites
			$fsigw = 0;
			$ftke = 0;
			$fedr = 0;
			$fspeed = 0;
			$fu = 0;
			$fv= 0;

			// valida limites e presença de valor
			if ($d['tke'] > 20 || $d['tke'] == 99.999) {
				$ftke = 1;
                $log .= ' | z=' . $d['z'] . ' tke=' . $d['tke'];
                $ct++;
			}
			if ($d['edr'] > 1 || $d['edr'] == 99.99999) {
				$fedr = 1;
                $log .= ' | z=' . $d['z'] . ' edr=' . $d['edr'];
                $ct++;
			}
			if ($d['speed'] > 30 || $d['speed'] == 99.99) {
				$fspeed = 1;
                $log .= ' | z=' . $d['z'] . ' speed=' . $d['speed'];
                $ct++;
			}
			if ($d['sigw'] < 0.001 || $d['sigw'] > 4 || $d['sigw'] == 99.99) {
				$fsigw = 1;
                $log .=  ' | z=' . $d['z'] . ' sigw=' . $d['sigw'];
                $ct++;
			}
			if ( abs($d['u']) > 30 || $d['u'] == 99.99) {
				$fu = 1;
                $log .= ' | z=' . $d['z'] . '> u=' . $d['u'];
                $ct++;
			}
			if ( abs($d['v']) > 30 || $d['v'] == 99.99) {
				$fv = 1;
                $log .= ' | z=' . $d['z'] . ' v=' . $d['v'] . "\n";
                $ct++;
			}

			// atribui valores para as variáveis
			if ($d['z'] == 30) {
				$sigw30 = ($fsigw ? $enData['sigw30'] : $d['sigw']);
				$tke30 = ($ftke ? $enData['tke30'] : $d['tke']);
				$edr30 = ($fedr ? $enData['edr30'] : $d['edr']);
				$u30 = ($fu ? $enData['u30'] : $d['u']);
				$v30 = ($fv ? $enData['v30'] : $d['v']);
				$speed30 = ($fspeed ? $enData['speed30'] : $d['speed']);
			} elseif ($d['z'] == 60) {
				$speed60 = ($fspeed ? $enData['speed60'] : $d['speed']);
				$sigw60 = ($fsigw ? $enData['sigw60'] : $d['sigw']);
				$tke60 = ($ftke ? $enData['tke60'] : $d['tke']);
				$edr60 = ($fedr ? $enData['edr60'] : $d['edr']);
				$u60 = ($fu ? $enData['u60'] : $d['u']);
				$v60 = ($fv ? $enData['v60'] : $d['v']);
			} elseif ($d['z'] == 90) {
				$speed90 = ($fspeed ? $enData['speed90'] : $d['speed']);
				$sigw90 = ($fsigw ? $enData['sigw90'] : $d['sigw']);
				$tke90 = ($ftke ? $enData['tke90'] : $d['tke']);
				$edr90 = ($fedr ? $enData['edr90'] : $d['edr']);
				$u90 = ($fu ? $enData['u90'] : $d['u']);
				$v90 = ($fv ? $enData['v90'] : $d['v']);
			} elseif ($d['z'] == 120) {
				$speed120 = ($fspeed ? $enData['speed120'] : $d['speed']);
				$sigw120 = ($fsigw ? $enData['sigw120'] : $d['sigw']);
				$tke120 = ($ftke ? $enData['tke120'] : $d['tke']);
				$edr120 = ($fedr ? $enData['edr120'] : $d['edr']);
				$u120 = ($fu ? $enData['u120'] : $d['u']);
				$v120 = ($fv ? $enData['v120'] : $d['v']);
			} elseif ($d['z'] == 150) {
				$speed150 = ($fspeed ? $enData['speed150'] : $d['speed']);
				$sigw150 = ($fsigw ? $enData['sigw150'] : $d['sigw']);
				$tke150 = ($ftke ? $enData['tke150'] : $d['tke']);
				$edr150 = ($fedr ? $enData['edr150'] : $d['edr']);
				$u150 = ($fu ? $enData['u150'] : $d['u']);
				$v150 = ($fv ? $enData['v150'] : $d['v']);
			} elseif ($d['z'] == 180) {
				$speed180 = ($fspeed ? $enData['speed180'] : $d['speed']);
				$sigw180 = ($fsigw ? $enData['sigw180'] : $d['sigw']);
				$tke180 = ($ftke ? $enData['tke180'] : $d['tke']);
				$edr180 = ($fedr ? $enData['edr180'] : $d['edr']);
				$u180 = ($fu ? $enData['u180'] : $d['u']);
				$v180 = ($fv ? $enData['v180'] : $d['v']);
			} elseif ($d['z'] == 210) {
				$speed210 = ($fspeed ? $enData['speed210'] : $d['speed']);
				$sigw210 = ($fsigw ? $enData['sigw210'] : $d['sigw']);
				$tke210 = ($ftke ? $enData['tke210'] : $d['tke']);
				$edr210 = ($fedr ? $enData['edr210'] : $d['edr']);
				$u210 = ($fu ? $enData['u210'] : $d['u']);
				$v210 = ($fv ? $enData['v210'] : $d['v']);
			} elseif ($d['z'] == 240) {
				$u240 = ($fu ? $enData['u240'] : $d['u']);
				$v240 = ($fv ? $enData['v240'] : $d['v']);
                $speed240 = ($fspeed ? $enData['speed240'] : $d['speed']);
				$sigw240 = ($fsigw ? $enData['sigw240'] : $d['sigw']);
			} elseif ($d['z'] == 270) {
				$u270 = ($fu ? $enData['u270'] : $d['u']);
				$v270 = ($fv ? $enData['v270'] : $d['v']);
				$speed270 = ($fspeed ? $enData['speed270'] : $d['speed']);
			} elseif ($d['z'] == 300) {
				$u300 = ($fu ? $enData['u300'] : $d['u']);
				$v300 = ($fv ? $enData['v300'] : $d['v']);
				$speed300 = ($fspeed ? $enData['speed300'] : $d['speed']);
			}
		}

		// coletar dados pretéritos (15min) de U e V nível 100, 200 e 300
		$datap = strtotime('-15 minute', strtotime($datahr));
        $datap = date("Y-m-d H:i:s",$datap);
        $result = $sodaratualdb->getDataPret($datap);
        $log .= chr(13) . 'Data Pretérita ' . $datap . chr(13);

		$u100_15p = 0;
		$v100_15p = 0;
		$u200_15p = 0;
		$v200_15p = 0;
		$u300_15p = 0;
		$v300_15p = 0;

        foreach($result as $d) {
            $fu = 0;
            $fv = 0;

            // valida limites e presença de valor
			if ( abs($d['u']) > 30 || $d['u'] == 99.99) {
				$fu = 1;
                $log .= ' | z=' . $d['z'] . ' u=' . $d['u'];
                $ct++;
			}
			if ( abs($d['v']) > 30 || $d['v'] == 99.99) {
				$fv = 1;
                $log .= ' | z=' . $d['z'] . ' v=' . $d['v'] . "\n";
                $ct++;
			}
            if ($d['z'] == 100) {
				$u100_15p = ($fu ? $enData['u100'] : $d['u']);
                $v100_15p = ($fv ? $enData['v100'] : $d['v']);
            } elseif ($d['z'] == 200) {
				$u200_15p = ($fu ? $enData['u200'] : $d['u']);
                $v200_15p = ($fv ? $enData['v200'] : $d['v']);
            } elseif ($d['z'] == 300) {
				$u300_15p = ($fu ? $enData['u300'] : $d['u']);
                $v300_15p = ($fv ? $enData['v300'] : $d['v']);
            }

        }

	// coletar dados de meteorologia
        $emswinddb = new emswinddb();
        $emsData = $emswinddb->getrna($datahr);

        // le valores do ensemble
        $enemsdb  = new enemsdb();
        $enems09R = $enemsdb->getensemble($semana,$hora,'09R');
        $enems27R = $enemsdb->getensemble($semana,$hora,'27R');
        $enems09L = $enemsdb->getensemble($semana,$hora,'09L');
        $enems27L = $enemsdb->getensemble($semana,$hora,'27L');

        $ws2a_09R = 0;
        $ws2a_27R = 0;
        $ws2a_09L = 0;
        $ws2a_27L = 0;
        $wd2a_09R = 0;

        foreach($emsData as $d) {
			$fws2a = 0;
			$fwd2a = 0;
            // valida limites
			if ($d['ws2a'] > 40 || $d['ws2a'] < 0) {
				$fws2a = 1;
                $log .= ' | ws2a=' . $d['ws2a'] ;
                $ct++;
			}
            if ($d['wd2a'] > 360 || $d['wd2a'] < 0) {
				$fwd2a = 1;
                $log .= ' | wd2a=' . $d['wd2a'] ;
                $ct++;
			}
            if ($d['runway'] == '09R') {
                $ws2a_09R = ($fws2a ? $enems09R['ws2a'] : $d['ws2a']);
                $wd2a_09R = ($fwd2a ? $enems09R['wd2a'] : $d['wd2a']);
            } elseif ($d['runway'] == '27R') {
                $ws2a_27R = ($fws2a ? $enems27R['ws2a'] : $d['ws2a']);
            } elseif ($d['runway'] == '09L') {
                $ws2a_09L = ($fws2a ? $enems09L['ws2a'] : $d['ws2a']);
            } elseif ($d['runway'] == '27L') {
                $ws2a_27L = ($fws2a ? $enems27L['ws2a'] : $d['ws2a']);
            }
        }

		// coletar dados de speed_atual, media_ws, u_sup, v_sup
        // Media ponderada de media_ws e o dobro do pico_ws

		$media_ws = round(($ws2a_09R + $ws2a_27R + $ws2a_09L + $ws2a_27L)/4,2);
        $pico_ws = $ws2a_09R;
        if ($ws2a_09L > $pico_ws) {
            $pico_ws=$ws2a_09L;
        }
        if ($ws2a_27R > $pico_ws) {
            $pico_ws=$ws2a_27R;
        }
        if ($ws2a_27L > $pico_ws) {
            $pico_ws=$ws2a_27L;
        }

        $speed_atual = round(($media_ws + 2*$pico_ws)/3,2);
        $u_sup = round(-1 * ($ws2a_09R * cos(deg2rad(90 - ($wd2a_09R - 20)))),2);
        $v_sup = round(-1 * ($ws2a_09R * sin(deg2rad(90 - ($wd2a_09R - 20)))),2);
        
		$entrada1 = $sigw30 .  " " . $edr30  . " " . $speed60 . " " . $u60 . " " . $v60 . " " . $sigw60 . " " . $tke60 . " " . $edr60 . " "
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " .  $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
            . $u100_15p . " " . $ws2a_09R . " " . $ws2a_27R . " " . $media_ws . " " . $u_sup;
			
		$entrada2 = $sigw30 .  " " . $edr30  . " " . $speed60 . " " . $u60 . " " . $v60 . " " . $sigw60 . " " . $tke60 . " " . $edr60 . " "
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " .  $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
            . $v100_15p . " " . $ws2a_09R . " " . $ws2a_27R . " " . $media_ws . " " . $v_sup;

		$entrada3 = $u60 . " " . $v60 . " " . $sigw60 . " " . $edr60 . " "
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " .  $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $u240 . " "
            . $u100_15p . " " . $u_sup;
		
		$entrada4 = $u60 . " " . $v60 . " " . $sigw60 . " " . $edr60 . " "
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " .  $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $u240 . " "
            . $v100_15p . " " . $v_sup;
			
		$entrada5 = $sigw60 . " " 
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " "
            . $u100_15p . " " .  $u200_15p . " " .$u_sup;
			
		$entrada6 = $sigw60 . " " 
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " "
            . $v100_15p . " " .  $v200_15p . " " .$v_sup;
			
		$entrada7 = $sigw60 . " " 
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " "
			. $u270 . " " . $u300 . " "
            . $u100_15p . " " .  $u200_15p . " " .$u300_15p;
			
		$entrada8 = $sigw60 . " " 
			. $speed90 . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " "
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " "
			. $v270 . " " . $v300 . " "
            . $v100_15p . " " .  $v200_15p . " " .$v300_15p;
			
		
		$entrada9 = $speed30  . " " . $v30 . " " . $tke30 . " " . $edr30 . " "
			. $speed60  . " " . $v60 . " " . $sigw60 . " " . $edr60 . " "
			. $speed90  . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $tke90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $tke150 . " " 
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $tke210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " " . $sigw240 . " "
			. $speed270 . " " . $u270 . " " . $v270 . " "
			. $u300 . " " . $v300 . " "
			. $u_sup;
			
		$entrada10 = $speed30 . " " . $u30 . " " . $v30 . " " . $edr30 . " "
			. $speed60  . " " . $sigw60 . " " 
			. $speed90  . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $tke90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $tke150 . " " . $edr150 . " " 
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $tke210 . " " . $edr210 . " "
			. $speed240 . " " . $u240 . " " . $v240 . " " . $sigw240 . " "
			. $speed270 . " " . $u270 . " " . $v270 . " "
			. $speed300 . " " . $u300 . " " . $v300;

			
		$entrada11 =  $speed60  . " " . $sigw60 . " " 
			. $speed90  . " " . $u90 . " "  . $v90  . " " . $sigw90  . " " . $tke90  . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $edr120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $tke150 . " " . $edr150 . " " 
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $edr180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $tke210 . " " . $edr150 . " " 
			. $speed240 . " " . $u240 . " " . $v240 . " " . $sigw240 . " "
			. $speed270 . " " . $u270 . " " . $v270 . " "
			. $speed300 . " " . $u300 . " " . $v300;
			
		$entrada12 = $speed60 . " " . $u60  . " " . $v60 . " " . $sigw60 . " " . $edr60 . " " 
			. $speed90  . " " . $u90 . " " . $v90 . " " . $sigw90 . " " . $edr90 . " "
			. $speed120 . " " . $u120 . " " . $v120 . " " . $sigw120 . " " . $tke120 . " "
			. $speed150 . " " . $u150 . " " . $v150 . " " . $sigw150 . " " . $edr150 . " " 
			. $speed180 . " " . $u180 . " " . $v180 . " " . $sigw180 . " " . $tke180 . " " 
			. $speed210 . " " . $u210 . " " . $v210 . " " . $sigw210 . " " . $edr210 . " " 
			. $speed240 . " " . $u240 . " " . $v240 . " " . $sigw240 . " "
			. $speed270 . " " . $u270 . " " . $v270 . " "
			. $speed300 . " " . $u300 . " " . $v300;
			
			
        $log .= " => Qtd de ensemble: " . $ct;
	
		
		// grava arquivo de entrada para a RNA
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_1.txt", $entrada1);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_2.txt", $entrada2);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_3.txt", $entrada3);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_4.txt", $entrada4);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_5.txt", $entrada5);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_6.txt", $entrada6);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_7.txt", $entrada7);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_8.txt", $entrada8);
		
		// entradas para WS
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_9.txt", $entrada9);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_10.txt", $entrada10);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_11.txt", $entrada11);
		file_put_contents(APP_ROOT . "/RNA/entrada/entrada_rna_12.txt", $entrada12);
		
		// seta flag para processamento da RNA
		$data = array (
			'flag_RNA' => 1,
		);
		try {
			$processodb->update($data, "id=1");
		} catch (Exception $e) {
			$this->gravarlog('err', utf8_encode($e->getMessage()), 'RNA');
		}

		// grava dados de entrada da RNA na tabela entradarna
		$data = [
			'datahora' => $datahr,
			'hora' => $hora,
			'semana' => $semana,
			'speed_30' => $speed30,
			'u_30' => $u30,
			'v_30' => $v30,
			'sigw_30' => $sigw30,
			'tke_30' => $tke30,
			'edr_30' => $edr30,
			'speed_60' => $speed60,
			'u_60' => $u60,
			'v_60' => $v60,
			'sigw_60' => $sigw60,
			'tke_60' => $tke60,
			'edr_60' => $edr60,
			'speed_90' => $speed90,
			'u_90' => $u90,
			'v_90' => $v90,
			'sigw_90' => $sigw90,
			'tke_90' => $tke90,
			'edr_90' => $edr90,
			'speed_120' => $speed120,
			'u_120' => $u120,
			'v_120' => $v120,
			'sigw_120' => $sigw120,
			'tke_120' => $tke120,
			'edr_120' => $edr120,
			'speed_150' => $speed150,
			'u_150' => $u150,
			'v_150' => $v150,
			'sigw_150' => $sigw150,
			'tke_150' => $tke150,
			'edr_150' => $edr150,
			'speed_180' => $speed180,
			'u_180' => $u180,
			'v_180' => $v180,
			'sigw_180' => $sigw180,
			'tke_180' => $tke180,
			'edr_180' => $edr180,
			'speed_210' => $speed210,
			'u_210' => $u210,
			'v_210' => $v210,
			'sigw_210' => $sigw210,
			'tke_210' => $tke210,
			'edr_210' => $edr210,
			'speed_240' => $speed240,
			'u_240' => $u240,
			'v_240' => $v240,
			'sigw_240' => $sigw240,
			'speed_270' => $speed270,
			'u_270' => $u270,
			'v_270' => $v270,
			'speed_300' => $speed300,
			'u_300' => $u300,
			'v_300' => $v300,
			'u100_15p' => $u100_15p,
			'v100_15p' => $v100_15p,
			'u200_15p' => $u200_15p,
			'v200_15p' => $v200_15p,
			'u300_15p' => $u300_15p,
			'v300_15p' => $v300_15p,
			'ws2a_09R' => $ws2a_09R,
			'wd2a_09R' => $wd2a_09R,
			'ws2a_09L' => $ws2a_09L,
			'ws2a_27R' => $ws2a_27R,
			'ws2a_27L' => $ws2a_27L,
			'pico' => $pico_ws,
			'speed_atual' => $speed_atual,
			'media_ws' => $media_ws,
			'u_sup' => $u_sup,
			'v_sup' => $v_sup,
			'qtd_ensemble' => $ct,
			'log' => $log,
			
		];
		try {
			$this->_db->insert('entrada_rna', $data);
		} catch (Exception $e) {
			$this->gravarlog('err', utf8_encode($e->getMessage()), 'RNA');
		}
		
        $this->gravarlog('info', $log, 'RNA');
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
