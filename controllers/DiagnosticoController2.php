<?php

require 'parametrosdb.php';
require 'processodb.php';
require 'dadosderivadosdb.php';
require 'alertasdb.php';

class DiagnosticoController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
// configurações de abertura
        $this->view->grafico = "barbela3h.png";
        $this->view->sel_barbela = "checked";
        $this->view->sel_seta = "";
        $this->view->sel_pista1 = "checked";
        $this->view->sel_pista2 = "";
        $this->view->sel_hr3 = "checked";
        $this->view->sel_hr6 = "";
        $this->view->sel_hr12 = "";
        $this->view->sel_hr24 = "";
        $this->view->pistagr = "pista09.png";
        $this->view->pista = "09";

        $request = $this->getRequest();

// Check if we have a POST request
        if ($request->isPost()) {
            $hr = $request->getPost('hora');
            $gr = $request->getPost('tipografico');
            $pista = $request->getPost('pista');

// verifica valores válidos
            if ($gr[0] <> 'barbela' && $gr[0] <> 'seta') {
                $this->_redirect('/');
            }
            if ($hr[0] <> 3 && $hr[0] <> 6 && $hr[0] <> 12 && $hr[0] <> 24) {
                $this->_redirect('/');
            }
            if ($pista[0] <> 9 && $pista[0] <> 27) {
                $this->_redirect('/');
            }

// seleção de tipo de gráfico e horário
            if ($hr[0] == 3) {
                if ($gr[0] == 'barbela') {
                    $this->view->grafico = "barbela3h.png";
                } else {
                    $this->view->grafico = "vetor3h.png";
                }
                $this->view->sel_hr3 = "checked";
                $this->view->sel_hr6 = "";
                $this->view->sel_hr12 = "";
                $this->view->sel_hr24 = "";
            }
            if ($hr[0] == 6) {
                if ($gr[0] == 'barbela') {
                    $this->view->grafico = "barbela6h.png";
                } else {
                    $this->view->grafico = "vetor6h.png";
                }
                $this->view->sel_hr3 = "";
                $this->view->sel_hr6 = "checked";
                $this->view->sel_hr12 = "";
                $this->view->sel_hr24 = "";
            }
            if ($hr[0] == 12) {
                if ($gr[0] == 'barbela') {
                    $this->view->grafico = "barbela12h.png";
                } else {
                    $this->view->grafico = "vetor12h.png";
                }
                $this->view->sel_hr3 = "";
                $this->view->sel_hr6 = "";
                $this->view->sel_hr12 = "checked";
                $this->view->sel_hr24 = "";
            }
            if ($hr[0] == 24) {
                if ($gr[0] == 'barbela') {
                    $this->view->grafico = "barbela24h.png";
                } else {
                    $this->view->grafico = "vetor24h.png";
                }
                $this->view->sel_hr3 = "";
                $this->view->sel_hr6 = "";
                $this->view->sel_hr12 = "";
                $this->view->sel_hr24 = "checked";
            }

// seleção de pista
            if ($pista[0] == 9) {
                $this->view->pista = "09";
                $this->view->pistagr = "pista09.png";
                $this->view->sel_pista1 = "checked";
                $this->view->sel_pista2 = "";
            } else {
                $this->view->pista = "27";
                $this->view->pistagr = "pista27.png";
                $this->view->sel_pista1 = "";
                $this->view->sel_pista2 = "checked";
            }

// seleçao de tipo de gráfico
            if ($gr[0] == 'barbela') {
                $this->view->sel_barbela = "checked";
                $this->view->sel_seta = "";
            } else {
                $this->view->sel_barbela = "";
                $this->view->sel_seta = "checked";
            }
        }

// verificar alertas
        // le parametros
        $paramdb = new parametrosdb();
        $paramData = $paramdb->getParametros();
        $this->view->local = $paramData['local'];

        // data do último processamento
        $processodb = new processodb();
        $processoData = $processodb->getProcessamento();
        $data = $processoData['datahora'];
        $data2 = new Zend_Date($data);
        $data2 = $data2->toString("dd/MM/yyyy - HH:mm");

        // le alertas
        $alertasdb = new alertasdb();
        $alertasData = $alertasdb->getAlerta($this->view->pista, $data);


        $this->view->dataProcesso = $data2;
        $this->view->VelocidadeCauda = $alertasData['velocidadecauda'];
        $this->view->VelocidadeProa = $alertasData['velocidadeproa'];
        $this->view->VelocidadeTravesEsquerdo = $alertasData['velocidadetravesesquerdo'];
        $this->view->VelocidadeTravesDireito = $alertasData['velocidadetravesdireito'];
        $this->view->VariacaoCauda = $alertasData['variacaocauda'];
        $this->view->VariacaoProa = $alertasData['variacaoproa'];
        $this->view->VariacaoTravesDir = $alertasData['variacaotravesdir'];
        $this->view->VariacaoTravesEsq = $alertasData['variacaotravesesq'];
        $this->view->Windshear = $alertasData['windshear'];
        $this->view->AlertaVelCauda = $alertasData['alertavelcauda'];
        $this->view->AlertaVelProa = $alertasData['alertavelproa'];
        $this->view->AlertaVelTE = $alertasData['alertavelte'];
        $this->view->AlertaVelTD = $alertasData['alertaveltd'];
        $this->view->AlertaVC = $alertasData['alertavc'];
        $this->view->AlertaVP = $alertasData['alertavp'];
        $this->view->AlertaVTD = $alertasData['alertavtd'];
        $this->view->AlertaVTE = $alertasData['alertavte'];
        $this->view->AlertaW = $alertasData['alertaw'];
    }

}
