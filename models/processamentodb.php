<?php

class processamentodb extends Zend_Db_Table {

    protected $_name = "processamento";
    protected $_primary = 'id';

    public function getfile() {
        $sql = $this->select()
                ->from($this->_name, array('nomearqxml_d'));
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret["nomearqxml_d"];
    }
    public function getProcessamento() {
        $sql = $this->select()
                ->from($this->_name, array('datahora'));
        return $this->getAdapter()->fetchRow($sql);
    }

    public function gravaData($dt) {
	$sql = "update processamento set datahora='" . $dt . "', flag_RNA = 1 where id=1";
	$this->getAdapter()->query($sql);
    }
}
