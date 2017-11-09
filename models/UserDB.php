<?php 
class UserDB extends Zend_Db_Table
{
    protected $_name = "usuarios";
    protected $_primary = 'idusu';

    public function usuAtivo()
    {
    	$sql = $this->select()
    		->from($this->_name, ['idusu','nome'])
    		->where('ativo=1')
    		->order('nome');
    	return $this->getAdapter()->fetchPairs($sql);
    }
    public function getAllUsers()
    {
    	$sql = $this->select()
    		->from($this->_name, ['idusu','nome','login','email','ativo','admin'])
    		->order('login');
    	return $this->getAdapter()->fetchAll($sql);
    }
    public function getUser($id)
    {
    	$sql = $this->select()
    		->from($this->_name, array('nome','login','email','ativo','admin','trocarsenha'))
    		->where('idusu=?',$id);
    	return $this->getAdapter()->fetchRow($sql);
    }    
    public function getUsuario($id)
    {
    	$sql = $this->select()
    		->from($this->_name, array('nome'))
    		->where('idusu=?',$id);
    	return $this->getAdapter()->fetchOne($sql);
    }
    
    public function getMail($id)
    {
        $sql = $this->select()
    		->from($this->_name, array('email'))
    		->where('idusu=?',$id);
    	return $this->getAdapter()->fetchOne($sql);
    }
    
    public function getId($login)
    {
    	$sql = $this->select()
    		->from($this->_name, array('idusu'))
    		->where('login=?',$login);
    	return $this->getAdapter()->fetchOne($sql);
    }
    
    public function setSenha($id,$pass)
    {
    	$pass = sha1($pass);
    	$data = array('senha'=> $pass);
    	$this->update($data,'idusu='.$id);
    }
     
    public function setAltSenha($id)
    {
    	$data = array('trocarsenha'=>1);
    	$this->update($data,'idusu='.$id);	
    }

    public function unsetAltSenha($id)
    {
    	$data = array('trocarsenha'=>0);
    	$this->update($data,'idusu='.$id);	
    }    
    
    public function getAltSenha($id)
    {
    	$sql = $this->select()
    		->from($this->_name, array('trocarsenha'))
    		->where('idusu='.$id);
    	return $this->getAdapter()->fetchOne($sql);
	
    }
    
    public function checkSenha($id,$senha)
    {
    	$pass = sha1($senha);
    	$sql = $this->select()
    		->from($this->_name, array('idusu'))
    		->where("idusu=$id")
    		->where("senha='$pass'");
    	$ret=$this->getAdapter()->fetchOne($sql);
    	if (!empty($ret))
    	{
    		return true;
    	} else {
    		return false;
    	}	    	
    }
    public function getLastId()
    {
    	$sql="select idusu from usuarios order by idusu desc limit 1";
    	$ret=$this->getAdapter()->fetchRow($sql);
    	return $ret['idusu'];
    }  
    
    public function resetSenha($id)
    {
    	$data = array('senha'=> sha1('12345678'), 'trocarsenha' => 1);
    	$this->update($data,'idusu='.$id);
    }
}
