<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function dctl_segmentize($theString) {
	$resultText = '::';
 $theArray = explode(DISTINCT_SEP2, $theString);
	$hash = array();
 foreach($theArray as $idx=>$theLine) {
		$thisLine = $theArray{$idx};
		if ($thisLine != '') {
			$theValues = explode(DISTINCT_SEP, $thisLine);
			$theCantoLabel = $theValues[0];
			$theCanto = intval(substr($theValues[1], 1));
			$theOctave = intval($theValues[2]);
			$theSegmentStart = intval($theValues[3]);
			$theSegmentEnd = intval($theValues[4]);
			$theOctaveCount = intval($theValues[5]);
			if ((($theSegmentStart == 1) && ($theSegmentEnd == $theOctaveCount)) || ($theOctaveCount == 0) || ($theSegmentStart == 0) || ($theSegmentEnd == 0)) {
				$theVerse = '';
			} else {
				if ($theSegmentStart != $theSegmentEnd) {
					$theVerse = ', vv.'.$theSegmentStart.'-'.$theSegmentEnd;
				} else {
					$theVerse = ', v.'.$theSegmentStart;
				};
			};
			$hash[$idx]['c'] = $theCanto;
			$hash[$idx]['l'] = $theCantoLabel;
			$hash[$idx]['o'] = $theOctave;
			$hash[$idx]['v'] = $theVerse;
  };
	};
	$idx = count($hash)-1;
	while (isset($hash[$idx-1])) {
	 if ($hash[$idx]['c'] != $hash[$idx-1]['c'])
	  $resultText = '; '.$hash[$idx]['l'].$resultText;
	 --$idx;
	};
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function SimpleXMLElementObj_into_xml($xml_parent, $xml_children, $linkingNode= "linkingNode" , $child_count = 0 , $simplexml = false ){
 if(!$simplexml)    {
  $simplexml = $xml_parent->addChild($linkingNode);
 }else{
  $simplexml = $xml_parent[$child_count];
 }
 $child_count = 0;
 foreach($xml_children->children() as $k => $v)    {
  if($simplexml->$k){
   $child_count++;
  }
  if($v->children())        {
   $simplexml->addChild($k);
   SimpleXMLElementObj_into_xml($simplexml->$k, $v, '', $child_count, true);
  }else{
   $simplexml->addChild($k, $v);
  }
 }
 return $simplexml;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function simplexml_append(SimpleXMLElement $parent, SimpleXMLElement $new_child){
 $node1 = dom_import_simplexml($parent);
 $dom_sxe = dom_import_simplexml($new_child);
 $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
 $node1->appendChild($node2);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function simplexml_insert(SimpleXMLElement $parent, SimpleXMLElement $new_child){
 $node1 = dom_import_simplexml($parent);
 $dom_sxe = dom_import_simplexml($new_child);
 $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
 $node1->parentNode->insertBefore($node2, $node1);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function generateId($random_id_length = 10)  {
 //generate a random id encrypt it and store it in $rnd_id
 $rnd_id = crypt(uniqid(rand(),1));
 //to remove any slashes that might have come
 $rnd_id = strip_tags(stripslashes($rnd_id));
 //Removing any . or / and reversing the string
 $rnd_id = str_replace(".","",$rnd_id);
 $rnd_id = strrev(str_replace("/","",$rnd_id));
 //finally I take the first $random_id_length characters from the $rnd_id
 return substr($rnd_id,0,$random_id_length);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getNiceId ($id, $niceId) {
 if (! preg_match('/^p\d{3}/', $id)) {
  $id = $niceId.'_'.$id;
 };
	return $id;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_deleteLink($id1='', $id2='', $label='', $what='')  {
 $resultText = '';
	$resultOK = '';
	$resultKO = '';
	$collection_id = explode('-', $id1);
	$collection_id = $collection_id	[0];
	$thePath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR;
	switch ($what) {
  case 'lnk':
			$thePath .= DCTL_FILE_LINKER;
		break;
  case 'map':
			$thePath .= DCTL_FILE_MAPPER;
		break;
  default:
				$resultKO .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
  break;
 };
	if (is_file($thePath)) {
		$file_content = file_get_contents($thePath);
		$file_content = preg_replace('/'.WHITESPACES.'+/',' ',$file_content);
		$text_head = substr($file_content,0,stripos($file_content,'%BEGIN%')).'%BEGIN% -->';
		$text_foot = '<!-- '.substr($file_content,stripos($file_content,'%END%'));
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
		forceUTF8($thePath);
		if ($dom->load($thePath, DCTL_XML_LOADER)) {
			$xpath = new DOMXPath($dom);
		 switch ($id2) {
    case '': // elimina un collegamento intero
					$query = 'id("'.$id1.'")';
					$entries = $xpath->query($query);
					foreach ($entries as $entry) {
						$entry->parentNode->removeChild($entry);
					};
    break;
    default: // elimina un id
					$query = 'id("'.$id1.'")';
					$entries = $xpath->query($query);
					foreach ($entries as $entry) {
						$target = $entry->getAttribute('target');
						$target = str_ireplace($id2,'',$target);
      if (substr_count($target,'://')<2) { // elimina un collegamento intero
       $entry->parentNode->removeChild($entry);
						} else {
							$entry->setAttribute('target', $target);
						};
					};
    break;
   };
			if ($resultKO == '') {
				$file_content = $dom->saveXML();
				$file_content = preg_replace('/'.WHITESPACES.'+/',' ',$file_content);
				$from = stripos($file_content,'%BEGIN%')+strlen('%BEGIN% -->');
				$to = stripos($file_content,'%END%')-strlen('<-- ') - $from -1;
				$text_content = substr($file_content,$from,$to);
				$file_content = $text_head.$text_content.$text_foot;
				doBackup($thePath);
				if (file_put_contents($thePath, $file_content, LOCK_EX) === false) {
					$resultKO .= 'Impossibile scrivere il file '.basename($thePath).'...';
				} else {
					@chmod($thePath, CHMOD);
					$resultOK .= 'Modifica eseguita con successo...';
				};
			};
		} else {
			$resultKO .= 'Impossibile leggere il file '.basename($thePath).'...';
		};
 } else {
	 $resultKO .= "Impossibile trovare ".basename($thePath).'...';
	};
	if ($resultKO !='') {
		$resultText .= '<span class="error">'.cleanWebString($resultKO).'</span>';
	} else {
		$resultText .= '<span class="ok">'.cleanWebString($resultOK).'</span>';
	};
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_saveLink($selector = 'new', $id1='', $id2='', $label='', $what='')  {
	$resultText = '';
	$resultOK = '';
	$resultKO = '';
	$collection_id = explode('/',str_ireplace('xml://','',$id1));
	$collection_id = $collection_id	[0];
	$label = ($label);
 $thePath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR;
 switch ($what ) {
  case 'lnk':
			$thePath .= DCTL_FILE_LINKER;
  break;
  case 'map':
			$thePath .= DCTL_FILE_MAPPER;
  break;
  default:
				$resultKO .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
  break;
 };
	if (is_file($thePath)) {
		switch ($selector) {
			case 'new':
			case 'add':
			case 'mod':
				$file_content = file_get_contents($thePath);
				$file_content = preg_replace('/'.WHITESPACES.'+/',' ',$file_content);
				$text_head = substr($file_content,0,stripos($file_content,'%BEGIN%')).'%BEGIN% -->';
				$text_foot = '<!-- '.substr($file_content,stripos($file_content,'%END%'));
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhiteSpace = false;
				forceUTF8($thePath);
				if ($dom->load($thePath, DCTL_XML_LOADER)) {
					$xpath = new DOMXPath($dom);
					switch ($selector) {
						case 'new':
							$type = 'link';
							$thisID = $collection_id.'-'.generateId();
							$head = $label;
							$query = 'id("placeholder")';
							$entries = $xpath->query($query);
							foreach ($entries as $entry) {
								$newNode = $dom->createElement('ref', $head);
								$newNode = $entry->parentNode->insertBefore($newNode,$entry);
								$newNode->setAttribute('xml:id',$thisID);
								$newNode->setAttribute('type',$type);
								$newNode->setAttribute('n',$label);
								$newNode->setAttribute('target',$id1.' '.$id2);
								$newNode = $dom->createComment(' ');
								$newNode = $entry->parentNode->insertBefore($newNode,$entry);
							};
						break;
						case 'add':
							$query = 'id("'.$id2.'")';
							$entries = $xpath->query($query);
							foreach ($entries as $entry) {
								$target = $entry->getAttribute('target');
								if (stripos($target, $id1)=== FALSE) {
									$entry->setAttribute('target', $id1.' '.$target);
								} else {
									$resultKO .= 'L\'ID '.$id1.' è gia nel collegamento...';
								};
							};
						break;
						case 'mod':
							$query = 'id("'.$id2.'")';
							$entries = $xpath->query($query);
							foreach ($entries as $entry) {
								$entry->setAttribute('n', $label);
								$entry->nodeValue = $label;
							};
						break;
					};
					if ($resultKO == '') {
						$file_content = $dom->saveXML();
						$file_content = preg_replace('/'.WHITESPACES.'+/',' ',$file_content);
						$from = stripos($file_content,'%BEGIN%')+strlen('%BEGIN% -->');
						$to = stripos($file_content,'%END%')-strlen('<-- ') - $from -1;
						$text_content = substr($file_content,$from,$to);
						$file_content = $text_head.$text_content.$text_foot;
						doBackup($thePath);
						if (file_put_contents($thePath, forceUTF8($file_content), LOCK_EX) === false) {
							$resultKO .= 'Impossibile scrivere il file '.basename($thePath).'...';
						} else {
							@chmod($thePath, CHMOD);
							$resultOK .= 'Modifica eseguita con successo...';
						};
					};
				} else {
					$resultKO .= 'Impossibile leggere il file '.basename($thePath).'...';
				};
			break;
			default:
				$resultKO .= '?'.$selector.'?';
			break;
		};
	} else {
		$resultKO .= "Impossibile trovare ".basename($thePath).'...';
	};
	if ($resultKO !='') {
		$resultText .= '<span class="error">'.cleanWebString($resultKO).'</span>';
	} else {
		$resultText .= '<span class="ok">'.cleanWebString($resultOK).'</span>';
	};
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadImageMap ($selector = 1, $id='', $uri='', $img='', $dim=array(), $coord=array(), $what='')  {
 $resultText = '';
	if (is_array($coord)) {
		$resultText .= '$(\':input[name=xml_id2]\').addClass(\'active\').val(\''.$uri.'@'.implode(',', $coord).'\');';
	} else{
	 $coord = array('0','0','0','0');
		$resultText .= '$(\':input[name=xml_id2]\').removeClass(\'active\').val(\'\');';
	};
	$resultText .= '$(\'img#img_edit\')';
	if ($id == '') {
		$resultText .= '.imgAreaSelect({disable:true, hide:true})';
	} else {
		$resultText .= '.attr(\'dbg\',\'1\')';
		$resultText .= '.unbind(\'load\')';
		$resultText .= '.load(function() {';
		$resultText .= 'if ($(\'img#img_edit\').attr(\'dbg\')) {';
		$resultText .= '$(\'img#img_edit\').removeAttr(\'dbg\');';
		$resultText .= 'var hRatio = $(\'img#img_edit\').width()/'.$dim[0].';';
		$resultText .= 'var vRatio = $(\'img#img_edit\').height()/'.$dim[1].';';
		$resultText .= '$(\'img#img_edit\').imgAreaSelect({enable:true, hide:true, x1:0, y1:0, x2:0, y2:0, outerOpacity: 0.17, imageWidth:'.$dim[0].', imageHeight:'.$dim[1].'});';
		$resultText .= '$(\'img#img_edit\').imgAreaSelect({x1:'.$coord[0].'*hRatio, y1:'.$coord[1].'*vRatio, x2:'.$coord[2].'*hRatio, y2:'.$coord[3].'*vRatio, onSelectEnd: function (img, selection) {
  	$(\':input[name=xml_id2]\').val(\''.$uri.'@\'+selection.x1+\',\'+selection.y1+\',\'+selection.x2+\',\'+selection.y2).addClass(\'active\');
		}
		});';
		$resultText .= '};';
		$resultText .= '})';
	};
	$resultText .= '.attr(\'src\', \''.$img.'\')';
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadImageList ($selector = 1, $collection_id='', $package_id='', $part_id='', $item_id='', $what='')  {
 $resultText = '';
 switch ($what ) {
  case 'lnk':
  break;
  case 'map':
			$resultText .= '<script>';
			$resultText .= '$(\'img#img_edit\').attr(\'src\', \'\').imgAreaSelect({disable:true, hide:true});';
			$resultText .= '</script>';
  break;
  default:
				$resultText .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
  break;
 };
 $resultText .= '<ul class="simpleTree">';
	$resultText .= '<li class="root">';
	$basePath = DCTL_PROJECT_PATH;
	$basePath .= $collection_id.SYS_PATH_SEPARATOR;
	getCollectionRecord($basePath, &$collectionRecord);
	$resultText .= '<span class="text">'.$collectionRecord['collection_full'].'</span>';
	$resultText .= '<ul>';
	$resultText .= '<li class="line"/>';
	$resultText .= '<li class="folder-open-last">';
	$basePath .= $package_id.SYS_PATH_SEPARATOR;
	getPackageRecord($basePath, &$packageRecord);
	$resultText .= '<span class="text">'.$packageRecord['package_full'].'</span>';
	$basePath .= SYS_PATH_SEPARATOR.$part_id;
	getPartRecord($basePath, &$partRecord);
	$resultText .= '<ul>';
	$resultText .= '<li class="line"/>';
	$resultText .= '<li class="folder-open-last">';
	$resultText .= '<span class="text">'.cleanWebString($partRecord['part_short'].': '.$partRecord['part_work'], FIELD_STRING_LENGTH).'</span>';
	$resultText .= '<ul>';
	$basePath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$part_id;
 if(getImageList($basePath, &$imageList)>0) {
		foreach ($imageList['path'] as $key=>$fPath) {
			getImageRecord($fPath, &$imageRecord, $imageList['image_short'][$key]);
			$resultText .= '<li class="line"/>';
			$resultText .= '<li class="doc'.(($key+1)==count($imageList['path'])?'-last':'').'">';
			// carica IMG
			$img = ajax_loadImage($collection_id.SYS_PATH_SEPARATOR.DCTL_MEDIA_BIG.$imageRecord['image_id'], $dim, $what);
   if ($img) {
				$mapped = ajax_loadLinkList($selector, $collection_id, $package_id, $part_id, $item_id, $what);
				$mapped = preg_match('/(.*)\?(.*)\#(.*)\@(.*)/', $mapped, $matches);
				if ($mapped) {
					$uri = $matches[3];
				} else {
					$uri = 'img://'.$imageRecord['image_id'];
				};
				$coord = '';
				$mapped = $mapped && ($item_id != '');
				if ($mapped) {
					$mapped = (basename($img) == basename($uri));
     $coord = explode(',', $matches[4]);
				};
				if ($mapped) {
					$resultText .= '<span class="active">';
					$resultText .= cleanWebString($imageRecord['image_work'].': '.$imageRecord['image_short'], FIELD_STRING_LENGTH);
				} else {
					$resultText .= '<span class="text">';
					$resultText .= '<a href="javascript:void(0);" onclick="';
					$resultText .= '$(this).parents(\'ul\').find(\'.active\').removeClass(\'active\');';
					$resultText .= '$(this).parent().addClass(\'active\');';
	    $resultText .= ajax_loadImageMap($selector, $item_id, $uri, $img, $dim, $coord, $what);
					$resultText .= ';" title="#">';
					$resultText .= cleanWebString($imageRecord['image_work'].': '.$imageRecord['image_short'], FIELD_STRING_LENGTH);
					$resultText .= '</a>';
				};
			} else {
				$resultText .= '<em>Not found: ';
				$resultText .= cleanWebString($imageRecord['image_work'].': '.$imageRecord['image_short'], FIELD_STRING_LENGTH);
				$resultText .= '</em>';
			};
			$resultText .= '</span>';
			$resultText .= '</li>';
		};
		$resultText .= '<script>';
		$resultText .= '$(\'#xml_tree2 .simpleTree\').find(\'.active\').parents(\'ul\').find(\'li span a\').each(function() {
		 $(this).parent().replaceWith($(this).text());
		});';
		$resultText .= '</script>';
	} else {
		$resultText .= '<li class="doc-last"><i>nessuna figure</i></li>';
	};
	$resultText .= '</ul>';
	$resultText .= '</li>';
	$resultText .= '</ul>';
	$resultText .= '</li>';
	$resultText .= '</ul>';
	$resultText .= '</li>';
	$resultText .= '</ul>';
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadLinkList ($selector = 1, $collection_id='', $package_id='', $part_id='', $item_id='', $what = '')  {
	$fullItem = 'xml://'.$collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$item_id;
	$resultText = '';
 $thePath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR;
	switch ($what) {
  case 'lnk':
			$thePath .= DCTL_FILE_LINKER;
		break;
  case 'map':
			$thePath .= DCTL_FILE_MAPPER;
		break;
  default:
				$resultText .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
  break;
 };
	if (is_file($thePath)) {
		forceUTF8($thePath);
		$simplexml = simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER);
		$namespaces = $simplexml->getDocNamespaces();
		foreach ($namespaces as $nsk=>$ns) {
			if ($nsk == '') $nsk = 'tei';
			$simplexml->registerXPathNamespace($nsk, $ns);
		};
		$simplexml = simplexml_load_string(str_ireplace('xml:id','id',$simplexml->asXML()), 'SimpleXMLElement');
// 		$s1=' '.$fullItem.' ';
// 		$s2=$fullItem.' ';
// 		$s3=' '.$fullItem;
//		 $resultXML = $simplexml->xpath('//*[contains(@target,\''.$s1.'\') or contains(@target,\''.$s2.'\') or @target=\''.$fullItem.'\']');
		$fullItem = preg_replace('/(\.\d\d\d)/','.001',$fullItem);
  $resultXML = $simplexml->xpath('//*[contains(@target,\''.$fullItem.'\') and substring(substring-after(@target,\''.$fullItem.'\'),1,1) != "."]');
		if (count($resultXML)>0) {
			switch ($what) {
				case 'lnk': {
					$resultText .= '<ul class="simpleTree">';
					$resultText .= '<li class="root">Collegamenti';
					$resultText .= '<ul>';
					foreach($resultXML as $n=>$link) {
						$attrs = $link->attributes();
						$label = $attrs['n'];
						$resultText .= '<li>';
						$resultText .= '<span class="text" onclick="';
						// carica ID
						$resultText .= '$(\':input[name=xml_lnk'.$selector.']\').addClass(\'active\').attr({value:\''.$label.'\'});';
						$resultText .= '$(\':input[name=xml_lnk'.$selector.'id]\').addClass(\'active\').attr({value:\''.$attrs['id'].'\'});';
						$resultText .= '">';
						$resultText .= 		cleanWebString(stripslashes($attrs['n']), 40).'&#160;</span>';
							$resultText .= 	'&#160;&#160;<img src="'.DCTL_IMAGES.'edit.gif" alt="edit" onclick="editLink(this, \''.$fullItem.'\',\''.$attrs['id'].'\', \''.$label.'\', \''.$what.'\')" />';
						if ($selector==2) {
							$resultText .= 	'&#160;&#160;<img src="'.DCTL_IMAGES.'published_no.png" alt="delete" onclick="deleteLink(\''.$attrs['id'].'\',\'\',\''.$label.'\', \''.$what.'\')" />';
						};
						$resultText .= '<ul>';
						foreach(explode(' ',$link['target']) as $k=>$v) {
							if ($v!=''){
								$parsed = explode(SYS_PATH_SEPARATOR,$v);
								$lnk_coll =$parsed[2];
								switch (count($parsed)) {
									case 4: // xml://_coll_/_id_ => linker
									$lnk_pack = '';
									$lnk_part = '';
									$lnk_item = $parsed[3];
									break;
									case 5: // xml://_coll_/_pack_/_id_ => package
									$lnk_pack = $parsed[3];
									$lnk_part = array();
									if(preg_match('/\d\d\d/',$parsed[4],$lnk_part)) {
										$lnk_part = str_ireplace('$',$lnk_part[0],DCTL_PACKAGE_BODY);
									};
									$lnk_item = $parsed[4];
									break;
								};
								$resultText .= '<li>';
								$resultText .= '<span class="text">';
								$resultText .= 	'<a href="javascript:void(0);" onclick="';
								// carica XML
								$resultText .= '$(\'#xml_chunk\').load(\'indexAjax.php\', {action:\'ajax_loadChunk\', collection_id:\''.$lnk_coll.'\', package_id:\''.$lnk_pack.'\', part_id:\''.$lnk_part.'\', item_id:\''.$lnk_item.'\', what:\''.$what.'\'});';
								$resultText .= '" title="#">'.cleanWebString(str_ireplace('xml://'.$lnk_coll.SYS_PATH_SEPARATOR,'',$v)).'</a>';
								$resultText .= '</span>';
								if ($v == $fullItem) {
									if ($selector==1) {
										$fullItem2 = 'xml://'.$lnk_coll.SYS_PATH_SEPARATOR.$lnk_pack.SYS_PATH_SEPARATOR.$lnk_item;
											$resultText .= '&#160;&#160;<img src="'.DCTL_IMAGES.'published_no.png" alt="" onclick="deleteLink(\''.$attrs['id'].'\',\''.$fullItem2.'\',\''.$label.'\', \''.$what.'\')" />';
									};
								};
								$resultText .= '</li>';
							};
						};
						$resultText .= '</ul>';
						$resultText .= '</li>';
					};
					$resultText .= '</ul>';
					$resultText .= '</li>';
					$resultText .= '</ul>';
					};
				break;
				case 'map': {
					foreach($resultXML as $n=>$link) {
						$attrs = $link->attributes();
						foreach(explode(' ',$link['target']) as $k=>$v) {
							if ($v != '') {
								if ($v != $fullItem) {
									$resultText = $attrs['id'].'?'.$attrs['n'].'#'.$v; // id ? label # uri @ map
								};
							};
						};
					};
					};
				break;
				default:
						$resultText .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
				break;
			};
		};
	};
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadId ($selector = 1, $collection_id='', $package_id='', $part_id='', $item_id='', $what='')  {
	$resultText = '';
 $resultText .= '<a href="javascript:void(0);" onclick="';
 $resultText .= '$(\'#xml_chunk\').load(\'indexAjax.php\', {action:\'ajax_loadChunk\', selector:\''.$selector.'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$part_id.'\', item_id:\''.$item_id.'\', what:\''.$what.'\'});';
 $resultText .= '" title="#">';
	$fullItem = 'xml://'.$collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$item_id;
	$resultText .= cleanWebString($fullItem);
	$resultText .= '</a>';
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadChunk ($selector = 1, $collection_id='', $package_id='', $part_id='', $item_id='', $what='')  {
	$resultText = '';
 $fullItem = $collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$part_id;
 $thePath = DCTL_PROJECT_PATH.$fullItem;
 // 	$resultText .= '<div class="lineH2">'.htmlentities($collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$part_id.SYS_PATH_SEPARATOR.$item_id,ENT_QUOTES,'UTF-8').'</div>';
	if (is_file($thePath)) {
		forceUTF8($thePath);
		$simplexml = simplexml_load_file($thePath, 'asPrettyXMLElement', DCTL_XML_LOADER);
		$namespaces = $simplexml->getDocNamespaces();
		foreach ($namespaces as $nsk=>$ns) {
			if ($nsk == '') $nsk = 'tei';
			$simplexml->registerXPathNamespace($nsk, $ns);
		};
		$resultXML = $simplexml->xpath('id("'.$item_id.'")');
		if (count($resultXML)>0) {
		 $high = 'xml:id=&quot;'.$item_id.'&quot;';
		 $xxml = htmlentities($resultXML[0]->asPrettyXML(1),ENT_QUOTES,'UTF-8');
			$resultText .= '<pre>'.str_ireplace($high,'<span class="highlight">'.$high.'</span>', $xxml).'</pre>';
  } else {
			$resultText .= '<span class="error">Non trovo ID '.$item_id.'...</span>';
  };
 } else {
  $resultText .= '<span class="error">Non trovo '.$fullItem.'...</span>';
 };
 return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadImage ($fullItem='', &$dim=array(), $what='')  {
	$resultText = '';
 $thePath = DCTL_PROJECT_PATH.$fullItem;
	if (is_file($thePath)) {
		$dim = getimagesize($thePath);
		$thePath = str_ireplace(DCTL_PROJECT_PATH, HOST_BASE_PATH.'data'.WEB_PATH_SEPARATOR.'dctl-project'.WEB_PATH_SEPARATOR, $thePath);
  $resultText .= $thePath;
	} else {
  $resultText .= '';
	};
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadTree ($selector = 1, $collection_id='', $package_id='', $part_id='', $item_id='', $what='') {
 $resultText = '';
	if (DCTL_EXT_IMT) {

	} else {
		switch ($what ) {
			case 'lnk':
			break;
			case 'map':
				$resultText .= '<script>';
				$resultText .= '$(\'img#img_edit\').attr(\'src\', \'\').imgAreaSelect({disable:true, hide:true});';
				$resultText .= '$(\':input[name=xml_id1]\').removeClass(\'active\').val(\'\');';
				$resultText .= '$(\':input[name=xml_id2]\').removeClass(\'active\').val(\'\');';
				$resultText .= '$(\':input[name=xml_label]\').removeClass(\'active\').val(\'\');';
				$resultText .= '$(\':input[name=xml_lnk1id]\').removeClass(\'active\').val(\'\');';
				$resultText .= '$(\'#xml_tree2\').html(\'\');';
				$resultText .= '</script>';
			break;
			default:
					$resultText .= 'ERROR: CASE UNIMPLEMENTED IN '.__FUNCTION__;
			break;
		};
 };
	// BEGIN
	$basePath = DCTL_PROJECT_PATH;
	$collectionPath = $basePath.$collection_id.SYS_PATH_SEPARATOR;
	if ($collection_id == '') {
  /*
  // ALL COLLECTIONS
		$resultText .= '<ul>';
		getCollectionList($basePath, &$collectionList);
		foreach ($collectionList['path'] as $key=>$fPath) {
			getCollectionRecord($fPath, &$collectionRecord);
			$resultText .= '<li>';
			$resultText .= '<span class="text">'.cleanWebString($collectionRecord['collection_short'].' - '.$collectionRecord['collection_work'], FIELD_STRING_LENGTH).'</span>';
			$resultText .= '<ul class="ajax">';
			$resultText .=			'<li>{url:indexAjax.php?action=ajax_loadTree&amp;selector='.$selector.'&amp;collection_id='.$collectionRecord['collection_id'].'}</li>';
			$resultText .= '</ul>';
			$resultText .= '</li>';
		};
		$resultText .= '</ul>';
  */
 } else {
		if ($package_id=='') {
		 $resultText .= '<ul class="simpleTree">';
			getCollectionRecord($collectionPath, &$collectionRecord);
			$resultText .= '<li class="root">';
			$resultText .= '<span class="text">'.$collectionRecord['collection_full'].'</span>';
  };
		$basePath = $collectionPath;
		$packagePath = $basePath.$package_id.SYS_PATH_SEPARATOR;
	 // ONE COLLECTION
		if ($package_id == '') {
			// ALL PACKAGES
			$resultText .= '<ul>';
			getPackageList($basePath, &$packageList);
			foreach ($packageList['path'] as $key=>$fPath) {
				getPackageRecord($fPath, &$packageRecord);
				$resultText .= '<li>';
				$resultText .= '<span class="text">'.$packageRecord['package_full'].'</span>';
				$resultText .=  '<ul class="ajax">';
				$resultText .= '<li>{url:indexAjax.php?action=ajax_loadTree&amp;selector='.$selector.'&amp;collection_id='.$collection_id.'&amp;package_id='.$packageRecord['package_id'].'&amp;what='.$what.'}</li>';
				$resultText .= '</ul>';
				$resultText .= '</li>';
			};
			$resultText .= '</ul>';
		} else {
				// ONE PACKAGE
				$basePath = $packagePath;
				$partPath = $basePath.$part_id.SYS_PATH_SEPARATOR;
				if ($part_id == '') {
					// ALL PARTS
					$resultText .= '<ul>';
					getPartList($basePath, &$partList);
					foreach ($partList['path'] as $key=>$fPath) {
						getPartRecord($fPath, &$partRecord);
						$resultText .= '<li>';
						if (DCTL_EXT_IMT && $what=='map') {
							$resultText .= '<span class="text"><a href="javascript:void(0);" onclick="';
							$resultText .= 'doProgress();$.post(\'indexAjax.php\',{action:\'ajax_loadTree\', selector:\''.$selector.'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$partRecord['part_id'].'\', what:\''.$what.'\'},';
							$resultText .= ' function('.DCTL_EXT_IMT_CBP.'){ jsapi_initializeIMT('.DCTL_EXT_IMT_CBP.'); killProgress();});';
							$resultText .= '">'.cleanWebString($partRecord['part_short'].': '.$partRecord['part_work'], FIELD_STRING_LENGTH). '&#160;</a></span>';
						} else {
							$resultText .= '<span class="text" onclick="doProgress();$(this).next().load(\'indexAjax.php\',{action:\'ajax_loadTree\', selector:\''.$selector.'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$partRecord['part_id'].'\', what:\''.$what.'\'},function(){
		$(\'#xml_tree'.$selector.' .simpleTree\').get(0).setTreeNodes(this, false);
		killProgress();
			}).insertAfter(this);">'.cleanWebString($partRecord['part_short'].': '.$partRecord['part_work'], FIELD_STRING_LENGTH). '&#160;</span>';
							$resultText .=  '<img src="'.DCTL_IMAGES.'refresh.gif" class="refresh" alt="(refresh)" onclick="doProgress();$(this).next().load(\'indexAjax.php\',{action:\'ajax_loadTree\', selector:\''.$selector.'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$partRecord['part_id'].'\', what:\''.$what.'\'},function(){
		$(\'#xml_tree'.$selector.' .simpleTree\').get(0).setTreeNodes(this, false);
		killProgress();
			}).insertAfter(this);" />';
							$resultText .=  '<ul class="ajax">';
							$resultText .= '<li>{url:indexAjax.php?action=ajax_loadTree&amp;selector='.$selector.'&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'&amp;part_id='.$partRecord['part_id'].'&amp;what='.$what.'}</li>';
							$resultText .= '</ul>';
						};
						$resultText .= '</li>';
					};
					$resultText .= '</ul>';
				} else {
					$basePath = $partPath;
					$itemPath = $basePath.$item_id.SYS_PATH_SEPARATOR;
					// ONE PART
					if ($item_id == '') {
						// ALL ITEMS
  				if (DCTL_EXT_IMT && $what=='map') {
       $resultText .= '<?xml version="1.0" encoding="UTF-8"?>';
							$resultText .= '<dctl_ext_init>';
						 $resultText .= '<xml>';
       if(getItemList($basePath, &$itemList, $what)>0) {
        $thePath = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR.DCTL_FILE_MAPPER;
								if (is_file($thePath)) {
									forceUTF8($thePath);
									$simplexml = simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER);
									$namespaces = $simplexml->getDocNamespaces();
									foreach ($namespaces as $nsk=>$ns) {
										if ($nsk == '') $nsk = 'tei';
										$simplexml->registerXPathNamespace($nsk, $ns);
									};
									$simplexml = simplexml_load_string(str_ireplace('xml:id','id',$simplexml->asXML()), 'SimpleXMLElement');
									foreach ($itemList['path'] as $key=>$fPath) {
										$uri = 'xml://'.$collection_id.'/'.$package_id.'/'.$itemList['item_id'][$key];
										$content = $itemList['item_short'][$key];
										$ref = '';
										$target = '';
										$label = '';
										$fullItem = $uri;
										$fullItem = preg_replace('/(\.\d\d\d)/','.001',$fullItem);
										$resultXML = $simplexml->xpath('//*[contains(@target,\''.$fullItem.'\') and substring(substring-after(@target,\''.$fullItem.'\'),1,1) != "."]');
          if (count($resultXML)>0) {
											foreach($resultXML as $n=>$link) {
												$attrs = $link->attributes();
												foreach(explode(' ',$link['target']) as $k=>$v) {
													if ($v != '') {
														if ($v != $fullItem) {
															$ref = $attrs['id'];
															$target = $v;
															$label = $attrs['n'];
														};
													};
												};
	 									};
										};
										$resultText .= '<a';
										$resultText .= ' r="'.$ref.'"';
										$resultText .= ' s="'.$uri.'"';
										$resultText .= ' t="'.$target.'"';
										$resultText .= ' l="'.$label.'"';
										$resultText .= ' c="'.$content.'"';
										$resultText .= ' />';
									};
								} else {
								 dump('ERRORE');
								};
							};
						 $resultText .= '</xml>';
						 $resultText .= '<img>';
						 if(getImageList($basePath, &$imageList)>0) {
								$key = 0;
        if (isset($imageList['path'][$key])) {
					 			$uri = 'img://'.$imageList['image_id'][$key];
					 			$url = DCTL_EXT_URL.'/indexAjax.php?&amp;action=get_file&amp;collection_id='.$collection_id.'&amp;url='.DCTL_MEDIA_MED.$imageList['image_id'][$key];
					 			$label = $imageList['image_short'][$key];
					 			$resultText .= '<a';
					 			$resultText .= ' s="'.$uri.'"';
					 			$resultText .= ' u="'.$url.'"';
									$resultText .= ' l="'.$label.'"';
									$resultText .= ' />';
								};
							};
						 $resultText .= '</img>';
						 $resultText .= '<cb';
						 $resultText .= ' u="'.DCTL_EXT_IMT_CB.'"';
						 $resultText .= ' p="'.DCTL_EXT_IMT_CBP.'"';
						 $resultText .= ' />';
							$resultText .= '</dctl_ext_init>';
       $resultText = base64_encode($resultText); //base64_encode
						} else {
							$resultText .= '<script>';
							switch ($what) {
								case 'lnk':
								break;
								case 'map':
									$resultText .= '$(\'#xml_tree'.($selector+1).'\').load(\'indexAjax.php\', {action:\'ajax_loadImageList\', selector:\''.($selector+1).'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$part_id.'\', what:\''.$what.'\'});';
								break;
								default:
									$resultText .= 'alert(\'ERROR: CASE UNIMPLEMENTED IN ...'.__FUNCTION__.'\');';
								break;
							};
							$resultText .= '</script>';
							if(getItemList($basePath, &$itemList, $what)>0) {
								$resultText .= '<ul>';
								foreach ($itemList['path'] as $key=>$fPath) {
									getItemRecord($fPath, &$itemRecord, $itemList['item_short'][$key]);
									$resultText .= '<li>';
									$resultText .= '<span class="text">';
									if ($itemRecord['item_id'] != '') {
										$resultText .= '<a href="javascript:void(0);" onclick="';
										// carica XML
										$resultText .= '$(\'#xml_chunk\').load(\'indexAjax.php\', {action:\'ajax_loadChunk\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$part_id.'\', item_id:\''.$itemRecord['item_short'].'\', what:\''.$what.'\'});';
										// carica ID
										$resultText .= '$(\':input[name=xml_id'.$selector.']\').addClass(\'active\').attr({value:\''.'xml://'.$collection_id.SYS_PATH_SEPARATOR.$package_id.SYS_PATH_SEPARATOR.$itemRecord['item_short'].'\'});';
										switch ($what) {
											case 'lnk':
												// carica LINK
													$resultText .= '$(\'#xml_lnk'.$selector.'\').load(\'indexAjax.php\', {action:\'ajax_loadLinkList\', selector:\''.$selector.'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$part_id.'\', item_id:\''.$itemRecord['item_short'].'\', what:\''.$what.'\'}, function(){$(\'#xml_lnk'.$selector.' .simpleTree\').simpleTree({activeLeaf: false}); });';
												$resultText .= '" title="#">';
												$resultText .= cleanWebString($itemRecord['item_short'].': '.$itemRecord['item_work'], FIELD_STRING_LENGTH);
											break;
											case 'map':
												$mapped = ajax_loadLinkList($selector, $collection_id, $package_id, $part_id, $itemRecord['item_id'], $what);
												$mapped = preg_match('/(.*)\?(.*)\#(.*)\@(.*)/', $mapped, $matches);
												if ($mapped) {
													$ref = $matches[1];
													$label = $matches[2];
												} else {
													$ref = '';
													$label = $itemRecord['item_work'];
												};
												$resultText .= '$(\':input[name=xml_label]\').removeClass(\'active\').val(\''.str_ireplace('&apos;', "\\'", $label).'\');';
												$resultText .= '$(\':input[name=xml_id2]\').removeClass(\'active\').val(\'\');';
												$resultText .= '$(\':input[name=xml_lnk1id]\').addClass(\'active\').attr({value:\''.$ref.'\'});';
												$resultText .= '$(\'#xml_tree'.($selector+1).'\').load(\'indexAjax.php\', {action:\'ajax_loadImageList\', selector:\''.($selector+1).'\', collection_id:\''.$collection_id.'\', package_id:\''.$package_id.'\', part_id:\''.$part_id.'\', item_id:\''.$itemRecord['item_short'].'\', what:\''.$what.'\'}';
												if ($mapped) {
													$uri = $matches[3];
													$img = str_ireplace('img://', $collection_id.SYS_PATH_SEPARATOR.DCTL_MEDIA_BIG, $uri);
													$coord = explode(',', $matches[4]);
													$resultText .= ', function () {';
													$img = ajax_loadImage($img, $dim, $what);
													$resultText .= ajax_loadImageMap($selector, $itemRecord['item_id'], $uri, $img, $dim, $coord, $what);
													$resultText .= '}';
												};
												$resultText .= ');';
												$resultText .= '" title="#">';
												if ($mapped) {
													$resultText .= '<span class="dctl_ok">';
													$resultText .= cleanWebString($itemRecord['item_short'].': '.$itemRecord['item_work'], FIELD_STRING_LENGTH);
													$resultText .= '</span>';
													$resultText .= 	'&#160;&#160;<img src="'.DCTL_IMAGES.'published_no.png" alt="delete" onclick="deleteLink(\''.$matches[1].'\',\''.$matches[3].'@'.$matches[4].'\',\''.$matches[2].'\', \''.$what.'\')" />';
												} else {
													$resultText .= cleanWebString($itemRecord['item_short'].': '.$itemRecord['item_work'], FIELD_STRING_LENGTH);
												};
											break;
											default:
												$resultText .= 'alert(\'ERROR: CASE UNIMPLEMENTED IN ...'.__FUNCTION__.'\');';
												$resultText .= '" title="#">';
												$resultText .= cleanWebString($itemRecord['item_short'].': '.$itemRecord['item_work'], FIELD_STRING_LENGTH);
											break;
										};
										$resultText .= '</a>';
									} else {
										$resultText .= '<em class="dctl_ko">';
										$resultText .= cleanWebString($itemRecord['item_short'].': '.$itemRecord['item_work'], FIELD_STRING_LENGTH);
										$resultText .= '</em>';
									};
									$resultText .= '</span>';
									$resultText .= '</li>';
								};
								$resultText .= '</ul>';
							} else {
								$resultText .= '<li><i>nessun id</i></li>';
							};
						};
					} else {
						$resultText .= 'UNIMPLEMENTED';
					};
				};
  };
  if (DCTL_EXT_IMT && $what=='map') {
   // nothing
  } else {
			$resultText .= '</li>';
			$resultText .= '</ul>';
  };

	};
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	function isImage($mime,$ext) {
		if (
			($mime=="image/gif")||
			($mime=="image/jpeg")||
			($mime=="image/jpg")||
			($mime=="image/pjpeg")||
			($mime=="image/png")||
			($ext=="jpg")||
			($ext=="jpeg")||
			($ext=="png")||
			($ext=="gif") ) {
			return true;
		} else {
			return false;
		};
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	function getMIME($file) {
		//If mime magic is installed
		if (function_exists("mime_content_type")) {
			$mime=mime_content_type($file);
		} else {
			$mime=image2MIME($file);
			if($mime==false) $mime="text/plain";
		}
		return strtolower($mime);
	};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	function image2MIME($file) {
		$fh=fopen($file,"r");
		if ($fh) {
			$start4=fread($fh,4);
			$start3=substr($start4,0,3);
			if ($start4=="\x89PNG") {
				return "image/png";
			} elseif ($start3=="GIF") {
				return "image/gif";
			} elseif ($start3=="\xFF\xD8\xFF") {
				return "image/jpeg";
			} elseif ($start4=="hsi1") {
				return "image/jpeg";
			} else {
				return false;
			}
			unset($start3);
			unset($start4);
			fclose($fh);
		} else {
			return false;
		}
	}
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function putEdit ($param = '') {
	$resultText = '';
	if (DCTL_USER_IS_SUPER) {
		$resultText .= '<a onclick="javascript:doProgress();" class="edit" href="'.$_SERVER['PHP_SELF'].'?';
		foreach ($_REQUEST as $k=>$v) {
			if ($k == $param) $v = 'true';
			$resultText .= $k."=".$v."&amp;";
		};
		$param .= '=';
		if (stripos($param, $resultText) === false) $resultText .= $param.'true';
		$resultText .= '" title="modifica">';
		$resultText .= '&#160;<img src="'.DCTL_IMAGES.'application_form_edit.png" alt="(edit icon)" />';
		$resultText .= '&#160;Modifica dati</a>';
	};
	return $resultText;
}
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function putOpenCloseLevel ($id, $loc4msg, $more=false, $label, &$resultMsg='') {
	$resultText = '';
	$isThis = ($loc4msg == $id) || $more;
	$resultText .= '<h3>';
	$resultText .= '<a class="toggler" onclick="toggleVisibility(this,\''.$id.'\');" title="'.TOOLTIP_TOGGLE.'">';
	$resultText .= '<img src="'.DCTL_IMAGES;
	if ($isThis) {
		$resultText .= 'collapse.gif';
	} else {
		$resultText .= 'expand.gif';
	};
	$resultText .= '" alt="(open/close level)" />&#160;';
	$resultText .= $label.'</a></h3>';
	$resultText .= '<div id="'.$id.'" class="';
	if ($isThis) {
		$resultText .= 'un';
	} else {
		$resultText .= '_hidden';
	};
	$resultText .= '">';
	if ($loc4msg == $id) {
		$resultText .= $resultMsg;
	}
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function putOpenLevel ($id, $loc4msg, $more=false, $label, &$resultMsg='') {
	$resultText = '';
	$isThis = ($loc4msg == $id) || $more;
	$resultText .= '<h3>';
	$resultText .= '&#160;';
	$resultText .= $label.'</h3>';
	$resultText .= '<div id="'.$id.'" class="';
	$resultText .= '">';
	if ($loc4msg == $id) {
		$resultText .= $resultMsg;
	}
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getModel ($originalPath) {
	$sourcePath = $originalPath;
	$filePath = str_ireplace(DCTL_PROJECT_PATH, '', $sourcePath);
	$zip = new zipfile();

	$zipdir = SYS_PATH_SEPARATOR;
	$zip->add_dir($zipdir);

	addToZip(DCTL_SETTINGS, $zip, $zipdir);

	$zipdir = SYS_PATH_SEPARATOR;
	$source = DCTL_PROJECT_PATH;
	foreach(explode(SYS_PATH_SEPARATOR, $filePath) as $item) {

		$source .= $item;
		$zipdir .= $item;
		if (is_dir($source)) {
			$source .= SYS_PATH_SEPARATOR;
			$zipdir .= SYS_PATH_SEPARATOR;
			$zip->add_dir($zipdir);
			$item = $source.DCTL_FILE_HEADER;
			if (is_file($item)) {
				addToZip($item, $zip, $zipdir);
			};
			// 				$item = $source.DCTL_MEDIA_SML;
			// 				if (is_dir($item)) {
    // 					addToZip($item, $zip, $zipdir);
			// 				};
		};
	};
	$zipdir = dirname($zipdir).SYS_PATH_SEPARATOR;
	if (is_dir($source)) {
		if ((stripos(DCTL_MEDIA_SML, $source) === FALSE) && (stripos(DCTL_MEDIA_MED, $source) === FALSE)) {
			addToZip($source, $zip, $zipdir);
		};
	} else {
		if (is_file($source)) {
			$item = $source;
			addToZip($item, $zip, $zipdir);
		};
	};
	$filePath = dirname($originalPath).SYS_PATH_SEPARATOR.str_ireplace(SYS_PATH_SEPARATOR, '#', $filePath).'.zip';
	$fd = fopen ($filePath, "wb");
	$out = fwrite ($fd, $zip->file());
	fclose ($fd);
	@chmod($filePath, CHMOD);
	return $filePath;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getListNameForField ($theFields, $theSelected) {
	global $COLLECTION_FIELDS;
	global $PACKAGE_FIELDS;
	$resultText = '';
	switch ($theFields) {
		case $COLLECTION_FIELDS:
  switch ($theSelected) {
   case 'collection_resp':
   $resultText = DCTL_LIST_RESP;
   break;
  };
  break;
  case $PACKAGE_FIELDS:
  switch ($theSelected) {
   case 'package_encoder':
   $resultText = DCTL_LIST_RESP;
   break;
   case 'source_lang':
   $resultText = DCTL_LIST_LANG;
   break;
   case 'source_genre':
   $resultText = DCTL_LIST_GENRE;
   break;
   case 'reference_lang':
   $resultText = DCTL_LIST_LANG;
   break;
   case 'reference_genre':
   $resultText = DCTL_LIST_GENRE;
   break;
  };
  break;
	};
	return $resultText ;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getTextClassList ($theList='', $theObject='') {
	$resultText = '';
	if ($theList != '') {
		if ($theObject != '') {
			$simplexml = '';
			if (is_file(DCTL_SETTINGS_TEXTCLASS)) {
				$resultText .= '<br />Aggiungi:<select onchange="javascript:addContent(\''.$theObject.'\', this.value);">';
				$resultText .= '<option value="" selected="selected" />';
  		forceUTF8(DCTL_SETTINGS_TEXTCLASS);
				$simplexml = simplexml_load_file(DCTL_SETTINGS_TEXTCLASS, 'SimpleXMLElement', DCTL_XML_LOADER);
				$simplexml = $simplexml->asXML();
				$simplexml = str_ireplace('xml:', '', $simplexml);
				$simplexml = simplexml_load_string($simplexml, 'SimpleXMLElement', DCTL_XML_LOADER);
				$resultXML = $simplexml->xpath('//classCode[@scheme="'.$theList.'"]/term');
				foreach ($resultXML as $k=>$v) {
					$id = strval($v['id']);
					$name = strval($v->eg);
					$resultText .= '<option value="'.$id.'">'.$name.SYS_DBL_SPACE.'</option>';
				};
				$resultText .= '</select>';
			};
		};
	};
	return $resultText ;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getManagementOfImages ($fDivX, $labelX, $collection_id, $itemName, $loc4msg, &$fCount, $resultMsg) {
 global $EXTENSION_ALLOWED;
	global $EXTENSION_TEXT;
	global $EXTENSION_GRAPHIC;
	$resultText = '';

	$array_estensioni_ammesse = $EXTENSION_ALLOWED;

	$basename = DCTL_PROJECT_PATH.$collection_id.SYS_PATH_SEPARATOR;

	$regexp = '[';
	foreach($array_estensioni_ammesse as $k=>$v) {
		if ($k >0) $regexp .= ' | ';
		$regexp .= $v;
	};
	$regexp .= ']';

	$dPath = $basename.DCTL_MEDIA_SML;
	if (!is_dir($dPath)) mkdir($dPath, CHMOD);
 @chmod($dPath, CHMOD);
	$dPath = $basename.DCTL_MEDIA_MED;
	if (!is_dir($dPath)) mkdir($dPath, CHMOD);
 @chmod($dPath, CHMOD);
	$dPath = $basename.DCTL_MEDIA_BIG;
	if (!is_dir($dPath)) mkdir($dPath, CHMOD);
 @chmod($dPath, CHMOD);

	$dPath = $basename.DCTL_MEDIA_BIG;
	$variants = array();
	$handle = opendir($dPath);
	while ($entry = readdir($handle)) {
		if (substr($entry, 0, 1) != '.') {
			$variants[] = $entry;
		};
	};
	$variants = array_values(preg_grep('/.*'.$regexp.'/', $variants));
	$idx = count($variants);
	sort($variants);

	$fDiv0 = str_ireplace('$', '', $fDivX);
	$label0 = str_ireplace('$', '', $labelX);
	//putOpenCloseLevel
	$resultText .= putOpenLevel($fDiv0, $loc4msg, false, 'Gestione '.$label0.' di "'.$itemName.'" ('.$idx.')', &$resultMsg);
	$resultText .= '<table>';
	$resultText .= '<thead>';
	$resultText .= '<tr>';
	$resultText .= '<th class="label">operazione</th>';
	$resultText .= '<th>azione</th>';
	$resultText .= '<th>risultato</th>';
	$resultText .= '</tr>';
	$resultText .= '</thead>';
	$resultText .= '<tbody>';
	/* CARICA UNA IMMAGINE */
	$dPath = $basename.DCTL_MEDIA_BIG;
	$resultText .= '<tr>';
	$resultText .= '<td>Carica un nuovo file ...</td>';
	$resultText .= '<td>';
	$resultText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['SCRIPT_NAME'].'" method="'.DCTL_FORM_METHOD_POST.'" enctype="'.DCTL_FORM_ENCTYPE_POST.'">';
	$resultText .= '<fieldset>';
	$resultText .= '<input type="file" id="multi_file_upload" name="FILE'.$fCount.'" size="50" />';
	$resultText .= '<input type="hidden" name="PATH" value="'.$dPath.'" />';
	$resultText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
	$resultText .= '<input type="hidden" name="ext" value="img" />';
	$resultText .= SYS_DBL_SPACE.SYS_DBL_SPACE.'<input type="submit" name="upload" value="invia" />';
	$resultText .= '<br /><strong>File da caricare</strong> (max 10): <div id="files_list"></div>
 <script>
	var multi_selector = new MultiSelector(document.getElementById("files_list"), 10);
	multi_selector.addElement(document.getElementById("multi_file_upload") );
 </script>
 ';
	$resultText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
	$resultText .= '</fieldset>';
	$resultText .= '</form>';
	$resultText .= '</td>';
	$resultText .= '<td>&#160;</td>';
	$resultText .= '</tr>';
	/* VISUALIZZA LE ANTEPRIME */
	$dPath = $basename.DCTL_MEDIA_BIG;
	$resultText .= '<tr>';
	$resultText .= '<td>Visualizza l\'anteprima di un file...<br /><br />';
	$resultText .= '<form action="">';
	$resultText .= '<fieldset>';
	$resultText .= '<input type="text" id="mediaFilter" value="-trova-" onkeyup="var vFilter=this.value;
	$(\'#mediaList li\').removeClass(\'found\');
	var index = $(\'#mediaList li\').index($(\'#mediaList a\').filter(function(node){return ((this.text.toLowerCase().indexOf(vFilter)>=0)&&(vFilter!=\'\'))}).parent().addClass(\'found\'));
	" />';
	$resultText .= '</fieldset>';
	$resultText .= '</form>';
	$resultText .= '</td>';
	$resultText .= '<td colspan="2">';

	if (is_dir($dPath)) {

		$resultText .= '<ul  id="mediaList" class="trueContainer';
		if (true) $resultText .= '2';
  $resultText .= '">';
		//  $resultText .= '<script type="text/javascript" src="../js/imager.js"></script>';
		//  $resultText .= '<div id="motioncontainer" style="position:relative;overflow:hidden;">';
		// 	$resultText .= '<div id="motiongallery" style="position:absolute;left:0;top:0;white-space: nowrap;">';
		// 	$resultText .= '<span id="trueContainer">';
		$tail = '';
		$dPath2 = $dPath; //$dPath2 = str_ireplace(DCTL_MEDIA_SML, DCTL_MEDIA_BIG, $dPath);
		$files = scandir($dPath2);
		$filePattern = '/-('.WHITESPACES.'*)(.*)('.WHITESPACES.'*)=('.WHITESPACES.'*)$('.WHITESPACES.'*)-/';
		if ($dPath != '') {
			$entries = array();
			$handle = opendir($dPath);
			while ($entry = readdir($handle)) {
				if (substr($entry, 0, 1) != '.') {
					$entries[] = $entry;
				};
			};
			sort($entries);
			foreach($entries as $entry) {
				$ext = strtolower(substr($entry, -3, 3));
				if (in_array($ext, $array_estensioni_ammesse)) {
					$labelval = str_ireplace('$', $entry, $filePattern);
					$labelx = array_values(preg_grep($labelval, $files));
					if (count($labelx)>0) {
						$label = $labelx[0];
						$labelx = preg_split('/('.WHITESPACES.'*)=('.WHITESPACES.'*)/', $label, -1);
						$label = str_ireplace('-', '', $labelx[0]);
					} else {
						$label = $entry;
					};
					if ($label != $entry) {
					 if (substr($label, 0, strlen($collection_id)) != $collection_id) {
					 	$label = $collection_id.'-'.$label;
					 };
					 rename($dPath.$entry, $dPath.$label);
					 rename($dPath2.$entry, $dPath2.$label);
					 $entry = $label;
					};
					$value = $dPath.$entry;
					$tail2 = '';
					$tail2 .= '<li id="'.$entry.'"><a href="javascript:indexAjax(\'load_preview\', \''.$fDiv0.'-img\', \''.$collection_id.'\', \'\', \'url='.str_ireplace(FS_BASE_PATH, HOST_BASE_PATH, $value).'\', \'posx='.$fDiv0.'\');" title="'.$label.'">';
					$tail2 .= $label;
					$tail2 .= '</a></li>';
					$tail = $tail.$tail2;
				};
			};
		};
		// 	$resultText .= '</span>';
		$resultText .=	$tail;
		$resultText .= '</ul>';

		$resultText .= '</div>';
		$resultText .= '</div>';
		$resultText .= '<div id="'.$fDiv0.'-img">&#160;</div>';
	};
	$resultText .= '</td>';
	$resultText .= '</tr>';
	if (DCTL_USER_IS_ADMIN) {
		/* RIGENERA LE ANTEPRIME */
		$dPath = $basename.DCTL_MEDIA_BIG;
		$resultText .= '<tr>';
		$resultText .= '<td>Rigenera le anteprime...</td>';
  $resultText .= '<td>'.'<a onclick="javascript: doProgress();" href="'.$_SERVER['SCRIPT_NAME'].'?createPreview='.str_ireplace(FS_BASE_PATH, HOST_BASE_PATH, $dPath).'&amp;collection_id='.$collection_id.'&amp;posx='.$fDivX.'" title="(???)">Procedi...</a>'.'</td>';
		$resultText .= '<td>'.'&#160;'.'</td>';
		$resultText .= '</tr>';
	};
	$resultText .= '</tbody>';
	$resultText .= '</table>';
	$resultText .= '<br /></div>';

	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getManagementOfXML ($fSelectorX, $fDivX, $labelX, $collection_id, $package_id, $itemName, $errorInDownload, $loc4msg, &$fCount, $resultMsg) {
	$resultText = '';
	$isMultiPart = stripos($fSelectorX, '$') !== FALSE;
	$basename = DCTL_PROJECT_PATH.$collection_id;
	if ($package_id != '') {
		$basename .= SYS_PATH_SEPARATOR.$package_id;
	};
	$dPath = $basename;
	$regexp = str_ireplace(DCTL_PACKAGE_BODY_REGEXP1, DCTL_PACKAGE_BODY_REGEXP2, $fSelectorX);
	$variants = array();
	$handle = opendir($dPath);
	while ($entry = readdir($handle)) {
		if (substr($entry, 0, 1) != '.') {
   if (($entry==DCTL_PACKAGE_FRONT)||($entry==DCTL_PACKAGE_BACK)||($isMultiPart)) {
	 		$variants[] = $entry;
 		};
		};
	};
	$variants = array_values(preg_grep('/^'.$regexp.'/', $variants));
	$idx = count($variants);
 sort($variants);
 $fDiv0 = str_ireplace('$', '', $fDivX);
	$label0 = str_ireplace('$', '', $labelX);
	$resultText .= putOpenCloseLevel($fDiv0, $loc4msg, false, 'Gestione '.$label0.' di "'.$itemName.'" ('.$idx.')', &$resultMsg);
	if ($errorInDownload && ($loc4msg == $fDiv0)) {
		$resultText .= '<span class="error">ATTENZIONE: il file "'.$_REQUEST['file'].'" di "'.$itemName.'" è già stato bloccato... download annullato!</span><br />';
	};
	$resultText .= '<table>';
	$resultText .= '<thead>';
	$resultText .= '<tr>';
	$resultText .= '<th class="label">nome</th>';
	$resultText .= '<th>ult. modifica</th>';
	$resultText .= '<th>operazioni</th>';
	$resultText .= '<th>utente</th>';
	$resultText .= '</tr>';
	$resultText .= '</thead>';
	$resultText .= '<tbody>';
	foreach($variants as $vKey=>$fSelector) {
	 switch ($vKey) {
	  case 0:
   $label = str_ireplace('$', 'iniziale <em>(front)</em>', $labelX);
	  break;
	  case (count($variants)-1):
   $vKey = 999;
   $label = str_ireplace('$', 'finale <em>(back)</em>', $labelX);
	  break;
	  default:
   $idx = sprintf("%03d", $vKey);
   $label = str_ireplace('$', $idx.' <em>(body)</em>', $labelX);
	  break;
	 };
  $idx = sprintf("%03d", $vKey);
		$fDiv = str_ireplace('$', $vKey, $fDivX);
		$fSelector = str_ireplace('$', $idx, $fSelectorX);
		$fPath = $basename.SYS_PATH_SEPARATOR.$fSelector;
		$who = '';
		$content = array();
		$label .= '<br/><span class="morelink">';
		getPartRecord($fPath, &$packageRecord);
		if($packageRecord['part_work']!='') {
			$label .= $packageRecord['part_work'];
		} else {
			$label .= '???';
		};
		$label .= '</span>';

		$isLocked = checkIfLocked ($fPath, &$who, &$content);
		$resultText .= '<tr>';
		$resultText .= '<td>'.$label.'</td>';
		if (is_file($fPath)) {
			$resultText .= '<td>'.date ("d-m-y H:i", filemtime($fPath)).'</td>';
			if ($isLocked) {
				if ($who == DCTL_USER_ID) {
					$fCount++;
					$resultText .= '<td>';
					$resultText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['SCRIPT_NAME'].'" method="'.DCTL_FORM_METHOD_POST.'" enctype="'.DCTL_FORM_ENCTYPE_POST.'">';
					$resultText .= '<fieldset>';
					$resultText .= '<span class="dctl_ok"><img src="'.DCTL_IMAGES.'file_alert.gif" alt="(alert icon)" />&#160;'.'Ricarica file</span><br />';
					$resultText .= '<input type="file" name="FILE'.$fCount.'" value="'.$fPath.'" />';
					$resultText .= '<input type="hidden" name="PATH" value="'.$fPath.'" />';
					$resultText .= '<input type="hidden" name="ext" value="txt" />';
					$resultText .= SYS_DBL_SPACE.'<input type="submit" name="upload" value="invia" />';
					$resultText .= '</fieldset>';

					$resultText .= '<br />';
					$resultText .= '<fieldset>';
					$resultText .= '<span class="dctl_ko"><img src="'.DCTL_IMAGES.'file_alert.gif" alt="(alert icon)" />&#160;'.'Sblocca il file senza ricaricare</span>';
					$resultText .= '<input type="hidden" name="PATH" value="'.$fPath.'" />';
					$resultText .= '<input type="hidden" name="ext" value="txt" />';
					$resultText .= SYS_DBL_SPACE.'<input type="submit" name="reset" value="sblocca" />';
					$resultText .= '</fieldset>';

					$resultText .= '<fieldset>';
					$resultText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
					$resultText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
					$resultText .= '<input type="hidden" name="package_id" value="'.$package_id.'" />';
					$resultText .= '</fieldset>';

					$resultText .= '</form>';
					$resultText .= '</td>';
					$resultText .= '<td><span class="dctl_ok">'.$who.'</span></td>';
				} else {
					$resultText .= '<td><span class="dctl_ko"><img src="'.DCTL_IMAGES.'page_lock.gif" alt="(locked file icon)" />&#160;&#160;'.'File bloccato</span></td>';
					$resultText .= '<td><span class="dctl_ko">'.$who.'</span></td>';
				};
			} else {
				$resultText .= '<td id="xml-'.$fDiv.'">';
				$resultText .= '<a href="indexDownload.php?';
				$resultText .= 'file='.$fPath.'&amp;';
				$resultText .= 'lock=yes&amp;';
				$resultText .= 'user='.DCTL_USER_ID.'&amp;';
				$resultText .= 'error=yes&amp;';
				$resultText .= 'posx='.$fDiv0.'&amp;';
				$resultText .= 'collection_id='.$collection_id.'&amp;';
				$resultText .= 'package_id='.$package_id.'&amp;';
				$resultText .= '" onclick="javascript:document.getElementById(\'xml-'.$fDiv.'\').innerHTML=\'';
				$resultText .= '(bloccato)';
				$resultText .= '\'';
				$resultText .= '" title="scarica il file e blocca">';
				$resultText .= '<img src="'.DCTL_IMAGES.'page_down.gif" alt="(download file icon)" />'.SYS_DBL_SPACE;
				$resultText .= 'Scarica e blocca</a>';
				$resultText .= '</td>';
				$resultText .= '<td>'.'-'.'</td>';
			};
		} else {
			$resultText .= '<td>'.'<span class="error">(missing)</span>'.'</td>';
			$resultText .= '<td>'.'-'.'</td>';
			$resultText .= '<td>'.'-'.'</td>';
		};
		$resultText .= '</tr>';
	};
	if ($isMultiPart) {
		if (DCTL_USER_IS_EDITOR) {
			$resultText .= '<tr>';
			$resultText .= '<td>&#160;</td>';
			$resultText .= '<td>&#160;</td>';
			$resultText .= '<td>';
			$resultText .= '<form id="form'.$fDiv0.'" action="'.$_SERVER['SCRIPT_NAME'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
			$resultText .= '<fieldset>';
			$resultText .= '<input name="createPart" type="submit" value="Crea nuova Parte..." />';
			$resultText .= '<input type="hidden" name="posx" value="'.$fDiv0.'" />';
			$resultText .= '<input type="hidden" name="collection_id" value="'.$collection_id.'" />';
			$resultText .= '<input type="hidden" name="package_id" value="'.$package_id.'" />';
			$resultText .= '</fieldset>';
			$resultText .= '</form>';
			$resultText .= '</td>';
			$resultText .= '<td>&#160;</td>';
			$resultText .= '</tr>';
		};
	};
	$resultText .= '</tbody>';
	$resultText .= '</table>';
	$resultText .= '<br /></div>';
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getCollectionRecord ($thePath, &$collectionRecord) {
	$collectionRecord = array();
	$collectionRecord['collection_id'] = '';
	$collectionRecord['collection_short'] = '';
	$collectionRecord['collection_work'] = '';
	$collectionRecord['collection_full'] = '';
	$header = $thePath.DCTL_FILE_HEADER;
	if (is_file($header)) {
		$contents = cleanUpIndentation(file_get_contents($header));
		$lines = explode('<!ENTITY ', $contents);
		$field = 'collection_id';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
		$collection_id = strtolower(basename($linex[1]));
		$collection_id = normalize($collection_id);
		$collectionRecord['collection_id'] = $collection_id;
		$field = 'collection_short';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
// 		$collection_short = strtolower(basename($linex[1]));
// 		$collection_short = strtoupper(normalize($collection_short));
// 		$collectionRecord['collection_short'] = $collection_short;
		$collectionRecord['collection_short'] = cleanWebString($linex[1]);
		$field = 'collection_work';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
		$collectionRecord['collection_work'] = cleanWebString($linex[1]);
		$collectionRecord['collection_full'] = cleanWebString($collectionRecord['collection_short'].': '.$collectionRecord['collection_work'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getPackageRecord ($thePath, &$packageRecord) {
	$packageRecord = array();
	$packageRecord['package_id'] = '';
	$packageRecord['package_ext'] = '';
	$packageRecord['package_short'] = '';
	$packageRecord['package_work'] = '';
	$packageRecord['package_full'] = '';
	$header = $thePath.DCTL_FILE_HEADER;
	if (is_file($header)) {
		$contents = cleanUpIndentation(file_get_contents($header));
		$lines = explode('<!ENTITY ', $contents);
		$field = 'package_id';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
		$package_id = strtolower(basename($linex[1]));
		$package_id = normalize($package_id);
		$packageRecord['package_id'] = $package_id;
		$package_ext = substr($package_id, -4, 4);
		$packageRecord['package_ext'] = $package_ext;
		$field = 'package_short';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
// 		$package_short = strtolower(basename($linex[1]));
// 		$package_short = strtoupper(normalize($package_short));
// 		$packageRecord['package_short'] = $package_short;
		$packageRecord['package_short'] = cleanWebString($linex[1]);
		$field = 'package_work';
		$linex = array_values(preg_grep('/'.$field.'.*/', $lines));
		$linex = explode('"', $linex[0]);
		$packageRecord['package_work'] = cleanWebString($linex[1]);
		$packageRecord['package_full'] = cleanWebString($packageRecord['package_id'].': '.$packageRecord['package_short'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getMediaRecord ($thePath, &$mediaRecord) {
	$mediaRecord = array();
	$mediaRecord['media_id'] = '';
	$mediaRecord['media_short'] = '';
	$mediaRecord['media_work'] = '';
	$media_id = basename($thePath);
	$media_id = normalize($media_id);
	$mediaRecord['media_id'] = $media_id;
	$media_short = basename($thePath);
	$media_short = normalize($media_short);
	$mediaRecord['media_short'] = cleanWebString($media_short);
	$media_work = basename($thePath);
	$media_work = normalize($media_work);
	$mediaRecord['media_work'] = cleanWebString($media_work);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getPartRecord ($thePath, &$partRecord) {
	$partRecord = array();
	$partRecord['part_id'] = '';
	$partRecord['part_short'] = '';
	$partRecord['part_work'] = '';
	$resultXML = basename($thePath);
	if (is_file($thePath)) {
		forceUTF8($thePath);
  $simplexml = @simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER); 		//
		if (!$simplexml) {
		 $resultXML = array(0=>'*** File con errori XML ***');
		} else {
		$namespaces = $simplexml->getDocNamespaces();
		foreach ($namespaces as $nsk=>$ns) {
			if ($nsk == '') $nsk = 'tei';
			$simplexml->registerXPathNamespace($nsk, $ns);
		};
		$resultXML = $simplexml->xpath('//tei:div[./tei:head/tei:index/tei:term != ""][1]/tei:head/tei:index/tei:term[. != ""][1]');
		if (!isset($resultXML[0]))
			$resultXML = $simplexml->xpath('//tei:div[./tei:head != ""][1]/tei:head[. != ""][1]');
		};
	};
	$part_id = basename($thePath);
	$part_id = normalize($part_id);
	$partRecord['part_id'] = $part_id;
	$part_short = basename($thePath);
	$part_short = normalize($part_short);
	$partRecord['part_short'] = cleanWebString($part_short);
 $part_work = (string)$resultXML[0];
	$partRecord['part_work'] = cleanWebString($part_work, 80);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getItemRecord ($thePath, &$itemRecord, $theLabel='') {
	$itemRecord = array();
	$itemRecord['item_id'] = '';
	$itemRecord['item_short'] = '';
	$itemRecord['item_work'] = '';
	$item_id = basename($thePath);
	$item_id = normalize($item_id);
	$itemRecord['item_id'] = $item_id;
	$item_short = $item_id;
	$itemRecord['item_short'] = $item_short;
	$item_work = $theLabel;
	$itemRecord['item_work'] = cleanWebString($item_work,18);
	if (checkIfLocked (dirname($thePath), &$who, &$content)) {
		$itemRecord['item_id'] = '';
		$itemRecord['item_work'] = 'in use by '.$who;
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getImageRecord ($thePath, &$imageRecord, $theLabel='') {
	$imageRecord = array();
	$imageRecord['image_id'] = '';
	$imageRecord['image_short'] = '';
	$imageRecord['image_work'] = '';
	$image_id = basename($thePath);
	$image_id = normalize($image_id);
	$imageRecord['image_id'] = $image_id;
	$image_short = $image_id;
	$imageRecord['image_short'] = $image_short;
	$image_work = $theLabel;
	$imageRecord['image_work'] = cleanWebString($image_work); // , 18
	$collection_id = '???';
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getCollectionList ($thePath, &$collectionList, $withEmpty=false) {
	$collectionList = array();
	if ($withEmpty) {
		$collectionList['collection_id'][] = '';
		$collectionList['collection_short'][] = '';
		$collectionList['collection_full'][] = '';
	 $collectionList['path'][] = '';
	};
	$collectionRecord = array();
	if ($thePath != '') {
		$entries = array();
		$handle = opendir($thePath);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				if (! preg_match('/^'.DCTL_RESERVED_PREFIX.'/',$entry)) {
				 $entries[] = $entry;
				};
			};
		};
		sort($entries);
		foreach($entries as $entry) {
			$full = $thePath.$entry.SYS_PATH_SEPARATOR;
			if (is_dir($full)) {
				getCollectionRecord ($full, &$collectionRecord);
				$collectionList['collection_id'][] = $collectionRecord['collection_id'];
				$collectionList['collection_short'][] = $collectionRecord['collection_short'];
				$collectionList['collection_full'][] = $collectionRecord['collection_full'];
				$collectionList['path'][] = $full;
			};
		};
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getPackageList ($thePath, &$packageList, $withEmpty=false) {
	$packageList = array();
	if ($withEmpty) {
	 $packageList['package_id'][] = '';
	 $packageList['package_ext'][] = '';
	 $packageList['package_short'][] = '';
	 $packageList['package_full'][] = '';
	 $packageList['path'][] = '';
	};
	$packageRecord = array();
	if ($thePath != '') {
		$entries = array();
		$handle = opendir($thePath);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				if (! preg_match('/^'.DCTL_RESERVED_PREFIX.'/',$entry)) {
				 $entries[] = $entry;
				};
			};
		};
		sort($entries);
		foreach($entries as $entry) {
			$full = $thePath.$entry.SYS_PATH_SEPARATOR;
			if (is_dir($full)) {
				getPackageRecord ($full, &$packageRecord);
				$packageList['package_id'][] = $packageRecord['package_id'];
				$packageList['package_ext'][] = $packageRecord['package_ext'];
				$packageList['package_short'][] = $packageRecord['package_short'];
				$packageList['package_full'][] = $packageRecord['package_full'];
				$packageList['path'][] = $full;
			};
		};
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getMediaList ($thePath, &$mediaList, $withEmpty=false) {
	$mediaList = array();
	if ($withEmpty) {
  $mediaList['media_id'][] = '';
  $mediaList['media_short'][] = '';
		$mediaList['path'][] = '';
	};
	$mediaRecord = array();
	if ($thePath != '') {
		$entries = array();
		$handle = opendir($thePath);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				if (! preg_match('/^'.DCTL_RESERVED_PREFIX.'/',$entry)) {
				 $entries[] = $entry;
				};
			};
		};
		sort($entries);
		foreach($entries as $entry) {
			$full = $thePath.$entry.SYS_PATH_SEPARATOR;
			if (is_dir($full)) {
				getMediaRecord ($full, &$mediaRecord);
				$mediaList['media_id'][] = $mediaRecord['media_id'];
				$mediaList['media_short'][] = $mediaRecord['media_short'];
				$mediaList['path'][] = $full;
			};
		};
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getPartList ($thePath, &$partList, $withEmpty=false) {
	$partList = array();
	if ($withEmpty) {
  $partList['part_id'][] = '';
  $partList['part_short'][] = '';
  $partList['path'][] = '';
	};
	$partRecord = array();
	if ($thePath != '') {
		$entries = array();
		$handle = opendir($thePath);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				if (! preg_match('/^'.DCTL_RESERVED_PREFIX.'/',$entry)) {
				 $entries[] = $entry;
				};
			};
		};
		sort($entries);
  foreach($entries as $entry) {
			if (($entry == DCTL_PACKAGE_FRONT) || (preg_match('/^'.str_ireplace(DCTL_PACKAGE_BODY_REGEXP1, DCTL_PACKAGE_BODY_REGEXP2, DCTL_PACKAGE_BODY).'/', $entry)) || ($entry == DCTL_PACKAGE_BACK)) {
				$full = $thePath.$entry;
				getPartRecord ($full, &$partRecord);
				$partList['part_id'][] = $partRecord['part_id'];
				$partList['part_short'][] = $partRecord['part_short'];
				$partList['path'][] = $full;
			};
		};
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getItemList ($thePath, &$itemList, $what, $withEmpty=false) {
	$itemList = array();
	if ($withEmpty) {
  $itemList['item_id'][] = '';
  $itemList['item_short'][] = '';
  $itemList['path'][] = '';
	};
	$itemRecord = array();
	if ($thePath[strlen($thePath)-1] == SYS_PATH_SEPARATOR) $thePath = dirname($thePath.'xxx');
	if ($thePath != '') {
		if (is_file($thePath)) {
			forceUTF8($thePath);
			$simplexml = simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER);
			$simplexml = simplexml_load_string(preg_replace('/xml\:id/','id',$simplexml->asXML()), 'SimpleXMLElement');
			$namespaces = $simplexml->getDocNamespaces();
			foreach ($namespaces as $nsk=>$ns) {
				if ($nsk == '') $nsk = 'tei';
				$simplexml->registerXPathNamespace($nsk, $ns);
			};
			$xpath = '//tei:text//*[@id != ""]';
			switch ($what) {
				case 'lnk':
				break;
				case 'map':
					$xpath .= '[contains(@ana, "key_item")]';
				break;
			};
			$resultXML = $simplexml->xpath($xpath);
   foreach ($resultXML as $k=>$v) {
				$entry = $v['id'];
				$full = $thePath.SYS_PATH_SEPARATOR.$entry;
				getItemRecord ($full, &$itemRecord, cleanWebString($v->asXML()));
				$itemList['item_id'][] = $itemRecord['item_id'];
				$itemList['item_short'][] = $itemRecord['item_work'];
				$itemList['path'][] = $full;
			};
		};
	};
//	asort($itemList);
	return count($itemList);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function getImageList ($thePath, &$imageList, $withEmpty=false) {
	$imageList = array();
	if ($withEmpty) {
  $imageList['image_id'][] = '';
  $imageList['image_short'][] = '';
  $imageList['image_work'][] = '';
  $imageList['path'][] = '';
	};
	$imageRecord = array();
	if ($thePath[strlen($thePath)-1] == SYS_PATH_SEPARATOR) $thePath = dirname($thePath.'xxx');
	if ($thePath != '') {
		if (is_file($thePath)) {
   forceUTF8($thePath);
			$simplexml = simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER);
	 	$simplexml = simplexml_load_string(preg_replace('/xml\:id/','id',$simplexml->asXML()), 'SimpleXMLElement');
			$namespaces = $simplexml->getDocNamespaces();
			foreach ($namespaces as $nsk=>$ns) {
				if ($nsk == '') $nsk = 'tei';
				$simplexml->registerXPathNamespace($nsk, $ns);
			};
			$xpath = '//tei:text//tei:figure';
			$resultXML = $simplexml->xpath($xpath);
   foreach ($resultXML as $k=>$v) {
    $entry = $v->graphic['url'];
    $full = $thePath.SYS_PATH_SEPARATOR.$entry;
    getImageRecord ($full, &$imageRecord, cleanWebString($v->figDesc->asXML()));
    $imageList['image_id'][] = $imageRecord['image_id'];
    $imageList['image_short'][] = $imageRecord['image_short'];
    $imageList['image_work'][] = $imageRecord['image_work'];
    $imageList['path'][] = $full;
   };
		};
	};
	// asort($imageList);
	return count($imageList);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function publish_transformXML($entry, $fullsrc, $fulldst, $xsl_proc, &$operationsPublish, &$toPublish) {
	$text = '';
	$prosecute = FALSE;
	$basename = basename(dirname($fullsrc));
	$uppername = basename(dirname(dirname($fullsrc)));
	$operationsPublish .= '&gt; processo "'.$basename.'"...';
	$level = count(explode(SYS_PATH_SEPARATOR, str_ireplace(DCTL_PROJECT_PATH, '', $fullsrc)));
	switch ($level) {
		case 2 : // COLLECTION
			switch ($entry) {
				case DCTL_FILE_BUILDER:
					$fTarget = $basename.DCTL_RESERVED_INFIX.DCTL_RESERVED_PREFIX.$basename.'.xml';
					$prosecute = TRUE;
				break;
				case DCTL_FILE_LINKER:
				case DCTL_FILE_MAPPER:
					$fTarget = $basename.DCTL_RESERVED_INFIX.$entry;
					$prosecute = TRUE;
				break;
			};
  break;
  case 3 : // PACKAGE
			switch ($entry) {
				case DCTL_FILE_BUILDER:
					copy(DCTL_SETTINGS_TEMPLATES_PACKAGE.DCTL_FILE_BUILDER, $fullsrc);
					@chmod($fullsrc, CHMOD);
					$fTarget = $uppername.DCTL_RESERVED_INFIX.$basename.'.xml';
					$prosecute = TRUE;
				break;
			};
  break;
	};
	if ($prosecute) {
		$xml_path = $fullsrc;
  try {
			if (isset($xml_dom)) unset($xml_dom);
			$xml_dom = new DOMDocument('1.0', 'UTF-8');
			$xml_dom->preserveWhiteSpace = false;
   forceUTF8($xml_path);
   $xml_dom->load($xml_path, DCTL_XML_LOADER);
		} catch (Exception $e) {
			$operationsPublish .= '<span class="error">impossibile caricare XML "'.$entry.'"... {'.$e.'}</span><br />';
			$prosecute = FALSE;
		};
		if ($prosecute) {
			try {
				$text .= $xsl_proc->transformToXML($xml_dom);
   } catch (Exception $e) {
				$operationsPublish .= '<span class="error">impossibile trasformare XML "'.$entry.'"... {'.$e.'}</span><br />';
				$prosecute = FALSE;
			};
		};
		if ($prosecute) {
			$text = str_ireplace('xmlns=""','', $text);
			if ($text != '') {
				try {
					$fulldst = dirname($fulldst).SYS_PATH_SEPARATOR.$fTarget;
					$fd = fopen ($fulldst, "wb");
					$out = fwrite ($fd, forceUTF8($text));
					fclose ($fd);
					@chmod($fulldst, CHMOD);
					$prosecute = TRUE;
					$toPublish[] = $fulldst;
				} catch (Exception $e) {
					$operationsPublish .= '<span class="error">impossibile riscrivere XML "'.$entry.'"... {'.$e.'}</span><br />';
					$prosecute = FALSE;
				};
			} else {
				$operationsPublish .= '<span class="error">impossibile riscrivere XML "'.$entry.'"... {'.$e.'}</span><br />';
				$prosecute = FALSE;
			};
		} else {
			$operationsPublish .= '<span class="error">impossibile riscrivere XML "'.$entry.'"... {'.$e.'}</span><br />';
			$prosecute = FALSE;
		};
	};
	if($prosecute) $operationsPublish .= 'ok<br />';
	hardFlush(&$operationsPublish);
	return TRUE && $prosecute;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function dctl_insertContent ($complete_id, $param) {
 $complete_id = explode( '-', $complete_id);
 $collection_id = $complete_id[0];
 $package_id = $complete_id[1];
 $text = '';
	$fSelectorX = trim($param);
	$fSelectorX = str_ireplace('"', '', $fSelectorX);
	$fSelectorX = str_ireplace('item=', '', $fSelectorX);
	$dPath = DCTL_PROJECT_PATH.$collection_id;
	if ($package_id != '') {
		$dPath .= SYS_PATH_SEPARATOR.$package_id;
	};
	$isMultiPart = stripos($fSelectorX, '$') !== FALSE;
	if ($isMultiPart) {
		$regexp = str_ireplace(DCTL_PACKAGE_BODY_REGEXP1, DCTL_PACKAGE_BODY_REGEXP2, $fSelectorX);
	} else {
		$regexp = $fSelectorX;
	};
	$variants = array();
	if (is_dir($dPath)) {
		$handle = opendir($dPath);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				$variants[] = $entry;
			};
		};
	};
	$variants = array_values(preg_grep('/^'.$regexp.'/', $variants));
	sort($variants);
 $max = count($variants)-1;
	foreach($variants as $vKey=>$fSelector) {
	 if ((!$isMultiPart) || ($isMultiPart && (($vKey>0) && ($vKey<$max)))) {
			$fPath = $dPath.SYS_PATH_SEPARATOR.$fSelector;
			$textContent = cleanUpIndentation(charset_decode_utf_8(file_get_contents($fPath)));
			$header = '<!-- %BEGIN% -->';
			$textContent = substr($textContent, stripos($textContent, $header)+strlen($header));
			$footer = '<!-- %END% -->';
			$textContent = substr($textContent, 0, stripos($textContent, $footer));
			$textContent = preg_replace('/(<!--'.WHITESPACES.'*BEGIN'.WHITESPACES.'*-->)/', '', $textContent);
			$textContent = preg_replace('/(<!--'.WHITESPACES.'*END'.WHITESPACES.'*-->)/', '', $textContent);
			$textContent = preg_replace('/'.WHITESPACES.'+/', ' ', $textContent);
			$textContent = forceUTF8($textContent);
			$checkContent = preg_replace('/\w+:(\w+)/','$1',$textContent);
			$checkContent = '<?xml version="1.0" encoding="UTF-8" ?><test>'.translateLiteral2NumericEntities($checkContent).'</test>';
			if ($e=simplexml_load_string($checkContent, 'SimpleXMLElement', DCTL_XML_LOADER)) {
				$checkChildren = count($e->children());
				if ($checkChildren<1) {
					$textContent = '<div type="part" />';
				};
			};
			$text .= $textContent;
		};
	};
	return strval($text);
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
  function translateLiteral2NumericEntities($xmlSource, $reverse =
FALSE) {
    static $literal2NumericEntity;
    if (empty($literal2NumericEntity)) {
      $transTbl = get_html_translation_table(HTML_ENTITIES);
      foreach ($transTbl as $char => $entity) {
        if (strpos('&"<>', $char) !== FALSE) continue;
        $literal2NumericEntity[$entity] = '&#'.ord($char).';';
      };
    };
    if ($reverse) {
      return strtr($xmlSource, array_flip($literal2NumericEntity));
    } else {
      return strtr($xmlSource, $literal2NumericEntity);
    };
  };
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function publish_recurseContents($pathFrom, $collName, $packName, $xsl_proc, &$operationsPublish, &$toPublish) {
	hardFlush(&$operationsPublish);
	$prosecute = false;
	$pathTo = DCTL_PUBLISH;
	$handle = opendir($pathFrom);

	$basex = basename($pathFrom);
	if ($basex==$collName) {
		// IN COLLECTION
		// BUILDER
		$fullsrc = DCTL_SETTINGS_TEMPLATES_COLLECTION.DCTL_FILE_BUILDER;
		$fulldst = DCTL_PROJECT_PATH.$collName.SYS_PATH_SEPARATOR.DCTL_FILE_BUILDER;
		copy($fullsrc, $fulldst);
		@chmod($fulldst, CHMOD);
	} else {
	 if (($packName=='')||($basex==$packName)) {
		// IN PACKAGE
			if ($basex==$packName) {
					// BUILDER
				$fullsrc = DCTL_SETTINGS_TEMPLATES_PACKAGE.DCTL_FILE_BUILDER;
				$fulldst = DCTL_PROJECT_PATH.$collName.SYS_PATH_SEPARATOR.$packName.SYS_PATH_SEPARATOR.DCTL_FILE_BUILDER;
				copy($fullsrc, $fulldst);
				@chmod($fulldst, CHMOD);
			};
	 };
	};
	while ($entry = readdir($handle)) {
		if (substr($entry, 0, 1) != '.') {
			$fullsrc = $pathFrom.SYS_PATH_SEPARATOR.$entry;
			$fulldst = $pathTo.SYS_PATH_SEPARATOR.$entry;
			switch ($entry) {
				case (basename(DCTL_MEDIA)):
				case (basename(DCTL_MEDIA_SML)):
				case (basename(DCTL_MEDIA_MED)):
				case (basename(DCTL_MEDIA_BIG)):
					$prosecute = TRUE;
    break;
				case DCTL_FILE_BUILDER:
				case DCTL_FILE_LINKER:
				case DCTL_FILE_MAPPER:
					$prosecute = publish_transformXML($entry, $fullsrc, $fulldst, $xsl_proc, &$operationsPublish, &$toPublish);
    break;
				default:
    if ($packName != '') {
     if ($packName == $entry) {
				// PUBLISH SELECTED PACKAGE
      $prosecute = publish_recurseContents(DCTL_PROJECT_PATH.$collName.SYS_PATH_SEPARATOR.$entry, $collName, $entry, $xsl_proc, &$operationsPublish, &$toPublish);
     };
    } else {
				// PUBLISH SELECTED COLLECTION
     if (! preg_match('/^'.DCTL_RESERVED_PREFIX.'/',$entry)) {
      $prosecute = publish_recurseContents(DCTL_PROJECT_PATH.$collName.SYS_PATH_SEPARATOR.$entry, $collName, $entry, $xsl_proc, &$operationsPublish, &$toPublish);
     };
    };
    break;
			};
		};
	};
	return TRUE && $prosecute;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function publish_recurseMedia($pathFrom, $pathTo, &$operationsPublish) {
	hardFlush(&$operationsPublish);
	$prosecute = false;
	if (is_dir($pathFrom)) {
		if (!is_dir($pathTo)) mkdir($pathTo, CHMOD);
  @chmod($pathTo, CHMOD);
		$handle = opendir($pathFrom);
		$basex = basename($pathFrom);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				$fullsrc = $pathFrom.SYS_PATH_SEPARATOR.$entry;
				$fulldst = $pathTo.SYS_PATH_SEPARATOR.$entry;
    if (is_dir($fullsrc)) {
					$operationsPublish .= '&gt; avvio la copia per "'.$entry.':<br />';
					hardFlush(&$operationsPublish);
					if (!is_dir($fulldst)) mkdir($fulldst, CHMOD);
     @chmod($fulldst, CHMOD);
					$prosecute = publish_recurseMedia($fullsrc, $fulldst, &$operationsPublish);
					$operationsPublish .= '... ok<br />';
					hardFlush(&$operationsPublish);
				} else {
					$srcx = filemtime($fullsrc);
					$tgtx = false || @filemtime($fulldst);
					if ($srcx > $tgtx) {
						$operationsPublish .= '&gt; copio "'.basename($pathFrom).SYS_PATH_SEPARATOR.$entry.'<br />';
						hardFlush(&$operationsPublish);
						copy($fullsrc, $fulldst);
						@chmod($fulldst, CHMOD);
						$prosecute = true;
					};
				};
			};
		};
	};
	return TRUE && $prosecute;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function publish_SendToXDB ($exist, $collName, $packName, $partName, &$operationsPublish) {
	global $EXTENSION_PACKAGE;
		// INIT
 $toPublish = array();
		// PREPARA FOLDERS
	$component = $collName;
	if ($packName != '') {
		$component .= SYS_PATH_SEPARATOR.$packName;
	};
	$operationsPublish .= '&gt; avvio la preparazione dei dati per "'.$component.':<br />';
	hardFlush(&$operationsPublish);
	$prosecute = TRUE;
 if (!is_dir(DCTL_PUBLISH)) {
		if (mkdir(DCTL_PUBLISH, CHMOD)) {
			$operationsPublish .= '&gt; creo cartella '.DCTL_PUBLISH.'...<br />';
		} else {
			$operationsPublish .= '<span class="error">impossibile creare la cartella "'.DCTL_PUBLISH.'"!</span><br />';
			$prosecute = FALSE;
		};
	};
 @chmod(DCTL_PUBLISH, CHMOD);
	$operationsPublish .= '&gt; avvio la preparazione dei file in "'.DCTL_PUBLISH.'":<br />';
	hardFlush(&$operationsPublish);
	if ($prosecute) {
		// COPIA I DTD
		dircopy(DCTL_SETTINGS_DTD, DCTL_PUBLISH);
		$operationsPublish .= '&#160;&#160;- copio "'.DCTL_DTD.'"...<br />';
		hardFlush(&$operationsPublish);
		// COPIA GLI ADD-ONS
		dircopy(DCTL_SETTINGS_ADDONS, DCTL_PUBLISH);
		$operationsPublish .= '&#160;&#160;- copio "'.DCTL_ADDONS.'"...<br />';
		hardFlush(&$operationsPublish);
		// AGGIORNA IL CATALOGO.XML
		$operationsPublish .= '&#160;&#160;- verifico il catalogo dei "'.DCTL_DTD.'":<br />';
		hardFlush(&$operationsPublish);
		$dtd_catalog = XMLDB_CATALOG;
  try {
			if (isset($xml_dom)) unset($xml_dom);
			$xml_dom = new DOMDocument('1.0', 'UTF-8');
			$xml_dom->preserveWhiteSpace = false;
			forceUTF8($dtd_catalog);
			$xml_dom->load($dtd_catalog, DCTL_XML_LOADER);
		} catch (Exception $e) {
			$operationsPublish .= '<span class="error">impossibile caricare XML "'.$entry.'"... {'.$e.'}</span><br />';
			$prosecute = FALSE;
		};
		if ($prosecute) {
			$upd = FALSE;
			$dtds_needed = array();
			$handle = opendir(DCTL_SETTINGS_DTD);
			while ($entry = readdir($handle)) {
				if (substr($entry, 0, 1) != '.') {
					$srcx = filemtime(DCTL_SETTINGS_DTD.$entry);
					$tgtx = false || @filemtime(XMLDB_ENTITIES.SYS_PATH_SEPARATOR.$entry);
					if ($srcx > $tgtx) {
					 $upd = TRUE;
					};
				};
			};
			dircopy(DCTL_SETTINGS_DTD, XMLDB_ENTITIES, &$dtds_needed);
			$operationsPublish .= '&#160;&#160;- copio da "'.DCTL_SETTINGS_DTD.'" a "'.XMLDB_ENTITIES.'"...<br />';
			hardFlush(&$operationsPublish);
			$dtds_present = $xml_dom->getElementsByTagName('system');
			foreach($dtds_present as $dtd_present) {
				$dtd_chk = $dtd_present->getAttribute('systemId');
				$dtd_key = array_search($dtd_chk, $dtds_needed);
				if ($dtd_key !== FALSE) {
					$dtds_needed[$dtd_key] = '';
				};
			};
			foreach($dtds_needed as $dtd_needed) {
				if ($dtd_needed != '') {
					$node = $xml_dom->createElement('system');
					$dtd_root = $xml_dom->getElementsByTagName('catalog')->item(0);
					$newnode = $dtd_root->appendChild($node);
					$newnode->setAttribute('systemId', $dtd_needed);
					$newnode->setAttribute('uri', 'entities'.SYS_PATH_SEPARATOR.$dtd_needed);
					$operationsPublish .= '&gt; aggiungo '.$dtd_needed.'...<br />';
					hardFlush(&$operationsPublish);
					$upd = TRUE;
				};
			};
			if ($upd == TRUE) {
				$xml_dom->save($dtd_catalog);
				$operationsPublish .= '<span class="error">Aggiornamento non eseguito per modifiche necessarie al database.</span><br />';
				$operationsPublish .= '<span class="error">IMPORTANTE: riavviare exist per proseguire correttamente (aggiornato "catalog.xml")!</span><br />';
				$prosecute = FALSE;
			};
			if ($prosecute) {
				// PREPARA XML #1
				$xsl_path = DCTL_COMMODORO_XSLT.'tei_publisher_1.xsl';
				if (!is_file($xsl_path)) {
					$operationsPublish .= '<span class="error">impossibile trovare XSLT "'.$xsl_path.'"!</span><br />';
					$prosecute = FALSE;
				} else {
					try {
						if (isset($xsl_dom)) unset($xsl_dom);
						$xsl_dom = new DOMDocument('1.0', 'UTF-8');
						$xsl_dom->preserveWhiteSpace = false;
						forceUTF8($xsl_path);
						$xsl_dom->load($xsl_path, DCTL_XML_LOADER);
					} catch (Exception $e) {
						$operationsPublish .= '<span class="error">impossibile caricare XSLT "'.$xsl_path.'"... {'.$e.'}</span><br />';
						$prosecute = FALSE;
					};
					if ($prosecute) {
						$operationsPublish .= '&gt; carico XSLT "'.$xsl_path.'"...<br />';
						hardFlush(&$operationsPublish);
						try {
							if (isset($xsl_proc)) unset($xsl_proc);
							$xsl_proc = new XSLTProcessor();
							$xsl_proc->registerPHPFunctions();
							$xsl_proc->importStyleSheet($xsl_dom); // attach the xsl rules
						} catch (Exception $e) {
							$operationsPublish .= '<span class="error">impossibile inizializzare il processore XSL... {'.$e.'}</span><br />';
							$prosecute = FALSE;
						};
						if ($prosecute) {
							$operationsPublish .= '&gt; trasformo i file XML (#1):<br />';
							$prosecute = publish_recurseContents(DCTL_PROJECT_PATH.$collName, $collName, $packName, $xsl_proc, &$operationsPublish, &$toPublish);
						};
						if ($prosecute) {
							// PREPARA XML #2
							$xsl_path = DCTL_COMMODORO_XSLT.'tei_publisher_2.xsl';
							if (!is_file($xsl_path)) {
								$operationsPublish .= '<span class="error">impossibile trovare XSLT "'.$xsl_path.'"!</span><br />';
								$prosecute = FALSE;
							} else {
								try {
									if (isset($xsl_dom)) unset($xsl_dom);
									$xsl_dom = new DOMDocument('1.0', 'UTF-8');
									$xsl_dom->preserveWhiteSpace = false;
									forceUTF8($xsl_path);
									$xsl_dom->load($xsl_path, DCTL_XML_LOADER);
								} catch (Exception $e) {
									$operationsPublish .= '<span class="error">impossibile caricare XSLT "'.$xsl_path.'"... {'.$e.'}</span><br />';
									$prosecute = FALSE;
								};
								if ($prosecute) {
									$operationsPublish .= '&gt; carico XSLT "'.$xsl_path.'"...<br />';
									hardFlush(&$operationsPublish);
									try {
										if (isset($xsl_proc)) unset($xsl_proc);
										$xsl_proc = new XSLTProcessor();
										$xsl_proc->registerPHPFunctions();
										$xsl_proc->importStyleSheet($xsl_dom); // attach the xsl rules
									} catch (Exception $e) {
										$operationsPublish .= '<span class="error">impossibile inizializzare il processore XSL... {'.$e.'}</span><br />';
										$prosecute = FALSE;
									};
									if ($prosecute) {
										$operationsPublish .= '&gt; trasformo i file XML :: ID/NUM (#2):<br />';
										hardFlush(&$operationsPublish);
										foreach($toPublish as $fullsrc) {
											$prosecute = true;
											$fulldst = $fullsrc;
											$entry = basename($fulldst);
											if (in_array(substr($entry, -8, 4), $EXTENSION_PACKAGE) !== FALSE) {
												$operationsPublish .= '&#160;- trasformazione XSLT per XML "'.$entry.'":';
            try {
													if (isset($xml_dom)) unset($xml_dom);
													$xml_dom = new DOMDocument('1.0', 'UTF-8');
													$xsl_dom->preserveWhiteSpace = false;
													forceUTF8($fullsrc);
													$xml_dom->load($fullsrc, DCTL_XML_LOADER);
												} catch (Exception $e) {
													$operationsPublish .= '<span class="error">impossibile caricare XML "'.$entry.'"... {'.$e.'}</span><br />';
													$prosecute = FALSE;
												};
												if ($prosecute) {
													$operationsPublish .= '&#160;aggiorno...';
													try {
														$text = $xsl_proc->transformToXML($xml_dom);
													} catch (Exception $e) {
														$operationsPublish .= '<span class="error">impossibile trasformare XML "'.$entry.'"... {'.$e.'}</span><br />';
														$prosecute = FALSE;
													};
													if ($prosecute) {
														$operationsPublish .= '&#160;scrivo XML...';
														$text = str_ireplace('xmlns=""','', $text);
														try {
															$fd = fopen ($fulldst, "wb");
															$out = fwrite ($fd, forceUTF8($text));
															fclose ($fd);
															@chmod($fulldst, CHMOD);
															$operationsPublish .= '&#160;ok<br/>';
														} catch (Exception $e) {
															$operationsPublish .= '<span class="error">impossibile riscrivere XML "'.$entry.'"... {'.$e.'}</span><br />';
															$prosecute = FALSE;
														};
														hardFlush(&$operationsPublish);
													};
												};
											};
										};
									};
         if ($prosecute) {
									// PREPARA XML #3
										// 3) carico i file XML e li trasformo
										$xsl_path = DCTL_COMMODORO_XSLT.'tei_publisher_3.xsl';
										if (!is_file($xsl_path)) {
											$operationsPublish .= '<span class="error">impossibile trovare XSLT "'.$xsl_path.'"!</span><br />';
											$prosecute = FALSE;
										};
										if ($prosecute) {
											try {
												if (isset($xsl_dom)) unset($xsl_dom);
												$xsl_dom = new DOMDocument('1.0', 'UTF-8');
												$xsl_dom->preserveWhiteSpace = false;
												forceUTF8($xsl_path);
												$xsl_dom->load($xsl_path, DCTL_XML_LOADER);
											} catch (Exception $e) {
												$operationsPublish .= '<span class="error">impossibile caricare XSLT "'.$xsl_path.'"... {'.$e.'}</span><br />';
												$prosecute = FALSE;
											};
											if ($prosecute) {
												$operationsPublish .= '&gt; carico XSLT "'.$xsl_path.'"...<br />';
												hardFlush(&$operationsPublish);
												try {
													if (isset($xsl_proc)) unset($xsl_proc);
													$xsl_proc = new XSLTProcessor();
													$xsl_proc->registerPHPFunctions();
													$xsl_proc->importStyleSheet($xsl_dom); // attach the xsl rules
												} catch (Exception $e) {
													$operationsPublish .= '<span class="error">impossibile inizializzare il processore XSL... {'.$e.'}</span><br />';
													$prosecute = FALSE;
												};
												if ($prosecute) {
													$operationsPublish .= '&gt; trasformo i file XML :: LABEL/NAME/ICONTERM (#3):<br />';
													hardFlush(&$operationsPublish);
													global $mysql_dbName;
													global $mysql_dbIconclass;
													try {
														$mysql_dbName = dctl_sql_connect(DCTL_DB_NAME);
													} catch (Exception $e) {
														die('<span class="wctl_error">[DB] ' . $e . '</span>');
													};
													try {
														$mysql_dbIconclass = dctl_sql_connect(DCTL_DB_ICONCLASS);
													} catch (Exception $e) {
														die('<span class="wctl_error">[DB] ' . $e . '</span>');
													};
													foreach($toPublish as $fullsrc) {
														$prosecute = true;
														$fulldst = $fullsrc;
														$entry = basename($fulldst);
														if (in_array(substr($entry, -8, 4), $EXTENSION_PACKAGE) !== FALSE) {
															$operationsPublish .= '&#160;- trasformazione XSLT per XML "'.$entry.'":';
															try {
																if (isset($xml_dom)) unset($xml_dom);
																$xml_dom = new DOMDocument('1.0', 'UTF-8');
																$xml_dom->preserveWhiteSpace = false;
																forceUTF8($fullsrc);
																$xml_dom->load($fullsrc, DCTL_XML_LOADER);
															} catch (Exception $e) {
																$operationsPublish .= '<span class="error">impossibile caricare XML "'.$entry.'"... {'.$e.'}</span><br />';
																$prosecute = FALSE;
															};
															if ($prosecute) {
																$operationsPublish .= '&#160;aggiorno...';
																if ($mysql_dbName && $mysql_dbIconclass) {
																	try {
																		$text = $xsl_proc->transformToXML($xml_dom);
																	} catch (Exception $e) {
																		$operationsPublish .= '<span class="error">impossibile trasformare XML "'.$entry.'"... {'.$e.'}</span><br />';
																		$prosecute = FALSE;
																	};
																	if ($prosecute) {
																		$operationsPublish .= '&#160;scrivo XML...';
																		$text = str_ireplace('xmlns=""','', $text);
																		try {
																			$fd = fopen ($fulldst, "wb");
																			$out = fwrite ($fd, forceUTF8($text));
																			fclose ($fd);
																			@chmod($fulldst, CHMOD);
																			$operationsPublish .= 'ok<br/>';
																		} catch (Exception $e) {
																			$operationsPublish .= '<span class="error">impossibile riscrivere XML "'.$entry.'"... {'.$e.'}</span><br />';
																			$prosecute = FALSE;
																		};
																		hardFlush(&$operationsPublish);
																	};
																} else {
																	$operationsPublish .= '<span class="error">impossibile connettersi a MySQL...</span><br />';
																	$prosecute = FALSE;
																};
															};
														};
													};
													mysql_close($mysql_dbName);
													mysql_close($mysql_dbIconclass);
													if ($prosecute) {
														// PREPARA XMLDB
														$operationsPublish .= '&gt; connessione al database "exist":<br />';
														hardFlush(&$operationsPublish);
														if (!$exist) {
															$operationsPublish .= '<span class="error">impossibile connettersi al database "exist"... {'.$e.'}</span><br />';
															$prosecute = FALSE;
														};
														if ($prosecute) {
															$baseDB = XMLDB_PATH_BASE;
               $result = ($exist->createCollection($baseDB) != FALSE) or dump($exist->getError());
															if (!$result) {
																$operationsPublish .= '<span class="error">impossibile creare la Collection base "'.$baseDB.'"!</span><br />';
																$prosecute = FALSE;
															};
															if ($prosecute) {
																// 1) creo la collezione in exist
																$operationsPublish .= '&#160;&#160;- aggiorno la Collection "'.$collName.'":<br />';
																hardFlush(&$operationsPublish);
																if ($packName == '') {
																	$exist->removeCollection($baseDB.$collName);
																	$operationsPublish .= '&#160;&#160;- ripulisco la Collection "'.$collName.'"...<br />';
																};
																$result = ($exist->createCollection($baseDB.$collName) != FALSE) or dump($exist->getError());
																if (!$result) {
																	$operationsPublish .= '<span class="error">impossibile creare la Collection "'.$collName.'"!</span><br />';
																	$prosecute = FALSE;
																};
																$operationsPublish .= '&gt; pubblico i dati nel database...<br />';
																hardFlush(&$operationsPublish);
																foreach($toPublish as $fullsrc) {
																	$prosecute = true;
																	$fulldst = $fullsrc;
																	$entry = basename($fulldst);
																	// 3) pubblico nella collezione di exist
																	$operationsPublish .= '&#160;&#160;- pubblico "'.$entry.'"...';
																	$pathTo = $baseDB.$collName.DB_PATH_SEPARATOR.$entry;
																	$result = ($exist->uploadResource($pathTo, $fulldst) !== FALSE) or dump($exist->getError());
																	if (!$result) {
																		$operationsPublish .= '<span class="error">impossibile pubblicare XML "'.$entry.'"!</span><br />';
																		$prosecute = FALSE;
																	} else {
																		$operationsPublish .= '&#160;ok<br />';
																		$prosecute = TRUE;
																	};
																		hardFlush(&$operationsPublish);
																};
																if ($prosecute) {
																	$fullsrc = DCTL_PROJECT_PATH.$collName.SYS_PATH_SEPARATOR.DCTL_MEDIA;
																 if (true) {
																		$prosecute = publish_recurseMedia($fullsrc, DCTL_PUBLISH_MEDIA, &$operationsPublish);
																	} else {
																		if (! COPY_MEDIA) {
																			$operationsPublish .= '<br /><span class="error">RI-ABILITA COPIA DEI MEDIA...';
																		} else {
																			// COPIA MEDIA
																			$operationsPublish .= '&#160;&#160;- copio "'.$fullsrc.'"...<br />';
																			hardFlush(&$operationsPublish);
																			dircopy($fullsrc, DCTL_PUBLISH_MEDIA);
																		};
																	};
																	$operationsPublish .= '<br /><span class="ok">Pubblicazione per "'.$collName;
																	if ($packName != '') {
																		$operationsPublish .= SYS_PATH_SEPARATOR.$packName;
																	};
																	$operationsPublish .= '" effettuata con successo.</span><br /><br />';
																} else {
																	$operationsPublish .= '<br /><span class="error">Errori nella pubblicazione per "'.$component.'"!</span><br /><br />';
																};
																hardFlush(&$operationsPublish);
															};
														};
													};
												};
											};
										};
									};
								};
							};
						};
					};
				};
			};
		};
	};
	return TRUE && $prosecute;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function publish_RemoveFromXDB ($exist, $collName, $packName, $partName, &$operationsPublish) {
	$toPublish = array();
	$component = $collName;
	if ($packName != '') {
		$component .= SYS_PATH_SEPARATOR.$packName;
	};
	$operationsPublish .= '(*) &gt; avvio la de-pubblicazione sul web per "'.$component.'":<br />';
	hardFlush(&$operationsPublish);
	$prosecute = $collName != '';
	if ($prosecute) {
		// DE-PUBBLICA SU XMLDB
		$operationsPublish .= '&gt; connessione al database "exist":<br />';
		hardFlush(&$operationsPublish);
		$baseDB = XMLDB_PATH_BASE;
		if ($packName == '') {
			if (@$exist->removeCollection($baseDB.$collName)) {
				$operationsPublish .= '&gt; de-pubblico la Collection "'.$collName.'"...<br />';
			} else {
				$operationsPublish .= '<span class="error">impossibile de-pubblicare la Collection "'.$collName.'"!</span><br />';
			};
		} else {
			if ($exist->removeDocument($baseDB.$collName.SYS_PATH_SEPARATOR.$packName.'.xml')) {
				$operationsPublish .= '&gt; de-pubblico il Package "'.$packName.'" in "'.$collName.'"...<br />';
			} else {
				$operationsPublish .= '<span class="error">impossibile de-pubblicare il Package "'.$packName.'" in "'.$collName.'"!</span><br />';
			};
		};
	};
	hardFlush(&$operationsPublish);
	return TRUE && $prosecute;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function simplexml2ISOarray ($simplexml,$attribsAsElements=0) {
	if (get_class($simplexml) == 'SimpleXMLElement') {
		$attributes = $simplexml->attributes();
		foreach($attributes as $k=>$v) {
			if ($v) $a[$k] = (string) $v;
		};
		$x = $simplexml;
		$simplexml = get_object_vars($simplexml);
	};
	if (is_array($simplexml)) {
		if (count($simplexml) == 0) return (string) $x; // for CDATA
		foreach($simplexml as $key=>$value) {
			$r[$key] = simplexml2ISOarray($value,$attribsAsElements);
			if (!is_array($r[$key])) $r[$key] = utf8_decode($r[$key]);
		};
		if (isset($a)) {
			if($attribsAsElements) {
				$r = array_merge($a,$r);
			} else {
				$r['@'] = $a; // Attributes
			};
		};
		return $r;
	};
	return (string) $simplexml;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

function doThumbnail() {
 // define the base image dir
 $base_img_dir = "./";

 // $QUERY_STRING =
 //  f(3c9b5fa6bc0fa)  img_file
 //  w(123|15%)        width of output
 //  h(123|10%)        height of output
 //  x(123)            max width of output
 //  y(123)            max height of output
 //  t(jpg|png)        type of output
 //  q(100)            quality of jpeg

 // find tags
 preg_match_all("/\+*(([a-z])\(([^\)]+)\))\+*/", $QUERY_STRING,
 $matches, PREG_SET_ORDER);

 // empty array and set regular expressions for the check
 $tags = array();
 $check = array( "f" => "[0-9a-zA-Z]{13}",
                "w" => "[0-9]+%?",
                "h" => "[0-9]+%?",
                "x" => "[0-9]+",
                "y" => "[0-9]+",
                "t" => "jpg|png",
                "q" => "1?[0-9]{1,2}" );

 // check tags and save correct values in array
 for ($i=0; $i<count($matches); $i++) {
  if (isset($check[$matches[$i][2]])) {
   if (preg_match('/^('.$check[$matches[$i][2]].')$/',
                  $matches[$i][3])) {
    $tags[$matches[$i][2]] = $matches[$i][3];
   }
  }
 }

 function notfound() {
  header("HTTP/1.0 404 Not Found");
  exit;
 }

 // check that filename is given
 if (!isset($tags["f"])) {
  notfound();
 }

 // check if file exists
 if (!file_exists($base_img_dir.$tags["f"])) {
  notfound();
 }

 // retrieve file info
 $imginfo = getimagesize($base_img_dir.$tags["f"]);

 // load image
 switch ($imginfo[2]) {
  case 2:     // jpg
  $img_in = imagecreatefromjpeg($base_img_dir.$tags["f"]) or notfound();
  if (!isset($tags["t"])) {
   $tags["t"] = "jpg";
  }
  break;
  case 3:     // png
  $img_in = imagecreatefrompng($base_img_dir.$tags["f"]) or notfound();
  if (!isset($tags["t"])) {
   $tags["t"] = "png";
  }
  break;
  default:
  notfound();
 }

 // check for maximum width and height
 if (isset($tags["x"])) {
  if ($tags["x"] < imagesx($img_in)) {
   $tags["w"] = $tags["x"];
  }
 }
 if (isset($tags["y"])) {
  if ($tags["y"] < imagesy($img_in)) {
   $tags["h"] = $tags["y"];
  }
 }

 // check for need to resize
 if (isset($tags["h"]) or isset($tags["w"])) {
  // convert relative to absolute
  if (isset($tags["w"])) {
   if (strstr($tags["w"], "%")) {
    $tags["w"] = (intval(substr($tags["w"], 0, -1)) / 100) *
    $imginfo[0];
   }
  }
  if (isset($tags["h"])) {
   if (strstr($tags["h"], "%")) {
    $tags["h"] = (intval(substr($tags["h"], 0, -1)) / 100) *
    $imginfo[1];
   }
  }

  // resize
  if (isset($tags["w"]) and isset($tags["h"])) {
   $out_w = $tags["w"];
   $out_h = $tags["h"];
  } elseif (isset($tags["w"]) and !isset($tags["h"])) {
   $out_w = $tags["w"];
   $out_h = $imginfo[1] * ($tags["w"] / $imginfo[0]);
  } elseif (!isset($tags["w"]) and isset($tags["h"])) {
   $out_w = $imginfo[0] * ($tags["h"] / $imginfo[1]);
   $out_h = $tags["h"];
  } else {
   $out_w = $tags["w"];
   $out_h = $tags["h"];
  }

  // new image in $img_out
  $img_out = imagecreate($out_w, $out_h);
  imagecopyresized($img_out, $img_in, 0, 0, 0, 0, imagesx($img_out),
                   imagesy($img_out), imagesx($img_in), imagesy($img_in));
 } else {
  // no resize needed
  $img_out = $img_in;
 }

 // check for a given jpeg-quality, otherwise set to default
 if (!isset($tags["q"])) {
  $tags["q"] = 75;
 }

 // returning the image
 switch ($tags["t"]) {
  case "jpg":
  header("Content-type: image/jpeg");
  imagejpeg($img_out, "", $tags["q"]);
  exit;
  case "png":
  header("Content-type: image/png");
  imagepng($img_out);
  exit;
  default:
  notfound();
 }
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function get_dbName ($theKey, $theAttribute) {
 global $mysql_dbName;
 $theAttribute = str_ireplace(' ', '', $theAttribute);
	$text = '';
	if ($theKey != '') {
		$query = 'SELECT  * FROM tNAME WHERE tNAME.id="'.$theKey.'"';
		$result = mysql_query($query, $mysql_dbName) or die ("Error in query: $query.  ".mysql_error());
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			switch ($theAttribute) {
				case 'name':
					$text = $row['name'];
					break;
				case 'type':
					$text = $row['type'];
					break;
				case 'subtype':
					$text = $row['subtype'];
					break;
				default:
					$text = '???';
					break;
			};
		};
	};
return strval(ucfirst(trim($text)));
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function get_dbIconclass ($theKey, $theAttribute) {
 global $mysql_dbIconclass;
 $theAttribute = str_ireplace(' ', '', $theAttribute);
	$text = '';
	if ($theKey != '') {
		$query = 'SELECT  * FROM tNAME WHERE tNAME.id="'.$theKey.'"';
$result = mysql_query($query, $mysql_dbIconclass) or die ("Error in query: $query.  ".mysql_error());
if (mysql_num_rows($result) == 1) {
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	switch ($theAttribute) {
		case 'name':
			$text = $row['name'];
			break;
		case 'iconclass':
			$text = $row['iconclass'];
			break;
		default:
			$text = '???';
			break;
	};
};
	};
return strval(ucfirst(trim($text)));
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


/* NO ?> IN FILE .INC */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// * UNUSED BUT USEFUL...
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function ajax_loadFullTree ($upToLevel=0, $collection_id='', $media_id='', $package_id='', $part_id='') {
	$resultText = '';
	// BEGIN
	$basePath = DCTL_PROJECT_PATH;
	$collectionPath = $basePath.$collection_id;
	if (($collection_id == '') || ($upToLevel==1)) {
	 // ALL COLLECTIONS
		$resultText .= '<ul>';
		getCollectionList($basePath, &$collectionList);
		foreach ($collectionList['path'] as $key=>$fPath) {
			getCollectionRecord($fPath, &$collectionRecord);
			$selected = $collection_id == $collectionRecord['collection_id'];
			$resultText .= '<li'.($selected ? ' class="open"' : '').'>';
			$resultText .= '<span class="text"><span class="collection '.($selected?' selected':'').'">'.$collectionRecord['collection_full'].'</span></span>';
			if ($selected) {
				$resultText .= ajax_loadTree(2, $collection_id, $media_id, $package_id, $part_id, $what);
			} else {
				$resultText .= '<ul'.($selected ? '' : ' class="ajax"').'>';
				$resultText .=			'<li>{url:indexAjax.php?action=ajax_loadFullTree&amp;collection_id='.$collectionRecord['collection_id'].'}</li>';
				$resultText .= '</ul>';
			};
			$resultText .= '</li>';
		};
		$resultText .= '<li class="add"><span class="text">aggiungi Collection...</span></li>';
		$resultText .= '</ul>';
	} else {
	 // ONE COLLECTION
		if ((($package_id == '') && ($media_id == '')) || ($upToLevel==2)) {
			$resultText .= '<ul>';
			$resultText .= '<li class="edit"><span class="text">edit Collection...</span></li>';
			$resultText .= '<li class="publish"><span class="text">Pubblicazione Web</span>';
			$resultText .= '<a href="#" title="Pubblica" class="publish"><span class="hidden">Pubblica</span></a>';
			$resultText .= '<a href="#" title="Ritira" class="unpublish"><span class="hidden">Ritira</span></a>';
			$resultText .= '</li>';
			// LOAD PACKAGES
			$resultText .= '<li'.($package_id!='' ? ' class="open"' : '').'>';
			$resultText .= '<span class="text">Gestione Package</span>';
			if ($package_id!='') {
			 $resultText .=ajax_loadTree(3, $collection_id, '', $package_id, $part_id, $what);
			} else {
				$resultText .= '<ul'.($package_id=='' ? ' class="ajax"' : '').'>';
			 $resultText .= '<li>{url:indexAjax.php?action=ajax_loadFullTree&amp;collection_id='.$collection_id.'&amp;package_id=*}</li>';
			 $resultText .= '</ul>';
			};
			$resultText .= '</li>';
			// LOAD MEDIA
			$resultText .= '<li'.($media_id!='' ? ' class="open"' : '').'>';
			$resultText .= '<span class="text">Gestione Media</span>';
			if ($media_id!='') {
			 $resultText .= ajax_loadTree(3, $collection_id, $media_id, '', $part_id, $what);
			} else {
	 		$resultText .= '<ul'.($media_id=='' ? ' class="ajax"' : '').'>';
			 $resultText .= '<li>{url:indexAjax.php?action=ajax_loadFullTree&amp;collection_id='.$collection_id.'&amp;media_id=*}</li>';
			 $resultText .= '</ul>';
			};
			$resultText .= '</li>';
			$resultText .= '</ul>';
		} else {
			if ($package_id != '') {
	 		if (($package_id == '*') || ($upToLevel==3)) {
	 			// ALL PACKAGES
					$basePath = $collectionPath;
					$packagePath = $basePath.$package_id;
					$resultText .= '<ul>';
					getPackageList($basePath, &$packageList);
					foreach ($packageList['path'] as $key=>$fPath) {
						getPackageRecord($fPath, &$packageRecord);
						$selected = $package_id == $packageRecord['package_id'];
						$resultText .= '<li'.($selected ? ' class="open"' : '').'>';
						$resultText .= '<span class="text"><span class="package'.($selected?' selected':'').'">'.$packageRecord['package_full'].'</span></span>';
						if ($selected) {
							$resultText .= ajax_loadTree(4, $collection_id, '', $package_id, $part_id, $what);
						} else {
	   			$resultText .=  '<ul'.($selected ? '' : ' class="ajax"').'>';
							$resultText .= '<li>{url:indexAjax.php?action=ajax_loadFullTree&amp;collection_id='.$collection_id.'&amp;package_id='.$packageRecord['package_id'].'}</li>';
							$resultText .= '</ul>';
						};
						$resultText .= '</li>';
					};
					$resultText .= '<li class="add"><span class="text">aggiungi Package...</span></li>';
					$resultText .= '</ul>';
				} else {
					if (($part_id == '')  || ($upToLevel==4)) {
						$resultText .= '<ul>';
						$resultText .= '<li class="edit"><span class="text">edit Package...</span></li>';
						$resultText .= '<li class="publish"><span class="text">Pubblicazione Web</span>';
						$resultText .= '<a href="#" title="Pubblica" class="publish"><span class="hidden">Pubblica</span></a>';
						$resultText .= '<a href="#" title="Ritira" class="unpublish"><span class="hidden">Ritira</span></a>';
						$resultText .= '</li>';
						// LOAD PARTS
						$resultText .= '<li'.($part_id!='' ? ' class="open"' : '').'>';
						$resultText .= '<span class="text">Gestione Part</span>';
						if ($part_id!='') {
							$resultText .= ajax_loadTree(5, $collection_id, '', $package_id, $part_id, $what);
						} else {
							$resultText .= '<ul'.($part_id=='' ? ' class="ajax"' : '').'>';
							$resultText .= '<li>{url:indexAjax.php?action=ajax_loadFullTree&amp;collection_id='.$collection_id.'&amp;package_id='.$package_id.'&amp;part_id=*}</li>';
	 					$resultText .= '</ul>';
						};
						$resultText .= '</li>';
	 				$resultText .= '</ul>';
					} else {
						// ALL PARTS
						$basePath = $collectionPath.$package_id.SYS_PATH_SEPARATOR;
						$partPath = $basePath.$part_id;
						$resultText .= '<ul>';
						getPartList($basePath, &$partList);
						foreach ($partList['path'] as $key=>$fPath) {
							getPartRecord($fPath, &$partRecord);
							$selected = $part_id == $partRecord['part_id'];
							$resultText .= '<li'.($selected?' class="open"':'').'>';
							$resultText .= '<span class="text"><span class="part'.($selected?' selected':'').'">'.cleanWebString($partRecord['part_short'].' - '.$partRecord['part_work'], FIELD_STRING_LENGTH).SYS_DBL_SPACE. '</span></span>';
							$resultText .= '<a href="#" title="Scarica" class="dnload"><span class="hidden">Scarica</span></a>';
							$resultText .= '<a href="#" title="Ricarica" class="upload"><span class="hidden">Ricarica</span></a>';
							$resultText .= '</li>';
						};
						$resultText .= '<li class="add"><span class="text">aggiungi Part...</span></li>';
						$resultText .= '</ul>';
					};
			 };
			};
			if ($media_id!='') {
				// ALL MEDIA
				$basePath = $collectionPath.DCTL_MEDIA_BIG;
				$mediaPath = $basePath.$media_id;
				$resultText .= '<ul>';
				getMediaList($basePath, &$mediaList);
				foreach ($mediaList['path'] as $key=>$fPath) {
					getMediaRecord($fPath, &$mediaRecord);
					$selected = $media_id == $mediaRecord['media_id'];
					$resultText .= '<li'.($selected?' class="open"':'').'>';
					$resultText .= '<span class="text"><span class="media'.($selected?' selected':'').'">'.cleanWebString($mediaRecord['media_short'].' - '.$mediaRecord['media_work'], FIELD_STRING_LENGTH).SYS_DBL_SPACE.'</span></span>';
					$resultText .= '<a href="#" title="Aggiorna" class="update"><span class="hidden">Aggiorna</span></a>';
					$resultText .= '<a href="#" title="Elimina" class="delete"><span class="hidden">Elimina</span></a>';
					$resultText .= '</li>';
				};
				$resultText .= '<li class="add"><span class="text">aggiungi Media...</span></li>';
				$resultText .= '</ul>';
			};
		};
	};
	return $resultText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

