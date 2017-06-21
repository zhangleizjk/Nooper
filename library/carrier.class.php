<?php
// declare(strict_types = 1);
namespace Nooper;

use Exception;

class Carrier {
	
	/**
	 * Properties
	 */
	protected $access_token;
	protected $create_qrcode_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create';
	protected $create_qrcode_image_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
	
	/**
	 * public void function __construct(string $token)
	 */
	public function __construct(string $token) {
		$this->access_token = $token;
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public ?array function create_short_qrcode(integer $seconds, integer $scene_id)
	 */
	public function create_short_qrcode(int $seconds, int $scene_id): array {
		$url = $this->create_qrcode_url . '?access_token=' . $this->access_token;
		$datas = ['action_name'=>'QR_SCENE', 'action_info'=>['scene'=>['scene_id'=>$scene_id]], 'expire_seconds'=>$seconds];
		return $this->send($url, $datas);
	}
	
	/**
	 * public ?array function create_qrcode(integer $scene_id)
	 */
	public function create_qrcode(int $scene_id): array {
		$url = $this->create_qrcode_url . '?access_token=' . $this->access_token;
		$datas = ['action_name'=>'QR_LIMIT_SCENE', 'action_info'=>['scene'=>['scene_id'=>$scene_id]]];
		return $this->send($url, $datas);
	}
	
	/**
	 * public ?array function create_qrcode_str(integer $scene_id)
	 */
	public function create_qrcode_str(string $scene_str): array {
		$url = $this->create_qrcode_url . '?access_token=' . $this->access_token;
		$datas = ['action_name'=>'QR_LIMIT_STR_SCENE', 'action_info'=>['scene'=>['scene_str'=>$scene_str]]];
		return $this->send($url, $datas);
	}
	
	/**
	 * public string function display_qrcode_image(string $ticket)
	 */
	public function display_qrcode_image(string $ticket): string {
		return $this->create_qrcode_image_url . '?ticket=' . rawurldecode($ticket);
	}
	
	/**
	 * publicc void function download_qrcode_image(string $ticket)
	 */
	public function download_qrcode_image(string $ticket): void {
		$url = $this->create_qrcode_image_url;
		$params = ['ticket'=>rawurlencode($ticket)];
		$this->header('image/jpeg', 'qrcode.jpg');
		$mimicry = new Mimicry();
		echo $mimicry->get($url, $params);
	}
	
	/**
	 * protected void function header(string $mime_type, string $file_name)
	 */
	protected function header(string $mime_type, string $file_name): void {
		header('Accept-Ranges:bytes');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		header('Content-Description: File Transfer');
		header('Content-Type: ' . $mime_type);
		header('Content-Disposition: attachment; filename=' . $file_name);
		header('Content-Transfer-Encoding: binary');
	}
	
	/**
	 * protected ?array function send(string $url, array $datas)
	 */
	protected function send(string $url, array $datas): array {
		$helper = new Translator();
		$mimicry = new Mimicry();
		try{
			return $helper->parseJSON($mimicry->post($url, $helper->createJSON($datas)));
		}catch(Exception $e){
			return null;
		}
	}
	//
}

