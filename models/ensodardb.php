<?php

class ensodardb extends Zend_Db_Table {

    protected $_name = "ensodar";

    public function getensemble($semana,$hora) {
        $sql = "select * from ensodar where semana = $semana and hora = $hora";
        return $this->getAdapter()->fetchRow($sql);
    }

}