<?php

class entradarnadb extends Zend_Db_Table {

    protected $_name = "entrada_rna";

    public function getEntrada($data) {
        $sql = $this->select()
                ->from($this->_name, array('*'))
                ->where("datahora='$data'");
        return $this->getAdapter()->fetchRow($sql);
    }
}
