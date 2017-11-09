<?php

class Clientesxmldb extends Zend_Db_Table {

    protected $_name = "clientesxml";
    protected $_primary = 'idclientesxml';

    public function getAll() {
        $sql = $this->select()
                ->from($this->_name, ['idclientesxml', 'identificacao', 'url', 'ativo','tipo','pista']);
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getClientexml($id) {
        $sql = $this->select()
                ->from($this->_name, ['idclientesxml', 'identificacao', 'url', 'ativo','tipo','pista'])
                ->where('idclientesxml=?',$id);
        return $this->getAdapter()->fetchRow($sql);

    }

    public function getAtivos() {
        $sql = $this->select()
                ->from($this->_name, ['idclientesxml', 'identificacao', 'url', 'ativo','tipo','pista'])
                ->where("ativo=1");
        return $this->getAdapter()->fetchAll($sql);
    }

     public function getAtivos2() {
        $sql = $this->select()
                ->from($this->_name, ['idclientesxml', 'identificacao', 'url', 'ativo','tipo','pista'])
                ->where("ativo=1 and tipo = 'M'");
        return $this->getAdapter()->fetchAll($sql);
    }

    public function getLastId() {
        $sql = "select idclientesxml from $this->_name order by idclientesxml desc limit 1";
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['idclientesxml'];
    }

}
