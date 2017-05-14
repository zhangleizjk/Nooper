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
	const operate_question = 9;
	const operate_answer = 10;
	const operate_notify = 11;
	const operate_reply = 12;
	
	/**
	 */
	protected $app;
	protected $mch;
	protected $key;
	protected $hash = 'MD5';
	protected $urls = [self::operate_create=>'https://api.mch.weixin.qq.com/pay/unifiedorder', self::operate_query=>'https://api.mch.weixin.qq.com/pay/orderquery', self::operate_close=>'https://api.mch.weixin.qq.com/pay/closeorder ', self::operate_refund=>'https://api.mch.weixin.qq.com/secapi/pay/refund', self::operate_refund_query=>'https://api.mch.weixin.qq.com/pay/refundquery', self::operate_download=>'https://api.mch.weixin.qq.com/pay/downloadbill', self::operate_qrcode_create=>'weixin://wxpay/bizpayurl', self::operate_qrcode_change=>'https://api.mch.weixin.qq.com/tools/shorturl', self::operate_callback_input=>null, self::operate_callback_output=>null, self::operate_notify=>null, self::operate_reply=>null];
	protected $createParams = [['trade_type', 'device_info', 'out_trade_no', 'product_id', 'openid', 'body', 'detail', 'total_fee', 'fee_type', 'limit_pay', 'goods_tag', 'spbill_create_ip', 'time_start', 'time_expire', 'attach'], ['return_code', 'result_code', 'trade_type', 'prepay_id', 'code_url']];
	protected $queryParams = [['transaction_id', 'out_trade_no'], ['return_code', 'result_code', 'trade_type', 'trade_state', 'transaction_id', 'out_trade_no', 'openid', 'total_fee', 'settlement_total_fee', 'cash_fee', 'coupon_fee', 'time_end', 'attach']];
	protected $closeParams = [['out_trade_no'], ['return_code', 'result_code']];
	protected $refundParams = [['device_info', 'transaction_id', 'out_trade_no', 'out_refund_no', 'total_fee', 'refund_fee', 'refund_fee_type', 'refund_account', 'op_user_id'], ['return_code', 'result_code', 'transaction_id', 'out_trade_no', 'refund_id', 'out_refund_no', 'refund_fee', 'settlement_refund_fee', 'cash_refund_fee', 'coupon_refund_fee']];
	protected $refundQueryParams = [['device_info', 'transaction_id', 'out_trade_no', 'refund_id', 'out_refund_no'], ['return_code', 'result_code', 'transaction_id', 'out_trade_no', 'refund_count']];
	protected $downloadParams = [['device_info', 'bill_date', 'bill_type', 'tar_type'], []];
	protected $qrcodeCreateParams = [['product_id', 'time_stamp'], []];
	protected $qrcodeChangeParams = [['long_url'], ['return_code', 'result_code', 'short_url']];
	protected $questionParams = [[], ['openid', 'product_id']];
	protected $answerParams = [['return_code', 'return_msg', 'prepay_id', 'result_code', 'err_code_des'], []];
	protected $notifyParams = [[], ['return_code', 'result_code', 'trade_type', 'transaction_id', 'out_trade_no', 'openid', 'total_fee', 'settlement_total_fee', 'cash_fee', 'coupon_fee', 'time_end', 'attach']];
	protected $replyParams = [['return_code', 'return_msg'], []];
	protected $params = [];
	protected $datas = [];
	
	/**
	 * public void function __construct(string $app_id, string $mch_id, string $app_key, string $notify_url)
	 */
	public function __construct(string $app_id, string $mch_id, string $app_key, string $notify_url) {
		$keys = array_merge($this->createParams[0], $this->queryParams[0], $this->closeParams[0]);
		$keys = array_merge($keys, $this->refundParams[0], $this->refundQueryParams[0], $this->downloadParams[0]);
		$keys = array_merge($keys, $this->qrcodeCreateParams[0], $this->qrcodeChangeParams[0]);
		$keys = array_merge($keys, $this->callbackInputParams[0], $this->callbackOutputParams[0]);
		$keys = array_merge($keys, $this->notifyParams[0], $this->replyParams[0]);
		$this->params = array_unique($keys);
		// sort($this->params);
		
		$this->urls[self::operate_notify] = $notify_url;
		$this->key = $app_key;
		$this->mch = $mch_id;
		$this->app = $app_id;
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
	 * public array function create(boolean $clip = true)
	 */
	public function create(bool $clip = true): array {
		$ends = $this->parse($this->send(self::operate_create));
		return $clip ? $this->clip(self::operate_create, $ends) : $ends;
	}
	
	/**
	 * public array function query(boolean $clip = true)
	 */
	public function query(bool $clip = true): array {
		$ends = $this->parse($this->send(self::operate_query));
		return $clip ? $this->clip(self::operate_query, $ends) : $ends;
	}
	
	/**
	 * pulblic array function close(boolean $clip = true)
	 */
	public function close(bool $clip = true): array {
		$ends = $this->parse($this->send(self::operate_close));
		return $clip ? $this->clip(self::operate_close, $ends) : $ends;
	}
	
	/**
	 * public array function refund(boolean $clip = true)
	 */
	public function refund(bool $clip = true): array {
		$ends = $this->parse($this->send(self::operate_refund));
		return $clip ? $this->clip(self::operate_refund, $ends) : $ends;
	}
	
	/**
	 * public array function queryr(boolean $clip = true)
	 */
	public function queryr(bool $clip = true): array {
		$ends = $this->parse($this->send(self::operate_refund_query));
		return $clip ? $this->clip(self::operate_refund_query, $ends) : $ends;
	}
	
	/**
	 * public array function download(boolean $pack = true)
	 */
	public function download(bool $pack = true): array {
		$this->data('tar_type', $pack ? 'GZIP' : null);
		$end = $this->send(self::operate_download);
		$mime_type = $pack ? 'application/zip' : 'text/plain';
		$file_basic_name = $this->datas['bill_date'] ?? 'bill';
		$file_name = $file_basic_name . '.' . $pack ? 'gzip' : 'txt';
		$this->header($mime_type, true, $file_name);
		echo $end;
	}
	
	/**
	 * public array function qrcode(string $prodouct_id)
	 */
	public function qrcode(string $product_id): array {
		$this->data('product_id', $product_id);
		$this->data('time_stamp', $this->now()['timestamp']);
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
		$ends = $this->parse($this->send(self::operate_qrcode_change));
		return $clip ? $this->clip(self::operate_qrcode_change, $ends) : $ends;
	}
	
	/**
	 * public array function question(boolean $clip = true)
	 */
	public function question(bool $clip = true): array {
		$xml = file_get_contents('php://input');
		$ends = $this->parse($xml);
		return $clip ? $this->clip(self::operate_question, $ends) : $ends;
	}
	
	/**
	 * public void function answer(void)
	 */
	public function answer(): void {
		$datas = $this->prepare(self::operate_answer, false);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		$this->header('text/xml');
		echo $xml;
	}
	
	/**
	 * public array function notify(boolean $clip = true)
	 */
	public function notify(bool $clip = true): array {
		$xml = file_get_contents('php://input');
		$ends = $this->parse($xml);
		return $clip ? $this->clip(self::operate_notify, $ends) : $ends;
	}
	
	/**
	 * public void function reply(string $code, ?string $message = null)
	 */
	public function reply(string $code, string $message = null): void {
		$this->data('return_code', $code);
		if(!is_null($message) && $message != '') $this->data('return_msg', $message);
		$datas = $this->prepare(self::operate_reply, false);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		$this->header('text/xml');
		echo $xml;
	}
	
	/**
	 * public array function prepare(integer $operate, boolean $primary = true)
	 */
	public function prepare(int $operate, bool $primary = true): array {
		$params = $this->map($operate);
		if(is_null($params)) $this->error(10001);
		foreach($params as $param){
			if(isset($this->datas[$param])) $datas[$param] = $this->datas[$param];
		}
		if(!isset($datas)) $this->error(10002);
		elseif($primary){
			$datas['appid'] = $this->app;
			$datas['mch_id'] = $this->mch;
			$datas['nonce_str'] = $this->rand();
			$datas['sign'] = $this->sign($datas);
		}
		return $datas;
	}
	
	/**
	 * public string function send(integer $operate, ?array $datas = null)
	 */
	public function send(int $operate, array $datas = null): string {
		$url = $this->urls[$operate] ?? null;
		if(is_null($url)) $this->error(10001);
		$datas = $datas ?? $this->prepare($operate);
		if(!$datas) $this->error(10002);
		$helper = new Translator();
		$xml = $helper->createXML($datas);
		$mimicry = new Mimicry();
		try{
			$end = $mimicry->post($url, $xml);
		}catch(Exception $e){
			return $this->error(20001, $e->getMessage());
		}
		return $end;
	}
	
	/**
	 * public array function parse(string $xml)
	 */
	public function parse(string $xml): array {
		if('' == $xml) $this->error(30001);
		$helper = new Translator();
		$datas = $helper->parseXML($xml);
		if(!is_array($datas)) $this->error(30002);
		foreach($datas as $data){
			if(!is_string($data)) $this->error(30002);
		}
		if(!isset($datas['return_code']) or strtolower($datas['return_code']) == 'fail') $this->error(40001);
		elseif(!isset($datas['result_code']) or strtolower($datas['result_code']) == 'fail') $this->error(50001, $datas['err_code'] ?? null);
		elseif(!isset($datas['sign']) or $datas['sign'] !== $this->sign($datas)) $this->error(60001);
		return $datas;
	}
	
	/**
	 * public string function rand(integer $length = 30)
	 */
	public function rand(int $length = 30): string {
		$queue = '';
		$chars = array_merge(range('0', '9'), range('a', 'z'));
		$end = count($chars) - 1;
		for($i = 0; $i < $length; $i++){
			$queue .= $chars[mt_rand(0, $end)];
		}
		return strtoupper($queue);
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
		$datas['timestamp'] = $dt->getTimestamp();
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
	 * protected void function header(string $mime_type, boolean $transfer = false, ?string $file_name = null)
	 */
	protected function header(string $mime_type, bool $transfer = false, string $file_name = null): void {
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-Type: ' . $mime_type);
		if($transfer){
			if(is_null($file_name)) $file_name = 'nooper';
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename=' . $file_name);
			header('Content-Transfer-Encoding: binary');
		}
	}
	
	/**
	 * protected void function error(integer $code, ?string $description = null)
	 */
	protected function error(int $code, string $description = null): void {
		switch($code){
			case 10001:
				$message = 'Nooper_Pay_Operate_Error';
				break;
			case 10002:
				$message = 'Nooper_Pay_Empty_Data_Error';
				break;
			case 20001:
				$message = 'Nooper_Pay_Curl_Error';
				break;
			case 30001:
				$message = 'Nooper_Pay_Empty_XML_Error';
				break;
			case 30002:
				$message = 'Nooper_Pay_XML_Format_Error';
				break;
			case 40001:
				$message = 'Nooper_Pay_Comm_Error';
				break;
			case 50001:
				$message = 'Nooper_Pay_Trade_Failure';
				break;
			case 60001:
				$message = 'Nooper_Pay_Sign_Failure';
			default:
				$messgae = 'Nooper_Pay_System_Error';
				break;
		}
		if(!is_null($description) and trim($description) != '') $message .= '[' . $description . ']';
		throw new Exception($message, $code);
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
			case self::operate_question:
				return $this->questionParams[$send ? 0 : 1];
				break;
			case self::operate_answer:
				return $this->answerParams[$send ? 0 : 1];
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
	
	/**
	 * protected array function clip(integer $operate, array $datas)
	 */
	protected function clip(int $operate, array $datas): array {
		$keys = $this->map($operate, false);
		if(is_null($keys)) return $datas;
		foreach($keys as $key){
			if(isset($datas[$key])) $ends[$key] = $datas[$key];
		}
		return $ends ?? $datas;
	}
	//
}












