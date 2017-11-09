<?php

class Alertasdb extends Zend_Db_Table {

    protected $_name = "alertas";

    public function getAlerta($pista, $data) {
        $sql = $this->select()
                ->from($this->_name, array('id', 'datahora', 'pista', 'velocidadecauda', 'velocidadeproa', 'velocidadetravesesquerdo', 'velocidadetravesdireito',
                    'variacaocauda', 'variacaoproa', 'variacaotravesesq', 'variacaotravesdir', 'windshear', 'alertavelcauda',
                    'alertavelproa', 'alertavelte', 'alertaveltd', 'alertavc', 'alertavp', 'alertavtd', 'alertavte', 'alertaw'))
                ->where("datahora='$data' and pista='$pista'");
        return $this->getAdapter()->fetchRow($sql);
    }

    public function getAll($filtro = null) {
        if ($filtro == null) {
            $filtro = "id<>0";
        }
        $sql = $this->select()
                ->from($this->_name, array('id', 'datahora', 'pista', 'velocidadecauda', 'velocidadeproa', 'velocidadetravesesquerdo', 'velocidadetravesdireito',
                    'variacaocauda', 'variacaoproa', 'variacaotravesesq', 'variacaotravesdir', 'windshear', 'alertavelcauda',
                    'alertavelproa', 'alertavelte', 'alertaveltd', 'alertavc', 'alertavp', 'alertavtd', 'alertavte', 'alertaw'))
                ->where($filtro)
                ->order('datahora DESC')
                ->limit(2000);
        return $this->getAdapter()->fetchALL($sql);
    }

}
