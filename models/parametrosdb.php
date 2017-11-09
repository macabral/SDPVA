<?php

class Parametrosdb extends Zend_Db_Table {

    protected $_name = "parametros";
    protected $_primary = 'idparametros';

    public function getParametros() {
        $sql = $this->select()
                ->from($this->_name, ['local','wind_var_cauda','wind_var_proa','wind_var_traves','wind_int_alerta1_min','wind_int_alerta1_max','wind_int_alerta2_max',
                            'wind_int_nome_alt_critica_1','wind_int_valor_alt_critica_1','wind_int_nome_alt_critica_2','wind_int_valor_alt_critica_2','wind_int_nome_alt_critica_3','wind_int_valor_alt_critica_3','wind_int_nome_alt_critica_4','wind_int_valor_alt_critica_4',
                            'dir_pista','d_mag','wind_int_limite_pista','wind_int_limite_330',
                            'wind_shear_interv1_max','wind_shear_interv2_max','wind_shear_interv3_max','wind_shear_interv4_max']);
        return $this->getAdapter()->fetchRow($sql);
    }
    public function getLocal() {
        $sql = $this->select()
                ->from($this->_name, array('local'));
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['local'];
    }
}
