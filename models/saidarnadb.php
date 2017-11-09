<?php

class Saidarnadb extends Zend_Db_Table {

    protected $_name = "saida_RNA";

    public function getSaida($data) {
        $sql = $this->select()
                ->from($this->_name, array('*'))
                ->where("datahora='$data'");
        return $this->getAdapter()->fetchRow($sql);
    }
}
