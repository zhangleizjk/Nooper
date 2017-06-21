<?php
// declare(strict_types = 1);
namespace SporeAura\WX;
use PDO;
use PDOException;
class App {
	/**
	 * 
	 */
	protected $mysql;
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		$dsn='mysql:host=127.0.0.1;dbname=wechat_ss_database;port=3306;charset=utf8';
		$username='root';
		$password='goodwin@000';
		try {
			$this->mysql=new \PDO($dsn,$username,$password);
		}catch(PDOException $e){
			$this->mysql=null;
		}
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		$this->mysql=null;
	}
	
	/**
	 * 
	 */
	public function setToken():bool{
		
	}
	
	/**
	 * 
	 */
	public function getToken(): string {
		$sql="select `app_access_token` from `app` limit 1";
		$ds=$this->mysql->query($sql);
		if($ds){
			$assoc=$ds->fetch(PDO::FETCH_ASSOC);
			if($assoc){
				return $assoc['app_access_token'];
			}
		}
		return null;
	}
	//
}

