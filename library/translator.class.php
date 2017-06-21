<?php
// declare(strict_types = 1);
namespace Nooper;

use DOMDocument;
use DOMElement;
use Exception;

class Translator {
	
	/**
	 * Properties
	 */
	protected $jsonErrors = [JSON_ERROR_NONE=>'json_error_none', JSON_ERROR_DEPTH=>'json_error_depth', JSON_ERROR_STATE_MISMATCH=>'json_error_state_mismatch', JSON_ERROR_CTRL_CHAR=>'json_error_ctrl_char', JSON_ERROR_SYNTAX=>'json_error_syntax', JSON_ERROR_UTF8=>'json_error_utf8', JSON_ERROR_RECURSION=>'json_error_recursion', JSON_ERROR_INF_OR_NAN=>'json_error_inf_or_nan', JSON_ERROR_UNSUPPORTED_TYPE=>'json_error_unsupported_type'];
	
	/**
	 * public void function __construct(void)
	 */
	public function __construct() {
		// echo '- begin -';
	}
	
	/**
	 * public void function __destruct(void)
	 */
	function __destruct() {
		// echo '- end -';
	}
	
	/**
	 * public string function createJSON(array $datas)
	 */
	public function createJSON(array $datas): string {
		$end = json_encode($datas, JSON_UNESCAPED_UNICODE);
		if(is_bool($end)){
			$code = json_last_error();
			$message = $this->getJSONErrMessage($code);
			throw new Exception($message, $code);
		}
		return $end;
	}
	
	/**
	 * public array function parseJSON(string $json)
	 */
	public function parseJSON(string $json): array {
		$end = json_decode($json, true);
		if(is_null($end)){
			$code = json_last_error();
			$message = $this->getJSONErrMessage($code);
			throw new Exception($message, $code);
		}
		return $end;
	}
	
	/**
	 * public string function createXML(array $datas, DOMElement $node = null, boolean $cdata = true, boolean $doctype = false)
	 */
	public function createXML(array $datas, DOMElement $node = null, bool $cdata = true, bool $doctype = false): string {
		if(is_null($node)){
			$doc = new DOMDocument('1.0', 'utf-8');
			$node = $doc->createElement('xml');
			$doc->appendChild($node);
		}else{
			$doc = $node->ownerDocument;
		}
		foreach($datas as $key => $data){
			$child = $doc->createElement(is_string($key) ? $key : 'node');
			$node->appendChild($child);
			if(is_array($data)) $this->createXML($data, $child, $cdata, $doctype);
			else{
				if(is_string($data)) $data;
				elseif(is_int($data) or is_float($data)) $data = (string)$data;
				elseif(is_bool($data)) $data = $data ? 'true' : 'false';
				elseif(is_null($data)) $data = '';
				elseif(is_resource($data)) $data = get_resource_type($data);
				elseif(is_object($data)) $data = get_class($data);
				else $data = '';
				$end = $cdata ? $doc->createCDATASection($data) : $doc->createTextNode($data);
				$child->appendChild($end);
			}
		}
		return $doctype ? $doc->saveXML() : $doc->saveXML($node);
	}
	
	/**
	 * public ?mixed function parseXML(string $xml, boolean $root = false)
	 */
	public function parseXML(string $xml, bool $root = false) {
		if($root) $xml = '<xml>' . $xml . '</xml>';
		$doctype = '<?xml version="1.0" encoding="utf-8"?>';
		$xml = $doctype . $xml;
		$doc = new DOMDocument('1.0', 'utf-8');
		if(!$doc->loadXML($xml, LIBXML_NOBLANKS | LIBXML_NOERROR)) return null;
		$node = $doc->documentElement;
		$children = $node->childNodes;
		$yes_node_types = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE, XML_ELEMENT_NODE];
		$yes_end_node_types = [XML_TEXT_NODE, XML_CDATA_SECTION_NODE];
		foreach($children as $child){
			if(!in_array($child->nodeType, $yes_node_types, true)) $node->removeChild($child);
		}
		$len = $children->length;
		if(0 == $len) $datas = '';
		elseif(1 == $len && in_array($children->item(0)->nodeType, $yes_end_node_types, true)) $datas = $child->wholeText;
		else{
			$datas = [];
			foreach($children as $child){
				if(in_array($child->nodeType, $yes_end_node_types, true)){
					$datas[] = $child->wholeText;
				}else{
					if('node' == $child->nodeName) $datas[] = $this->parseXML($doc->saveXML($child));
					else $datas[$child->nodeName] = $this->parseXML($doc->saveXML($child));
				}
			}
		}
		return $datas;
	}
	
	/**
	 * protected string function getJSONErrMessage(integer $err_no)
	 */
	protected function getJSONErrMessage($err_no): string {
		return $this->jsonErrors[$err_no] ?? 'json_error_unknown';
	}
	//
}

