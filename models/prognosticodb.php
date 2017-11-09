<?php

class Prognosticodb extends Zend_Db_Table {

    protected $_name = "prognostico";

    public function getPrognostico($data,$pista,$hrprev) {
        $sql = $this->select()
                ->from($this->_name, array('*'))
                ->where("datahora='$data' and  hr_prev='$hrprev'")
                ->order('ordem');
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getPistaSug($data,$hrprev,$z) {
        $sql = $this->select()
                ->from($this->_name, array('pista_sug'))
                ->where("datahora='$data' and z='$z' and hr_prev='$hrprev'");
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['pista_sug'];
    }

    public function getPistaSug2($data,$pista,$hrprev,$z) {
        $sql = $this->select()
                ->from($this->_name, array('pista_sug','obs'))
                ->where("datahora='$data' and z='$z' and pistaref='$pista' and hr_prev='$hrprev'");
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['pista_sug']. " " . $ret['obs'];
    }

    public function getAllPrognostico($data) {
        $sql = $this->select()
                ->from($this->_name, array('*'))
                ->where("datahora='$data'")
                ->order('ordem');
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getLastPrev() {
        $sql = "select datahora,hr_prev, pista_sug, obs from prognostico where z='SUP'  and hr_prev = date_add(datahora, interval 15 minute) order by id desc,hr_prev desc limit 12";
        return $this->getAdapter()->fetchAll($sql);
    }

}
