<?php
// declare(strict_types = 1);
namespace SporeAura\Nooper;

use Throwable;

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
	protected $key;
	protected $urlDomain = 'https://api.mch.weixin.qq.com';
	protected $urlDetails = [operate_create=>'pay/unifiedorder', operate_query=>'pay/orderquery', operate_close=>'pay/closeorder ', operate_refund=>'secapi/pay/refund', operate_refund_query=>'pay/refundquery', operate_download_bill=>'pay/downloadbill'];
	protected $urls = [];
	protected $datas = [];
	protected $params = ['sign_type'=>'MD5'];
	protected $createParams = ['device_info', 'nonce_str', 'sign', 'sign_type', 'body', 'detail', 'attach', 'out_trade_no', 'fee_type', 'total_fee', 'spbill_create_ip', 'time_start', 'time_expire', 'goods_tag', 'notify_url', 'trade_type', 'product_id', 'limit_pay', 'openid'];
	protected $queryParams = ['transaction_id', 'out_trade_no', 'nonce_str', 'sign', 'sign_type'];
	protected $closeParams = [];
	protected $refundParams = [];
	protected $queryRefundParams = [];
	protected $downloadBillParams = [];
	
	/**
	 * public void function __construct(string appid, string $mchid, string $key)
	 */
	public function __construct(string $appid, string $mchid, string $key) {
		$this->appid = $appid;
		$this->mchid = $mchid;
		$this->key = $key;
		
		$keys = array_merge($this->createParams, $this->queryParams, $this->closeParmas, $this->refundParams, $this->queryRefundParams, $this->downloadBillParams);
		$this->params = array_merge(array_unique($keys));
		
		foreach($this->urlDetails as $key => $detail){
			$this->urls[$key] = implode('/', [$this->urlDomain, $detail]);
		}
		//
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
	 * public string function key(string $data)
	 */
	public function key(string $data): string {
		$this->key = $data;
		return $data;
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
	 * public boolean function data(string $name, string $value)
	 */
	public function data(string $name, string $value): bool {
		if(in_array($name, $this->params, true)){
			$this->datas[$name] = $value;
			return true;
		}
		return false;
	}
	
	/**
	 * public integer function datas(array $params)
	 */
	public function datas(array $params): int {
		$counter = 0;
		foreach($params as $key => $param){
			try{
				$this->param($key, $param);
				$counter++;
			}catch(Throwable $e){
			}
		}
		return $counter;
	}
	
	/**
	 * public void function clear(void)
	 */
	public function clear(): void {
		$this->datas = [];
	}
	
	/**
	 * public ?array function create(boolean $clip = true)
	 */
	public function create(bool $clip = true): array {
		$ends = $this->send(self::operate_create);
		if(!is_null($ends)){
			$keys = ['trade_type', 'prepay_id', 'code_url'];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function query(boolean $clip = true)
	 */
	public function query(bool $clip = true): array {
		$ends = $this->send(self::operate_query);
		if(!is_null($ends)){
			$keys = [];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * pulblic ?array function close(boolean $clip = true)
	 */
	public function close(bool $clip = true): array {
		$ends = $this->send(self::operate_close);
		if(!is_null($ends)){
			$keys = [];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function refund(boolean $clip = true)
	 */
	public function refund(bool $clip = true): array {
		$ends = $this->send(self::operate_refund);
		if(!is_null($ends)){
			$keys = [];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function queryr(boolean $clip = true)
	 */
	public function queryr(bool $clip = true): array {
		$ends = $this->send(self::operate_query_refund);
		if(!is_null($ends)){
			$keys = [];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function download(boolean $clip = true)
	 */
	public function download(bool $clip = true): array {
		$ends = $this->send(self::operate_download_bill);
		if(!is_null($ends)){
			$keys = [];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public array function prepare(integer $operate)
	 */
	public function prepare(int $operate): array {
		$params = $this->map($operate);
		if(is_null($params)) return [];
		foreach($params as $param){
			if(isset($this->datas[$param])) $datas[$param] = $this->datas[$param];
		}
		$datas['appid'] = $this->appid;
		$datas['mchid'] = $this->mchid;
		$datas['nonce_str'] = $this->rand();
		$datas['sign'] = $this->sign($params, $this->signType);
		return $datas;
	}
	
	/**
	 * public ?array function send(integer $operate, ?array $datas = null)
	 */
	public function send(int $operate, array $datas = null): array {
		$datas = $datas ?? $this->prepare($operate);
		if(!$datas) return null;
		$url = $this->urls[$operate] ?? null;
		if(is_null($url)) return null;
		$translator = new Translator();
		$xml = $translator->createXML($datas);
		$mimicry = new Mimicry();
		$end = $mimicry->post($url, $xml);
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
	public function sign(array $datas): string {
		foreach($datas as $key => $data){
			if(!is_string($key)) unset($datas[$key]);
			elseif(!is_scalar($data)) unset($datas[$key]);
			elseif('' === $data) unset($datas[$key]);
			elseif('sign' == $key) unset($datas[$key]);
		}
		sort($datas, SORT_STRING);
		foreach($datas as $key => $data){
			$params[] = $key . '=' . $data;
		}
		$params[] = ('key=' . $this->key);
		$str = implode('&', $params);
		return strtoupper(md5($str));
	}
	
	/**
	 * protected ?array function map(int $operate)
	 */
	protected function map(int $operate): array {
		switch($operate){
			case self::operate_create:
				return $this->createParamss;
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
				return null;
				break;
		}
	}
	
	/**
	 *  protected array function clip(array $datas, array $keys)
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
	protected function rand(int $len = 30): string {
		$str = '';
		$chars = array_merge(range('0', '9'), range('a', 'z'));
		$end = count($chars) - 1;
		for($i = 0;$i < $len;$i++){
			$str .= $chars[mt_rand(0, $end)];
		}
		return strtoupper($str);
	}
	//
}






























































