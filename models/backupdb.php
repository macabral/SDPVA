<?php
class backup extends Zend_Db_Table {

    public function getAllTable($table,$ini,$fim) {
        $sql = "select * from $table limit $ini,$fim";
        $ret = $this->getAdapter()->fetchAll($sql);
        return $ret;
    }

    public function getCreateTable($table) {
        $sql = "show create table $table";
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['Create Table'];
    }

    public function getShowFields($table) {
        $sql = "show fields from $table";
        $ret = $this->getAdapter()->fetchAll($sql);
        return $ret;
    }

    public function getTotalRows($table) {
        $sql = "select count(*) as total from $table";
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['total'];
    }
}