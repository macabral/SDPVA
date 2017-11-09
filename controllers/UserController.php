<?php

class UserController extends Zend_Controller_Action {

    protected $_logger;
    protected $_db;
    protected $_idusu;
    protected $_session;

    public function preDispatch() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('login');
        }
    }

    public function init() {
        /* Initialize action controller here */

        $this->_admin = Zend_Auth::getInstance()->getIdentity()->admin;
        $this->_idusu = Zend_Auth::getInstance()->getIdentity()->idusu;
        $this->_db = Zend_Db_Table::getDefaultAdapter();
        if (!$this->_admin) {
            $this->_redirect('login/logout');
        }
        Zend_Loader::loadFile('UserDB.php');
        $this->_session = new Zend_Session_Namespace("cadcli");
        unset($this->_session->token);
        unset($this->_session->fieldName);
        unset($this->_session->usuid);
    }

    public function indexAction() {

        $userdb = new UserDB();
        $this->view->usuData = $userdb->getAllUsers();
    }

    public function caduserAction() {
        $flag = true;
        $this->view->errorMsg = "";

        $id = $this->_getParam("id", 0);

        // valida parâmetro do id do usuário
        if (!is_numeric($id)) {
            $this->view->errorMsg .= "Parametro id incorreto $id.<br>";
            $flag = false;
        }

        $this->_session->usuid = (int) $id;
        $userdb = new UserDB();

        $request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {

            // valida token do formulário
            $token = $this->_session->token;
            $fieldName = $this->_session->fieldName;
            $id = $this->_session->usuid;
            $formtoken = htmlentities($request->getPost('$fieldName'), ENT_QUOTES, 'UTF-8');

            if ($formtoken <> $token) {
                $this->view->errorMsg .= "Token incorreto.<br>";
                $flag = false;
            }

            // valida dados do formulário
            $fd = $request->getPost();
            $fd['nome'] = htmlspecialchars($fd['nome']);
            $fd['login'] = htmlspecialchars($fd['login']);
            $fd['email'] = htmlspecialchars($fd['email']);

            if (empty($fd['nome'])) {
                $this->view->errorMsg .= "O campo [Nome] não pode ser vazio.<br>" . $fd['nome'];
                $flag = false;
            }
            if (empty($fd['login'])) {
                $this->view->errorMsg .= "O campo [Login] não pode ser vazio.<br>";
                $flag = false;
            }
            $valEmail = new Zend_Validate_EmailAddress();
            if (!$valEmail->isValid($fd['email'])) {
                $this->view->errorMsg .= "O campo [email] não está no formato correto.<br>";
                $flag = false;
            }
            if (strlen($fd['login']) < 4 || strlen($fd['login']) > 15) {
                $this->view->errorMsg .= "O campo [Login] deve ter no mínimo 4 caracteres e no máximo 15.<br>";
                $flag = false;
            }
            if (strlen($fd['nome']) > 75) {
                $this->view->errorMsg .= "O campo [Nome] deve ter no máximo 75 caracteres.<br>";
                $flag = false;
            }
            if (strlen($fd['email']) > 65) {
                $this->view->errorMsg .= "O campo [Email] deve ter no máximo 65 caracteres.<br>";
                $flag = false;
            }
            // Inserindo 

            if (isset($fd['ativo'])) {
                $fd['ativo'] = 1;
            } else {
                $fd['ativo'] = 0;
            }
            if (isset($fd['admin'])) {
                $fd['admin'] = 1;
            } else {
                $fd['admin'] = 0;
            }
            if (isset($fd['trocarsenha'])) {
                $fd['trocarsenha'] = 1;
            } else {
                $fd['trocarsenha'] = 0;
            }
            if ($flag) {
                if ($id == 0) {
                    $data = [
                        'nome' => $fd['nome'],
                        'login' => $fd['login'],
                        'ativo' => $fd['ativo'],
                        'email' => $fd['email'],
                        'admin' => $fd['admin'],
                        'senha' => sha1("12345678"),
                        'trocarsenha' => 1,
                    ];
                } else {
                    $data = [
                        'nome' => $fd['nome'],
                        'login' => $fd['login'],
                        'ativo' => $fd['ativo'],
                        'email' => $fd['email'],
                        'admin' => $fd['admin'],
                        'trocarsenha' => $fd['trocarsenha'],
                    ];
                }

                $dbtable = 'usuarios';
                try {
                    if ($id == 0) {
                        $this->_db->insert($dbtable, $data);
                        $id = $userdb->getLastId();
                        $this->view->errorMsg = "Registro incluído.";
                        $this->_helper->Logger->gravarlog('info', 'Usuário cadastrado.', 'Cadastro de Usuários');
                    } else {
                        $userdb->update($data, "idusu = $id");
                        $this->view->errorMsg = "Registro alterado.";
                        $this->_helper->Logger->gravarlog('info', 'Usuário Alterado. Id = ' . $id, 'Cadastro de Usuários');
                    }
                } catch (Exception $e) {
                    $this->view->errorMsg = "Registro não incluído.<br>" . utf8_encode($e->getMessage());
                    $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Cadastro de Usuários');
                }
            }
        }
        $this->view->userData = $userdb->getUser($id);
        $this->view->id = $id;

        // gera fieldName e token
        $this->view->fieldName = "mytkn" . substr(hash('sha256', rand(10, 9999)), 5, 5);
        $this->view->token = hash('sha256', rand(10, 9999999));
        $this->_session->fieldName = $this->view->fieldName;
        $this->_session->token = $this->view->token;
    }

    public function excluirAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        $id = $this->_getParam("id", 1);

        if (!is_numeric($id) || $id == 0) {
            $this->_redirect('login/logout');
        }
        $userdb = new UserDB();
        $data = $userdb->getUser($id);
        if (!empty($data)) {
            $dbtable = "usuarios";
            $where = "idusu=$id";
            try {
                $this->_db->delete($dbtable, $where);
                $this->view->errorMsg = 'Registro excluído!';
                $this->_helper->Logger->gravarlog('info', 'Usuário Excluído Id = ' . $id . " [" . $data['login'] . "]", 'Cadastro de Usuários');
            } catch (Exception $e) {
                $this->view->errorMsg = 'O registro não pode ser excluído!<br>' . utf8_encode($e->getMessage());
                $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Cadastro de Usuários');
            }
        } else {
            $this->_helper->Logger->gravarlog('warn', 'Tentativa de Excluir Usuário não cadastrado. ID=' . $id, 'Cadastro de Usuários');
            $id = 0;
        }
        $this->_redirect("user");
    }

    public function resetsenhaAction() {
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_getParam("id", 1);

        if (!is_numeric($id) || $id == 0) {
            $this->_redirect('login/logout');
        }
        $userdb = new UserDB();

        $data = $userdb->getUser($id);
        if (!empty($data)) {
            try {
                $userdb->resetSenha($id);
                $this->_helper->Logger->gravarlog('info', 'Senha Resetada do Usuário ID = ' . $id . " [" . $data['login'] . "]", 'Cadastro de Usuários');
            } catch (Exception $e) {
                $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Cadastro de Usuários');
            }
        } else {
            $this->_helper->Logger->gravarlog('warn', 'Tentativa de Resetar Senha de Usuário não cadastrado. ID=' . $id, 'Cadastro de Usuários');
            $id = 0;
        }
        $this->_redirect('/user/caduser/id/' . $id);
    }

    public function trocarsenhaAction() {

        $userdb = new UserDB();

        $request = $this->getRequest();

        // Check if we have a POST request
        if ($request->isPost()) {

            $flag = true;  // flag para verificar a validade do formulário

            $fd = $this->getRequest()->getPost();

            // valida token do formulário
            $token = $this->_session->token;
            $fieldName = $this->_session->fieldName;
            $formtoken = htmlentities($request->getPost('$fieldName'), ENT_QUOTES, 'UTF-8');

            if ($formtoken <> $token) {
                $this->view->errorMsg .= "Token incorreto.<br>";
                $flag = false;
            }

            // valida campos
            $senhaAtual = $fd['senhaAtual'];
            $novaSenha1 = $fd['senhaNova1'];
            $novaSenha2 = $fd['senhaNova2'];

            if (!$userdb->checkSenha($this->_idusu, $senhaAtual)) {
                $this->view->errorMsg = "Senha Atual não confere. Tente Novamente.";
                $flag = false;
            }

            if ($novaSenha1 <> $novaSenha2) {
                $this->view->errorMsg = "A confirmação da senha não confere. Tente Novamente.";
                $flag = false;
            }

            if ($senhaAtual == $novaSenha1) {
                $this->view->errorMsg = "A nova senha deve ser diferente da senha atual. Tente Novamente.";
                $flag = false;
            }

            if (strlen($novaSenha1) < 6 && strlen($novaSenha1) > 15) {
                $this->view->errorMsg = "A senha deve ter no mínimo 6 caracteres e no máximo 15. Tente Novamente.";
                $flag = false;
            }

            if ($flag) {
                // altera a senha no banco de dados
                $data = array(
                    'senha' => sha1($fd['senhaNova1']),
                    'trocarsenha' => 0,
                );
                try {
                    $userdb->update($data, "idusu = $this->_idusu");
                    $this->view->errorMsg = "Registro alterado.";
                    $this->_helper->Logger->gravarlog('info', 'Senha Alterada', 'Trocar Senha');
                } catch (Exception $e) {
                    $this->view->errorMsg = "Registro não incluído.<br>" . utf8_encode($e->getMessage());
                    $this->_helper->Logger->gravarlog('err', utf8_encode($e->getMessage()), 'Trocar Senha');
                }
            }
        }
        // gera fieldName e token
        $this->view->fieldName = "mytkn" . substr(hash('sha256', rand(10, 9999)), 5, 5);
        $this->view->token = hash('sha256', rand(10, 9999999));
        $this->_session->fieldName = $this->view->fieldName;
        $this->_session->token = $this->view->token;
    }

}
