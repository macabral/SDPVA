<?php

require 'LogDB.php';

class AdminController extends Zend_Controller_Action {

    protected $_db;
    protected $_admin;

    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('login');
        }
    }

    public function init() {
        /* Initialize action controller here */
        $this->_admin = Zend_Auth::getInstance()->getIdentity()->admin;
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        if (!$this->_admin) {
            $this->_redirect('login/logout');
        }
    }

    public function indexAction() {
        // action body
        $this->_redirect("/");
    }

    public function sendxmlfileAction() {
        $this->_helper->Logger->gravarlog('info', 'Enviar Arquivo XML', 'Service');
        $url = APP_HOST . '/SDPVA/services/sendxml';
        $this->_redirect($url);
        //$client = new Zend_Rest_Client($url);
    }

    public function logAction() {

        $this->_helper->Logger->gravarlog('info', 'Exibir log.', 'Log');
        $dt = new Zend_Date();
        $dt1 = $dt->toString("YYYY-MM-dd");
        $dt2 = $dt->toString("YYYY-MM-dd");
        $idtipo = "TODOS";

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
            $idtipo = $request->getPost('idtipo');
            if ($idtipo <> "TODOS" && $idtipo <> "INFO" && $idtipo <> "ERR" && $idtipo <> "WARN") {
                $idtipo = "TODOS";
            }
            $dt1 = $dt1->get('YYYY-MM-dd');
            $dt2 = $dt2->get('YYYY-MM-dd');
        }

        // montar filtro
        $filtro = "datalog between '$dt1 00:00:00' and '$dt2 23:59:59'";
        if ($idtipo <> "TODOS") {
            $filtro .= " and lvl='$idtipo'";
        }
        $log = new LogDB();
        $this->view->data = $log->getALLLog($filtro);
        $this->view->tipoData = json_decode('[{"idtipo":"TODOS","descri":"TODOS"},{"idtipo":"INFO","descri":"INFO"},{"idtipo":"ERR","descri":"ERR"},{"idtipo":"WARN","descri":"WARN"}]', true);
        $this->view->dt1 = $dt1;
        $this->view->dt2 = $dt2;
        $this->view->idtipo = $idtipo;
        $data = new Zend_Date(strtotime($dt1));
        $this->view->dt1 = $data->toString("dd/MM/yyyy");
        $data = new Zend_Date(strtotime($dt2));
        $this->view->dt2 = $data->toString("dd/MM/yyyy");
    }

    public function apagarlogAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();
        $this->_db->delete('log');
        $this->_helper->Logger->gravarlog('info', 'Log Apagado', 'Log');
        $this->_redirect("/admin/log");
    }

    public function exportarlogAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        $hoje = date("Y_m_j");
        $arquivo = 'SDPVA_ExportLog_' . $hoje . '.xls';

        $logdb = new LogDB();
        $logData = $logdb->getAllLog();
        $html = '';
        $html .= '<html>';
        $html .= '<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">';
        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<td>Data</td>';
        $html .= '<td>Módulo</td>';
        $html .= '<td>Evento</td>';
        $html .= '<td>Usuário</td>';
        $html .= '<td>IP</td>';
        $html .= '<td>Tipo</td>';
        $html .= '</tr>';
        foreach ($logData as $d) {
            $html .= '<tr>';
            $html .= '<td>' . $d['datalog'] . '</td>';
            $html .= '<td>' . $d['modulo'] . '</td>';
            $html .= '<td>' . $d['msg'] . '</td>';
            $html .= '<td>' . $d['username'] . '</td>';
            $html .= '<td>' . $d['userip'] . '</td>';
            $html .= '<td>' . $d['lvl'] . '</td>';
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
        $this->_helper->Logger->gravarlog('info', 'Log Exportado', 'Log');
    }
    public function limparbaseAction() {
        // esta função deve apagar os dados pretéritos
        // executar mensalmente.

        // apagar arquivos XML
        // SBGR
        // define pasta de armazenamento dos arquivos XML
        $data = new Zend_Date();
        $ano = $data->get(Zend_Date::YEAR);
        $mes = $data->get(Zend_Date::MONTH) - 3;
        if ($mes<=0) {
            $mes = (int) 12 + $mes;
            $ano = $ano - 1;
        }
        $anomes = $ano . str_pad($mes,2,"0",STR_PAD_LEFT);
        $msg = "";
        $this->view->anomes = $anomes;
        $request = $this->getRequest();
        if ($request->isPost()) {
            $pasta =  APP_XML . 'SBGR/alertas/09' ;
            $msg = "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            $pasta =  APP_XML . 'SBGR/alertas/27' ;
            $msg .=  "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            $pasta =  APP_XML . 'SBGR/diagnostico/09' ;
            $msg .=  "Limpando pasta " . $pasta . "<br \><br \>"; "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            $pasta =  APP_XML . 'SBGR/diagnostico/27' ;
            $msg .=  "Limpando pasta " . $pasta . "<br \><br \>"; "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            $pasta =  APP_XML . 'SBGR/geral' ;
            $msg .=  "Limpando pasta " . $pasta . "<br \><br \>"; "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            $pasta =  APP_XML . 'SBGR/prognostico' ;
            $msg .=  "Limpando pasta " . $pasta . "<br \><br \>"; "Limpando pasta " . $pasta . "<br \><br \>";
            if ($dh = opendir($pasta)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..' ) {
                       if (substr($file,0,6) < $anomes) {
                            unlink($pasta . "/" . $file) ;
                       }
                    }
                }
                closedir($dh);
            }
            
            $msg .=  "Fim de limpeza de dados.<br \><br \>";
        }
        
        $this->view->mensagem = $msg;
    }

}
