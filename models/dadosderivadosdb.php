<?php

class Dadosderivadosdb extends Zend_Db_Table {

    protected $_name = "dados_derivados";
    protected $_primary = '';

    public function getAll($filtro = null) {
        if ($filtro == null) {
            $filtro = "id<>0";
        }
        $sql = $this->select()
                ->from($this->_name, array('alinhado','traves','proa','cauda','traves_dir','traves_esq','sodar_atual_data','sodar_atual_z'))
                ->where($filtro)
                ->order('sodar_atual_data desc,sodar_atual_z')
                ->limit(2000);
        return $this->getAdapter()->fetchALL($sql);
    }

    public function getVelocidadeCauda($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, MAX(d.cauda) as cauda
                    FROM dados_derivados d	
                    WHERE d.sodar_atual_data='$dt' 
                    GROUP BY altura ORDER BY cauda DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getVelocidadeProa($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, MAX(d.proa) as proa
                    FROM dados_derivados d	
                    WHERE d.sodar_atual_data='$dt'
                    GROUP BY altura ORDER BY proa DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);

    }

    public function getTravesEsquerdo($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, MAX(d.traves_esq) as traves_esq
                    FROM dados_derivados d	
                    WHERE d.sodar_atual_data='$dt'
                    GROUP BY altura 
                    ORDER BY traves_esq DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);

    }

    public function getTravesDireito($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, MAX(d.traves_dir) as traves_dir
                    FROM dados_derivados d	
                    WHERE d.sodar_atual_data='$dt'  
                    GROUP BY altura 
                    ORDER BY traves_dir DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);

    }

    public function getVariacaoCauda($dt) {
        $sql = "SELECT DATE(d.sodar_atual_data) as data_ncqar, TIME_FORMAT(d.sodar_atual_data, '%H:%i' ) as hora_ncqar, d.sodar_atual_z as altura, d1.sodar_atual_z, 
                    MAX(ROUND(Abs(d.cauda - d1.cauda), 2)) as var_cauda
                    FROM dados_derivados d	
                    INNER JOIN
                    (SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT DATE_SUB('$dt', INTERVAL '15' MINUTE) FROM dados_derivados d LIMIT 1)) d1
                    ON d.sodar_atual_z = d1.sodar_atual_z
                     WHERE d.sodar_atual_data='$dt' group by altura order by var_cauda DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getVariacaoProa($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, d1.sodar_atual_z, 
                    MAX(ROUND(Abs(d.proa - d1.proa), 2)) as var_proa
                    FROM dados_derivados d	
                    INNER JOIN
                    (SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT DATE_SUB('$dt', INTERVAL '15' MINUTE) FROM dados_derivados d LIMIT 1)) d1
                    ON d.sodar_atual_z = d1.sodar_atual_z
                    WHERE d.sodar_atual_data='$dt' group by altura order by var_proa DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);

    }

    public function getVariacaoTravesDir($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, d1.sodar_atual_z, 
                    MAX(ROUND(Abs(d.traves_dir - d1.traves_dir), 2)) as var_traves_dir
                    FROM dados_derivados d	
                    INNER JOIN
                    (SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT DATE_SUB('$dt', INTERVAL '15' MINUTE) FROM dados_derivados d LIMIT 1)) d1
                    ON d.sodar_atual_z = d1.sodar_atual_z
                    WHERE d.sodar_atual_data='$dt' group by altura order by var_traves_dir DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getVariacaoTravesEsq($dt) {
        $sql = "SELECT d.sodar_atual_data as data_ncqar, d.sodar_atual_z as altura, d1.sodar_atual_z,
                    MAX(ROUND(Abs(d.traves_esq - d1.traves_esq), 2)) as var_traves_esq
                    FROM dados_derivados d	
                    INNER JOIN
                    (SELECT * FROM dados_derivados d2 WHERE d2.sodar_atual_data=(SELECT DATE_SUB('$dt', INTERVAL '15' MINUTE) FROM dados_derivados d LIMIT 1)) d1
                    ON d.sodar_atual_z = d1.sodar_atual_z
                    WHERE d.sodar_atual_data='$dt' group by altura order by var_traves_esq DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getWindshear($dt) {
        $sql = "SELECT s.data, s.z as altura, MAX(ROUND(s.shear*59.15, 2)) as windshear
                    FROM sodar_atual s  WHERE s.data='$dt' AND s.shear<99.999 GROUP BY altura ORDER BY windshear DESC LIMIT 1";
        return $this->getAdapter()->fetchRow($sql);
    }

}
