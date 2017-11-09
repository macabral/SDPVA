<?php

class SbrjController extends Zend_Controller_Action {

    protected $_db;
    protected $_admin;

    public function preDispatch() {

    }

    public function init() {
        /* Initialize action controller here */
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    public function indexAction() {
        $this->view->layout()->disableLayout();
        $this->view->local = "SBRJ";
        $this->view->localAPRUN = APP_APRUN2 . strtoupper($this->view->local) . "/mnd/";
    }

}
