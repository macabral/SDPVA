<?php

class emsptudb extends Zend_Db_Table {

    protected $_name = "ems_ptu";

    public function getemsptu($local, $data1, $data2) {
        $sql = $this->select()
                ->from($this->_name, array('id','local','timestamp','runway','pa_ins','pa_qnh','pa_qfe','th_tt',
                                'th_uu','th_td','th_tt_2','th_uu_2','th_td_2','ground_t','ground_t_2'))
                ->where("local='$local' and timestamp between '$data1' and '$data2'");
        return $this->getAdapter()->fetchRow($sql);
    }

    public function lastptu($local) {
        $sql = "select timestamp from $this->_name where local=$local order by timestamp desc limit 1";
        $ret = $this->getAdapter()->fetchRow($sql);
        return $ret['timestamp'];
    }

    public function getAll($filtro = null) {
        if ($filtro == null) {
            $filtro = "id<>0";
        }
        $sql = $this->select()
                ->from($this->_name, array('id','local','timestamp','runway','pa_ins','pa_qnh','pa_qfe','th_tt','th_uu','th_td','th_tt_2','th_uu_2','th_td_2','ground_t','ground_t_2'))
                ->where($filtro . " and minute(timestamp) in (00,15,30,45)")
                ->order('timestamp desc, runway')
                ->limit(2000);
        return $this->getAdapter()->fetchALL($sql);
    }
}