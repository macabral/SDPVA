<?php

require 'alertasdb.php';
require 'dadosderivadosdb.php';
require 'parametrosdb.php';
require 'emsptudb.php';
require 'emswinddb.php';
require 'sodaratualdb.php';

class HistoricoController extends Zend_Controller_Action {

    protected $_db;
    protected $_admin;

    public function preDispatch() {
        
    }

    public function init() {
        /* Initialize action controller here */
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction() {
        $this->_redirect('historico/alertas');
    }

    public function alertasAction() {

        $this->_helper->Logger->gravarlog('info', 'Consulta Histórico de Alertas.', 'Historico');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");
        $dt2 = $dt->toString("YYYY-MM-dd");
        $pista = "TODAS";

        $request = $this->getRequest();
        if ($request->isPost()) {
            $valdate = new Zend_Validate_Date();

            $dt1 = htmlspecialchars($request->getPost('dt1'));
            $dt2 = htmlspecialchars($request->getPost('dt2'));

            if (!$this->_helper->Myhelper->valData($dt1)) {
                $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
            } else {
                $dt1 = new Zend_Date($dt1);
                if (!$valdate->isValid($dt1)) {
                    $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
                }
            }
            if (!$this->_helper->Myhelper->valData($dt2)) {
                $dt2 = new Zend_Date($dt);
            } else {
                $dt2 = new Zend_Date($dt2);
                if (!$valdate->isValid($dt2)) {
                    $dt2 = new Zend_Date($dt);
                }
            }
            $pista = $request->getPost('pista');
            if ($pista <> "09" && $pista <> "27" && $pista <> "TODAS") {
                $pista = "TODAS";
            }

            $dt1 = $dt1->get('YYYY-MM-dd');
            $dt2 = $dt2->get('YYYY-MM-dd');
        }
        // motar filtro
        $filtro = "datahora between '$dt1 00:00:00' and '$dt2 23:59:59'";
        if ($pista <> "TODAS") {
            $filtro .= " and pista='" . $pista . "'";
        }
        $log = new alertasdb();
        $this->view->data = $log->getALL($filtro);
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
        $data = new Zend_Date(strtotime($dt2));
        $this->view->dt2 = $data->toString("dd/MM/yyyy");
    }

    public function dadosderivadosAction() {

        $this->_helper->Logger->gravarlog('info', 'Consulta Histórico de Dados Derivados.', 'Historico');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");
        $dt2 = $dt->toString("YYYY-MM-dd");

        $request = $this->getRequest();
        if ($request->isPost()) {
            $valdate = new Zend_Validate_Date();

            $dt1 = htmlspecialchars($request->getPost('dt1'));
            $dt2 = htmlspecialchars($request->getPost('dt2'));

            if (!$this->_helper->Myhelper->valData($dt1)) {
                $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
            } else {
                $dt1 = new Zend_Date($dt1);
                if (!$valdate->isValid($dt1)) {
                    $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
                }
            }
            if (!$this->_helper->Myhelper->valData($dt2)) {
                $dt2 = new Zend_Date($dt);
            } else {
                $dt2 = new Zend_Date($dt2);
                if (!$valdate->isValid($dt2)) {
                    $dt2 = new Zend_Date($dt);
                }
            }
            $dt1 = $dt1->get('YYYY-MM-dd');
            $dt2 = $dt2->get('YYYY-MM-dd');
        }
        // motar filtro
        $filtro = "sodar_atual_data between '$dt1 00:00:00' and '$dt2 23:59:59'";
        $log = new dadosderivadosdb();
        $this->view->data = $log->getALL($filtro);
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
        $data = new Zend_Date(strtotime($dt2));
        $this->view->dt2 = $data->toString("dd/MM/yyyy");
    }
    
    public function arquivosaprunAction() {
        $localdb = new parametrosdb();
        $local = $localdb->getLocal();
        $this->view->local = $local;
        
        if (is_dir(APP_APRUN)) {
            if ($dh = opendir(APP_APRUN)) {
                while (($file = readdir($dh)) !== false) {
                    $ext = strtolower(strrchr($file, "."));
                    if ($file !== '.' && $file !== '..'  && $ext == '.mnd') {
                        //$size = filesize(APP_APRUN.$file);
                        $arqs[] = $file;
                     }
                }
                closedir($dh);
                rsort($arqs);
                $this->view->arquivos = $arqs;
            } 
        } 
        
        
    }
    
    public function emsptuAction() {

        $this->_helper->Logger->gravarlog('info', 'Consulta Histórico de EMS PTU.', 'Historico');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");

        $request = $this->getRequest();
        if ($request->isPost()) {
            $valdate = new Zend_Validate_Date();

            $dt1 = htmlspecialchars($request->getPost('dt1'));

            if (!$this->_helper->Myhelper->valData($dt1)) {
                $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
            } else {
                $dt1 = new Zend_Date($dt1);
                if (!$valdate->isValid($dt1)) {
                    $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
                }
            }
            $dt1 = $dt1->get('YYYY-MM-dd');
        }
        // motar filtro
        $filtro = "timestamp between '$dt1 00:00:00' and '$dt1 23:59:59'";
        $db = new emsptudb();
        $this->view->data = $db->getALL($filtro);
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
    }
 
    public function emswindAction() {

        $this->_helper->Logger->gravarlog('info', 'Consulta Histórico de EMS Wind.', 'Historico');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");
        $request = $this->getRequest();
        if ($request->isPost()) {
            $valdate = new Zend_Validate_Date();

            $dt1 = htmlspecialchars($request->getPost('dt1'));
            if (!$this->_helper->Myhelper->valData($dt1)) {
                $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
            } else {
                $dt1 = new Zend_Date($dt1);
                if (!$valdate->isValid($dt1)) {
                    $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
                }
            }
            $dt1 = $dt1->get('YYYY-MM-dd');
        }
        // motar filtro
        $filtro = "timestamp between '$dt1 00:00:00' and '$dt1 23:59:59'";
        $db = new emswinddb();
        
        $this->view->data = $db->getALL($filtro);

        $ret = $db->getProg($dt1);
        $txt1 = ''; $txt2 = ''; $txt3 = ''; $txt4 = ''; $txt5 =''; $txt6 = ''; $txt7 = ''; $txt8 = ''; $txt9 = ''; 
        foreach($ret as $d) {
			$txt1 .= "'".$d['ws2a']."',";
            $txt2 .= "'".$d['ws2m']."',";
            $txt3 .= "'".$d['ws2x']."',";
            $txt4 .= "'".$d['vetor_kt']."',";
            $txt5 .= "'".substr($d['hr_prev'],0,5)."',";
            $txt6 .= "'".$d['wd2a']."',";
            $txt7 .= "'".$d['wd2m']."',";
            $txt8 .= "'".$d['wd2x']."',";
            $txt9 .= "'".$d['vetor_grau']."',";
		}
		$this->view->smax = $txt3;
        $this->view->savg = $txt1;
        $this->view->smin = $txt2;
        $this->view->sprog = $txt4;
        $this->view->label = $txt5;
        
		$this->view->dmax = $txt8;
        $this->view->davg = $txt6;
        $this->view->dmin = $txt7;
        $this->view->dprog = $txt9;
        
        
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
    }

    public function diagxprogAction() {

        $this->_helper->Logger->gravarlog('info', 'Consulta Diagnóstico x Prognóstico.', 'Historico');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");

        $request = $this->getRequest();
        if ($request->isPost()) {
            $valdate = new Zend_Validate_Date();

            $dt1 = htmlspecialchars($request->getPost('dt1'));

            if (!$this->_helper->Myhelper->valData($dt1)) {
                $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
            } else {
                $dt1 = new Zend_Date($dt1);
                if (!$valdate->isValid($dt1)) {
                    $dt1 = new Zend_Date($dt, 'YYYY-MM-dd');
                }
            }
            $dt1 = $dt1->get('YYYY-MM-dd');
        }
       
        $db = new sodaratualdb();
        $this->view->data = $db->seldprog($dt1);
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
    }
    
    public function exportdiagxprogAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        $dt1 = $this->_getParam("data");

        $dt =  new Zend_Date($dt1, 'YYYY-MM-dd');
        $dt1 = $dt->get('YYYY-MM-dd');
        
        $db = new sodaratualdb();
        $datadb = $db->seldprog($dt1);
        
        $hoje = date("Y_m_j");
        $arquivo = 'SDPVA_ExportDiagProg_' . $hoje . '.xls';

        $html = '';
        $html .= '<html>';
        $html .= '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td>Data/Hora (SODAR)</td>';
        $html .= '<td>Data/Hora Prog</td>';
        $html .= '<td>HR Prog</td>';
        $html .= '<td>z</td>';
        $html .= '<td>speed (SODAR)</td>';
        $html .= '<td>vetor_kt (Prog)</td>';
        $html .= '<td>direcao (SODAR)</td>';
        $html .= '<td>vetor_grau (Prog)</td>';
        $html .= '</tr>';
                                      
        foreach ($datadb as $d) {
            $html .= '<tr>';
            $html .= '<td>' . $d['data_sodar'] . '</td>';
            $html .= '<td>' . $d['datahora'] . '</td>';
            $html .= '<td>' . $d['hr_prev'] . '</td>';
            $html .= '<td>' . $d['z'] . '</td>';
            $html .= '<td>' . $d['speed'] . '</td>';
            $html .= '<td>' . $d['vetor_kt'] . '</td>';
            $html .= '<td>' . $d['dir'] . '</td>';
            $html .= '<td>' . $d['vetor_grau'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table></html>';
        header("Pragma: public");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: pre-check=0, post-check=0, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: none");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=\"$arquivo\"");
        header("Content-Description: SDPVA");
        echo $html;
        $this->_helper->Logger->gravarlog('info', 'Exportação XML Diagnóstico x Prognóstico', 'Log');
    }
}
