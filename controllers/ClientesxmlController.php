<?php

class ClientesxmlController extends Zend_Controller_Action {

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
        $this->_session = new Zend_Session_Namespace("clixml");
        unset($this->_session->token);
        unset($this->_session->fieldName);

        if (!$this->_admin) {
            $this->_redirect('login/logout');
        }
        Zend_Loader::loadFile('clientesxmldb.php');
    }

    public function indexAction() {
        $clientesxmldb = new clientesxmldb();
        $this->view->clientesxmlData = $clientesxmldb->getAll();
    }

    public function cadAction() {

        // inicializa variáveis para validação do formulário
        $flag = true;
        $this->view->errorMsg = "";
        $this->view->pista = json_decode('[{"id":"09","descri":"Pista 09"},{"id":"27","descri":"Pista 27"}]', true);
        $this->view->tipo = json_decode('[{"id":"D","descri":"Diagnóstico"},{"id":"P","descri":"Prognóstico"},{"id":"A","descri":"Alertas"},{"id":"G","descri":"Geral (Dados Derivados)"}]', true);
        
        $clientesxmldb = new clientesxmldb();

        $id = $this->_getParam("id", 1);

        if (!is_numeric($id)) {
            $this->view->errorMsg .= "Parametro [id] incorreto $id.<br>";
            $flag = false;
        }

        $request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {

            // valida token do formulário
            $token = $this->_session->token;
            $fieldName = $this->_session->fieldName;
            $formtoken = htmlentities($request->getPost('$fieldName'), ENT_QUOTES, 'UTF-8');

            if ($formtoken <> $token) {
                $this->view->errorMsg .= "Token incorreto.<br>$token";
                $flag = false;
            }

            $fd = $this->getRequest()->getPost();

            // valida dados do formulário

            $fd['identificacao'] = htmlspecialchars($fd['identificacao']);
            $fd['url'] = htmlspecialchars($fd['url']);

            if (empty($fd['identificacao'])) {
                $this->view->errorMsg .= "O campo [Identificação] não pode ser vazio.<br>";
                $flag = false;
            }
            if (empty($fd['url'])) {
                $this->view->errorMsg .= "O campo [URL] não pode ser vazio.<br>";
                $flag = false;
            }
            if ($fd['tipo']<>'D' && $fd['tipo']<>'A' && $fd['tipo']<>'P' && $fd['tipo']<>'G' && $fd['tipo']<>'M') {
                $this->view->errorMsg .= "O campo [Tipo] deve ser D = Diagnóstico, A = Alertas, P = Prognóstico ou G = Geral.<br>";
                $flag = false;
            }
            if ($fd['pista']<>'09' && $fd['pista']<>'27') {
                $this->view->errorMsg .= "O campo [Pista] deve ser 09 ou 27.<br>";
                $flag = false;
            }
            if (strlen($fd['identificacao']) > 45) {
                $this->view->errorMsg .= "O campo [Identificação] deve ter no máximo 45 caracteres.<br>";
                $flag = false;
            }
            if (strlen($fd['url']) > 120) {
                $this->view->errorMsg .= "O campo [URL] deve ter no máximo 120 caracteres.<br>";
                $flag = false;
            }
            if (!Zend_Uri::check($fd['url'])) {
                $this->view->errorMsg .= "O campo [URL] deve indicar uma URL válida.<br>";
                $flag = false;
            }

            // Inserindo / Update
            if (isset($fd['ativo'])) {
                $fd['ativo'] = 1;
            } else {
                $fd['ativo'] = 0;
            }
            if ($flag) {
                $data = [
                    'identificacao' => $fd['identificacao'],
                    'url' => $fd['url'],
                    'ativo' => $fd['ativo'],
                    'tipo' => $fd['tipo'],
                    'pista' => $fd['pista']
                ];
                $dbtable = 'clientesxml';
                try {
                    if ($id == 0) {
                        $this->_db->insert($dbtable, $data);
                        $id = $clientesxmldb->getLastId();
                        $this->view->errorMsg = "Registro incluído.";
                        $this->_helper->Logger->gravarlog('info', 'Cliente XML Inserido ID ' . $id, 'Clientes XML');
                    } else {
                        $clientesxmldb->update($data, "idclientesxml = $id");
                        $this->view->errorMsg = "Registro alterado.";
                        $this->_helper->Logger->gravarlog('info', 'Cliente XML Alterado ID ' . $id, 'Clientes XML');
                    }
                } catch (Exception $e) {
                    $this->view->errorMsg = "Registro não incluído.<br>" . utf8_encode($e->getMessage());
                    $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Clientes XML');
                }
            }
        }
        $this->view->clientesxmlData = $clientesxmldb->getClientexml($id);
        $this->view->id = $id;
        $this->view->mypista = $this->view->clientesxmlData['pista'];
        $this->view->mytipo = $this->view->clientesxmlData['tipo'];

        // gera fieldName e token
        $this->view->fieldName = "mytkn" . substr(hash('sha256', rand(10, 9999)), 5, 5);
        $this->view->token = hash('sha256', rand(10, 9999999));
        $this->_session->fieldName = $this->view->fieldName;
        $this->_session->token = $this->view->token;
        $this->view->sessiontkn = $this->_session->token;
        $this->view->sessionfld = $this->_session->fieldName;
    }

    public function excluirAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        $id = $this->_getParam("id");

        if ($id == 0 || ! is_numeric($id)) {
            $this->_redirect('login/logout');
        }

        $dbtable = "clientesxml";
        $where = "idclientesxml=$id";
        try {
            $this->_db->delete($dbtable, $where);
            $this->view->errorMsg = 'Registro excluído!';
            $this->_helper->Logger->gravarlog('info', 'Cliente XML Excluído ID ' . $id, 'Clientes XML');
        } catch (Exception $e) {
            $this->view->errorMsg = 'O registro não pode ser excluído!<br>' . utf8_encode($e->getMessage());
            $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Clientes XML');
        }
        $this->_redirect("clientesxml");
    }

}
