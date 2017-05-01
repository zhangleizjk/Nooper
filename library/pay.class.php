<?php
// declare(strict_types = 1);
namespace SporeAura\Nooper;

class Pay {
	/**
	 */
	const operate_create = 1;
	const operate_query = 2;
	const operate_close = 3;
	const operate_refund = 4;
	const operate_query_refund = 5;
	const operate_download_bill = 6;
	
	/**
	 */
	protected $appid;
	protected $mchid;
	protected $urlDomain = 'https://api.mch.weixin.qq.com';
	protected $urlDetails = [operate_create=>'pay/unifiedorder', operate_query=>'pay/orderquery', operate_close=>'pay/closeorder ', operate_refund=>'secapi/pay/refund', operate_refund_query=>'pay/refundquery', operate_download_bill=>'pay/downloadbill'];
	protected $urls = [];
	protected $params = ['sign_type'=>'MD5'];
	protected $createParamKeys = ['device_info', 'nonce_str', 'sign', 'sign_type', 'body', 'detail', 'attach', 'out_trade_no', 'fee_type', 'total_fee', 'spbill_create_ip', 'time_start', 'time_expire', 'goods_tag', 'notify_url', 'trade_type', 'product_id', 'limit_pay', 'openid'];
	protected $queryParamKeys = ['transaction_id', 'out_trade_no', 'nonce_str', 'sign', 'sign_type'];
	protected $closeParamKeys = [];
	protected $refundParamKeys = [];
	protected $queryRefundParamKeys = [];
	protected $downloadBillParamKeys = [];
	
	/**
	 * public void function __construct(?string appid = null, ?string $mchid = null)
	 */
	public function __construct(string $appid = null, string $mchid = null) {
		if(!is_null($appid)) $this->appid = $appid;
		if(!is_null($mchid)) $this->mchid = $mchid;
		
		foreach($this->urlDetails as $key => $detail){
			$this->urls[$key] = implode('/', [$this->urlDomain, $detail]);
		}
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		//
	}
	
	/**
	 * public string function appid(string $appid)
	 */
	public function appid(string $appid): string {
		$this->appid = $appid;
		return $appid;
	}
	
	/**
	 * public string function mchid(string $mchid)
	 */
	public function mchid(string $mchid): string {
		$this->mchid = $mchid;
		return $mchid;
	}
	
	/**
	 * public boolean function url(int $operate, string $url)
	 */
	public function url(int $operate, string $url): bool {
		$keys = array_keys($this->urls);
		if(in_array($operate, $keys, true)){
			$this->urls[$operate] = $url;
			return true;
		}
		return false;
	}
	
	/**
	 * public boolean function param(string $name, mixed $value)
	 */
	public function param(string $name, $value): bool {
		if(in_array($name, $this->params, true)){
			$this->datas[$name] = $value;
			return true;
		}
		return false;
	}
	
	/**
	 * public integer function params(array $params)
	 */
	public function params(array $params): int {
		$counter = 0;
		foreach($params as $key => $param){
			if(is_string($key) && $this->param($key, $param)) $counter++;
		}
		return $counter;
	}
	
	/**
	 * public ?array function create(boolean $clip = true, ?string $rul = null)
	 */
	public function create(bool $clip = true, string $url = null): array {
		$ends = $this->send(self::operate_create, $url);
		if(!is_null($ends)){
			if($clip){
				$keys = ['trade_type', 'prepay_id', 'code_url'];
				return $this->lose($ends, $keys);
			}
			return $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function query(boolean $clip = true, ?string $url = null)
	 */
	public function query(bool $clip = true, string $url = null): array {
		$ends = $this->send(self::operate_query, $url);
		if(!is_null($ends)){
			if($clip){
				$keys = [];
				return $this->clip($ends, $keys);
			}
			return $ends;
		}
		return null;
	}
	
	/**
	 * pulblic ?array function close(boolean $clip = true, ?string $url = null)
	 */
	public function close(bool $clip = true, string $url = null): array {
		$ends = $this->send(self::operate_close, $url);
		if(!is_null($ends)){
			if($clip){
				$keys = [];
				return $this->clip($ends, $keys);
			}
			return $ends;
		}
		return null;
	}
	
	/**
	 * protected array function prepare(void)
	 */
	protected function prepare($operation): array {
		$params = ['appid'=>$this->appid, 'mch_id'=>$this->mchid];
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
	protected function clip(array $datas, array $keys): array {
		foreach($keys as $key){
			if(is_string($key) && isset($datas[$key])){
				$ends[$key] = $datas[$key];
			}
			return $ends ?? [];
		}
	}
	
	/**
	 * protected string function rand(integer $len = 30)
	 */
	protected function rand(int $len= 30): string {
		
	} 
	//
}






























































