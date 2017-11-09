<?php

class Processodb extends Zend_Db_Table {

    protected $_name = "processamento";
    protected $_primary = 'id';

    public function getProcessamento() {
        $sql = $this->select()
                ->from($this->_name, array('datahora'));
        return $this->getAdapter()->fetchRow($sql);
    }

    public function gravar($sql) {
        $this->getAdapter()->query($sql);
    }

    public function getProcData() {
        $sql = "SELECT datahora, dayofyear(DATE(datahora)) as dia_juliano, HOUR(datahora) as hora, MONTH(datahora) as mes FROM processamento ";
        return  $this->getAdapter()->fetchRow($sql);
    }
}