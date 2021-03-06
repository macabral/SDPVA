<?php

class ErrorController extends Zend_Controller_Action {

    public function errorAction() {
        $errors = $this->_getParam('error_handler');
        $this->view->exception = '';

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                $this->view->message = 'Um erro foi encontrado';
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->exception = $errors->exception;
                $this->view->request = $errors->request;
                $this->view->errors = $errors;
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Página não encontrada';
                $this->view->exception = $errors->exception;
                $this->view->request = $errors->request;
                $this->view->errors = $errors;
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Um erro foi encontrado';
                $this->view->exception = $errors->exception;
                $this->view->request = $errors->request;
                $this->view->errors = $errors;
        }
    }

}
