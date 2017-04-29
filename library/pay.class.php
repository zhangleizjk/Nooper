<?php
// declare(strict_types = 1);
namespace SporeAura\Nooper;

class Pay {
	/**
	 */
	const operation_create = 1;
	const operate_query = 2;
	const operate_close = 3;
	const operate_refund = 4;
	const operate_query_refund = 5;
	const operate_download_bill = 6;
	
	/**
	 */
	protected $appid;
	protected $mch_id;
	protected $params = [];
	protected $createParams = ['device_info', 'nonce_str', 'sign', 'sign_type', 'body', 'detail', 'attach', 'out_trade_no', 'fee_type', 'total_fee', 'spbill_create_ip', 'time_start', 'time_expire', 'goods_tag', 'notify_url', 'trade_type', 'product_id', 'limit_pay', 'openid'];
	protected $queryParams = [];
	protected $closeParams = [];
	protected $refundParams = [];
	protected $queryRefundParams = [];
	protected $downloadBillParams = [];
	
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
	 * public ?array function create()
	 */
	public function create(string $api = null): array {
		$ends = $this->send(self::operation_create, $api);
		if(!is_null($ends)){
			$keys = ['trade_type', 'prepay_id', 'code_url'];
			return $this->lose($ends, $keys);
		}
		return null;
	}
	
	/**
	 * public ?array function query(void)
	 */
	public function query(string $api=null): array {
		
	}
	
	/**
	 * protected array function prepare(void)
	 */
	protected function prepare($operation): array {
		$params = ['appid'=>$this->appId, 'mch_id'=>$this->mchId];
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
	 * protected ?array function send(array $datas, int $operation, ?string $api = null)
	 */
	protected function send(int $operation, string $api = null): array {
		$datas = $this->prepare($operation);
		if(!$datas) return [];
		$api = $api ?? $this->api($operation);
		$translator = new Translator();
		$xml = $translator->createXML($datas);
		$mimicry = new Mimicry();
		$end = $mimicry->post($api, $xml);
		$ends = $translator->parseXML($end);
		
		if(strtolower($ends['return_code']) == 'fail'){
			$code = '';
			$msg = $ends['return_msg'];
			throw new PayException($msg, $code);
			return null;
		}elseif(strtolower($ends['result_code']) == 'fail'){
			$code = $ends['err_code'];
			$msg = $ends['err_msg'];
			throw new PayException($msg, $code);
			return null;
		}else
			return $ends;
	}
	
	/**
	 */
	protected function lose(array $datas, array $keys): array {
		foreach($keys as $key){
			if(is_string($key) && isset($datas[$key]))) $ends[$key]=$datas[$key];
		}
		return $ends?? [];
	}
	
	//
}






























































