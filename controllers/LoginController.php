<?php

require_once 'UserDB.php';

class LoginController extends Zend_Controller_Action {

    protected $_db;
    protected $_session;

    public function init() {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->logoutAction();
        }
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        $this->_session = new Zend_Session_Namespace("sdpva");
    }

    public function indexAction() {

 
        $request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {
            // valida token do formulário
            $token = $this->_session->token;
            $fieldName = $this->_session->fieldName;
            
            $formfieldName = (string) $request->getPost($fieldName);
            $formtoken = htmlentities($formfieldName, ENT_QUOTES, 'UTF-8');
            
            if ($formtoken<>$token){
                $this->_redirect('/index');
            }
            
            $fusername = $request->getPost('username');
            $fsenha = $request->getPost('senha');
            
            // recupera o nome de usuário e senha do formulário de login
            $username = htmlentities($fusername, ENT_QUOTES, 'UTF-8');
            $password = htmlentities($fsenha, ENT_QUOTES, 'UTF-8');
            
            // Valida o formulário
            if (empty($username) || empty($password)){
                $this->_redirect('/index'); 
            }
            if ((strlen($username)>15 && strlen($username)<4) || (strlen($password)>15 && strlen($password)<6)){
                $this->_redirect('/index');
            }

            // Carrega o Auth
            $authAdapter = new Zend_Auth_Adapter_DbTable($this->_db, 'usuarios', 'login', 'senha', 'SHA1(?)');
            $authAdapter->setIdentity($username)->setCredential($password);
            $result = $authAdapter->authenticate();
            if ($result->isValid()) {
                $auth = Zend_Auth::getInstance();
                $data = $authAdapter->getResultRowObject(null, 'senha');
                $auth->getStorage()->write($data);

                // verifica se usuário está ativo
                // se não estiver ativo faz um logout
                $ativo = $auth->getIdentity()->ativo;
                if ($ativo == 0) {
                    $this->_helper->Logger->gravarlog('warn', 'Tentativa de Login de Usuário Inativo', 'Login');
                    $this->logoutAction();
                }

                $userId = $auth->getIdentity()->idusu;
                $this->_userid = $userId;
                
                if ($auth->getIdentity()->trocarsenha){
                    $this->_redirect('/user/trocarsenha');
                }

                // redireciona para a página principal
                $this->_helper->Logger->gravarlog('info', 'Login de Usuário', 'Login');
                $this->_redirect('/index');
            } 
        }
        
        // gera fieldName e token
        $this->view->fieldName = "mytkn".substr(hash('sha256',rand(10,9999)),5,5);
        $this->view->token = hash('sha256',rand(10,9999999));
        $this->_session->fieldName = $this->view->fieldName;
        $this->_session->token = $this->view->token;
    }

    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        session_destroy();
        $this->_redirect('/');
    }

}
