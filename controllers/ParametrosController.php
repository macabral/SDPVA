<?php

class ParametrosController extends Zend_Controller_Action {

    protected $_logger;
    protected $_db;
    protected $_session;

    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('login');
        }
    }

    public function init() {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_admin = Zend_Auth::getInstance()->getIdentity()->admin;
        $this->_session = new Zend_Session_Namespace("param");
        unset($this->_session->token);
        unset($this->_session->fieldName);

        if (!$this->_admin) {
            $this->_redirect('login/logout');
        }
        Zend_Loader::loadFile('parametrosdb.php');
    }

    public function indexAction() {
        $this->_redirect('/parametros/cad');
    }

    public function cadAction() {

        // inicializa variáveis para validação do formulário
        $flag = true;
        $this->view->errorMsg = "";

        $parametrosdb = new parametrosdb();

        $request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {

            // valida token do formulário
            $token = $this->_session->token;
            $fieldName = $this->_session->fieldName;
            $formtoken = $request->getPost($fieldName);

           // if ($formtoken <> $token) {
           //     $this->view->errorMsg .= "Token incorreto.<br>";
           //     $flag = false;
           // }

            $fd = $request->getPost();

            // valida campos
            if (!is_numeric($fd['wind_var_proa'])) {
                $this->view->errorMsg .= "O campo [Variação de Proa] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_var_cauda'])) {
                $this->view->errorMsg .= "O campo [Variação de Cauda] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_var_traves'])) {
                $this->view->errorMsg .= "O campo [Variação de Través] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_alerta1_min'])) {
                $this->view->errorMsg .= "O campo [Alerta de Intensidade de Proa] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_alerta1_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Intensidade de Cauda] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_limite_pista'])) {
                $this->view->errorMsg .= "O campo [Velocidade Limite para Seleção de Pista] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_limite_330'])) {
                $this->view->errorMsg .= "O campo [Velocidade Limite para Seleção de Pista no nível 330] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_alerta2_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Intensidade de Través] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_int_valor_alt_critica_1']) || ! is_numeric($fd['wind_int_valor_alt_critica_2']) || ! is_numeric($fd['wind_int_valor_alt_critica_3']) || ! is_numeric($fd['wind_int_valor_alt_critica_4'])) {
                $this->view->errorMsg .= "O campo [Valor de Altitude Crítica] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_shear_interv1_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Windshear leve] deve ser numérico.<br>";
                $flag = false;
            } elseif (!($fd['wind_shear_interv1_max'] > 0 && $fd['wind_shear_interv1_max'] < 4)) {
                $this->view->errorMsg .= "O valor do campo [Alerta de Windshear leve] deve ser >0 e <4 kt por 100ft.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_shear_interv2_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Windshear moderado] deve ser numérico.<br>";
                $flag = false;
            } elseif (!($fd['wind_shear_interv2_max'] >= 4 && $fd['wind_shear_interv2_max'] < 8)) {
                $this->view->errorMsg .= "O valor do campo [Alerta de Windshear moderado] deve ser >=4 e <8 kt por 100ft.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_shear_interv3_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Windshear forte] deve ser numérico.<br>";
                $flag = false;
            } elseif (!($fd['wind_shear_interv3_max'] >= 8 && $fd['wind_shear_interv3_max'] <= 12)) {
                $this->view->errorMsg .= "O valor do campo [Alerta de Windshear forte] deve ser >=8 e <=12 kt por 100ft.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['wind_shear_interv4_max'])) {
                $this->view->errorMsg .= "O campo [Alerta de Windshear Severo] deve ser numérico.<br>";
                $flag = false;
            }
            if (!$fd['wind_shear_interv4_max'] > 12) {
                $this->view->errorMsg .= "O valor do campo [Alerta de Windshear Severo] deve ser >12 kt por 100ft.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['dir_pista'])) {
                $this->view->errorMsg .= "O campo [Direção da Pista] deve ser numérico.<br>";
                $flag = false;
            }
            if (!is_numeric($fd['d_mag'])) {
                $this->view->errorMsg .= "O campo [Declinação Magnética] deve ser numérico.<br>";
                $flag = false;
            }
            // salva dados
            if ($flag) {
                $data = [
                    'wind_var_proa' => $fd['wind_var_proa'],
                    'wind_var_cauda' => $fd['wind_var_cauda'],
                    'wind_var_traves' => $fd['wind_var_traves'],
                    'wind_int_alerta1_min' => $fd['wind_int_alerta1_min'],
                    'wind_int_alerta1_max' => $fd['wind_int_alerta1_max'],
                    'wind_int_alerta2_max' => $fd['wind_int_alerta2_max'],
                    'wind_int_nome_alt_critica_1' => $fd['wind_int_nome_alt_critica_1'],
                    'wind_int_valor_alt_critica_1' => $fd['wind_int_valor_alt_critica_1'],
                    'wind_int_nome_alt_critica_2' => $fd['wind_int_nome_alt_critica_2'],
                    'wind_int_valor_alt_critica_2' => $fd['wind_int_valor_alt_critica_2'],
                    'wind_int_nome_alt_critica_3' => $fd['wind_int_nome_alt_critica_3'],
                    'wind_int_valor_alt_critica_3' => $fd['wind_int_valor_alt_critica_3'],
                    'wind_int_nome_alt_critica_4' => $fd['wind_int_nome_alt_critica_4'],
                    'wind_int_valor_alt_critica_4' => $fd['wind_int_valor_alt_critica_4'],
                    'wind_shear_interv1_max' => $fd['wind_shear_interv1_max'],
                    'wind_shear_interv2_max' => $fd['wind_shear_interv2_max'],
                    'wind_shear_interv3_max' => $fd['wind_shear_interv3_max'],
                    'wind_shear_interv4_max' => $fd['wind_shear_interv4_max'],
                    'wind_int_limite_pista' => $fd['wind_int_limite_pista'],
                    'wind_int_limite_330' => $fd['wind_int_limite_330'],
                    'dir_pista' => $fd['dir_pista'],
                    'd_mag' => $fd['d_mag'],
                ];
                $parametrosdb->update($data, "idparametros = 1");
                $this->view->errorMsg = "Registro alterado.";
                $this->_helper->Logger->gravarlog('info', 'Parametro Alterado.', 'Parametros');
            }
        }

        $this->view->parametrosData = $parametrosdb->getParametros();

        // gera fieldName e token
        $this->view->fieldName = "mytkn" . substr(hash('sha256', rand(10, 9999)), 5, 5);
        $this->view->token = hash('sha256', rand(10, 9999999));
        $this->_session->fieldName = $this->view->fieldName;
        $this->_session->token = $this->view->token;
    }

}
