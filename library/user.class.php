<?php
// declare(strict_types = 1);
namespace Nooper;

use Exception;

class User {
	
	/**
	 * Properties
	 */
	protected $access_token;
	protected $create_tag_url = 'https://api.weixin.qq.com/cgi-bin/tags/create';
	protected $update_tag_url = 'https://api.weixin.qq.com/cgi-bin/tags/update';
	protected $delete_tag_url = 'https://api.weixin.qq.com/cgi-bin/tags/delete';
	protected $get_tag_url = 'https://api.weixin.qq.com/cgi-bin/tags/get';
	protected $get_user_url = 'https://api.weixin.qq.com/cgi-bin/user/get';
	protected $get_user_info_url = ' https://api.weixin.qq.com/cgi-bin/user/info';
	
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
	 * public ?array function get_tags(void)
	 */
	public function get_tags(): array {
		$params = ['access_token'=>$this->access_token];
		$end = $this->send_get($this->get_tag_url, $params);
		return $end['tags'] ?? null;
	}
	
	/**
	 * public ?integer function create_tag(string $name)
	 */
	public function create_tag(string $name): int {
		$url = $this->create_tag_url . '?access_token=' . $this->access_token;
		$datas = ['tag'=>['name'=>$name]];
		$end = $this->send($url, $datas);
		return $end['tag']['id'] ?? null;
	}
	
	/**
	 * public boolean function edit_tag(integer $id, string $name)
	 */
	public function edit_tag(int $id, string $name): bool {
		$url = $this->update_tag_url . '?access_token=' . $this->access_token;
		$datas = ['tag'=>['id'=>$id, 'name'=>$name]];
		$end = $this->send($url, $datas);
		return 0 == $end['errcode'] ? true : false;
	}
	
	/**
	 * public boolean function delete_tag(integer $id)
	 */
	public function delete_tag(int $id): bool {
		$url = $this->delete_tag_url . '?access_token=' . $this->access_token;
		$datas = ['tag'=>['id'=>$id]];
		$end = $this->send($url, $datas);
		return 0 == $end['errcode'] ? true : false;
	}
	
	/**
	 * public ?integer get_users_num(void)
	 */
	public function get_users_num(): int {
		$params = ['access_token'=>$this->access_token];
		$end = $this->send_get($this->get_user_url, $params);
		return $end['total'] ?? null;
	}
	
	/**
	 * public ?array function get_users(?string $openid = null)
	 */
	public function get_users(string $openid = null): array {
		$datas = [];
		$params = ['access_token'=>$this->access_token];
		if(!is_null($openid)) $params['next_openid'] = $openid;
		$ends = $this->send_get($this->get_user_url, $params);
		if(isset($ends['total'])){
			if(0 == $ends['total']) return [];
			if(isset($ends['data']['openid'])) $datas = $ends['data']['openid'];
			if(isset($ends['next_openid']) && $ends['next_openid'] != ''){
				$follow_datas = $this->get_users($ends['next_openid']);
				if(is_array($follow_datas)) $datas = array_merge($datas, $follow_datas);
			}
			return $datas;
		}
		return null;
	}
	
	/**
	 * 
	 */
	public function set_user_remark(string $openid, string $remark):bool {
		
	}
	
	/**
	 * public ?array function get_user_info(string $openid, ?string $language = null)
	 */
	public function get_user_info(string $openid, string $language = null): array {
		$params = ['access_token'=>$this->access_token, 'openid'=>$openid];
		if(!is_null($language)) $params['lang'] = $language;
		$ends = $this->send_get($this->get_user_info_url, $params);
		return isset($ends['openid']) ? $ends : null;
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
	
	/**
	 * protected ?array function send_get(string $url, array $params = null)
	 */
	protected function send_get(string $url, array $params = null): array {
		$helper = new Translator();
		$mimicry = new Mimicry();
		try{
			return $helper->parseJSON($mimicry->get($url, $params));
		}catch(Exception $e){
			return null;
		}
	}
	//
}

