<?php

class emswinddb extends Zend_Db_Table {

    protected $_name = "ems_wind";

    public function getemswind($local, $data1, $data2) {
        $sql = $this->select()
                ->from($this->_name, array('id','local','timestamp','runway','wsins','ws2a','ws2m','ws2x','ws10a','ws10m','ws10x','wdins','wd2a','wd2m','wd2x','wd10a','wd10m','wd10x','wdvar'))
                ->where("local=$local and timestamp between '$data1' and '$data2'");
        return $this->getAdapter()->fetchRow($sql);
    }

    public function lastwind($local) {
        $sql = "select timestamp from $this->_name where local=$local order by timestamp desc limit 1";
        $ret = $this->getAdapter()->fetchRow($sql);
		return $ret['timestamp'];
    }

	public function getAll($filtro = null) {
        if ($filtro == null) {
            $filtro = "id<>0";
        }
        $sql = $this->select()
                ->from($this->_name, array('id','local','timestamp','runway','wsins','ws2a','ws2m','ws2x','ws10a','ws10m','ws10x','wdins','wd2a','wd2m','wd2x','wd10a','wd10m','wd10x','wdvar'))
                ->where($filtro . " and minute(timestamp) in (00,15,30,45)")
                ->order('timestamp desc, runway')
                ->limit(2000);
        return $this->getAdapter()->fetchALL($sql);
    }

	public function getProg($dt1) {
        $sql = "select timestamp, datahora, hr_prev, ws2a, ws2m, ws2x, vetor_kt, wd2m, wd2x, wd2a, vetor_grau
		    from ems_wind
			left join prognostico on date(datahora)='$dt1' and z='SUP' and time(timestamp) = hr_prev and vetor_kt>0 and vetor_kt<40
			where date(timestamp)='$dt1' and minute(timestamp) in (00,15,30,45) and runway='09R'
			order by hr_prev limit 2000";
        $ret = $this->getAdapter()->fetchAll($sql);
		return $ret;
    }

	public function getrna($data) {
		$sql = "SELECT runway, ws2a, wd2a, ws10x FROM ems_wind WHERE timestamp='$data'";
		return $this->getAdapter()->fetchALL($sql);

	}

	public function getemsdiag($local, $dt) {
	
        $sql = $this->select()
                ->from($this->_name, array('id','local','timestamp','runway','ws2a','ws10a','wd2a','wd10a'))
                ->where("local=$local and timestamp = '$dt'");
        return $this->getAdapter()->fetchAll($sql);
    }

	public function getlastDate($local) {
		$sql= "SELECT timestamp FROM `ems_wind` order by timestamp desc limit 1";
		$ret = $this->getAdapter()->fetchRow($sql);
		return $ret['timestamp'];
	}

}


