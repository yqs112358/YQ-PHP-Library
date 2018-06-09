<?php
//数据库配置文件
class DBCtrl
{
	var $dbtype;
	var $host;
	var $user;
	var $pwd;
	var $dbname;
	var $conn;
	var $code;
	var $last;
	
	function DBCtrl($dbtype,$host,$user,$pwd,$dbname,$code="utf-8",$last=false)
	{
		$this->dbtype=$dbtype;
		$this->host=$host;
		$this->user=$user;
		$this->pwd=$pwd;
		$this->dbname=$dbname;
		$this->code=$code;
		$this->conn=0;
		$this->last=$last;
	}
	
	function __destruct()
	{
		if($this->conn != null && !$this->last)
			mysql_close($this->conn);
		$this->conn=null;
	}
	
	function ConnDB()
	{
		$this->conn=$this->last?mysql_pconnect($this->host,$this->user,$this->pwd):mysql_connect($this->host,$this->user,$this->pwd);
		if(!$this->conn)
			return false;
		if(!mysql_select_db($this->dbname,$this->conn))
			return false;
		mysql_query("set names ".$this->code,$this->conn);
		return $this->conn;
	}
	
	function CloseDB()
	{
		if($this->conn != null)
			mysql_close($this->conn);
		$this->conn=null;
	}
	
	function ExecSQL($sqlstr)
	{
		$sqltype=strtolower(substr(trim($sqlstr),0,6));
		
		$rs=mysql_query($sqlstr,$this->conn);
		if(!$rs)
			return false;
		if($sqltype == "select")
		{
			$array=mysql_fetch_array($rs,MYSQL_ASSOC);
			if(!$rs || count($array) == 0 ||(count($array)==1 && $array[0] == ""))
				return false;
			else
			{
				$arr=array();
				do
				{
					array_push($arr,$array);
					$array=mysql_fetch_array($rs,MYSQL_ASSOC);
				}
				while($array);
				return $arr;
			}
		}
		elseif($sqltype == "update" || $sqltype == "insert" || $sqltype == "delete")
		{
			if($rs)
				return true;
			else
				return false;
		}
		return $rs;
	}
	
	function CloseRes($res)
	{
		mysql_free_result($res);
	}
	
	function Error()
	{
		return mysql_error();
	}
}
?>