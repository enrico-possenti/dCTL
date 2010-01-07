<?php

if (!empty($isInclude)) define('_INCLUDE', TRUE);
if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');
$isInclude = TRUE;

/**
	*  Core for dCTL
	*  @package    dCTL
	*  @subpackage engine
	*  @author    NoveOPiu di Enrico Possenti
	*  @version   $Id: core.php 2009-12-05 noveopiu $
	*/
// +----------------------------------------------------------------------+
// $Id: core.php,v 0.x 2009/11/30 $
/**
	* dCTL class provides a way to perform queries on an XML database of
	* dCTL-extended TEI encoded documents.
	*
	* To use this class you need to access a dCTL environment offering this kind
	* of webservice.
	* {@link http://www.ctl.sns.it/dctl/}
	*
	* @author Enrico Possenti <info@noveopiu.com>
	* @version $Revision: 0.x $
	* @package dCTL-Engine
	*/
// |
// +----------------------------------------------------------------------+
// $New: 0.7.01
// - da ora in avanti tutte le modifiche sono riportate in CHANGELOG
// $New: 0.7.00
// - modificate tutte le variabili path
// - un sacco di cose, troppe da elencare (speriamo funzioni tutto)
// $New: 0.6.03
// - un sacco di cose
// $New: 0.6.02
// - aggiunto qualche $debug
// - corretto bug di errore sul primo <pb/> (let $ms1 := if ($ms1) the $ms1 else $node)
// $New: 0.6.01
// - (un sacco di cose)
// $New: 0.6.00
// - modified: $this->_db_base_path =
// - (un sacco di altre cose)
// $New: 0.5.00
// - modified: <link><item> -> <link><group>;
// - modified: if (preg_match('/^_\w{3}$/', $parsed['query'])) {
// - modified: $this->_db_base_path =
// $New: 0.4.04
// - added: $(hier);
// $New: 0.4.03
// - added: $xq = dirname(__FILE__).'xquery_base.xq';
// - added: if ($howMany < 0) $howMany = PHP_INT_MAX;
// +----------------------------------------------------------------------+
// | this file is meant to be an include...
if(empty($isInclude)) {
	echo "No direct access!!";
	die();
}
/* AUTHENTICATE */
//  if (is_file(str_replace('//','/',dirname(__FILE__).'/').'.htaccess')) {
// 		require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/authenticate.inc.php');
// 	} else {
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/_protected/db.inc.php');
// 	};
// | load config files
require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/config.inc.php');
$config = (str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');
if (is_file($config)) require_once($config);
// +----------------------------------------------------------------------+
// | MAIN CLASS DEFINITION
// +----------------------------------------------------------------------+
class dCTL {
	// | Declaration of private members
	private static $_instance;
	protected $_xml_header;
	protected $_db;
	protected $_db_base_path;
	protected $_db_publish_path;
	protected $_fs_publish_path;
	protected $_web_publish_path;
	protected $_use_private_db;
	protected $_debug;
	// +----------------------------------------------------------------------+
	// | INTERFACE TO THE CLASS
	// +----------------------------------------------------------------------+
	public function getStructure ($resourceList='') {
		return $this->_get_resource (true, $resourceList);
	}
	// +----------------------------------------------------------------------+
	public function getBlock ($resourceList='') {
		return $this->_get_resource (false, $resourceList);
	}
	// +----------------------------------------------------------------------+
	public function getOptions ($resourceList='', $xpath='') {
		// wraps results
		return $this->_resources_to_xml($this->_get_resource (true, $resourceList, $xpath.' '));
	}
	// +----------------------------------------------------------------------+
	public function getLinks ($resourceList='') {
		return $this->_get_link (true, $resourceList, DCTL_FILE_LINKER);
	}
	// +----------------------------------------------------------------------+
	public function getMaps ($resourceList='') {
		return $this->_get_link (true, $resourceList, DCTL_FILE_MAPPER);
	}
	// +----------------------------------------------------------------------+
	public function getAuthorityName () {
		return '<dctl><stub>UNIMPLEMENTED '.__METHOD__.', JUST A STUB TO CATCH CALLS...</stub></dctl>';
	}
	// +----------------------------------------------------------------------+
	public function getAuthorityIconclass () {
		return '<dctl><stub>UNIMPLEMENTED '.__METHOD__.', JUST A STUB TO CATCH CALLS...</stub></dctl>';
	}
	// +----------------------------------------------------------------------+
	public static function singleton () {
		if (! isset(self::$_instance)) {
			$c = __CLASS__;
			self::$_instance = new $c;
		};
		return self::$_instance;
	}
	// +----------------------------------------------------------------------+
	public function __set($name='', $value='') {
		switch($name) {
			case 'use_private_db':
			case 'debug':
				$name = '_'.$name;
				$oldValue = $this->$name;
				$this->$name = $value;
				if ($oldValue != $value) {
					switch ($name) {
						case '_use_private_db':
							$this->_set_archive_to_use();
							break;
						case '_debug':
							break;
					};
				};
				break;
			default:
				return '<dctl><stub>UNACCESSIBLE '.strtoupper($name).'...</stub></dctl>';
				break;
		};
	}
	// +----------------------------------------------------------------------+
	public function __get($name='') {
		switch($name) {
			case 'use_private_db':
			case 'debug':
			case 'db':
			case 'web_publish_path':
			case 'db_publish_path':
				$name = '_'.$name;
				return $this->$name;
				break;
			default:
				return '<dctl><stub>UNACCESSIBLE '.strtoupper($name).'...</stub></dctl>';
				break;
		};
	}
	// +----------------------------------------------------------------------+
	public function __toString() {
		// | not yet implemented
		return 'This set of classes allows to query the dCTL-Engine';
	}
	// +----------------------------------------------------------------------+
	public function __clone () {
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	// +----------------------------------------------------------------------+
	protected function __construct() {
		// | private & protected
		$this->_xml_header = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		require_once(DCTL_APPS_PATH.'_shared/exist-api.inc.php');
		$this->_db_base_path = XMLDB_DBCTL_BASEPATH;
		$this->_db_publish_path = '';
		$this->_fs_publish_path = '';
		$this->_web_publish_path = '';
		// | public
		$this->_use_private_db = false;
		$this->_debug = false;
		return $this->_set_archive_to_use();
	}
	// +----------------------------------------------------------------------+
	public function __destruct() {
  dctl_xmldb_disconnect($this->_db);
		unset($this);
	}
	// +----------------------------------------------------------------------+
	protected function _set_archive_to_use() {
		$this->_db_publish_path = $this->_use_private_db ? XMLDB_DBCTL_TMP : XMLDB_DBCTL_PUB;
		$this->_fs_publish_path = $this->_use_private_db ? DCTL_DBCTL_TMP : DCTL_DBCTL_PUB;
		$this->_web_publish_path = WWW_HOST.($this->_use_private_db ? WEB_DBCTL_TMP : WEB_DBCTL_PUB);
		// | initialize
		if ($this->_db = dctl_xmldb_connect('admin', true)) {
			try {
				$chk = $this->_db->getCollectionDesc($this->_db_publish_path);
			} catch( SoapFault $e ) {
				$chk = NULL;
			};
			if (($chk->name.DB_PATH_SEP) != $this->_db_publish_path) {
				$this->_db->createCollection($this->_db_publish_path);
				$this->_db->setPermissions($this->_db_publish_path, DCTL_XMLDB_USER_ADMIN, DCTL_XMLDB_GROUP_ADMIN, DCTL_XMLDB_PERMISSIONS_ADMIN);
			};
		};
		$this->_db = dctl_xmldb_connect('query', true);
		if (!$this->_db) {
			$this->__destruct();
			unset($this);
			return false;
		} else {
			return true;
		};
		// if (is_file($this->_fs_publish_path.DCTL_TEXTCLASS)) $this->_classification = loadXML($this->_fs_publish_path.DCTL_TEXTCLASS);
	}
	// +----------------------------------------------------------------------+
	protected function _get_collection_list($justRefs=false, $thePath='', &$collectionList, $withEmpty=false) {
		// | Returns a list of records describing a collection
		$collectionList = array();
		if ($thePath != '') {
			$collectionRecord = array();
			if ($collections = $this->_db->getCollections($thePath)) {
				foreach ((array) @$collections->collections->elements as $key=>$collection) {
					$thePath2 = $thePath.$collection.DB_PATH_SEP;
					$this->_get_collection_record ($justRefs, $thePath2, &$collectionRecord);
					$collectionList[$key]['kind'] = 'collection';
					$collectionList[$key]['ref'] = $collectionRecord['ref'];
					$collectionList[$key]['path'] = $collectionRecord['path'];
					$collectionList[$key]['short'] = $collectionRecord['short'];
					if (! $justRefs) {
						$collectionList[$key]['id'] = $collectionRecord['id'];
						$collectionList[$key]['full'] = $collectionRecord['full'];
						$collectionList[$key]['desc'] = $collectionRecord['desc'];
						$collectionList[$key]['packages'] = $collectionRecord['packages'];
					};
				};
				asort($collectionList);
			};
		};
	}
	// +----------------------------------------------------------------------+
	protected function _get_collection_record ($justRefs=false, $thePath='', &$collectionRecord, $filter= array()) {
		// | Returns a record for the resource:
		// | 'kind'     : framework's kind of resource (collection)
		// | 'id'       : user defined mnemonic identifier
		// | 'ref'      : framework's unique identifier, must be used as a reference to the resource
		// | 'path'     : framework's unique path to the resource in the xmldb
		// | 'short'    : user defined short mnemonic identifier
		// | 'full'     : framework's composite identifier
		// | 'desc'     : user defined description
		// | 'packages' : list of package resources
		$collectionRecord = array();
		$collectionRecord['kind'] = '';
		$collectionRecord['ref'] = '';
		$collectionRecord['path'] = '';
		$collectionRecord['short'] = '';
		if (! $justRefs) {
			$collectionRecord['id'] = '';
			$collectionRecord['full'] = '';
			$collectionRecord['desc'] = '';
		};
		$collectionRecord['packages'] = array();
		$collection_id = basename($thePath);
		if ($collection_id != '') {
			$xml_resource = $thePath.$collection_id.DCTL_RESERVED_INFIX.DCTL_RESERVED_PREFIX.$collection_id.'.xml';
			$xquery = DCTL_XQUERY_BASE;
			$xquery .= "\n".' let $node := doc("'.$xml_resource.'")/tei:TEI ';
			$xquery .= "\n".' let $node1 := $node//tei:projectDesc/tei:p ';
			$xquery .= "\n".' return ';
			$xquery .= "\n".' <node';
			$xquery .= "\n".' ref="{$node/@xml:id}"';
			$xquery .= "\n".' short="{$node1[@n=\'short\']}"';
			if (! $justRefs) {
				$xquery .= "\n".' id="{$node1[@n=\'id\']}"';
				$xquery .= "\n".' desc="{$node/@n}"';
			};
			$xquery .= "\n".'> ';
			$xquery .= "\n".' </node> ';
			if ($result = $this->_db->xquery($xquery)) {
				$resultXML = (array) $result["XML"];
				foreach ($resultXML as $node) {
					$xml_node = $node;
					$xml_node = simplexml_load_string($xml_node, 'SimpleXMLElement', DCTL_XML_LOADER);
					$namespaces = $xml_node->getDocNamespaces();
					foreach ($namespaces as $nsk=>$ns) {
						if ($nsk == '') $nsk = 'tei';
						$xml_node->registerXPathNamespace($nsk, $ns);
					};
					if ((string)$xml_node['ref'] != '') {
						$collectionRecord['kind'] = 'collection';
						$collectionRecord['ref'] = 'xml://'.(string)$xml_node['ref'];
						$collectionRecord['path'] = $xml_resource;
						$collectionRecord['short'] = (string)$xml_node['short'];
						if (! $justRefs) {
							$collectionRecord['id'] = (string)$xml_node['id'];
							$collectionRecord['full'] = cleanWebString($collectionRecord['id'].': '.$collectionRecord['short'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
							$collectionRecord['desc'] = (string)$xml_node['desc'];
							$this->_get_package_list($justRefs, $thePath, &$collectionRecord['packages'], $filter);
						};
					};
				};
			};
		};
	}
	// +----------------------------------------------------------------------+
	protected function _get_package_list($justRefs=false, $thePath='', &$packageList, $filter=array(), $withEmpty=false, $sortBy='date') {
		// | Returns a list of records describing a package
		if (! is_array($filter)) $filter = (array) $filter;
		$packageList = array();
		if ($thePath != '') {
			$packageRecord = array();
			if ($packages = $this->_db->getCollections($thePath)) {
				foreach ((array) @$packages->resources->elements as $key=>$package) {
					$package_id = explode(DCTL_RESERVED_INFIX, $package);
					$collection_id = $package_id[0];
					$package_id = $package_id[1];
					if($package_id[0] != DCTL_RESERVED_PREFIX) {
						$ext = substr($package_id, -8, 4); // _img
						$name = substr($package_id, 0, -7); // marmi_
						if ((count($filter)==0) || (in_array($ext, $filter)) || (preg_grep('/'.$name.'/', $filter))) {
							$thePath2 = $thePath.DB_PATH_SEP.$package;
							$this->_get_package_record ($justRefs, $thePath2, &$packageRecord);
							$packageList[$key]['kind'] = 'package';
							$packageList[$key]['ref'] = $packageRecord['ref'];
							$packageList[$key]['path'] = $packageRecord['path'];
							$packageList[$key]['short'] = $packageRecord['short'];
							$packageList[$key]['type'] = $packageRecord['type'];
							$packageList[$key]['date'] = $packageRecord['date'];
							$packageList[$key]['collection_ref'] = $packageRecord['collection_ref'];
							if (! $justRefs) {
								$packageList[$key]['id'] = $packageRecord['id'];
								$packageList[$key]['full'] = $packageRecord['full'];
								$packageList[$key]['desc'] = $packageRecord['desc'];
								$packageList[$key]['collection'] = $packageRecord['collection'];
								$packageList[$key]['author'] = $packageRecord['author'];
								$packageList[$key]['title'] = $packageRecord['title'];
								$packageList[$key]['publisher'] = $packageRecord['publisher'];
							};
						};
					};
				};
				//  asort($packageList);
				$docx = array();
				$packageList2 = array();
				foreach($packageList as $k=>$package) {
					$packageList2[$package['ref']] = $package[$sortBy];
				};
				asort($packageList2);
				foreach($packageList2 as $packRef=>$dummy) {
					foreach ($packageList as $key=>$package) {
						if ($package['ref'] == $packRef) $docx[$key] = $package;
					};
				};
				$packageList = $docx;
			};
		};
	}
	// +----------------------------------------------------------------------+
	protected function _get_package_record ($justRefs=false, $thePath='', &$packageRecord) {
		// | Returns a record for the resource:
		// | 'kind'           : framework's kind of resource (package)
		// | 'id'             : user defined mnemonic identifier
		// | 'ref'            : framework's unique identifier, must be used as a reference to the resource
		// | 'path'           : framework's unique path to the resource in the xmldb
		// | 'short'          : user defined short mnemonic identifier
		// | 'full'           : framework's composite identifier
		// | 'desc'           : user defined description
		// | 'collection_ref' : framework's unique identifier, must be used as a reference to the resource
		// | 'collection'     : user defined mnemonic identifier
		// | 'type'           : framework's type of resource
		// | 'author'         : <teiHeader> field
		// | 'title'          : <teiHeader> field
		// | 'publisher'      : <teiHeader> field
		// | 'date'           : <teiHeader> field
		$packageRecord = array();
		$packageRecord['kind'] = '';
		$packageRecord['ref'] = '';
		$packageRecord['path'] = '';
		$packageRecord['short'] = '';
		$packageRecord['type'] = '';
		$packageRecord['date'] = '';
		$packageRecord['collection_ref'] = '';
		if (! $justRefs) {
			$packageRecord['id'] = '';
			$packageRecord['full'] = '';
			$packageRecord['desc'] = '';
			$packageRecord['collection'] = '';
			$packageRecord['author'] = '';
			$packageRecord['title'] = '';
			$packageRecord['publisher'] = '';
		};
		$package_id = basename($thePath);
		$package_id = explode(DCTL_RESERVED_INFIX, $package_id);
		$collection_id = isset($package_id[0]) ? $package_id[0] : '';
		$package_id = isset($package_id[1]) ? $package_id[1] : '';
		if ($package_id != '') {
			$thePath = dirname($thePath).DB_PATH_SEP;
			$xml_resource = $thePath.$collection_id.DCTL_RESERVED_INFIX.$package_id;
			$ext = str_ireplace('.xml','', $package_id);
			$ext = substr($ext, -4, 4);
			$xquery = DCTL_XQUERY_BASE;
			$xquery .= "\n".' let $node := doc("'.$xml_resource.'")/tei:TEI ';
			$xquery .= "\n".' let $node1 := $node//tei:samplingDecl/tei:p ';
			$xquery .= "\n".' let $node2 := $node//tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt ';
			$xquery .= "\n".' let $node3 := $node//tei:fileDesc/tei:titleStmt ';
			$xquery .= "\n".' let $node4 := $node//tei:projectDesc/tei:p ';
			$xquery .= "\n".' return ';
			$xquery .= "\n".' <node';
			$xquery .= "\n".' ref="{$node/@xml:id}"';
			$xquery .= "\n".' short="{$node1[@n=\'short\']}"';
			$xquery .= "\n".' type="'.$ext.'"';
			$xquery .= "\n".' date="{$node2/tei:date}"';
			$xquery .= "\n".' collection_ref="{$node4[@n=\'id\']}"';
			if (! $justRefs) {
				$xquery .= "\n".' id="{$node1[@n=\'id\']}"';
				$xquery .= "\n".' collection="{$node4[@n=\'short\']}"';
				$xquery .= "\n".' desc="{$node/@n}"';
				$xquery .= "\n".' author="{$node3/tei:author}"';
				$xquery .= "\n".' title="{$node3/tei:title[@type=\'main\']}"';
				$xquery .= "\n".' publisher="{$node2/tei:publisher}"';
			};
			$xquery .= "\n".'> ';
			$xquery .= "\n".' </node> ';
			if ($result = $this->_db->xquery($xquery)) {
				$resultXML = (array) $result["XML"];
				foreach ($resultXML as $node) {
					$xml_node = $node;
					$xml_node = simplexml_load_string($xml_node, 'SimpleXMLElement', DCTL_XML_LOADER);
					$namespaces = $xml_node->getDocNamespaces();
					foreach ($namespaces as $nsk=>$ns) {
						if ($nsk == '') $nsk = 'tei';
						$xml_node->registerXPathNamespace($nsk, $ns);
					};
					if ((string)$xml_node['ref'] != '') {
						$packageRecord['kind'] = 'package';
						$packageRecord['ref'] = 'xml://'.str_ireplace((string)$xml_node['collection_ref'].DCTL_RESERVED_INFIX, (string)$xml_node['collection_ref'].DB_PATH_SEP, (string)$xml_node['ref']);
						$packageRecord['path'] = $xml_resource;
						$packageRecord['short'] = (string)$xml_node['short'];
						$packageRecord['type'] = (string)$xml_node['type'];
						$packageRecord['date'] = (string)$xml_node['date'];
						$packageRecord['collection_ref'] = 'xml://'.(string)$xml_node['collection_ref'];
						if (! $justRefs) {
							$packageRecord['id'] = (string)$xml_node['id'];
							$packageRecord['full'] = cleanWebString($packageRecord['id'].': '.$packageRecord['short'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
							$packageRecord['desc'] = (string)$xml_node['desc'];
							$packageRecord['collection'] = (string)$xml_node['collection'];
							$packageRecord['author'] = (string)$xml_node['author'];
							$packageRecord['title'] = (string)$xml_node['title'];
							$packageRecord['publisher'] = (string)$xml_node['publisher'];
						};
					};
				};
			};
		};
	}
	// +----------------------------------------------------------------------+
	protected function _parse_uri($resourceList='', $xpath='') {
		// | an array is needed
		if (! is_array($resourceList)) {
			$rs = (split(",", $resourceList));
			$resourceList = array();
			$next = true;
			foreach ($rs as $k=>$str) {
				if ((substr_count ($str,'"') % 2) && ($k>0) && ($next)) {
					$resourceList[$k-1] .= ','.$str;
					$next = false;
				} else {
					$resourceList[$k] = $str;
					$next = true;
				};
			};
		};
		$resourceList = array_filter(array_unique($resourceList));
		$docList = array();
		foreach($resourceList as $k=>$resource) {
			$resource = trim($resource).(($xpath != '') ? '?'.$xpath: '');
			// | force xml:// scheme if none present
			if (! preg_match('/^(([^:\/?#]+):)/i', $resource)) {
				$resource = 'xml://'.$resource;
			};
			// | parse URI
			$parsed = array();
			$preg_match = array();
			$parsed['source'] = $resource;
			if (preg_match('/^(([^:\/?#]+):)?(\/\/([^\/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/i', $resource, $preg_match)) {
				$parsed['scheme']    = isset($preg_match[2]) ? (($preg_match[2] != '') ? $preg_match[2] : '') : '';
				$parsed['authority'] = isset($preg_match[4]) ? (($preg_match[4] != '') ? $preg_match[4].'/' : '') : '';
				$parsed['path']      = isset($preg_match[5]) ? (($preg_match[5] != '') ? $preg_match[5].'/' : '') : '';
				$parsed['query']     = isset($preg_match[7]) ? (($preg_match[7] != '') ? '?'.($preg_match[7]) : '') : '';
				$parsed['anchor']    = isset($preg_match[9]) ? (($preg_match[9] != '') ? '#'.$preg_match[9] : '') : '';
				$parsed['uri'] = $parsed['scheme'].'://'.$parsed['authority'].$parsed['path'].$parsed['query'].$parsed['anchor'];
				switch ($parsed['scheme']) {
					case 'xml':
					case 'img': {
							$parsed['collection'] = $parsed['authority'];
							$parsed['path'] = str_ireplace(DB_PATH_SEP.DB_PATH_SEP, DB_PATH_SEP, $parsed['path']);
							$path = explode(DB_PATH_SEP, $parsed['path']);
							$parsed['package'] = isset($path[1]) ? $path[1] : '';
							$parsed['locator'] = str_ireplace(DB_PATH_SEP.$parsed['package'].DB_PATH_SEP, '', $parsed['path']);
							$normalize = '';
							$normalize .= DB_PATH_SEP.str_ireplace(DCTL_RESERVED_INFIX, DB_PATH_SEP, $parsed['collection']).DB_PATH_SEP;
							$normalize .= str_ireplace(DCTL_RESERVED_INFIX, DB_PATH_SEP, $parsed['package']).DB_PATH_SEP;
							$normalize = str_ireplace(DB_PATH_SEP.DB_PATH_SEP, DB_PATH_SEP, $normalize);
							$rebuilt = array_filter(explode(DB_PATH_SEP, $normalize));
							$unwanted = '(\/$)*|(\*_+\*)*|(\*)*';
							$parsed['collection'] = preg_replace('/'.$unwanted.'/', '', isset($rebuilt[1]) ? $rebuilt[1] : '');
							$parsed['package'] = preg_replace('/'.$unwanted.'/', '', isset($rebuilt[2]) ? $rebuilt[2] : '');
							$parsed['locator'] = preg_replace('/\/$/', '', stripslashes($parsed['locator']));
							$parsed['query'] = preg_replace('/^\?/', '', stripslashes($parsed['query']));
							$parsed['anchor'] = preg_replace('/^\#/', '', stripslashes($parsed['anchor']));
						};
						break;
					default:
						break;
				};
			};
			$docList[$k] = array_map('trim', $parsed);
		};
		return $docList;
	}
	// +----------------------------------------------------------------------+
	protected function _get_resource ($justRefs=false, $resourceList='', $xpath='') {
		$asOptions = $xpath != '';
		$xpath = trim($xpath);
		$resourceList = $this->_parse_uri($resourceList, $xpath);
		$docList = array();
		foreach($resourceList as $key4docList=>$parsed) {
			$resList = array();
			switch ($parsed['scheme']) {
				case 'xml':
				case 'img': {
						switch ($parsed['scheme']) {
							case 'xml': {
									if ($parsed['collection'] == '') {
										$this->_get_collection_list(false, $this->_db_publish_path, &$resList);
									} else {
										$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP;
										if ($parsed['package'] == '') {
											$this->_get_collection_record(false, $xml_resource, &$resList[]);
										} else {
											if (preg_match('/^(_\w{3})|(\w*_)$/',  $parsed['package'], $preg_match)) {
												$this->_get_package_list(false, $xml_resource, &$resList, $preg_match[0]);
											} else {
												$package = (preg_match('/\.xml$/i', $parsed['package']) ? $parsed['package'] : $parsed['package'].'.xml');
												$xml_resource .= $parsed['collection'].DCTL_RESERVED_INFIX.$package;
												$this->_get_package_record(false, $xml_resource, &$resList[]);
// per ritornare almeno la collezione, ma non mi pare coerente
// 	          if (!$resList[0]['ref']) {
// 	  									$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP;
//             $this->_get_collection_record(true, $xml_resource, &$resList[]);
// 	          };
											};
										};
										if (($parsed['locator'] != '') || ($parsed['query'] != '') || ($parsed['anchor'] != '') || ($asOptions)) {
											$packageList = $resList;
											unset($resList);
											$resList = array();
											foreach ($packageList as $key4package=>$package) {
												$parsed_locator = $parsed['locator'];
												$parsed_query = $parsed['query'];
												$parsed_anchor = $parsed['anchor'];
												$xml_resource = $package['path'];
												$db_resource = '';
												$partial = false;
												$context = '';
												$context .= (($parsed_locator == '') && ($parsed_query == '')) ? '/' : ''; // per errore: un solo & !!!
												$context .= ($parsed_locator != '') ? '/id("'.$parsed_locator.'")' : '';
												$context .= ($parsed_query) ? $parsed_query : '';
												$last_attr = '';
												$last_val = '';
												if (preg_match('/\@(\w+)'.WS.'*.?'.WS.'*='.WS.'*"'.WS.'*(.*)'.WS.'*"'.WS.'*/', $context, $matches)) {
													$last_attr = $matches[count($matches)-2];
													$last_val = $matches[count($matches)-1];
												};
												$match = str_ireplace('*', '', $last_val);
												$tag = '';
												$absLevel = '';
												$startAt = '';
												$howMany = '';
												$upTo = '';
												$forced = false;
												$jolly = false;
												$withHierarchy = false;
												$withPage = false;
												$atPage = false;
												if (preg_match('/(\$\(*(.*)\)*)/', $parsed_anchor, $matchesX)) {
													$parsed_anchor = preg_replace('/'.escapeshellcmd($matchesX[0]).'/', '', $parsed_anchor);
													$extenders = explode('&', preg_replace('/[\$\(\)]/', '', $matchesX[0]));
													$withHierarchy = array_search('hier', $extenders) !== FALSE;
													$withPage = array_search('page', $extenders) !== FALSE;
												};
												if ($asOptions) {
													if ($parsed_query != '') {
														if (preg_match('/^'.WS.'*\@'.WS.'*(\w+)'.WS.'*\&*\|*'.WS.'*\='.WS.'*\".*\"'.WS.'*$/', $context, $matchesX) ) {
															$forced = true;
															$context = '//*['.$context.']';
														};
													};
													switch (true) {
														case preg_match('/^'.WS.'*(\w*|\.)'.WS.'*(\@'.WS.'*\"'.WS.'*((\w|\W|\*)(?:\+*)*)'.WS.'*(\+*)'.WS.'*(.*)'.WS.'*\"'.WS.'*(\;'.WS.'*(\-*\d+)'.WS.'*)?)/', $parsed_anchor, $matches):
															$tag = isset($matches[1]) ? $matches[1] : $tag;
															$startAt = isset($matches[3]) ? $matches[3] : $startAt;
															$upTo = isset($matches[4]) ? (($matches[4] != '') ? escapeshellcmd($startAt) : '') : '';
															$startAt = isset($matches[6]) ? $startAt.$matches[6] : $startAt;
															$howMany = ($startAt) ? 1 : $startAt;
															$howMany = isset($matches[8]) ? (($matches[8] != '') ? intval($matches[8]) : $howMany) : $howMany;
															if ($tag == '') $tag = $last_attr;
															$jolly = stripos($startAt, '*');
															if ($jolly !== false) {
																$startAt = substr($startAt,0,$jolly);
																$upTo = escapeshellcmd($startAt).'.';
																if (!isset($matches[8])) $howMany = '';
																++$jolly;
															};
														 $justContent = ($tag == '.');
															if ($justContent) {
															 //$tag = './/text()[normalize-space(.) != ""][position() = 1]';
															 $tag = '.';
															} else {
															 $tag = '@'.$tag;
															};
															$context .= '[lower-case('.$tag.') >= "'.strtolower($startAt).'"]';
															if ($upTo != '') $context .= '[matches('.$tag.', "^'.$upTo.'", "msi")]';
															if ($justContent) {
																if ($jolly) {
																 $context .= '//text()'; // cosi ritorna le iniziali ma non dioscoride
															 } else {
															  $context .= ''; // cosi ritorna dioscoride ma non le iniziali
															 };
															} else {
															 $context .= '/'.$tag;
															};
															$startAt = 1;
															break;
														default:
															if ($parsed_query != '') {
																if ($forced) {
																	$context .= '/@'.$matchesX[1];
																};
															} else {
																$context .= ($parsed_locator == '') ? 'tei:div[.//text()]' : '';
															};
															break;
													};
												} else {
													switch (true) {
														// #page : la <pb> precedente il nodo
														case preg_match('/^'.WS.'*(page)'.WS.'*/', $parsed_anchor, $matches):
														 $atPage = true;
															break;
														// #divX : la <div> di livello assoluto X che contiene il nodo
														case preg_match('/^'.WS.'*(div)'.WS.'*(\-*\d+)/', $parsed_anchor, $matches):
															$tag = isset($matches[1]) ? $matches[1] : $tag;
															$absLevel = isset($matches[2]) ? abs(strval($matches[2])) : '';
															$context .= '/ancestor-or-self::tei:'.$tag.'[count(ancestor::tei:'.$tag.')='.($absLevel-1).']'.'[child::text() or child::node()]';
															break;
														// #div : le <div> children del nodo
														// #div@X
														// #div@X;Z
														case preg_match('/^'.WS.'*(div)'.WS.'*($|\@'.WS.'*(\-*\d+)'.WS.'*(\;'.WS.'*(\-*\d+))?)/', $parsed_anchor, $matches):
															$tag = isset($matches[1]) ? $matches[1] : $tag;
															$startAt =  isset($matches[3]) ? abs(intval($matches[3])) : $startAt;
															$howMany =  isset($matches[5]) ? intval($matches[5]) : (($startAt) ? 1 : $startAt);
															$context .= (($parsed_locator != '') | ($parsed_query != '')) ? '/' : '';
															$context .= 'tei:'.$tag.'[child::text() or child::node()]';
															break;
														// #pb : le <pb> children del nodo
														// #pb@X
														// #pb@X;Z
														case preg_match('/^'.WS.'*(pb)'.WS.'*($|\@'.WS.'*(\-*\d+)'.WS.'*(\;'.WS.'*(\-*\d+))?)/', $parsed_anchor, $matches):
															$tag = isset($matches[1]) ? $matches[1] : $tag;
															$startAt =  isset($matches[3]) ? abs(intval($matches[3])) : $startAt;
															$howMany =  isset($matches[5]) ? intval($matches[5]) : (($startAt) ? 1 : $startAt);
															$context .= (($parsed_locator != '') | ($parsed_query != '')) ? '/' : '';
															$context .= '/tei:'.$tag.'[not(@ed = "fake")]';
															break;
														//
														default:
															if ($parsed_query != '') {
															} else {
																$context .= ($parsed_locator == '') ? 'tei:div[child::text() or child::node()]' : '';
															};
															break;
													};
												};
												$xquery = '';
												if ($howMany < 0) $howMany = PHP_INT_MAX;
												if ($asOptions) { // from getOptions
													$xquery .= "\n".' let $e := for $node in ';
													$xquery .= "\n".' xmldb:document("';
													$xquery .= $xml_resource.'")//tei:text/*'.$context.' ';
// 													$xquery .= "\n".' let $chunk := ';
// 													$xquery .= "\n".' if ($node instance of text()) then $node else $node//text() ';
													$xquery .= "\n".' return ';
													$xquery .= "\n".' if ($node/node()) ';
													$xquery .= "\n".'  then ';
// 													$xquery .= "\n".' ("<item><", node-name($node), for $att in $node/@* return (" ", node-name($att), "=&quot;", $att, "&quot;"), ">",';
// 													$xquery .= "\n".' $chunk, "</", node-name($node), "></item>") ';
// 													$xquery .= "\n".' element item { element {node-name($node)} {$node/@*, text {$chunk}} } ';
														$xquery .= "\n".'  $node ';

													$xquery .= "\n".'  else ';
													$xquery .= "\n".'   let $what := if (/id($node)) then ';
													$xquery .= "\n".'    tokenize(tokenize($node, "'.WS.'"), "'.WS.'") else ';
// 													if ($jolly) {
													$xquery .= ' $node ';
// 														} else {
// 														$xquery .= "\n".' element {node-name($node)} {$node/@*, text {$chunk}} ';
// 													};
													$xquery .= "\n".'    for $item in distinct-values( ';
 													$xquery .= "\n".'     for $token in $what ';
//													$xquery .= "\n".'     for $token in $node ';
													$xquery .= "\n".'      let $include := if ($node/node() or ('.!$last_val.') or name($node) != "'.$last_attr.'") then true() ';
													$xquery .= ' else contains($token, tokenize("'.$match.'", "'.WS.'")) ';
													$xquery .= "\n".'      return if ($include) then $token else () ';
													$xquery .= "\n".'    ) return ';
													if ($jolly) {
														$xquery .= ' lower-case(substring($item,1,'.$jolly.')) ';
													} else {
														$xquery .= ' $item ';
													};
													$xquery .= "\n".' return ';
													if ($howMany) $xquery .= "\n".' for $final in subsequence(';
// 													$xquery .= "\n".' if (matches($e, "<\w+")) then $e else for $x in distinct-values($e) order by $x return <item>{$x}</item> ';
													$xquery .= "\n".'  for $x in distinct-values($e) order by $x return element item {normalize-space($x)} ';
													if ($howMany) $xquery .= ', '.$startAt.', '.$howMany.' ) return $final ';
												} else {
													$xquery .= "\n".' let $base := xmldb:document("'.$xml_resource.'")//tei:text ';
													$xquery .= "\n".' for $node in ';
													if ($howMany) $xquery .= ' subsequence(';
													$xquery .= "\n".' $base/*';
													$xquery .= $context;
													$xquery .= ' ';
													if ($howMany) $xquery .= ', '.$startAt.', '.$howMany.' ) ';
													if ($justRefs) {
														$xquery .= "\n".' let $chunk := ';
														$xquery .= ' if (local-name($node) = "figure") then ';
														$xquery .= ' $node/tei:figDesc//text() else ';
														$xquery .= ' if (local-name($node) = "graphic") then ';
														$xquery .= ' $node/parent::tei:figure/tei:figDesc//text() else ';
														$xquery .= ' if ($node//text() != "") then ';
														$xquery .= ' $node//text() else '; ///*[not(self::tei:figDesc)]
														$xquery .= ' if ($node/parent::*//text() != "") then ';
														$xquery .= ' subsequence($node/parent::*//text(), 1)[. >> $node][position() < 5] else ';
														$xquery .= ' $node//text() ';
			 										if ($atPage) {
															$xquery .= "\n".' let $node := tei:getPage($node, 2) ';
														};
														$xquery .= "\n".' let $nodeT := element {node-name($node)} {$node/@*, text {text:kwic-display($chunk, 80, $highlight, ())}} ';
														if ($withPage) {
														 $xquery .= "\n".' let $nodeT := functx:add-attributes($nodeT, xs:QName("synch"), tei:getPage($node, 1)) ';
														};
														$xquery .= "\n".' return ';
														if ($withHierarchy) {
															$xquery .= "\n".' tei:getTree($node, $nodeT) ';
														} else {
															$xquery .= "\n".' $nodeT ';
														};
													} else {
														$xquery .= "\n".' return ';
														$xquery .= "\n".' if (not($base/descendant::dctl:*[1])) ';
														$xquery .= "\n".' then tei:getPage($node, 0) ';
														$xquery .= "\n".' else tei:getBlock($node) ';
													};
												};
// $this->_getDebug($parsed);
// $this->_getDebug($context);
// $this->_getDebug($xquery);
												if ($this->_debug && NOVEOPIU) {
													$xq = DCTL_TMP_PATH.'xquery_dbg'.basename($xml_resource).'.xq';
													@file_put_contents($xq, DCTL_XQUERY_BASE.str_ireplace('  ', ' '."\n".' ', $xquery));
												};
												$result = $this->_db->xquery(DCTL_XQUERY_BASE.$xquery);
												$gotError = $this->_db->getError();
												$resultXML = (array) $result["XML"];
												foreach ($resultXML as $node) {
													$node = preg_replace('/<pb.*ed\s*=\s*".*fake.*".*>/', '', $node); // strip out <pb ed="fake" />
													if ($withHierarchy) {
													 $node = preg_replace('/(<div.*)xml:id(\s*=\s*".*".*>)/', '$1sameAs$2', $node); // strip out <pb ed="fake" />
													};
													$db_resource .= $node;
												};
												// $this->_getDebug($db_resource);
												$this->_get_package_record ($justRefs, $xml_resource, &$resList[$key4package]);
												$resList[$key4package]['xquery'] = htmlentities($context);
												if ($resList[$key4package]['ref'] != '') {
													if ($parsed_locator != '') $resList[$key4package]['ref'] .= DB_PATH_SEP.$parsed_locator;
												};
// 											if (preg_match('/\w+/', $db_resource)) {
//             $resList[$key4package]['check'] = preg_match_all('/\<\w+/', $db_resource, $matches);
// 											};
												if ($gotError) {
													if ($this->_debug) {
														$this->_getDebug($gotError);
														// $this->_getDebug($context);
														// $this->_getDebug($xquery);
													};
													$resList[$key4package]['error'] = '<!--[CDATA[:'."\n".$gotError.' in :'."\n".$xquery."\n".']]-->';
												} else {
													if ($asOptions) {
														$resList[$key4package]['kind'] = "list";
														$resList[$key4package]['list'] = $db_resource;
													} else {
														$resList[$key4package]['kind'] = "tei";
														$resList[$key4package]['fragment'] = $db_resource;
													};
												};
											};
										};
									};
								};
								break;
							// · · · · · · · · · · · · · · · · · · · · · · · · · · · · · · · ·
							case 'img': {
									$parsed['query'] = $parsed['locator'];
									// | media -> img://coll-pippero.pdf, img://coll/pippero.pdf
									$parsed['package'] = $parsed['collection'].DCTL_RESERVED_INFIX.$parsed['package'];
									$parsed['collection'] = '';
									if ($parsed['query'] != '') {
										$parsed['package'] .= DCTL_RESERVED_INFIX.$parsed['query'];
										$parsed['query'] = '';
									};
									$resList[0]['kind'] = "media";
									$resList[0]['ref'] = '';
									$resList[0]['icon'] = '';
									$resList[0]['file'] = '';
									$hi = $this->_fs_publish_path.DCTL_MEDIA_BIG.$parsed['package'];
									if (is_file($hi)) {
										$lo = $this->_fs_publish_path.DCTL_MEDIA_SML.$parsed['package'];
										if (! is_file($lo)) {
											makePreview (DCTL_MEDIA_SML, $hi);
										};
										$resList[0]['ref'] = $parsed['scheme'].'://'.$parsed['package'];
										$resList[0]['icon'] = $this->_web_publish_path.DCTL_MEDIA_SML.$parsed['package'];
										$resList[0]['file'] = $this->_web_publish_path.DCTL_MEDIA_BIG.$parsed['package'];
									};
								};
								break;
						};
					};
					break;
				default:
					$resList[0]['kind'] = "url";
					$resList[0]['ref'] = $resource;
					break;
			};
			$docList = array_merge($docList, $resList);
		};
		if (! $asOptions) {
			// wraps results
			return $this->_resources_to_xml($docList);
		} else {
			// raw results
			return $docList;
		};
	}
	// +----------------------------------------------------------------------+
	protected function _get_link ($justRefs=false, $resourceList='', $resFile = '') {
		$resourceList = $this->_parse_uri($resourceList);
		$docList = array();
		foreach($resourceList as $key4docList=>$parsed) {
			$resList = array();
			$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP.$parsed['collection'].DCTL_RESERVED_INFIX.$resFile;
			$db_resource = '';
			switch($resFile) {
				case DCTL_FILE_LINKER:
					$fullItem = $parsed['scheme'].'://'.$parsed['collection'].DB_PATH_SEP.$parsed['package'].DB_PATH_SEP.$parsed['locator'];
					$s1 = ' '.$fullItem;
					$s2 = $fullItem.' ';
					$s3 = ' '.$fullItem.' ';
					$context = '//tei:ref[index-of(distinct-values(tokenize(@target, "\s+", "msi")), "'.$fullItem.'") > 0]';
					$xquery = '';
					$xquery .= "\n".' for $node in ';
					$xquery .= "\n".' xmldb:document("';
					$xquery .= $xml_resource.'")/tei:TEI/tei:text/*'.$context.' ';
					$xquery .= "\n".' return ';
					$xquery .= "\n".' ("<group n=&quot;", $node/@n, "&quot;>", ';
					$xquery .= "\n".' for $target in distinct-values(tokenize($node/@target, "\s+")) ';
					$xquery .= "\n".' let $id := tokenize ($target, "/") ';
					$xquery .= "\n".' let $doc := concat("'.$this->_db_publish_path.'", $id[3], "/", $id[3], "-", $id[4], ".xml") ';
					$xquery .= "\n".' return ';
					$xquery .= "\n".' if ($target = "'.$fullItem.'") then () else ';
					if (preg_match('/^_\w{3}$/', $parsed['query'])) {
						$xquery .= "\n".' if (not(substring-before($id[4], "'.$parsed['query'].'"))) then () else ';
					};
					$xquery .= "\n".' ("<anchor target=&quot;", $target, "&quot; status=&quot;", ';
					$xquery .= "\n".' if (doc-available($doc)) then let $a := doc($doc)/id($id[5]) return if ($a) then ("ok&quot; rend=&quot;", $a/@rend, "&quot;") else ("ko&quot;") else ("ko&quot;") ';
					$xquery .= ' , " />") ';
					$xquery .= ' , "</group>") ';
					if ($this->_debug && NOVEOPIU) {
						$xq = DCTL_TMP_PATH.'xquery_dbg'.basename($xml_resource).'.xq';
						@file_put_contents($xq, DCTL_XQUERY_BASE.str_ireplace('  ', ' '."\n".' ', $xquery));
					};
					$result = $this->_db->xquery(DCTL_XQUERY_BASE.$xquery);
					$resultXML = (array) $result["XML"];
					foreach ($resultXML as $node) {
						$db_resource .= $node;
					};
					$key4package = 0;
					$package = (preg_match('/\.xml$/i', $parsed['package']) ? $parsed['package'] : $parsed['package'].'.xml');
					$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP.$parsed['collection'].DCTL_RESERVED_INFIX.$package;
					$this->_get_package_record (true, $xml_resource, &$resList[$key4package]);
					$resList[$key4package]['xquery'] = htmlentities($context);
					if ($resList[$key4package]['ref'] != '') {
						if ($parsed['locator'] != '') $resList[$key4package]['ref'] .= DB_PATH_SEP.$parsed['locator'];
					};
					$resList[$key4package]['kind'] = "link";
					$resList[$key4package]['link'] = $db_resource;
					break;
				case DCTL_FILE_MAPPER:
				 switch ($parsed['scheme']) {
				  case 'xml':
							$fullItem = $parsed['scheme'].'://'.$parsed['collection'].DB_PATH_SEP.$parsed['package'].DB_PATH_SEP.$parsed['locator'];
							$s1 = ' '.$fullItem;
							$s2 = $fullItem.' ';
							$s3 = ' '.$fullItem.' ';
							$context = '//tei:ref[(@target = "'.$s1.'") or contains(@target, "'.$s2.'") or contains(@target, "'.$s3.'") or (@target = "'.$fullItem.'")]';
				   break;
				  case 'img':
  					$fullItem = $parsed['scheme'].'://'.$parsed['collection'].DB_PATH_SEP.$parsed['package'].'@';
							$s1 = str_ireplace($parsed['collection'].DB_PATH_SEP, $parsed['collection'].'-', $fullItem);
							$s2 = str_ireplace($parsed['collection'].'-', $parsed['collection'].DB_PATH_SEP, $fullItem);
							$context = '//tei:ref[contains(@target, "'.$s1.'") or contains(@target, "'.$s2.'")]';
							break;
					};

					$xquery = '';
					$xquery .= "\n".' for $node in ';
					$xquery .= "\n".' xmldb:document("';
					$xquery .= $xml_resource.'")/tei:TEI/tei:text/*'.$context.' ';
					$xquery .= "\n".' let $coord := "" ';
				 switch ($parsed['scheme']) {
				  case 'xml':
							$xquery .= "\n".' let $coord := "" ';
				   break;
				  case 'img':
							$xquery .= "\n".' let $coord := ("@", substring-after(distinct-values(tokenize($node/@target, " "))[contains(., "'.$s1.'") or contains(., "'.$s2.'")], "@")) ';
				   break;
					};
					$xquery .= "\n".' return ';
 				$xquery .= "\n".' ("<group n=&quot;", $node/@n, "&quot;>", ';
					$xquery .= "\n".' for $target in distinct-values(tokenize($node/@target, " ")) ';
				 switch ($parsed['scheme']) {
				  case 'xml':
							$xquery .= "\n".' let $id := tokenize ($target, "//") ';
							$xquery .= "\n".' let $id := tokenize ($id[2], "@") ';
							$xquery .= "\n".' let $doc := concat("'.$this->_fs_publish_path.'", "'.DCTL_MEDIA_BIG.'", $id[1] ) ';
				   break;
				  case 'img':
							$xquery .= "\n".' let $id := tokenize ($target, "/") ';
							$xquery .= "\n".' let $doc := concat("'.$this->_db_publish_path.'", $id[3], "/", $id[3], "-", $id[4], ".xml") ';
				   break;
				 };
					$xquery .= "\n".' return ';
					$xquery .= "\n".' if ($target = "'.$fullItem.'") then () else ';
				 switch ($parsed['scheme']) {
				  case 'xml':
				   break;
				  case 'img':
  					$xquery .= "\n".' if (not(contains($target, "xml://"))) then () else ';
				   break;
				 };
					$xquery .= "\n".' ("<anchor target=&quot;", $target, $coord, "&quot; status=&quot;", ';
				 switch ($parsed['scheme']) {
				  case 'xml':
							// $xquery .= ' ("unknown&quot;") ';
							$xquery .= ' "not yet implemented: status check&quot;" ';
				   break;
				  case 'img':
							$xquery .= ' if (doc-available($doc)) then let $a := doc($doc)/id($id[5]) return if ($a) then ("ok&quot; rend=&quot;", $a/@rend, "&quot;") else ("ko&quot;") else ("ko&quot;") ';
				   break;
				 };
					$xquery .= ' , " />") ';
					$xquery .= ' , "</group>") ';
					if ($this->_debug && NOVEOPIU) {
						$xq = DCTL_TMP_PATH.'xquery_dbg'.basename($xml_resource).'.xq';
						@file_put_contents($xq, DCTL_XQUERY_BASE.str_ireplace('  ', ' '."\n".' ', $xquery));
					};
					$result = $this->_db->xquery(DCTL_XQUERY_BASE.$xquery);
					$resultXML = (array) $result["XML"];
					foreach ($resultXML as $node) {
						$db_resource .= $node;
					};
					$key4package = 0;
				 switch ($parsed['scheme']) {
				  case 'xml':
							$package = (preg_match('/\.xml$/i', $parsed['package']) ? $parsed['package'] : $parsed['package'].'.xml');
							$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP.$parsed['collection'].DCTL_RESERVED_INFIX.$package;
							$this->_get_package_record (true, $xml_resource, &$resList[$key4package]);
							if ($resList[$key4package]['ref'] != '') {
								if ($parsed['locator'] != '') $resList[$key4package]['ref'] .= DB_PATH_SEP.$parsed['locator'];
							};
				   break;
				  case 'img':
							$collection = (preg_match('/\.xml$/i', $parsed['collection']) ? $parsed['collection'] : $parsed['collection'].'.xml');
							$xml_resource = $this->_db_publish_path.$parsed['collection'].DB_PATH_SEP;
       $this->_get_collection_record (true, $xml_resource, &$resList[]);
				   break;
				 };
					$resList[$key4package]['xquery'] = htmlentities($context);
					$resList[$key4package]['kind'] = "link";
					$resList[$key4package]['link'] = $db_resource;
					break;
			};
			$docList = array_merge($docList, $resList);
		};
		// wraps results
		return $this->_resources_to_xml($docList);
	}
	// +----------------------------------------------------------------------+
	protected function _resources_to_xml($resourceList, $recursion=false) {
		// | convert record to xml
		$return = '';
		if (! is_array($resourceList)) $resourceList = explode(',', $resourceList);
		if (! $recursion) {
			$return .= $this->_xml_header.'<dctl version="'.APPS_VERSION.'">';
		};
		foreach ($resourceList as $resource) {
			if (! is_array($resource)) $resource = explode(',', $resource);
			if (! empty($resource['ref'])) {
				$return .= '<resource>';
				foreach($resource as $k=>$v) {
					if (is_array($v)) {
						$v = $this->_resources_to_xml($v, true);
					};
					$return .= '<'.$k.'>'.$v.'</'.$k.'>';
				};
				$return .= '</resource>';
			};
		};
		if (! $recursion) {
			$return .= '</dctl>';
		};
		$return = preg_replace('/\s\s+/imu', ' ', $return);
		$return = preg_replace('/xmlns\s*=\s*"\s*'.preg_quote(XMLDB_TEI_S, '/').'\s*"/', '', $return);
  return $return;
	}
	// +----------------------------------------------------------------------+
	protected function _comma2pipe($a) {
		return str_replace(',', chr(0), $a[0]);
	}
	// +----------------------------------------------------------------------+
	protected function _getDebug ($text='** DEBUG **', $class='') {
		if ($this->_debug) {
			dump($text, $class);
		};
	}
	// +----------------------------------------------------------------------+
};
// +----------------------------------------------------------------------+
// | END OF MAIN CLASS DEFINITION
// +----------------------------------------------------------------------+
// |
// +----------------------------------------------------------------------+
// | RETRIEVER CLASS DEFINITION
// +----------------------------------------------------------------------+
class dCTLRetriever extends dCTL {
	protected function __construct() {
		parent::__construct();
	}
	// +----------------------------------------------------------------------+
};
// +----------------------------------------------------------------------+
// | END OF RETRIEVER CLASS DEFINITION
// +----------------------------------------------------------------------+
