<?php

class enemsdb extends Zend_Db_Table {

    protected $_name = "enems";

    public function getensemble($semana,$hora,$pista) {
        $sql = "select pista,ws2a,wd2a from enems where semana=$semana and hora=$hora and pista='". $pista . "'";
        return $this->getAdapter()->fetchRow($sql);
    }



}