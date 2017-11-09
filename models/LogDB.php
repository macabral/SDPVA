<?php
class LogDB extends Zend_Db_Table
{
    protected $_name = "log";
    
    public function getAllLog($filtro=null)
    {
    	if ($filtro == null){
    		$filtro = "idlog<>0";
    	}
    	$sql = $this->select()
    		->from($this->_name, array('fkidusu','datalog','msg','modulo','userip','lvl','username'))
    		->where($filtro)
    		->order('datalog desc')
                ->limit(2000);
    	return $this->getAdapter()->fetchAll($sql);
  	
    }

	public function getLastErr() {
		$sql = "select msg,lvl from log order by datalog desc limit 1";
		$ret = $this->getAdapter()->fetchRow($sql);
		if (strtoupper($ret['lvl'])=='ERR') {
			return $ret['msg'];
		} else {
			return '';
		}
	}
    

}
