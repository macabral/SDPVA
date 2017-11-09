<?php

require_once 'parametrosdb.php';
require_once 'processodb.php';
require_once 'dadosderivadosdb.php';
require_once 'prognosticodb.php';
require_once 'saidarnadb.php';
require_once 'sodaratualdb.php';

class ProcessaController extends Zend_Controller_Action {

    protected $_limkt;
    protected $_limkt_330;
    protected $_prognosticodb;

    // CALCULO DO PROGNÓSTICO
    public function calcprognosticoAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        // calcula prognóstico e armazena em banco de dados
        // data do último processamento

        $processodb = new processodb();
        $processoData = $processodb->getProcessamento();
        $datap = $processoData['datahora'];

        $parametrosdb = new parametrosdb();
        $parametroData = $parametrosdb->getParametros();
        $this->_limkt = $parametroData['wind_int_limite_pista'];
        $this->_limkt_330 = $parametroData['wind_int_limite_330'];
        $prognosticodb = new Prognosticodb();
        $this->_prognosticodb = $prognosticodb;

        $pistaref = '09';

        // le dados da saída rna

        $rnadb = new saidarnadb();
        $saida = $rnadb->getSaida($datap);

        if (!empty($saida)) {

            // PREVISÃO PARA 15 MIN: SUPERFÍCIE
            $u = $saida['sup_u15'];
            $v = $saida['sup_v15'];
            $ws = $saida['sup_ws15'];
            $alt = "SUP";
            $hrprev = $this->arredonda_cima('00:15:00', $datap);
            $ordem = 1;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 15 MIN: 330
            $u = $saida['100_u15'];
            $v = $saida['100_v15'];
            $ws = $saida['100_ws15'];
            $alt = "330";
            $hrprev = $this->arredonda_cima('00:15:00', $datap);
            $ordem = 2;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 15 MIN: 660
            $u = $saida['200_u15'];
            $v = $saida['200_v15'];
            $ws = $saida['200_ws15'];
            $alt = "660";
            $hrprev = $this->arredonda_cima('00:15:00', $datap);
            $ordem = 3;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 15 MIN: 1000
            $u = $saida['300_u15'];
            $v = $saida['300_v15'];
            $ws = $saida['300_ws15'];
            $alt = "1000";
            $hrprev = $this->arredonda_cima('00:15:00', $datap);
            $ordem = 4;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 30 MIN: SUPERFICIE
            $u = $saida['sup_u30'];
            $v = $saida['sup_v30'];
            $ws =  $saida['sup_ws30'];
            $alt = "SUP";
            $hrprev = $this->arredonda_cima('00:30:00', $datap);
            $ordem = 5;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 30 MIN: 330
            $u = $saida['100_u30'];
            $v = $saida['100_v30'];
            $ws = $saida['100_ws30'];
            $alt = "330";
            $hrprev = $this->arredonda_cima('00:30:00', $datap);
            $ordem = 6;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 30 MIN: 660
            $u = $saida['200_u30'];
            $v = $saida['200_v30'];
            $ws = $saida['200_ws30'];
            $alt = "660";
            $hrprev = $this->arredonda_cima('00:30:00', $datap);
            $ordem = 7;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 30 MIN: 1000
            $u = $saida['300_u30'];
            $v = $saida['300_v30'];
            $ws =  $saida['300_ws30'];
            $alt = "1000";
            $hrprev = $this->arredonda_cima('00:30:00', $datap);
            $ordem = 8;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 45 MIN: SUPERFICIE
            $u = $saida['sup_u45'];
            $v = $saida['sup_v45'];
            $ws = $saida['sup_ws45'];
            $alt = "SUP";
            $hrprev = $this->arredonda_cima('00:45:00', $datap);
            $ordem = 9;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 45 MIN: 330
            $u = $saida['100_u45'];
            $v = $saida['100_v45'];
            $ws = $saida['100_ws45'];
            $alt = "330";
            $hrprev = $this->arredonda_cima('00:45:00', $datap);
            $ordem = 10;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 45 MIN: 660
            $u = $saida['200_u45'];
            $v = $saida['200_v45'];
            $ws = $saida['200_ws45'];
            $alt = "660";
            $hrprev = $this->arredonda_cima('00:45:00', $datap);
            $ordem = 11;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // PREVISÃO PARA 45 MIN: 1000
            $u = $saida['300_u45'];
            $v = $saida['300_v45'];
            $ws =  $saida['300_ws45'];
            $alt = "1000";
            $hrprev = $this->arredonda_cima('00:45:00', $datap);
            $ordem = 12;
            $this->salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem);

            // seleção de pista
            $progData = $this->_prognosticodb->getAllPrognostico($datap);
            foreach($progData as $d) {
                $pista = trim($d['pista_sug']);
                $id = $d['id'];
                $obs = "";
                // se não foi possível determinar a pista em uso em SUP verifica o nível 330
                if ($pista == 'XX') {
                    $pista = trim($this->_prognosticodb->getPistaSug($datap,$d['hr_prev'],"330"));
                    // se a pista sugerida no nível 330 continuar indefinida (XX) colocar obs 2 - Utililizar pista em operação.
                    if ($pista == 'XX') {
                        $obs = "(2)";
                    } else {
                        // observação 1 - pista sugerida baseada no nível 330.
                        $obs = "(1)";
                    }
                    $data = [
                        'pista_sug' => $pista,
                        'obs' => $obs,
                    ];
                    $this->_prognosticodb->update($data,"id=$id");
                } 

            }
            
            // gera XML

            $this->geraxml($datap);

            $this->_helper->Logger->gravarlog('info', 'Prognóstico Processado.', 'Prognóstico');
        }
    }

    private function arredonda_cima($timestring, $data) {
        $nDate = new Zend_Date($data, Zend_Date::ISO_8601);
        $newDate = $nDate->add($timestring, Zend_Date::TIMES);
        return $newDate->toString('HH:mm');
    }

    private function arredonda_baixo($timestring, $data) {
        $nDate = new Zend_Date($data, Zend_Date::ISO_8601);
        $newDate = $nDate->add($timestring, Zend_Date::TIMES);
        return $newDate->toString('hh:mm');
     }

    private function geraxml($datap) {

        $progData = $this->_prognosticodb->getAllPrognostico($datap);
	
	$datap = substr($datap,0,10) . "T" . substr($datap,11,8);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<sdpva pista="09" tipo="P" datahora="' . $datap . '" local="SBGR">';
        $hora = '';
        $flag = true;
        foreach($progData as $d) {
           
            if ($hora <> $d['hr_prev'] && !$flag) {
                $xml .= '</previsao>';
                $flag = true;
            }
            if ($hora <> $d['hr_prev']) {
                $xml .= '<previsao pistasug="' . trim($d['pista_sug'] . " " .$d['obs'] ) . '" hora="' . substr($d['hr_prev'],0,5) . ':00">';
                $hora = $d['hr_prev'];
                $flag = false;
            }
            
            $xml .= '<valores>';
            $xml .= '<altura>' . $d['z'] . '</altura>';
            $xml .= '<vetorkt>' . round($d['vetor_kt'],1) . '</vetorkt>';
            $xml .= '<vetorgraus>' . $d['vetor_grau'] . '</vetorgraus>';
            $xml .= '<proa>' . $d['proa'] . '</proa>';
            $xml .= '<cauda>' . $d['cauda']. '</cauda>';
            $xml .= '<traves_esq>' . $d['traves_esq'] . '</traves_esq>';
            $xml .= '<traves_dir>' . $d['traves_dir']. '</traves_dir>';
            $xml .= '<windshear>' .  $d['windshear'] . '</windshear>';
            $xml .= '</valores>';
            
        }
            
        $xml .= '</previsao></sdpva>';
        $arqdata = substr($datap,0,4) . substr($datap,5,2) . substr($datap,8,2) .  substr($datap,11,2) . substr($datap,14,2);
        $filename = $arqdata . ".xml";
        $arq = APP_XML . 'SBGR/prognostico/' . $filename;
        try {
            $hc = fopen($arq, 'wb');
            fwrite($hc, $xml);
            fclose($hc);
        } catch (Exception $e) {
            $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
            
    }
    
    public function salvaprev($u, $v, $datap, $hrprev, $alt, $ws, $ordem) {
            
        $vetorkt =  sqrt(pow($u,2) + pow($v,2));
        $vetorgrau = (270-(atan2($v,$u)*57.2958))%360;
        
        $pista = 'XX';
        if ($alt <> '330') {
            if ( $u > $this->_limkt) {
                   $pista = '27';
            } elseif ($u < ($this->_limkt * -1)) {
                    $pista = '09';
            }
        } else {
            if ( $u > $this->_limkt_330) {
                   $pista = '27';
            } elseif ($u < ($this->_limkt_330 * -1)) {
                    $pista = '09';
            }
        }
        //if ($pista=='09' || $pista=='XX'){
            if ($u > 0){
                $proa = '0';
                $cauda = number_format(abs($u), 1);
            } else {
                $proa = number_format(abs($u), 1);
                $cauda = '0';
            }
            if ($v > 0) {
                $traves_esq = '0';
                $traves_dir = number_format(abs($v), 1);
            } else {
                $traves_dir = '0';
                $traves_esq = number_format(abs($v), 1);
            }
        //} elseif ($pista == '27') {
        //    if ($u < 0){
        //        $proa = '0';
        //        $cauda = number_format($u*-1, 1);
        //    } else {
        //        $proa = number_format($u, 1);
        //        $cauda = '0';
        //    }
        //    if ($v < 0) {
        //        $traves_esq = '0';
        //        $traves_dir = number_format($v*-1, 1);
        //    } else {
        //        $traves_dir = '0';
        //        $traves_esq = number_format($v, 1);
        //    }
        //}
       
        // armazena na tabela prognostico
	if ($vetorgrau==0) {
		$vetorgrau = 360;
	}

        $data = [
            'datahora' => $datap,
            'pistaref' => '09',
            'hr_prev' => $hrprev,
            'pista_sug' => $pista,
            'z' => $alt,
            'vetor_kt' => $vetorkt,
            'vetor_grau' => $vetorgrau,
            'proa' => (float) $proa,
            'cauda' => (float) $cauda,
            'traves_esq' => (float) $traves_esq,
            'traves_dir' => (float) $traves_dir,
            'windshear' => (float) round($ws, 2),
            'ordem' => $ordem,
            'z_metros' => 0,
            'u' => $u,
            'v' => $v,
        ];
        try {
            $this->_prognosticodb->insert($data);
        } catch (Exception $e) {
            $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Processamento');
        }
    }
    
}
