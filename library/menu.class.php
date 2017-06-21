<?php
// declare(strict_types = 1);
namespace SporeAura\WX;

class Menu {
	
	/**
	 */
	const media_all = 'media_id';
	const media_text = 'view_limited';
	const click_custom = 'click';
	const click_scan_push = 'scancode_push';
	const click_scan_waiting = 'scancode_waiting';
	const click_camera = 'pic_sysphoto';
	const click_camera_photo = 'pic_photo_or_album';
	const click_wx_photo = 'pic_weixin';
	const click_location = 'location_select';
	
	/**
	 */
	protected $domain;
	protected $token;
	protected $menus = [];
	
	/**
	 * public void function __construct(string $token, string $domain = null)
	 */
	public function __construct(string $token, string $domain = null) {
		$this->domain = is_null($domain) ? 'api.weixin.qq.com' : $domain;
		$this->token = $token;
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		//
	}
	
	/**
	 * public array function createMenu(string $name)
	 */
	public function createMenu(string $name): array {
		return [
			'name'=>$name, 
			'sub_button'=>[]
		];
	}
	
	/**
	 * public array function createView(string $name, string $url)
	 */
	public function createView(string $name, string $url): array {
		return [
			'type'=>'view', 
			'name'=>$name, 
			'url'=>$url
		];
	}
	
	/**
	 * public array function createClick(string $name, string $key, string $type = self::click_custom)
	 */
	public function createClick(string $name, string $key, string $type = self::click_custom): array {
		return [
			'type'=>$type, 
			'name'=>$name, 
			'key'=>$key
		];
	}
	
	/**
	 * public array creatgeMedia(string $name, string $id, string $type = self::media_all)
	 */
	public function createMedia(string $name, string $id, string $type = self::media_all): array {
		return [
			'type'=>$type, 
			'name'=>$name, 
			'media_id'=>$id
		];
	}
	
	/**
	 * public array function createMiniProgram(string $name, string $url, string $appId, string $pagePath)
	 */
	public function createMiniProgram(string $name, string $url, string $appId, string $pagePath): array {
		return [
			'type'=>'miniprogram', 
			'name'=>$name, 
			'url'=>$url, 
			'appid'=>$appId, 
			'pagepath'=>$pagePath
		];
	}
	
	/**
	 * public array function addMenu(array $menu)
	 */
	public function addMenu(array $menu): array {
		$this->menus[] = $menu;
		return $this->menus;
	}
	
	/**
	 * public function addSubMenu(array &$menu, array $sub)
	 */
	public function addSubMenu(array &$menu, array $sub): array {
		$menu['sub_button'][] = $sub;
		return $menu;
	}
	
	/**
	 * public boolean function create(void)
	 */
	public function create(): bool {
		$translator = new Translator();
		$json = $translator->createJSON([
			'button'=>$this->menus
		]);
		$mm = new Mimicry();
		$url = 'https://' . $this->domain . '/cgi-bin/menu/create?access_token=' . $this->token;
		$msg = $mm->post($url, $json);
		$end = $translator->parseJSON($msg);
		return 0 == $end['errcode'] ? true : false;
	}
	
	/**
	 * public boolean function delete(void)
	 */
	public function delete(): bool {
		$url = 'https://' . $this->domain . '/cgi-bin/menu/delete';
		$qryStrings = array(
			'access_token'=>$this->accessToken
		);
		$mm = new Mimicry();
		$json = $mm->get($url, $qryStrings);
		$translator = new Translator();
		$ends = $translator->parseJSON($json);
		return 0 == $ends['errcode'] ? true : false;
	}
	
	/**
	 * public string function getMenus(void)
	 */
	public function getMenus(): string {
		$translator = new Translator();
		return $translator->createJSON([
			'button'=>$this->menus
		]);
	}
	
	//
}

