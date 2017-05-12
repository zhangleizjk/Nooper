<?php
// declare(strict_types = 1);
namespace Nooper;

use Throwable;
use Exception;
use DateTime;
use DateTimeZone;
use DateInterval;

class Payer {
	/**
	 */
	const operate_create = 1;
	const operate_query = 2;
	const operate_close = 3;
	const operate_refund = 4;
	const operate_refund_query = 5;
	const operate_download = 6;
	const operate_qrcode_create = 7;
	const operate_qrcode_change = 8;
	const operate_callback_input = 9;
	const operate_callback_output = 10;
	const operate_notify = 11;
	const operate_reply = 12;
	
	/**
	 */
	protected $app;
	protected $mch;
	protected $key;
	protected $hash = 'MD5';
	protected $datas = [];
	protected $urls = [
		self::operate_create=>'https://api.mch.weixin.qq.com/pay/unifiedorder', 
		self::operate_query=>'https://api.mch.weixin.qq.com/pay/orderquery', 
		self::operate_close=>'https://api.mch.weixin.qq.com/pay/closeorder ', 
		self::operate_refund=>'https://api.mch.weixin.qq.com/secapi/pay/refund', 
		self::operate_refund_query=>'https://api.mch.weixin.qq.com/pay/refundquery', 
		self::operate_download=>'https://api.mch.weixin.qq.com/pay/downloadbill', 
		self::operate_qrcode_create=>'weixin://wxpay/bizpayurl', 
		self::operate_qrcode_change=>'https://api.mch.weixin.qq.com/tools/shorturl', 
		self::operate_callback_input=>null, 
		self::operate_callback_output=>null, 
		self::operate_notify=>null, 
		self::operate_reply=>null
	];
	protected $params = [];
	protected $createParams = [
		[
			'trade_type', 
			'device_info', 
			'out_trade_no', 
			'product_id', 
			'openid', 
			'body', 
			'detail', 
			'total_fee', 
			'fee_type', 
			'limit_pay', 
			'goods_tag', 
			'spbill_create_ip', 
			'time_start', 
			'time_expire', 
			'attach'
		], 
		[
			'return_code', 
			'result_code', 
			'trade_type', 
			'prepay_id', 
			'code_url'
		]
	];
	protected $queryParams = [
		[
			'transaction_id', 
			'out_trade_no'
		], 
		[
			'return_code', 
			'result_code', 
			'trade_type', 
			'trade_state', 
			'transaction_id', 
			'out_trade_no', 
			'openid', 
			'total_fee', 
			'settlement_total_fee', 
			'cash_fee', 
			'coupon_fee', 
			'time_end', 
			'attach'
		]
	];
	protected $closeParams = [
		[
			'out_trade_no'
		], 
		[
			'return_code', 
			'result_code'
		]
	];
	protected $refundParams = [
		[
			'device_info', 
			'transaction_id', 
			'out_trade_no', 
			'out_refund_no', 
			'total_fee', 
			'refund_fee', 
			'refund_fee_type', 
			'refund_account', 
			'op_user_id'
		], 
		[
			'return_code', 
			'result_code', 
			'transaction_id', 
			'out_trade_no', 
			'refund_id', 
			'out_refund_no', 
			'refund_fee', 
			'settlement_refund_fee', 
			'cash_refund_fee', 
			'coupon_refund_fee'
		]
	];
	protected $refundQueryParams = [
		[
			'device_info', 
			'transaction_id', 
			'out_trade_no', 
			'refund_id', 
			'out_refund_no'
		], 
		[
			'return_code', 
			'result_code', 
			'transaction_id', 
			'out_trade_no', 
			'refund_count'
		]
	];
	protected $downloadParams = [
		[
			'device_info', 
			'bill_date', 
			'bill_type', 
			'tar_type'
		], 
		[]
	];
	protected $qrcodeCreateParams = [
		[
			'product_id', 
			'time_stamp'
		], 
		[]
	];
	protected $qrcodeChangeParams = [
		[
			'long_url'
		], 
		[]
	];
	protected $callbackInputParams = [
		[], 
		[
			'openid', 
			'product_id'
		]
	];
	protected $callbackOutputParams = [
		[
			'return_code', 
			'return_msg', 
			'prepay_id', 
			'result_code', 
			'err_code_des'
		], 
		[]
	];
	protected $notifyParams = [
		[], 
		[
			'return_code', 
			'result_code', 
			'trade_type', 
			'transaction_id', 
			'out_trade_no', 
			'openid', 
			'total_fee', 
			'settlement_total_fee', 
			'cash_fee', 
			'coupon_fee', 
			'time_end', 
			'attach'
		]
	];
	protected $replyParams = [
		[
			'return_code', 
			'return_msg'
		], 
		[]
	];
	
	/**
	 * public void function __construct(string $app_id, string $mch_id, string $app_key, string $notify_url)
	 */
	public function __construct(string $app_id, string $mch_id, string $app_key, string $notify_url) {
		$keys = array_merge($this->createParams[0], $this->queryParams[0], $this->closeParams[0]);
		$keys = array_merge($keys, $this->refundParams[0], $this->refundQueryParams[0], $this->downloadParams[0]);
		$keys = array_merge($keys, $this->qrcodeCreateParams[0], $this->qrcodeChangeParams[0]);
		$keys = array_merge($keys, $this->callbackInputParams[0], $this->callbackOutputParams[0]);
		$keys = array_merge($keys, $this->notifyParams[0], $this->replyParams[0]);
		$keys = array_unique($keys);
		sort($keys);
		$this->params = $keys;
		
		$this->urls[self::operate_notify] = $notify_url;
		$this->key = $app_key;
		$this->mch = $mch_id;
		$this->app = $app_id;
		var_dump($this->urls);
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public string function app(string $app_id)
	 */
	public function app(string $app_id): string {
		$this->app = $app_id;
		return $app_id;
	}
	
	/**
	 * public string function mch(string $mch_id)
	 */
	public function mch(string $mch_id): string {
		$this->mch = $mch_id;
		return $mch_id;
	}
	
	/**
	 * public string function key(string $app_key)
	 */
	public function key(string $app_key): string {
		$this->key = $app_key;
		return $app_key;
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
	 * public boolean function data(string $key, string $param)
	 */
	public function data(string $key, string $param): bool {
		if(in_array($key, $this->params, true)){
			$this->datas[$key] = $param;
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
				$this->data($key, $param);
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
		$ends = $this->filter($this->send(self::operate_create));
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_create, $ends) : $ends;
		return null;
	}
	
	/**
	 * public ?array function query(boolean $clip = true)
	 */
	public function query(bool $clip = true): array {
		$ends = $this->filter($this->send(self::operate_query));
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_query, $ends) : $ends;
		return null;
	}
	
	/**
	 * pulblic ?array function close(boolean $clip = true)
	 */
	public function close(bool $clip = true): array {
		$ends = $this->filter($this->send(self::operate_close));
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_close, $ends) : $ends;
		return null;
	}
	
	/**
	 * public ?array function refund(boolean $clip = true)
	 */
	public function refund(bool $clip = true): array {
		$ends = $this->filter($this->send(self::operate_refund));
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_refund, $ends) : $ends;
		return null;
	}
	
	/**
	 * public ?array function queryr(boolean $clip = true)
	 */
	public function queryr(bool $clip = true): array {
		$ends = $this->filter($this->send(self::operate_refund_query));
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_refund_query, $ends) : $ends;
		return null;
	}
	
	/**
	 * public ?array function download(boolean $pack = true)
	 */
	public function download(bool $pack = true): array {
		$this->data('tar_type', $pack ? 'GZIP' : null);
		$end = $this->send(self::operate_download);
		$mime_type = $pack ? 'application/zip' : 'text/plain';
		$file_basic_name = $this->datas['bill_date'] ?? 'bill';
		$file_name = $file_basic_name . '.' . $pack ? 'gzip' : 'txt';
		$this->transfer($mime_type, $file_name);
		echo $end;
	}
	
	/**
	 * public array function qrcode(string $prodouct_id, ?string $timestamp = null)
	 */
	public function qrcode(string $productId): array {
		$this->data('product_id', $productId);
		$this->data('time_stamp', $this->now()['stamp']);
		$datas = $this->prepare(self::operate_qrcode_create);
		foreach($datas as $key => &$data){
			$data = ($key . '=' . $data);
		}
		$ends['long_url'] = $this->urls[self::operate_qrcode_create] . '?' . implode('&', $datas);
		$ends['short_url'] = $this->qrcodec($ends['long_url']);
		return $ends;
	}
	
	/**
	 * public ?array function qrcodec(string $url, boolean $clip = true)
	 */
	public function qrcodec(string $url, bool $clip = true): array {
		$this->data('long_url', $url);
		$ends = $this->send(self::operate_qrcode_change);
		if(!is_null($ends)){
			$keys = [
				'short_url'
			];
			return $clip ? $this->clip($ends, $keys) : $ends;
		}
		return null;
	}
	
	/**
	 * public ?array function question(boolean $clip = true)
	 */
	public function question(bool $clip = true): array {
		$xml = file_get_contents('php://input');
		$ends = $this->filter($xml);
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_callback_input, $ends) : $ends;
		return null;
	}
	
	/**
	 * public void function answer(void)
	 */
	public function answer(): void {
		$datas = $this->prepare(self::operate_reply, false);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-type: text/xml');
		echo $xml;
	}
	
	/**
	 * public ?array function notify(boolean $clip = true)
	 */
	public function notify(bool $clip = true): array {
		$xml = file_get_contents('php://input');
		$ends = $this->filter($xml);
		if(!is_null($ends)) return $clip ? $this->clip(self::operate_notify, $ends) : $ends;
		return null;
	}
	
	/**
	 * public void function reply(string $code, ?string $message = null)
	 */
	public function reply(string $code, string $message = null): void {
		$this->data('return_code', $code);
		if(!is_null($message)) $this->data('return_msg', $message);
		$datas = $this->prepare(self::operate_reply, false);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-type: text/xml');
		echo $xml;
	}
	
	/**
	 * public ?array function prepare(integer $operate, boolean $primary=true)
	 */
	public function prepare(int $operate, bool $primary = true): array {
		$params = $this->map($operate);
		if(is_null($params)) return $this->error(10001, 'Nooper_Pay_NO_Operate');
		foreach($params as $param){
			if(isset($this->datas[$param])) $datas[$param] = $this->datas[$param];
		}
		if(!isset($datas)) return $this->error(10002, 'Nooper_Pay_Empty_Prepare_Data');
		elseif($primary){
			$datas['appid'] = $this->appid;
			$datas['mch_id'] = $this->mchid;
			$datas['nonce_str'] = $this->rand();
			$datas['sign'] = $this->sign($datas);
		}
		return $datas;
	}
	
	/**
	 * public ?array function send(integer $operate, ?array $datas = null)
	 */
	public function send(int $operate, array $datas = null): array {
		$url = $this->urls[$operate] ?? null;
		if(is_null($url)) return $this->error(10001, 'Nooper_Pay_NO_Operate');
		$datas = $datas ?? $this->prepare($operate);
		if(!$datas) return $this->error(10002, 'Nooper_Pay_Empty_Prepare_Data');
		$translator = new Translator();
		$xml = $translator->createXML($datas);
		$mimicry = new Mimicry();
		try{
			$end = $mimicry->post($url, $xml);
		}catch(Exception $e){
			return $this->error(20001, 'Nooper_Pay_Curl_Failure[' . $e->getMessage() . ']');
		}
		return $end;
	}
	
	/**
	 * public array function now(integer $seconds = 0)
	 */
	public function now(int $seconds = 0): array {
		$dt = new DateTime();
		$dt->setTimezone(new DateTimeZone('Asia/Shanghai'));
		try{
			$dt->add(new DateInterval('PT' . $seconds . 'S'));
		}catch(Exception $e){
		}
		$datas['stamp'] = $dt->getTimestamp();
		$datas['datetime'] = $dt->format('YmdHis');
		$datas['date'] = $dt->format('Ymd');
		return $datas;
	}
	
	/**
	 * public ?string sign(array $datas)
	 */
	public function sign(array $datas): string {
		foreach($datas as $key => $data){
			if(!is_string($key) or !is_string($data)) return null;
			elseif('sign' == $key) unset($datas[$key]);
			elseif('' == $data) unset($datas[$key]);
		}
		if(!$datas) return null;
		ksort($datas);
		foreach($datas as $key => $data){
			$params[] = $key . '=' . $data;
		}
		$params[] = ('key=' . $this->key);
		return strtoupper(md5(implode('&', $params)));
	}
	
	/**
	 * protected ?array function filter(string $xml)
	 */
	protected function filter(string $xml): array {
		if('' == $xml) return $this->error(30001, 'Nooper_Pay_Empty_Return_Data');
		$helper = new Translator();
		$datas = $helper->parseXML($xml);
		if(!is_array($datas)) return $this->error(30002, 'Nooper_Pay_XML_Format_Error');
		foreach($datas as $data){
			if(!is_string($data)) return $this->error(30002, 'Nooper_Pay_XML_Format_Error');
		}
		if(!isset($datas['return_code']) or strtolower($datas['return_code']) == 'fail') return $this->error(40001, 'Nooper_Pay_Comm_Error');
		elseif(!isset($datas['result_code']) or strtolower($datas['result_code']) == 'fail') return $this->error(50001, 'Nooper_Pay_Trade_Error[' . $datas['err_code'] ?? 'NO_Des' . ']');
		elseif(!isset($datas['sign']) or $datas['sign'] != $this->sign($datas)) return $this->error(60001, 'Nooper_Pay_Sign_Error');
		return $datas;
	}
	
	/**
	 * protected void function error(string $code, string $message)
	 */
	protected function error(string $code, string $message): void {
		throw new Exception($message, $code);
	}
	
	/**
	 * protected array function clip(integer $operate, array $datas)
	 */
	protected function clip(int $operate, array $datas): array {
		$keys = $this->map($operate, false);
		if(is_null($keys)) return $datas;
		foreach($keys as $key){
			if(is_string($key) && isset($datas[$key])) $ends[$key] = $datas[$key];
		}
		return $ends ?? $datas;
	}
	
	/**
	 *
	 * @name protected string function rand(integer $length = 30)
	 */
	protected function rand(int $length = 30): string {
		$str = '';
		$chars = array_merge(range('0', '9'), range('a', 'z'));
		$end = count($chars) - 1;
		for($i = 0; $i < $length; $i++){
			$str .= $chars[mt_rand(0, $end)];
		}
		return strtoupper($str);
	}
	
	/**
	 * protected void function transfer(string $mime_type, string $file_name)
	 */
	protected function transfer(string $mime_type, string $file_name): void {
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-Description: File Transfer');
		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename=' . $file_name);
		header('Content-Transfer-Encoding: binary');
	}
	
	/**
	 * protected ?array function map(int $operate, boolean $send = true)
	 */
	protected function map(int $operate, bool $send = true): array {
		switch($operate){
			case self::operate_create:
				return $this->createParams[$send ? 0 : 1];
				break;
			case self::operate_query:
				return $this->queryParams[$send ? 0 : 1];
				break;
			case self::operate_close:
				return $this->closeParams[$send ? 0 : 1];
				break;
			case self::operate_refund:
				return $this->refundParams[$send ? 0 : 1];
				break;
			case self::operate_refund_query:
				return $this->refundQueyParams[$send ? 0 : 1];
				break;
			case self::operate_download:
				return $this->downloadParams[$send ? 0 : 1];
				break;
			case self::operate_qrcode_create:
				return $this->qrcodeCreateParams[$send ? 0 : 1];
				break;
			case self::operate_qrcode_change:
				return $this->qrcodeChangeParams[$send ? 0 : 1];
				break;
			case self::operate_callback_input:
				return $this->callbackInputParams[$send ? 0 : 1];
				break;
			case self::operate_callback_output:
				return $this->callbackParams[$send ? 0 : 1];
				break;
			case self::operate_notify:
				return $this->notifyParams[$send ? 0 : 1];
				break;
			case self::operate_reply:
				return $this->replyParams[$send ? 0 : 1];
				break;
			default:
				return null;
				break;
		}
	}
	//
}












