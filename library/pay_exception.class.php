<?php
// declare(strict_types = 1);
namespace SporeAura\Nooper;
use Exception;

class PayException extends Exception {

	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		//
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		//
	}
	
	/**
	 * public boolean function setParam(string $name, mixed $value)
	 */
	public function setParam(string $name, $value): bool {
		if(in_array($name, $this->params, true)){
			$this->datas[$name] = $value;
			return true;
		}
		return false;
	}
	
	/**
	 * public integer function setParams(array $params)
	 */
	public function setParams(array $params): int {
		$counter = 0;
		foreach($params as $key => $param){
			if(is_string($key) && $this->setParam($key, $param)) $counter++;
		}
		return $counter;
	}
	
	/**
	 * protected array function prepare(void)
	 */
	protected function prepare($operation): array {
		$params = [
			'appid'=>$this->appId, 
			'mch_id'=>$this->mchId
		];
		$params = $this->map($operation);
		if(!$params) return [];
		foreach($params as $name){
			if(isset($this->params[$name])) $params[$name] = $this->params[$name];
		}
		return $params;
	}
	
	/**
	 * protected array function map(int $operation)
	 */
	protected function map(int $operation): array {
		switch($operation){
			case self::operation_create:
				return $this->createParams;
				break;
			case self::operate_query:
				return $this->queryParams;
				break;
			case self::operate_close:
				return $this->closeParams;
				break;
			case self::operate_refund:
				return $this->refundParams;
				break;
			case self::operate_query_refund:
				return $this->queryRefundParams;
				break;
			case self::operate_download_bill:
				return $this->downloadBillParams;
				break;
			default:
				return [];
				break;
		}
	}
	
	/**
	 * protected array function send(array $datas, int $operation, ?string $api = null)
	 */
	protected function send(int $operation, string $api = null): array {
		$datas = $this->prepare($operation);
		if(!$datas) return [];
		$api = $api ?? $this->api($operation);
		$translator = new Translator();
		$xml = $translator->createXML($datas);
		$mimicry = new Mimicry();
		$end = $mimicry->post($api, $xml);
		return $translator->parseXML($end);
	}
	
	//
}






























































