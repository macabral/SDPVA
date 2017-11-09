<?php

require_once 'processodb.php';
require_once 'prognosticodb.php';
require_once 'parametrosdb.php';

class PrognosticoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // data do último processamento
        $processodb = new processodb();
        $processoData = $processodb->getProcessamento();
        $datap = $processoData['datahora'];

        $dth = $this->_getParam('dth');

        if ($dth<>'') {
            $datap = substr($dth,0,4) . "-" . substr($dth,4,2) . "-" . substr($dth,6,2) . " " . substr($dth,8,2) . ":" . substr($dth,10,2) . ":00";
        }

        // le dados da saída rna

        $pista="09";
        
        $this->view->pistaref = $pista;

        $prognosticodb = new prognosticodb();

        $this->view->LastPrev = $prognosticodb->getLastPrev();

        $parametrosdb = new parametrosdb();
        $parametroData = $parametrosdb->getParametros();
        $this->view->limproa = $parametroData['wind_int_alerta1_min'];
        $this->view->limcauda = $parametroData['wind_int_alerta1_max'];
        $this->view->limtraves = $parametroData['wind_int_alerta2_max'];
        $this->view->wind_shear_interv1_max = $parametroData['wind_shear_interv1_max'];
        $this->view->wind_shear_interv2_max = $parametroData['wind_shear_interv2_max'];
        $this->view->wind_shear_interv3_max = $parametroData['wind_shear_interv3_max'];


       // PREVISÃO PARA 15 MIN
        $hr_prev = $this->arredonda_cima('00:15:00',$datap);
        $this->view->prognosticoData15 = $prognosticodb->getPrognostico($datap,$pista,$hr_prev);
        $this->view->t15_previsaoHora = substr($hr_prev,0,5);
        $this->view->t15_pistaSugerida = $prognosticodb->getPistaSug2($datap,$pista,$hr_prev,"SUP");

        // PREVISÃO PARA 30 MIN

        $hr_prev = $this->arredonda_cima('00:30:00',$datap);
        $this->view->prognosticoData30 = $prognosticodb->getPrognostico($datap,$pista,$hr_prev);
        $this->view->t30_previsaoHora = substr($hr_prev,0,5);
        $this->view->t30_pistaSugerida =  $prognosticodb->getPistaSug2($datap,$pista,$hr_prev,"SUP");

        // PREVISÃO PARA 45 MIN

        $hr_prev = $this->arredonda_cima('00:45:00',$datap);
        $this->view->prognosticoData45 = $prognosticodb->getPrognostico($datap,$pista,$hr_prev);  
        $this->view->t45_previsaoHora = substr($hr_prev,0,5);
        $this->view->t45_pistaSugerida =  $prognosticodb->getPistaSug2($datap,$pista,$hr_prev,"SUP");
      
        $this->view->grafico = "barbela.png";
        $this->view->pistagr = "pista09.png";
        
        $this->view->datap =  substr($datap,8,2)  . "/" . substr($datap,5,2) . "/" . substr($datap,0,4). " " . substr($datap,11,2) . ":" .  substr($datap,14,2);

        $prox = $this->arredonda_cima("00:15:00",$datap);
        $ant = $this->arredonda_baixo("00:15:00",$datap);

        $this->view->ant = substr($datap,0,4) . substr($datap,5,2) . substr($datap,8,2)  . substr($ant,0,2) . substr($ant,3,2);
        $this->view->prox =  substr($datap,0,4) . substr($datap,5,2) . substr($datap,8,2)  .  substr($prox,0,2) . substr($prox,3,2);

    }

    private function arredonda_cima($timestring,$data) {
        $nDate = new Zend_Date($data,Zend_Date::ISO_8601);
        $newDate = $nDate->add($timestring,Zend_Date::TIMES);
        return $newDate->toString("HH:mm:ss");
    }
    
    private function arredonda_baixo($timestring,$data) {
        $nDate = new Zend_Date($data,Zend_Date::ISO_8601);
        $newDate = $nDate->sub($timestring,Zend_Date::TIMES);
        return $newDate->toString("HH:mm:ss");
    }

}
