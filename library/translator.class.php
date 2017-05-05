<?php
// declare(strict_types = 1);
namespace Nooper;

use DOMDocument;
use DOMElement;
use DOMException;

class Translator {
	
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
	 * public string function createJSON(array $datas)
	 */
	public function createJSON(array $datas): string {
		$end = json_encode($datas, JSON_UNESCAPED_UNICODE);
		return is_bool($end) ? '{"error":"' . (string)json_last_error_msg() . '"}' : $end;
	}
	
	/**
	 * public array function parseJSON(string $json)
	 */
	public function parseJSON(string $json): array {
		$end = json_decode($json, true);
		return is_null($end) ? ['error'=>(string)json_last_error_msg()] : $end;
	}
	
	/**
	 * public string function createXML(array $datas DOMDocument $doc = null, DOMElement $node = null, boolean $cdata = true)
	 */
	public function createXML(array $datas, DOMDocument $doc = null, DOMElement $node = null, bool $cdata = true): string {
		if(is_null($doc)) $doc = new DOMDocument('1.0', 'utf-8');
		if(is_null($node)){
			$node = $doc->createElement('xml');
			$doc->appendChild($node);
		}
		foreach($datas as $key => $data){
			$childNode = $doc->createElement(is_string($key) ? $key : 'node');
			$node->appendChild($childNode);
			if(is_array($data)) $this->createXML($data, $doc, $childNode, $cdata);
			else $leaf = $cdata ? $doc->createCDATASection($data) : $doc->createTextNode($data);
			$childNode->appendChild($leaf);
		}
		try{
			$end = $doc->saveXML();
		}catch(DOMException $err){
			$end = '';
		}
		return $end;
	}
	
	/**
	 * public array function parseXML(string $xml)
	 */
	public function parseXML(string $xml, bool $root = false){
		if($root)$xml='<xml>'.$xml.'</xml>';
		$doc = new DOMDocument('1.0', 'utf-8');
		$doc->loadXML($xml);
		$node = $doc->documentElement;
		$name = $node->nodeName;
		$children = $node->childNodes;
		$yesNodeTypes = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE, XML_ELEMENT_NODE];
		$yesEndNodeTypes = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE];
		foreach($children as $child){
			if(!in_array($child->nodeType, $yesNodeTypes, true)) $node->removeChild($child);
		}
		$length = $children->length;
		
		if(0 == $length) $datas = null;
		elseif(1 == $length && in_array($children->item(0)->nodeType, $yesEndNodeTypes, true)) $datas = $child->wholeText;
		else{
			$datas = [];
			foreach($children as $child){
				if(in_array($child->nodeType, $yesEndNodeTypes, true)){
					$datas[] = $child->wholeText;
				}else{
					$datas[$child->nodeName] = $this->parseXML($doc->saveXML($child));
				}
			}
		}
		
		return $datas;
	}
	//
}

