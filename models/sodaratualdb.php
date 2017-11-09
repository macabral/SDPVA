<?php

class Sodaratualdb extends Zend_Db_Table {

    protected $_name = "sodar_atual";
    protected $_primary = 'id';

    public function gravar($sql) {
        $this->getAdapter()->query($sql);
    }

    public function getDerivados1($dt) {
        $sql = "SELECT s.data, z, dir, speed FROM sodar_atual s where s.data='$dt' AND s.dir<999.9";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function seldprog($dt) {

        $sql = "SELECT s.data as data_sodar, p.datahora, p.hr_prev, p.z_metros, s.z, p.vetor_kt, s.speed, p.vetor_grau, s.dir FROM  sodar_atual s
            LEFT JOIN sdpva.prognostico p ON DATE(p.datahora)=DATE(s.data) AND p.z_metros = s.z  AND p.hr_prev = TIME(s.data)
            WHERE DATE(s.data) = '$dt' and s.z in (100,200,300)
            ORDER BY data_sodar desc, z_metros";
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getData($data) {
        $sql = "SELECT z, sigw, tke, edr, speed, u, v
            FROM sodar_atual
            WHERE data='$data' AND
            z IN(30,60,90,120,150,180,210,240,270,300) ORDER BY z";
         return $this->getAdapter()->fetchAll($sql);
    }

    public function getDataPret($data) {
        $sql = "SELECT z, u, v
            FROM sodar_atual
            WHERE data='$data' AND
            z IN(100,200,300) ORDER BY z";
         return $this->getAdapter()->fetchAll($sql);
    }

}