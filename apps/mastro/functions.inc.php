<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

/* - - - - - - - - - - - - - - - - - */
function dctl_getMediaPath4($html) {
 return str_ireplace('img://', WEB_PUBLISH.DCTL_MEDIA_BIG, $html);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function generateVerticalString($string, $id, $doc, $invert=false) {
	$string = '     '.iconv('UTF-8', 'macintosh', $string);
	$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
	$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
	$dest = '';
	$dest .= DCTL_DBCTL_BASEPATH.'coste';
	if (!is_dir($dest)) mkdir($dest, CHMOD);
 @chmod($dest, CHMOD);
 $dest .= SYS_PATH_SEP.$doc.'-'.$id.'.gif';
	$font = 'font/Georgia.ttf';
	$fontsize = 12;
	$bolder = false;
	$fontangle = 0;
	$rotateangle = 90;
	$x_cord = 0;
	$y_cord = $fontsize*2;
	$box = @imageTTFBbox($fontsize,$fontangle,$font,$string);
	$g_iw = abs($box[4] - $box[0]) + $fontsize + $x_cord;
	$g_ih = $fontsize*2.3;
	$img_dst = imagecreatetruecolor($g_iw, $g_ih);
	$fg = imagecolorallocate($img_dst, 0, 0, 0);
	$bg = imagecolorallocate($img_dst, 127, 127, 127);
	if ($invert) {
		$bg = $fg;
		$fg = imagecolorallocate($img_dst, 255, 255, 255);
	};
	imagecolortransparent($img_dst, $bg);
	imagefilledrectangle($img_dst, 0, 0, $g_iw, $g_ih, $bg);
	if ($bolder) {
		imagettftext($img_dst, $fontsize, $fontangle, $x_cord+2, $y_cord+2, $bg, $font, $string);
		$_x = array(1, 0, 1, 0, -1, -1, 1, 0, -1);
		$_y = array(0, -1, -1, 0, 0, -1, 1, 1, 1);
		for($n=0;$n<=8;$n++) {
			imagettftext($img_dst, $fontsize, $fontangle, $x_cord+$_x[$n], $y_cord+$_y[$n], $fg, $font, $string);
		};
	} else {
//		imagettftext($img_dst, $fontsize, $fontangle, $x_cord+1, $y_cord+1, $bg, $font, $string);
		imagettftext($img_dst, $fontsize, $fontangle, $x_cord, $y_cord, $fg, $font, $string);
	};
// 	$icon_src = imagecreatefromgif(DCTL_IMAGES.'box_opened.gif');
// 	imagecopy($img_dst, $icon_src, 3, 9, 0, 0, 20, 20);
	$img_dst = imagerotate($img_dst, $rotateangle,0);
	imagegif($img_dst, $dest);
	imagedestroy($img_dst);

};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getRetrieverDB ($doc, $what, $selector) {
 $returnText = '';
	global $mysql_dbName;
	try {
		$mysql_dbName = dctl_sql_connect(DCTL_DB_NAME);
	} catch (Exception $e) {
		die('<div class="wctl_error">[DB] ' . $e . '</div>');
	};
 $name = $what;
	$nameX = my_strtoupper ($name);
	$nameSoundex = my_soundex($nameX);
	$query = "SELECT * FROM tNAME WHERE";
	$byCode = preg_match('/^\d{6}$/', $nameX);
	if ($byCode) {
		$query .= " tNAME.id = $nameX ";
	} else {
		$query .= " tNAME.nameNormalized LIKE '%$nameX%' ";
		if ($selector != '') $query .= "AND tNAME.type='$selector' ";
	};
	$query .= "ORDER BY tNAME.name";
	$result = mysql_query($query, $mysql_dbName) or die ("Error in query: $query.  ".mysql_error());
	$code = array();
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if (intval($row['collector']) != 0) {
			$code[$row['collector']] = $row['collector'];
		} else {
			$code[$row['id']] = $row['id'];
		};
	};
	if (count($code)>0) {
		$iter = -1;
		$query = "SELECT * FROM tNAME WHERE ";
			foreach($code as $collector=>$id) {
			++$iter;
				if ($iter > 0) {
					$query .=	"OR ";
				};
				$query .=	"tNAME.id='$collector' ";
			};
		$query .=	"ORDER BY tNAME.type, tNAME.name";
		$result = mysql_query($query, $mysql_dbName) or die ("Error in query: $query.  ".mysql_error());
		mysql_close($mysql_dbName);
  if (mysql_num_rows($result)) {
			$cachedKEY = ''; // load all KEYs to cache for next XSLT
			preloadKEY($doc, &$cachedKEY);
			if ($cachedKEY) {
				while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					$theKey = sprintf("%06s", $row['id']);
					if (stripos($cachedKEY, $theKey) !== false) {
						$returnText .= $row['name'];
						$returnText .= ' ['.$row['type'].']';
						$returnText .= "|".$theKey."\n";
					};
				};
			};
		};
	};
return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getRetrievePalette ($doc, $where) {
 $returnText = '';
 $doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
 $db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
 $config = DCTL_MASTRO_RETRIEVE_XSLT.'config.xml';
 if ($db_collection) {
  $config2 = str_ireplace(DCTL_MASTRO_RETRIEVE_XSLT, DCTL_MASTRO_RETRIEVE_XSLT.$db_collection.SYS_PATH_SEP, $config);
  if (is_file($config2)) $config = $config2;
		forceUTF8($config);
		$configFile = simplexml_load_file($config, 'SimpleXMLElement', DCTL_XML_LOADER);
		$returnText .= '<ul class="action">';
		foreach ($configFile->children() as $menu) {
   $tooltip = $menu['tooltip'];
   $label = $menu['label'];
			$xslt = $menu['xslt'];
			$icon = $menu['icon'];
   $link = '$().mastro(\'retrieve\',\''.$doc.'\',\'navigator\',\''.$xslt.'\',\'\',\'\',\''.$config.'\',\''. fixLabel($label).'\');';
			$returnText .= '<li><a ';
			$returnText .= 'style="background-image: url('.DCTL_IMAGES.'sidebar_icon_'.$icon.'.gif);" ';
			$returnText .= 'href="javascript:void(0);" title="'.$tooltip.'" onclick="'.$link.'">'.$label.'</a></li>';
		};
		$returnText .= '</ul>';
 };
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getRetrieveNavigator ($doc, $where, $what, $label, $allowedExt = array()) {
 $returnText = '';
 if ($doc != '') {
		$baseDoc = $doc;
  $fields = explode(DISTINCT_SEP, $label);
		$label = $fields[0];
		$desc = $fields[1];
		// IDENTIFICA IL DOC
		$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
		$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
		$xml_resource = XMLDB_PATH_BASE.$db_collection;
		global $exist;
		$packageList = array();
		$returnText .= '<h2>'.$label.'</h2>';
 	$returnText .= $desc;
		$returnText .= '<div class="sidebar_box">';
  $what = preg_replace('/(\..*)/', '', $what);
		switch ($what) {

   case 'collection_by_package':
				// *** THE COLLECTION *** //
// 			 $allowedExt[] = '_ptx';
// 				$allowedExt[] = '_img';
    getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
				$docx = array();
				foreach ($packageList as $package) {
					$docx[] = $package['ref'];
				};
				$docx = join($docx, DISTINCT_SEP);
				$collectionRecord = array();
				getCollectionRecord ($exist, $xml_resource, &$collectionRecord);
//				$label = $collectionRecord['desc'];
				$label = 'Vedi i documenti della collezione';
				$link = '$().mastro(\'retrieve\',\''.$docx.'\', \'1\', \''.$what.'\' ,\'\',\'\',\'\',\''.fixLabel($label).'\');';
				$returnText .= '<ul>';
				$returnText .= '<li class="line">';
				$returnText .= '<a href="javascript:void(0);" onclick="'.$link.'" title="'.TOOLTIP_SELECT.'">'.$label.'</a>';
				$returnText .= '</li>';
				$returnText .= '</ul>';
    break;

   case 'image_by_package':
				// *** BY EDITION *** //
// 				$allowedExt[] = '_img';
				getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
				$returnText .= '<ul>';
				foreach ($packageList as $package) {
					$label = $package['short'];
 				$returnText .= '<li class="line">';
					$link = '$().mastro(\'retrieve\',\''.$package['ref'].'\',\'1\',\''.$what.'\',\'\',\'\',\'\',\''.fixLabel($label).'\');';
					$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_SELECT.'" onclick="'.$link.'">';
					$returnText .= $label;
					$returnText .= '</a>';
					$returnText .= '</li>';
				};
				$returnText .= '</ul>';
    break;

   case 'image_by_div':
   case 'ecphrasis_by_div':
				// *** BY CANTO *** //
    switch ($what) {
					case 'image_by_div':
// 						$allowedExt[] = '_img';
					 break;
					case 'ecphrasis_by_div':
// 						$allowedExt[] = '_txt';
					 break;
    };
				getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
    $returnText .= '<ul>';
				$db_resource = '';
				$xquery = DCTL_XQUERY_BASE;
				$xquery .= ' for $doc in xmldb:document(';
				$iter = -1;
				foreach ($packageList as $package) {
					++$iter;
					if ($iter>0) $xquery .= ', ';
					$xquery .= '"'.$package['path'].'"';
				};
				$xquery .= ')';
				$xquery .= ', $block in $doc/tei:TEI/tei:text//tei:div[count(ancestor::tei:div) = 0] ';
				$xquery .= ' let $ref := $block/@xml:id ';
				$xquery .= ' let $sort := $doc/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date[1] ';
				$xquery .= ' order by $sort, $ref ';
				$xquery .= ' return ';
				$xquery .= ' if (((substring-before($ref, ".") = "") and ($ref >= "p001") and ($ref <= "p999")) and (';
    switch ($what) {
					case 'image_by_div':
						$xquery .= 'true()';
					 break;
					case 'ecphrasis_by_div':
						$xquery .= '$block//*[@ana |= "verbfig_ecphrasis"]';
					 break;
    };
				$xquery .= '))	then ';
				$xquery .= ' <tei:div doc="{util:document-name($doc)}" ref="{string($ref)}" block="{string($ref)}" at="{string($ref)}" high="{string($ref)}" desc="{string($block/tei:head[.//text() != ""])}" /> ';
				$xquery .= ' else () ';
				$result = $exist->xquery($xquery) or dump($exist->getError());
				$resultXML = (array) $result["XML"];
				$docs = array();
				foreach ($resultXML as $k1=>$node) {
					$xml_node = $node;
					$xml_node = simplexml_load_string($xml_node, 'SimpleXMLElement', DCTL_XML_LOADER);
					$namespaces = $xml_node->getDocNamespaces();
					foreach ($namespaces as $nsk=>$ns) {
						if ($nsk == '') $nsk = 'tei';
						$xml_node->registerXPathNamespace($nsk, $ns);
					};
					$doc = (string)$xml_node['doc'];
					$ref = (string)$xml_node['ref'];
					$block = (string)$xml_node['block'];
					$at = (string)$xml_node['at'];
					$high = (string)$xml_node['high'];
					$desc = (string)$xml_node['desc'];
					if (isset($docs[$ref]['doc'])) {
						$docs[$ref]['doc'] .= DISTINCT_SEP.$doc;
					} else {
						$docs[$ref]['doc'] = $doc;
					};
					$docs[$ref]['desc'] = $desc;
					$docs[$ref]['at'] = $at;
					$docs[$ref]['high'] = $high;
					$docs[$ref]['block'] = $block;
					$docs[$ref]['ref'] = $ref;
				};
				foreach ($docs as $ref=>$node) {
					$block = $node['block'];
					$label = $node['desc'];
					$link = '$().mastro(\'retrieve\',\''.$node['doc'].'\',\'1\',\''.$what.'\',\''.$block.'\',\'\',\'\',\''.fixLabel($label).'\');';
					$returnText .= '<li class="line">';
					$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_SELECT.'" onclick="'.$link.'">';
					$returnText .= $label;
					$returnText .= '</a>';
					$returnText .= '</li>';
				};
				$returnText .= '</ul>';
    break;

   case 'scene_by_package':
//    case 'scene_by_package_img':
//    case 'scene_by_package_ptx':
// 				// *** SCENE (IMG / PTX) *** //
// 				$ext = array_reverse(explode('_', $what));
// 				$allowedExt[] = '_'.$ext[0];
// 				$what = str_ireplace('_'.$ext[0], '', $what);
    getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
				$returnText .= '<ul>';
				foreach ($packageList as $package) {
					$returnText .= '<li class="line">';
					$label = $package['short'];
					$link = '$().mastro(\'retrieve\',\''.$package['ref'].'\',\'1\',\''.$what.'\',\'\',\'\',\'\',\''.fixLabel($label).'\');';
					$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_SELECT.'" onclick="'.$link.'">';
					$returnText .= $label;
					$returnText .= '</a>';
					$returnText .= '</li>';
				};
				$returnText .= '</ul>';
    break;

   case 'index_by_setting':
				global $cachedCLASS;
				preloadCLASS(&$cachedCLASS); // load all CLASS to cache for next XSLT
   case 'index_by_character':
   case 'index_by_place':
   case 'index_by_object':
    // *** CHARACTER / SETTING *** //
				getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
				$docx = array();
				foreach ($packageList as $package) {
					$docx[] = $package['ref'];
				};
				$docx = join($docx, DISTINCT_SEP);
				$xslt = $what.'.xsl';
    $returnText .= transformXMLwithXSLT(MASTRO_RETRIEVE, $docx, $where, $xslt, '', '', '', $label);
    break;

   case 'query_by_block':
				// *** BY CRITERIA *** //
				$db_resource = '';
				$returnText .= '<form id="retrieveForm" method="post" action="retrieveAjax.php">';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
				global $EXTENSION_PACKAGE;
				$allowedExt[] = '_img';
				$allowedExt[] = '_ptx';
				$allowedExt[] = '_txt';
				getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
				$itemList = array();
				foreach($allowedExt as $ext) {
					foreach ($packageList as $package) {
						$value = $package['type'];
						$label = array_search($value, $EXTENSION_PACKAGE);
						if ($value == $ext) {
							if ((isset($itemList['value'])) ? !in_array($value, $itemList['value']) : true) {
								$itemList['label'][] = $label;
								$itemList['value'][] = $value;
							};
						};
					};
				};
				$returnText .= '<fieldset id="retrieve_filter">';
				$returnText .= '<ul class="collapsible">';
				$returnText .= '<li>';
				$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_TOGGLE.'" class="collapsible_handle2 h5">Filtra</a>';
				$returnText .= '<ul class="collapsible_body">';
				$returnText .= '<li><div class="sidebar_box_info">Seleziona le tipologie e i documenti<br/></div></li>';
				$returnText .= '<li>';
				$returnText .= '<ul class="checktree">';
				$inputType = 'checkbox';
				$iter = -1;
				$label = '';
				foreach ($itemList['value'] as $idx=>$item) {
					++$iter;
					$returnText .= '<li>';
					$returnText .= '<input type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'[]" value="'.$item.'" ';
					$returnText .= '/>';
					$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;'.$itemList['label'][$idx].'</label>';
					$iter1 = -1;
					$label1 = 'set';
					$returnText .= '<ul>';
					foreach ($packageList as $package) {
						if ($package['type'] == $item) {
							++$iter1;
							$returnText .= '<li>';
							$returnText .= '<input type="'.$inputType.'" id="'.$label1.'_'.$iter1.'" name="'.$label1.'[]" value="'.$package['ref'].'" ';
							$returnText .= '/>';
							$returnText .= '<label for="'.$label1.'_'.$iter1.'">&#160;'.$package['short'].'</label>';
							$returnText .= '</li>';
						};
					};
					$returnText .= '</ul>';
					$returnText .= '</li>';
				};
				$returnText .= '</ul>';
				$returnText .= '</li>';
				$returnText .= '</ul>';
				$returnText .= '</li>';
				$returnText .= '</ul>';
				$returnText .= '</fieldset>';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
				$returnText .= '<fieldset id="retrieve_criteria">';
// 				$returnText .= '<ul>';
// 				$returnText .= '<li>';
// 				$returnText .= '<div class="h5"><a href="javascript:void(0);" title="(...)">Ricerca</a></div>';

				$labelDB = 'func';
				$searchDB = '';
				$searchDB .= 'if ($(this).attr(\'class\').substring(0,2) == \'c_\') {';
				$searchDB .= '$(\'.c_'.$labelDB.' :checkbox:checked\').not(\'[class=\'+$(this).attr(\'class\')+\']\').attr(\'checked\',\'\');';
				$searchDB .= 'var x = $(\'.c_'.$labelDB.' .\'+$(this).attr(\'class\')).find(\':radio\').attr(\'checked\',\'checked\').nextAll().find(\':checkbox\');';
				$searchDB .= 'if (x.filter(\':checked\').size() < 1) x.filter(\':not(:checked):eq(0)\').attr(\'checked\',\'checked\');';
				$searchDB .= '};';

				$searchDB .= '$(\'#terms_0\').searchDB(\''.$baseDoc.'\', \''.DCTL_DB_NAME.'\', \'#terms_\', $(\'.c_'.$labelDB.' :radio\').fieldValue(), $(\'.c_'.$labelDB.' li :radio\').nextAll().find(\':checkbox\').fieldValue());';

				$returnText .= '<ul class="tab-container">';
		// * * BANCA DATI * * * * * * * * * * * *
				$returnText .= '<li class="tab-anchor">';
				$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_SELECT.'" onclick="$(\'#criteria\').val(\'db\');$(\'#terms_2\').hide();'.$searchDB.'">Banca dati</a>';
				$returnText .= '<ul class="tab-content">';//
				$returnText .= '<li><div class="sidebar_box_info">Seleziona uno dei criteri di ricerca.<br/>Scrivi poi una parte del nome da ricercare nella banca dati.<br/>Scegli quindi una voce dall\'elenco delle corrispondenze trovate.<br/></div></li>';

				$label = 'criteria_name';
				$iter = -1;

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<div class="away">';
				$returnText .= '<input type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'" value="name" checked="checked" onclick="this.checked=\'\checked\'"/>';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;Nome proprio</label>';
				$returnText .= '</div>';

				$label = $labelDB;
				$returnText .= '<ul class="c_'.$label.'">';

				$inputType = 'radio';
				$value = '';
				++$iter;
				$returnText .= '<li class="c_'.$value.'">';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'" value="'.$value.'" onclick="'.$searchDB.'" checked="checked" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;Nome (generico)</label>';
				$returnText .= '</li>';

				$inputType = 'radio';
				$value = 'person';
				++$iter;
				$returnText .= '<li class="c_'.$value.'">';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'" value="'.$value.'" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;Personaggio</label>';

				$returnText .= '<ul>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="func_character[]" value="_img" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;rappresentato nelle illustrazioni</label>';
				$returnText .= '</li>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="func_character[]" value="_ptx" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;citato nelle allegorie</label>';
				$returnText .= '</li>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="desc_character[]" value="_txt" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;descritto nel testo</label>';
				$returnText .= '</li>';

				$returnText .= '</ul>';
				$returnText .= '</li>';

				$inputType = 'radio';
				$value = 'place';
				++$iter;
				$returnText .= '<li class="c_'.$value.'">';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'" value="'.$value.'" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;Luogo</label>';
				$returnText .= '<ul>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="func_place[]" value="_img" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;rappresentato nelle illustrazioni</label>';
				$returnText .= '</li>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="func_place[]" value="_ptx" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;citato nelle allegorie</label>';
				$returnText .= '</li>';

				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="desc_place[]" value="_txt" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;descritto nel testo</label>';
				$returnText .= '</li>';

				$returnText .= '</ul>';
				$returnText .= '</li>';

				$inputType = 'radio';
				$value = 'object';
				++$iter;
				$returnText .= '<li class="c_'.$value.'">';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'" value="'.$value.'" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;Oggetto citato nelle allegorie</label>';

				$returnText .= '<div class="away">';
				$returnText .= '<ul>';
				$inputType = 'checkbox';
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input class="c_'.$value.'" type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="func_object[]" value="_img+_ptx" onclick="'.$searchDB.'" />';
				$returnText .= '<label for="'.$label.'_'.$iter.'">&#160;rappresentato in IMG/PTX</label>';
				$returnText .= '</li>';

				$returnText .= '</ul>';
				$returnText .= '</div>';
				$returnText .= '</li>';

				$returnText .= '</ul>';
				$returnText .= '</li>';

				$returnText .= '</ul>';
				$returnText .= '</li>';
		// * * FULL TEXT * * * * * * * * * * * *
				$returnText .= '<li class="tab-anchor">';
				$returnText .= '<a href="javascript:void(0);" title="'.TOOLTIP_SELECT.'" onclick="$(\'#criteria\').val(\'text\');$(\'#terms_2\').show();			$(\'.help .collapsible_body\').hide();'.$searchDB.'">Full-Text</a>';
				$returnText .= '<ul class="tab-content">';
				$returnText .= '<li><div class="sidebar_box_info">';
				$returnText .= 'Inserisci uno o più termini da ricercare, separati da uno spazio. La ricerca non distingue lettere maiuscole e minuscole. Verranno restituiti risultati contenenti tutti i termini inseriti.';
				$returnText .= '
				 <ul class="collapsible help">
				 <li><a href="javascript:void(0);" title="'.TOOLTIP_TOGGLE.'" class="collapsible_handle2">suggerimenti per la ricerca</a>
				 <ul class="collapsible_body">
				 <li></li>
				 <li>- l\'<strong>asterisco</strong> (*) permette di trovare parole inserendole solo parzialmente (es. "<strong>art*</strong>" restituisce "arte", "artista", "artistico"...)</li>
				 <li>- il <strong>punto interrogativo</strong> (?) permette di trovare parole in cui varia un solo carattere (es. "<strong>ar?o</strong>" restituisce "arco", "arso"...)</li>
				 <li>- sia l\'asterisco che il punto interrogativo possono essere usati contemporaneamente e in maniera ripetuta, per creare complesse combinazioni di termini</li>
				 <li>- i termini ricercati entro una certa distanza devono essere nella giusta sequenza (es. "angelo ali" non darà gli stessi risultati di "ali angelo")</li>
					</ul>
					</li>
					</ul>
				';
				$returnText .= '</div></li>';
				$returnText .= '</ul>';
				$returnText .= '</li>';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
				$returnText .= '</ul>';
// 				$returnText .= '</li>';
//  			$returnText .= '</ul>';
 			$returnText .= '</fieldset>';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
				$returnText .= '<fieldset id="retrieve_search">';
// 				$returnText .= '<ul>';
// 				$returnText .= '<li>';
// 				$returnText .= '<div class="h5"><a href="javascript:void(0);" title="(...)">Trova</a></div>';
				$returnText .= '<ul>';
// 				$returnText .= '<li><div class="sidebar_box_info">(...).<br/></div></li>';
				$inputType = 'text';
				$label = 'terms';
				$iter = -1;
				++$iter;
				$returnText .= '<li>';
				$returnText .= '<input type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="'.$label.'"  value=""';
				$returnText .= ' onkeyup="'."if (\$('#criteria').val() == 'db'){ \$('#terms_1').val(''); };".'"';
// 				$returnText .= ' onblur="'."if (\$('#criteria').val() == 'db'){ if (\$('#terms_1').val().indexOf('[') <0) {\$('#terms_0').val('')}; };".'"';
// 				$returnText .= ' onfocus="'."if (\$('#criteria').val() == 'db'){ if (\$('#terms_1').val().indexOf('[') <0) {\$('#terms_0').val('')}; };".'"';
				$returnText .= ' />';
				++$iter;
				$inputType = 'hidden';
				$returnText .= '<input type="'.$inputType.'" id="'.$label.'_'.$iter.'" name="key" value="" />';
				$returnText .= '</li>';

				++$iter;
				$returnText .= '<li id="'.$label.'_'.$iter.'">';
				$returnText .= '<label for="'.$label.'_'.$iter.'">Distanza tra i termini: </label>';
				$returnText .= '<select name="dist">';
				$returnText .= '<option value="5" selected="selected">fino a 5 </option>';
				$returnText .= '<option value="10">fino a 10 </option>';
				$returnText .= '<option value="20">fino a 20 </option>';
				$returnText .= '</select>';

				$returnText .= '</li>';
				$returnText .= '</ul>';
				$returnText .= '<p class="align_center">';
				$returnText .= '<input type="submit" value="Avvia la ricerca" />';
				$returnText .= '</p>';
// 				$returnText .= '</li>';
// 				$returnText .= '</ul>';
				$returnText .= '</fieldset>';
		// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
				$returnText .= '<fieldset>';
				$returnText .= '<input type="hidden" id="criteria" name="criteria" value="" />';
				$returnText .= '<input type="hidden" id="where" name="where" value="1" />';
				$returnText .= '<input type="hidden" id="what" name="what" value="'.MASTRO_RETRIEVE.'" />';
define('DCTL_RSRC_COLLECTIONSX', DCTL_RSRC_COLLECTIONS.'-_'.DCTL_RSRC_COLLECTIONS.'.xml');
				$returnText .= '<input type="hidden" id="doc" name="doc" value="'.DCTL_RSRC_COLLECTIONSX.'" />';
    $returnText .= '<input type="hidden" id="temp" name="temp" value="'.TEMPORARY_SYSTEM.'" />';
				$returnText .= '</fieldset>';
				$returnText .= '</form>';
				break;
  };
		$returnText .= '</div>';
 };
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getDisplayPalette ($doc, $where, $what, $block, $at, $high, $label, $terms) {
 $returnText = '';
 $doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
 $db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
	$ext = str_ireplace('.xml','', $doc);
	$ext = substr($ext, -4, 4);
 $config = DCTL_MASTRO_DISPLAY_XSLT.'config'.$ext.'.xml';
 if ($db_collection) {
  $config2 = str_ireplace(DCTL_MASTRO_DISPLAY_XSLT, DCTL_MASTRO_DISPLAY_XSLT.$db_collection.SYS_PATH_SEP, $config);
  if (is_file($config2)) $config = $config2;
		forceUTF8($config);
		$configFile = simplexml_load_file($config, 'SimpleXMLElement', DCTL_XML_LOADER);
		$returnText .= '<ul class="action">';
		foreach ($configFile->children() as $menu) {
   $tooltip = $menu['tooltip'];
   $label = $menu['label'];
			$xslt = $menu['xslt'];
			$icon = $menu['icon'];
   $link = '$().mastro(\'display\',\''.$doc.'\',\'navigator\',\''.$xslt.'\',\''.$block.'\',\''.$at.'\',\''.$high.'\',\''.$label.'\',\''. $terms.'\',\''. $config.'\');';
			$returnText .= '<li><a ';
			$returnText .= 'style="background-image: url(../img/sidebar_icon_'.$icon.'.gif);" ';
			$returnText .= 'href="javascript:void(0);" title="'.$tooltip.'" onclick="'.$link.'">'.$label.'</a></li>';
		};
		$returnText .= '</ul>';
 };
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getDisplayNavigator ($doc, $where, $what, $block, $at, $high, $label) {
 $returnText = '';
 if ($doc != '') {
  $fields = explode(DISTINCT_SEP, $label);
		$label = $fields[0];
		$desc = $fields[1];
		$xslt = $what;
		switch ($xslt) {
			case 'menu_setting.xsl':
				global $cachedCLASS;
				preloadCLASS(&$cachedCLASS); // load all CLASS to cache for next XSLT
				break;
		};
		$returnText .= '<h2>'.$label.'</h2>';
		$returnText .= $desc;
		$returnText .= '<div class="sidebar_box">';
		$returnText .= transformXMLwithXSLT(MASTRO_DISPLAY, $doc, $where, $xslt, $block, $at, $high, $label);
		$returnText .= '</div>';
	};
	return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function queryXML ($mastro= '', $doc='', $xslt='', $query=array(), $label='', $mode='') {
 $returnText = '';
 if ($doc != '') {
		// IDENTIFICA IL DOC
		$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
		$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
		$xml_resource = XMLDB_PATH_BASE.$db_collection;
		global $exist;
		$packageList = array();
  $allowedExt = array();
// RETRIEVE BY FULL-TEXT
  $terms = isset($query['terms']) ? $query['terms'] : '';
		$terms = preg_match('/(.{3})+/', $terms) ? $terms : '';
		$terms = trim($terms);
		$count_words = 0;
		if (strlen(preg_replace('/'.WS.'[\?\*]/', '', $terms)) > 2) {
			$terms = preg_replace('/'.WS.''.WS.'+/', ' ', $terms);
			$terms = preg_replace('/\*+/', '*', $terms);
			$terms = preg_replace('/[^a-zA-Z0-9\?\*\']'.WS.'/', '?', $terms);
			$terms = preg_replace('/\?+/', '?', $terms);
			$terms = preg_split('/'.WS.'/', $terms);
			$count_words = count($terms);
			$terms = join(' ', $terms);
		};
		$db_resource = '';
  $doQuery = false;
  $context = '';
  $func = '';
		$highlight = false;
		$high = '';
		$kwicSize = 80;
		$kwic = false;
		if (isset($query['criteria'])) {
   switch ($query['criteria']) {

				case 'text':
	//let $filtered-q := replace($q, "[&amp;&quot;-*;-`~!@#$%^*()_+-=\[\]\{\}\|';:/.,?(:]", "")
					$doQuery = ($terms != '');
     $context = '';
     if ($count_words>1) {
// 					$context = 'tei:div[ancestor-or-self::tei:div[./@xml:id and ./ancestor-or-self::tei:text][position()=last()]]'; // trova SEMPRE una div di primo livello
 					$context = 'tei:div[count(ancestor::tei:div)=0]'; // trova SEMPRE una div di primo livello
 					$func .= '[near(., "'.$terms.'", '.$query['dist'].')]';
//       $func = '';
// 						$func .= '*[near(., "'.$terms.'", '.$query['dist'].')';
//       $func .= ' or ';
//       $func .= 'near((./tei:div or ./tei:div/following-sibling::tei:div[1]), "'.$terms.'", '.$query['dist'].')]';
      $kwicSize = $kwicSize * (1 + $query['dist']/5);
					} else {
      $func = '*[. &= "'.$terms.'"]';
					};
// TROVA TUTTO CON CONTIENE
// 					$func = '[text:match-all(.';
// 					foreach(explode(' ', $terms) as $term) $func .= ', "'.$term.'"';
// 					$func .= ')]';
					$kwic = true;
					$highlight = false;
					$context .= $func;
					$high = $terms;
				break;

				case 'db':
				 if ($query['criteria_name'] != '') {
						$key = isset($query['key']) ? (intval($query['key']) != 0) ? sprintf("%06d", $query['key']) : '' : '';
						$doQuery = ($key != '');
						$context = 'tei:name[@key = "'.$key.'"]';
      $type_name = array();
      $type_name[] = 'desc_character';
      $type_name[] = 'func_character';
      $type_name[] = 'desc_place';
      $type_name[] = 'func_place';
      $type_name[] = 'desc_object';
      $type_name[] = 'func_object';
      foreach ($type_name as $type) {
							if (isset($query[$type])) {
								$func .= $type.' ';
        if (is_array($query[$type])) {
 								$allowedExt = array_merge($allowedExt, $query[$type]);
        } else {
 								$allowedExt = array_merge($allowedExt, explode('+', $query[$type]));
 							};
							};
      };
						if ($func != '') {
							$func = trim($func);
							$func = '[@ana |= "'.$func.'"]';
						};
						$kwic = true;
						$context .= $func;
					};
				break;

			};
		};
		if ($doQuery) {
   getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
	// FILTER BY DOC
			if (isset($query['set'])) {
				$packageList2 = $packageList;
				$packageList = array();
				foreach($packageList2 as $package) {
					foreach($query['set'] as $item) {
						if (preg_match('/'.$item.'/', $package['ref'])) {
							$packageList[] = $package;
						};
					};
				};
				if (count($packageList) < 1) {
				 $packageList[] = chr(0);
				};
			};
			$xquery = DCTL_XQUERY_BASE;
			$xquery .= ' declare function tei:highlight($term as xs:string, $node as text(), $args as item()*) as element() { ';
			if ($highlight) {
				$xquery .= ' <span>{$term}</span> ';
			} else {
				$xquery .= ' <span>{$term}</span> ';
			};
			$xquery .= ' }; ';
			$xquery .= ' declare function tei:shrink($nodes as node()*, $width as xs:integer, $args as item()*) as node()* { ';
			$xquery .= ' <span>{$nodes}</span> ';
			$xquery .= ' }; ';
			$xquery .= ' let $highlight := util:function("tei:highlight", 3) ';
			$xquery .= ' let $shrink := util:function("tei:shrink", 3) ';
			$xquery .= ' for $doc in xmldb:document(';
			$iter = -1;
			foreach ($packageList as $package) {
				++$iter;
				if ($iter>0) $xquery .= ', ';
				$xquery .= '"'.$package['path'].'"';
			};
			$xquery .= '), $node in $doc/tei:TEI/tei:text//'.$context;
			$xquery .= ' ';
			$xquery .= ' let $docNAME := util:document-name($doc) ';
			$xquery .= ' let $block := tei:getBlock($node) ';
			$xquery .= ' let $blockID := $block/@xml:id ';
			$xquery .= ' let $parentID := $node/ancestor-or-self::tei:div[1]/@xml:id ';
			$xquery .= ' let $date := $doc/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date[1] ';
			$xquery .= ' let $head := ';
			$xquery .= ' if ($node/ancestor-or-self::tei:div[1]/@rend) ';
			$xquery .= ' then ';
			$xquery .= ' $node/ancestor-or-self::tei:div[1]/@rend ';
			$xquery .= ' else ';
			$xquery .= ' $block/@rend ';
			$xquery .= ' order by $blockID, $date, $docNAME, $head, $node ';
			$xquery .= ' return ';
			$xquery .= ' <tei:div ref="{$docNAME}'.DISTINCT_SEP.'{string($blockID)}'.DISTINCT_SEP.'{string($parentID)}'.DISTINCT_SEP.'{string($node/@xml:id)}'.DISTINCT_SEP.'{local-name($node)}'.DISTINCT_SEP.'{string($node/@ana)}" rend="{string($head)}">';
			if ($kwic) {
				$xquery .= '{text:kwic-display($node//text(), '.$kwicSize.', $highlight, () )}';
   } else {
				if ($highlight) {
					$xquery .= '{text:highlight-matches($node//text(), $highlight, () )}';
				} else {
					$xquery .= ' {$node} ';
				};
   };
   $xquery .= ' </tei:div>';
   $result = $exist->xquery($xquery); //  or dump($exist->getError())
			$resultXML = (array) $result["XML"];
			foreach ($resultXML as $k1=>$node) {
				$db_resource .= $node;
			};
			$db_resource = '<stub>'.$db_resource.'</stub>';
			$where = '';
			$at = '';
   $label = '';
			$returnText .= transformXMLwithXSLT(MASTRO_RETRIEVE, $db_collection, $where, $xslt, $db_resource, $at, $high, $label, $mode);
		} else {
			$returnText .= 'Definisci i criteri di ricerca...';
		};
	};
	return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function transformXMLwithXSLT ($mastro= '', $doc='', $where='', $xslt='', $block='', $curr_at='', $curr_high='', $label='', $mode='') {
 $dump = false;
 $html_resource = '';
	$db_resource = '';
	$docz = '';
 switch (true) {
  case (is_array($block)): {
   return '';
  };
  break;
  case (preg_match('/^</', substr($block, 0, 10))): {
   $db_resource = $block;
			$block = '';
			$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
			$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
		};
  break;
  case (preg_match('/\.xml$/', $doc)): {
			global $exist;
   $docz = explode(DISTINCT_SEP, $doc);
   foreach ($docz as $doc) {
				// IDENTIFICA IL DOC
				$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
				$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
				$xml_resource = XMLDB_PATH_BASE.$db_collection.DB_PATH_SEP.$doc;
				$ext = substr(str_ireplace('.xml', '', $doc), -4, 4);
				// CARICA IL DOC
				$debugTime = microtime(true);
				$xquery = DCTL_XQUERY_BASE;
				$xquery .= ' let $node := ()';
				switch (true) {

					case (stripos($xslt, 'collection_by_package') !== false):
      $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text/tei:body//tei:div[. != ""][1] ';
						$xquery .= ' let $node := <stub doc="'.$doc.'" rend="{$node/@rend}">{$node}</stub> ';
					break;

					case (stripos($xslt, 'image_by_div') !== false):
						$xquery .= ' let $node := doc("'.$xml_resource.'")//id("'.$block.'") ';
						$xquery .= ' let $node := <stub doc="'.$doc.'" rend="{$node/@rend}">{$node}</stub> ';
					break;

					case (stripos($xslt, 'image_by_package') !== false):
						$xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div[tei:figure]';
						$xquery .= '[count(ancestor-or-self::tei:div) = 1]';
						$xquery .= ' ';
					break;

					case (stripos($xslt, 'index_by_character') !== false):
      $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div//(tei:name | tei:rs)[@ana |= "func_character"] ';
					 break;

					case (stripos($xslt, 'index_by_place') !== false):
					 $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div//(tei:name | tei:rs)[@ana |= "func_place"] ';
					 break;

					case (stripos($xslt, 'index_by_object') !== false):
					 $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div//(tei:name | tei:rs)[@ana |= "func_object"] ';
					 break;

					case (stripos($xslt, 'index_by_setting') !== false):
      $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div//dctl:settings[@ana != ""] ';
					 break;

					case (stripos($xslt, 'character_by_block') !== false):
					case (stripos($xslt, 'setting_by_block') !== false):
			   switch (true) {
							case (stripos($xslt, 'character_by_block') !== false):
								$context = '(tei:name | tei:rs)[@ana |= "func_character"][(@key = "'.$block.'") | (@n = "'.$block.'")]';
								break;
			    case (stripos($xslt, 'setting_by_block') !== false):
								$context = 'dctl:settings[@ana |= "'.$block.'"]';
								break;
			   };
						// IDENTIFICA IL DOC
						$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
						$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
						$xml_resource = XMLDB_PATH_BASE.$db_collection;
						$allowedExt = array();
						getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
						$xquery .= ' let $node := for $doc in xmldb:document(';
						$iter = -1;
						foreach ($packageList as $package) {
							++$iter;
							if ($iter>0) $xquery .= ', ';
							$xquery .= '"'.$package['path'].'"';
						};
						$xquery .= '), $item in $doc/tei:TEI/tei:text//'.$context;
						$xquery .= ' ';
      $xquery .= ' let $docNAME := util:document-name($item) ';
						$xquery .= ' let $block := tei:getBlock($item) ';
						$xquery .= ' let $blockID := $block/@xml:id ';
						$xquery .= ' let $parentID := tei:getParent($item)/@xml:id '; //$item/ancestor-or-self::tei:div[last()]/@xml:id
						$xquery .= ' let $date := $doc/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date[1] ';
						$xquery .= ' let $head := ';
						$xquery .= ' if ($item/ancestor-or-self::tei:div[1]/@rend) ';
						$xquery .= ' then ';
						$xquery .= ' $item/ancestor-or-self::tei:div[1]/@rend ';
						$xquery .= ' else ';
						$xquery .= ' $block/@rend ';
						$xquery .= ' order by $blockID, $date, $docNAME, $head, $item ';
						$xquery .= ' return ';
						$xquery .= ' <tei:div ';
						$xquery .= 'ref="{$docNAME}'.DISTINCT_SEP.'{string($blockID)}'.DISTINCT_SEP.'{string($parentID)}'.DISTINCT_SEP.'{string($item/@xml:id)}'.DISTINCT_SEP.'{local-name($item)}'.DISTINCT_SEP.'{string($item/@ana)}" rend="{string($head)}"';
						$xquery .= ' >';
						$xquery .= ' {$item} ';
						$xquery .= ' </tei:div>';
						break;

					case (stripos($xslt, 'ecphrasis_by_div') !== false):
						$context = 'id("'.$block.'")//*[@ana |= "verbfig_ecphrasis"][substring-after(@xml:id, ".") = "001" or substring-after(@xml:id, ".") = ""]'; //
						$xquery .= ' let $node := for $doc in xmldb:document(';
      $xquery .= '"'.$xml_resource.'"';
      $xquery .= '), $item in $doc/tei:TEI/tei:text//'.$context;
						$xquery .= ' ';
      $xquery .= ' let $docNAME := util:document-name($item) ';
						$xquery .= ' let $block := tei:getBlock($item) ';
						$xquery .= ' let $blockID := $block/@xml:id ';
						$xquery .= ' let $parentID := tei:getParent($block)/@xml:id '; //$item/ancestor-or-self::tei:div[last()]/@xml:id
						$xquery .= ' let $date := $doc/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date[1] ';
						$xquery .= ' let $head := ';
						$xquery .= ' if ($item/@rend) ';
						$xquery .= ' then ';
						$xquery .= ' $item/@rend ';
						$xquery .= ' else ';
						$xquery .= ' $item/ancestor-or-self::tei:div[1]/@rend ';
//						$xquery .= ' order by $blockID, $date, $docNAME, $head, $item ';
						$xquery .= ' return ';
						$xquery .= ' <tei:div ';
						$xquery .= 'ref="{$docNAME}'.DISTINCT_SEP.'{string($parentID)}'.DISTINCT_SEP.'{string($blockID)}'.DISTINCT_SEP.'{string($item/@xml:id)}'.DISTINCT_SEP.'{local-name($item)}'.DISTINCT_SEP.'{string($item/@ana)}" rend="{string($head)}"';
						$xquery .= ' >';
						$xquery .= ' {$item} ';
						$xquery .= ' </tei:div>';
      break;

					case (stripos($xslt, 'scene_by_package') !== false): // sia IMG che PTX
						$context = '(dctl:item | tei:div[@type="dctlObject"][count(./descendant::dctl:item) = 0 and count(ancestor::tei:div[@type="dctlObject"]) = 0])';
						$xquery .= ' let $node := for $doc in xmldb:document(';
      $xquery .= '"'.$xml_resource.'"';
      $xquery .= '), $item in $doc/tei:TEI/tei:text//'.$context;
						$xquery .= ' ';
						$xquery .= ' let $docNAME := util:document-name($item) ';
						$xquery .= ' let $block := $item/descendant-or-self::tei:div[1] ';//tei:getBlock($item)
						$xquery .= ' let $blockID := $block/@xml:id ';
						$xquery .= ' let $parentID := $item/ancestor-or-self::tei:div[last()]/@xml:id ';
						$xquery .= ' let $date := $doc/tei:TEI/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date[1] ';
						$xquery .= ' let $head := ';
						$xquery .= ' if ($item/ancestor-or-self::tei:div[1]/@rend) ';
						$xquery .= ' then ';
						$xquery .= ' $item/ancestor-or-self::tei:div[1]/@rend ';
						$xquery .= ' else ';
						$xquery .= ' $block/@rend ';
// 						$xquery .= ' let $image := if (count($item//tei:graphic) = 0) ';
// 						$xquery .= ' then ';
// 						$xquery .= ' $item/ancestor-or-self::tei:figure[tei:graphic][1] ';
// 						$xquery .= ' else ';
// 						$xquery .= ' () ';
						//$xquery .= ' order by $blockID, $date, $docNAME, $head, $item ';
						$xquery .= ' return ';
						$xquery .= ' <tei:div ';
						$xquery .= ' ref="{$docNAME}'.DISTINCT_SEP.'{string($parentID)}'.DISTINCT_SEP.'{string($blockID)}'.DISTINCT_SEP.'{string($item/@xml:id)}'.DISTINCT_SEP.'{local-name($item)}'.DISTINCT_SEP.'{string($item/@ana)}" rend="{string($head)}"';
						$xquery .= ' >';
// 						$xquery .= ' {($image, $item)} ';
						$xquery .= ' {$item} ';
						$xquery .= ' </tei:div>';
     break;

					case (stripos($xslt, 'edition') !== false):
						$xquery .= ' let $node := doc("'.$xml_resource.'")//tei:teiHeader ';
					break;

					case (stripos($xslt, 'navigator') !== false):
							if ($block != '') {
								$xquery .= ' let $node_c := tei:getBlock(doc("'.$xml_resource.'")/id("'.$block.'")) ';
							} else {
								$xquery .= ' let $node_c := doc("'.$xml_resource.'")/tei:TEI/tei:text//tei:div[. != ""][1] ';
							};
							$xquery .= ' let $node_p := $node_c/preceding-sibling::tei:div[1] ';
							$xquery .= ' let $node_n := $node_c/following-sibling::tei:div[1] ';
							$xquery .= ' let $node := ("<stub>", $node_p, $node_c, $node_n, "</stub>") ';
						break;

					default:
						if ($block != '') {
						 $omissis = '';//', "<p class=\'omissis\' />"';
							$partial = preg_match('/(.*)((\.\d\d\d)$)/', $block, $matches);
							if ($partial) {
								$xquery .= ' let $nodes := for $node in doc("'.$xml_resource.'")//*[substring-after(@xml:id, "'.$matches[1].'.")] ';
							} else {
								$xquery .= ' let $nodes := for $node in doc("'.$xml_resource.'")/id("'.$block.'") ';
       };
							$xquery .= ' let $block := tei:getParent($node) ';
							$xquery .= ' return ';
							$xquery .= ' if (local-name($node)="div") ';
							$xquery .= ' then ';
							$xquery .= ' ($node';
							if ($partial) {
								$xquery .= $omissis;
							};
							$xquery .= ') ';
							$xquery .= ' else  ';
							$xquery .= ' ("<div type=\'", $block/@type, "\'><head><index><term>", $block/@n, "</term></index></head><p>", $node';
							$xquery .= ', "</p></div>"';
							if ($partial) {
								$xquery .= $omissis;
							};
							$xquery .= ') ';
							if ($partial) {
								$xquery .= ' let $node := ("<div>"';
								$xquery .= $omissis;
								$xquery .= ', $nodes, "</div>") ';
							} else {
								$xquery .= ' let $node := $nodes ';
							};

						} else {
       if ($where == 'navigator') {
								$context = 'doc("'.$xml_resource.'")/tei:TEI/tei:text';
								switch (true) {
// 								 case (stripos($xslt, 'menu_div') !== false):
// 										$xquery .= ' let $nodes := for $node in ';
// 								  $context .= '//tei:div[count(ancestor::tei:div) < 2]';
// 										$xquery .= $context.' ';
// 										$xquery .= ' let $block := $node ';
// 										$xquery .= ' return ';
// 								  $xquery .= ' ("<div type=\'", $block/@type, "\'><head><index><term>", $block/@n, "</term></index></head><p></p></div>") ';
// 										$xquery .= ' let $node := ("<stub>", $nodes, "</stub>") ';
// 								  break;
								 default:
										$xquery .= ' let $node := ';
										$xquery .= $context.' ';
								 	break;
								};
							} else {
								$xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI/tei:text/tei:body//tei:div[. != ""][1] ';
							};
						};
						break;
				};
				$xquery .= ' return $node ';
				$result = $exist->xquery($xquery);
				$err = $exist->getError();
				$resultXML = (array) $result["XML"];
				foreach ($resultXML as $node) {
					$db_resource .= $node;
				};
				if (preg_match('/failed/', $err)) {
				  dump($err);
				};
			};
			if ((count($docz) >1) || (stripos($xslt, 'by_') !== false)) {
			 $db_resource = '<stub>'.$db_resource.'</stub>';
   };
//  dump($xquery);
  if ($dump) dump($db_resource);
		};
		break;
  default:
			// $db_resource = $doc;
  break;
 };
 //  CARICA IN DOM
 if ($db_resource) {
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;
  if ($dom->loadXML($db_resource, DCTL_XML_LOADER)) {
			$xml_resource = $dom;
		} else {
			die('<div class="error">! cannot DOM load resource: '.$doc.'</div>');
		};
		//  CARICA XSLT
		$xsl_resource = '';
		$xslt_path = '';
		switch ($mastro) {
			case MASTRO_RETRIEVE:
				$xslt_path = DCTL_MASTRO_RETRIEVE_XSLT;
				break;
			case MASTRO_DISPLAY:
				$xslt_path = DCTL_MASTRO_DISPLAY_XSLT;
				break;
			default:
				die('<div class="error">! cannot load xslt path: '.$mastro.'</div>');
				break;
		};
		$base = str_ireplace($xslt_path, '', $xslt);
		$xslt = $xslt_path.$base;
		if ($db_collection) {
			$xslt2 = str_ireplace($base, $db_collection.SYS_PATH_SEP.$base, $xslt);
			if (is_file($xslt2)) $xslt = $xslt2;
		};
		if (is_file($xslt)) {
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			forceUTF8($xslt);
   if ($dom->load($xslt, DCTL_XML_LOADER)){
				$xsl_resource = $dom;
			} else {
				die('<div class="error">! cannot load resource: '.$xslt.'</div>');
			};
		} else {
			die('<div class="error">! cannot find resource: '.$xslt.'</div>');
		};
		// TRAFORMA DOC CON XSLT
		$proc = new XSLTProcessor();
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsl_resource);
		$proc->setParameter('', 'doc', $doc);
		$proc->setParameter('', 'block', $block);
		$proc->setParameter('', 'at', $curr_at);
		$proc->setParameter('', 'high', $curr_high);
		$proc->setParameter('', 'where', $where);
		$proc->setParameter('', 'label', $label);
		$proc->setParameter('', 'mode', $mode);
		$html_resource .= $proc->transformToXML($xml_resource);
		// stripNamespaces(&$xml_resource);
		if (DCTL_DEBUG) dump( substr(microtime(true)-$debugTime,0,5));
	} else {
//		die('<div class="error">! cannot DB load resource: '.$doc.'</div>');
	};
	if (DCTL_DEBUG) dump( substr(microtime(true)-$debugTime,0,5));
 return forceUTF8($html_resource);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function preloadID($doc, &$xml_resource) {
 $doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
 $db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
 $thePath = DCTL_PUBLISH.$db_collection.DCTL_RESERVED_INFIX.DCTL_FILE_LINKER;
 $xml_resource = loadXML($thePath);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function preloadKEY($doc, &$xml_resource) {
 $doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
 $db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
	$db_resource = '';
 $xml_resource = XMLDB_PATH_BASE.$db_collection;
	global $exist;
	$packageList = array();
	$allowedExt = array();
	getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
	$xquery = DCTL_XQUERY_BASE;
	$xquery .= ' let $doc := xmldb:document(';
	$iter = -1;
	foreach ($packageList as $package) {
		++$iter;
		if ($iter>0) $xquery .= ', ';
		$xquery .= '"'.$package['path'].'"';
	};
	$xquery .= ') ';
	$xquery .= ' for $key in distinct-values($doc/tei:TEI/tei:text//tei:name/@key) ';
	$xquery .= ' order by $key ';
	$xquery .= ' return ';
	$xquery .= ' concat($key, " ") ';
	$result = $exist->xquery($xquery) or dump($exist->getError());
	$resultXML = (array) $result["XML"];
	foreach ($resultXML as $k1=>$node) {
		$db_resource .= $node;
	};
	$db_resource = '<stub>'.$db_resource.'</stub>';
	$xml_resource = simplexml_load_string($db_resource, 'SimpleXMLElement', DCTL_XML_LOADER);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function preloadCLASS(&$xml_resource) {
 $thePath = DCTL_PUBLISH_TEXTCLASS;
 $xml_resource = loadXML($thePath);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_getDistincts ($theVar, $sep, $putCount=true) {
 $returnText = '';
 $theVar = preg_replace ('/'.WS.'+/', ' ', (string)$theVar);
 $theVar = explode ($sep.' ', $sep.' '.$theVar);
 $theVar2 = array_unique($theVar);
 if ($putCount) {
  foreach($theVar2 as $k=>$v) {
   if ($v != '') {
    $howMany = count(array_intersect($theVar, array($v)));
    if ($howMany>1) {
     $theVar2[$k] .= ' ('.$howMany.')';
    };
   };
  };
 };
 $returnText = implode ($sep.' ', $theVar2);
 $returnText = preg_replace('/'.WS.''.$sep.'/', $sep, $returnText);
 $returnText = preg_replace('/^('.$sep.''.WS.')/', '', $returnText);
 return trim($returnText);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_getValueFromClass ($value) {
 $returnText = '';
	global $cachedCLASS;
 $xpath = 'id("'.$value.'")/eg[@xml:lang="it"]/text()';
 $returnText = $cachedCLASS->xpath($xpath);
 $returnText = strip_html($returnText[0]);
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_putRefs($theContent, $sep, $doc, $where, $index=-1) {
 $returnText = '';
	if ((stripos($theContent, $sep) !== false) && (! preg_match('/^</', substr($theContent, 0, 10)))) {
  // $returnText .= $theContent.'=>';
		$theContent2 = explode($sep, $theContent);
		$doc = str_ireplace('.xml', '', $doc);
  $ext = substr($doc, -4, 4);
  $theContent3 = array();
  switch (true) {
		 case ($index > 0):
				$theContent3[] = $theContent2[--$index];
		 break;
		 default:
		  $theContent3 = $theContent2;
		 break;
		};
		$prev = '';
		foreach ($theContent3 as $theContent) {
			if ($theContent != $prev) {
				if ($returnText != '') $returnText .= ', ';
				$returnText .= $theContent;
			};
			$prev = $theContent;
		};
 } else {
  $returnText .= $theContent;
	};
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_putLink($theContent, $doc, $where, $selector='', $label='', $docBase='', $class='') {
 $justLink = ($label == '') || (preg_match('/\(_remove_\)/',$label));
 $doc_xml = $doc;
 if ($selector != '') $selector = strtolower($selector);
 $returnText = '';
 if (($docBase != '') && ($docBase != $doc)) {
		$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
		$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
		$xml_resource = XMLDB_PATH_BASE.$db_collection;
		global $exist;
		$packageList = array();
// TEXT
  $allowedExt = array();
		$allowedExt[] = '_img';
		$allowedExt[] = '_ptx';
		getPackageList($exist, $xml_resource, &$packageList, $allowedExt);
		$num = 0;
		foreach ($packageList as $idx=>$package) {

// if ($label == 'X') {
// dump($docBase);
// };
// || (($package['type'] !== $selector) && (preg_match('/'.$package['ref'].'/i', $docBase)))
		 if (($package['type'] == $selector) && ($doc != $package['ref']) && (preg_match('/'.$docBase.'/i', $package['ref']))) {
    $link = '';
				$link .= '$().mastro(\'display\',\''.$package['ref'].'\', \''.$where.'\', \'\', \''.fixLabel($theContent).'\');';
				$returnText .= '<li>';
				$returnText .= '<a class="box_head_extend_field '.$class.'" href="javascript:void(0);" onclick="'.$link.'" title="'.TOOLTIP_SELECT.'">';
				$returnText .= $package['short'];
				$returnText .= '</a>';
				$returnText .= '</li>';
			 ++$num;
			 $idx2 = $idx;
			};
		};
		if ($returnText != '') {
			if (($num == 1) && (!stripos($label, ':'))) {
    $returnText = '<ul class="box_head_extend_item">'.str_ireplace($packageList[$idx2]['short'], $label, $returnText).'</ul>';
			} else {
  		$returnText = '<ul class="collapsible box_head_extend_item"><li><a class="collapsible_handle2" title="'.TOOLTIP_TOGGLE.'">'.$label.'&#160;</a><ul class="collapsible_body">'.$returnText.'</ul></li></ul>';
			};
		};

 } else {
		global $cachedID;
		if ($cachedID) {
		 $label = preg_replace('/\(_remove_\)/','',$label);
   $theRegExp = '/^(._)?p\d\d\d/i';
			if (preg_match($theRegExp, $theContent)) {
				$fullItem = preg_replace('/^(._)/i', '', $theContent);
				$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
				$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
				$doc = str_ireplace('.xml','', $doc_exploded[1]);
				$fullItem = 'xml://'.$db_collection.DB_PATH_SEP.$doc.DB_PATH_SEP.$fullItem;
				$s1=' '.$fullItem;
				$s2=$fullItem.' ';
				$resultXML = $cachedID->xpath('//*[contains(@target, \''.$s1.'\') or contains(@target, \''.$s2.'\') or (@target = \''.$fullItem.'\')]');
				if (count($resultXML)>0) {
					global $exist;
					foreach($resultXML as $n=>$link) {
						$attrs = $link->attributes();
						if ($label =='') $label = fixLabel($attrs['n']);
						$targets = explode(' ',$link['target']);
						if (in_array($fullItem, $targets)) {
							$links = array();
							foreach($targets as $k=>$v) {
								if ($v!=''){
									if ($v != $fullItem) {
										$parsed = explode(SYS_PATH_SEP,$v);
										$lnk_coll =$parsed[2];
										$lnk_pack = isset($parsed[3]) ? $parsed[3] : '';
										$lnk_item = isset($parsed[4]) ? $parsed[4] : '';
										$ext = substr($lnk_pack, -4, 4);
										if (($selector == $ext) || ($selector == '')) {
											$links[$k]['rev'] = strrev($lnk_pack);
											$links[$k]['ext'] = $ext;
											$links[$k]['item'] = $v;
										};
									};
								};
							};
							global $EXTENSION_PACKAGE;
							$links2 = $links;
							$links = array();
							$kk = 0;
							foreach($EXTENSION_PACKAGE as $k=>$v) {
								foreach($links2 as $k1=>$v1) {
									$ext = $v1['ext'];
									if ($ext == $v) {
										++$kk;
										$item = $links2[$k1]['item'];
										$parsed = explode(SYS_PATH_SEP, $item);
										$lnk_coll =$parsed[2];
										$lnk_pack = $parsed[3];
										$lnk_item = $parsed[4];
										$lnk_itemblock = $lnk_item; // $lnk_itemblock = ($ext == '_txt') ? $lnk_item : substr($lnk_item, 0, 4);
										$doc = $lnk_coll.DCTL_RESERVED_INFIX.$lnk_pack.'.xml';
										$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
										$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
										$xml_resource = XMLDB_PATH_BASE.$db_collection.DB_PATH_SEP.$doc;
										$packageRecord = array();
										getPackageRecord($exist, $xml_resource, &$packageRecord);
										$links[$kk]['date'] = $packageRecord['date'];
										$links[$kk]['short'] = $packageRecord['short'];
										$links[$kk]['ext'] = $links2[$k1]['ext'];
										$links[$kk]['item'] = $item;
									};
								};
							};
       if (count($links) > 0) $links = php_multisort($links, array(array('key'=>'date'), array('key'=>'short')));
							$baseDoc = basename($doc);
							$prev = '';
							$closeIt = false;
							$iter = -1;
							foreach($links as $k=>$v) {
								$ext = $v['ext'];
								if ($ext == $selector) {
									++$iter;
									if ($ext != $prev) {
										$prev = $ext;
										if (!$justLink) {
											if ($closeIt) $returnText .= '</span>';
											$returnText .= '<span class="'.$class.'">';
											$returnText .= $label;
											$returnText .= '</span>';
											$returnText .= '<span class="widget_field">';
											$closeIt = true;
										};
									};
									$parsed = explode(SYS_PATH_SEP,$v['item']);
									$lnk_coll =$parsed[2];
									$lnk_pack = $parsed[3];
									$lnk_item = $parsed[4];
									$lnk_itemblock = $lnk_item; // $lnk_itemblock = ($ext == '_txt') ? $lnk_item : substr($lnk_item, 0, 4);
									$doc = $lnk_coll.DCTL_RESERVED_INFIX.$lnk_pack.'.xml';
									$doc_exploded = explode(DCTL_RESERVED_INFIX, $doc);
									$db_collection = isset($doc_exploded[0]) ? $doc_exploded[0]: '';
									$xml_resource = XMLDB_PATH_BASE.$db_collection.DB_PATH_SEP.$doc;
									$block = $lnk_item;
									$db_resource = '';
									$xquery = DCTL_XQUERY_BASE;
									$xquery .= ' let $base := doc("'.$xml_resource.'")/id("'.$block.'") ';
									$xquery .= ' return ';
									$xquery .= ' if ($base) then ';
									$xquery .= '  if ($base/@rend != "") then ';
									$xquery .= '   ($base/@rend) ';
									$xquery .= '  else ';
									$xquery .= '   <div class="error">? UNDEFINED @REND ?</div> ';
									$xquery .= ' else ';
									$xquery .= '  <div class="error">'.basename($xml_resource).'/'.$block.' : ID non trovato...</div> ';
									$result = $exist->xquery($xquery) or dump($exist->getError());
									$resultXML = (array) $result["XML"];
									foreach ($resultXML as $k1=>$node) {
										$db_resource .= $node;
									};
									$lnk_block = $lnk_item;
									$link = '$().mastro(\'display\',\''.$lnk_coll.DCTL_RESERVED_INFIX.$lnk_pack.'.xml\', \''.$where.'\', \'\', \''.$lnk_itemblock.'\', \''.$lnk_item.'\', \'\', \''.fixLabel($label).'\');';
									if (!$justLink) {
										if ($iter > 0) $returnText .= '; ';
										$returnText .= '<a href="javascript:void(0);" onclick="'.$link.'" title="'.TOOLTIP_GOTO.'">';
										$returnText .= dctl_putRefs($db_resource, DISTINCT_SEP, $xml_resource, $where);
										$returnText .= '</a>';
									} else {
										$returnText .= $link;
									};
								};
							};
							if (!$justLink) {
								if ($closeIt) $returnText .= '</span>';;
							};
						};
					};
				};
			};
		};
	};
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getCollectionList($exist, $thePath, &$collectionList, $withEmpty=false) {
 $collectionList = array();
 if ($withEmpty) {
  $collectionList['ref'][] = '';
  $collectionList['desc'][] = '';
  $collectionList['path'][] = '';
  $collectionList['id'][] = '';
  $collectionList['short'][] = '';
  $collectionList['full'][] = '';
  $collectionList['packages'][] = '';
 };
 if ($thePath != '') {
  $collectionRecord = array();
  $collections = $exist->getCollections($thePath);
  foreach ((array) $collections->collections->elements as $key=>$collection) {
   $thePath2 = $thePath.$collection;
   $collectionList[$key]['ref'] = $collection;
   getCollectionRecord ($exist, $thePath2, &$collectionRecord);
   $collectionList[$key]['path'] = $collectionRecord['path'];
   $collectionList[$key]['desc'] = $collectionRecord['desc'];
   $collectionList[$key]['id'] = $collectionRecord['id'];
   $collectionList[$key]['short'] = $collectionRecord['short'];
   $collectionList[$key]['full'] = $collectionRecord['full'];
   $collectionList[$key]['packages'] = $collectionRecord['packages'];
  };
 };
 asort($collectionList);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getCollectionRecord ($exist, $thePath, &$collectionRecord, $filter= array()) {
 $collectionRecord = array();
 $collectionRecord['ref'] = '';
 $collectionRecord['desc'] = '';
 $collectionRecord['path'] = '';
 $collectionRecord['id'] = '';
 $collectionRecord['short'] = '';
 $collectionRecord['full'] = '';
 $collectionRecord['packages'] = array();
 $collection_id = basename($thePath);
 $thePath .= DB_PATH_SEP;
	$xml_resource = $thePath.$collection_id.DCTL_RESERVED_INFIX.DCTL_RESERVED_PREFIX.$collection_id.'.xml';
 $xquery = DCTL_XQUERY_BASE;
 $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI ';
 $xquery .= ' return ';
 $xquery .= ' <node';
 $xquery .= ' id="{$node/tei:teiHeader/tei:encodingDesc/tei:projectDesc/tei:p[@n=\'id\']}"';
 $xquery .= ' short="{$node/tei:teiHeader/tei:encodingDesc/tei:projectDesc/tei:p[@n=\'short\']}"';
 $xquery .= ' ref="{$node/@xml:id}"';
 $xquery .= ' desc="{$node/@n}"';
 $xquery .= '> ';
 $xquery .= ' </node> ';
 $result = $exist->xquery($xquery) or dump($exist->getError());
 $resultXML = (array) $result["XML"];
 foreach ($resultXML as $node) {
  $xml_node = $node;
  $xml_node = simplexml_load_string($xml_node, 'SimpleXMLElement', DCTL_XML_LOADER);
  $namespaces = $xml_node->getDocNamespaces();
  foreach ($namespaces as $nsk=>$ns) {
   if ($nsk == '') $nsk = 'tei';
   $xml_node->registerXPathNamespace($nsk, $ns);
  };
  $collectionRecord['path'] = $xml_resource;
  $collectionRecord['ref'] = (string)$xml_node['ref'];
  $collectionRecord['desc'] = (string)$xml_node['desc'];
		$collectionRecord['id'] = (string)$xml_node['id'];
		$collectionRecord['short'] = (string)$xml_node['short'];
		$collectionRecord['full'] = cleanWebString($collectionRecord['id'].': '.$collectionRecord['short'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
  getPackageList($exist, $thePath, &$collectionRecord['packages'], $filter);
 };
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getPackageList($exist, $thePath, &$packageList, $filter=array(), $sortBy='date', $withEmpty=false) {
 $packageList = array();
 if ($withEmpty) {
  $packageList['ref'][] = '';
  $packageList['desc'][] = '';
  $packageList['path'][] = '';
		$packageList['type'][] = '';
		$packageList['author'][] = '';
		$packageList['title'][] = '';
		$packageList['publisher'][] = '';
		$packageList['date'][] = '';
		$packageList['id'][] = '';
		$packageList['short'][] = '';
		$packageList['collection'][] = '';
		$packageList['full'][] = '';
 };
 if ($thePath != '') {
  $packageRecord = array();
  $packages = $exist->getCollections($thePath);
  foreach ((array) $packages->resources->elements as $key=>$package) {
   $package_id = explode(DCTL_RESERVED_INFIX, $package);
   $collection_id = $package_id[0];
   $package_id = $package_id[1];
   if($package_id[0] != DCTL_RESERVED_PREFIX) {
				$ext = substr($package_id, -8,4);
				if (count(array_filter($filter))==0 || in_array($ext, $filter)) {
					$thePath2 = $thePath.DB_PATH_SEP.$package;
					$packageList[$key]['ref'] = $package;
					getPackageRecord ($exist, $thePath2, &$packageRecord);
					$packageList[$key]['path'] = $packageRecord['path'];
					$packageList[$key]['desc'] = $packageRecord['desc'];
					$packageList[$key]['type'] = $packageRecord['type'];
					$packageList[$key]['author'] = $packageRecord['author'];
					$packageList[$key]['title'] = $packageRecord['title'];
					$packageList[$key]['publisher'] = $packageRecord['publisher'];
					$packageList[$key]['date'] = $packageRecord['date'];
					$packageList[$key]['id'] = $packageRecord['id'];
					$packageList[$key]['short'] = $packageRecord['short'];
					$packageList[$key]['collection'] = $packageRecord['collection'];
					$packageList[$key]['full'] = $packageRecord['full'];
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
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function getPackageRecord ($exist, $thePath, &$packageRecord) {
 $packageRecord = array();
 $packageRecord['ref'] = '';
 $packageRecord['desc'] = '';
 $packageRecord['path'] = '';
 $packageRecord['type'] = '';
 $packageRecord['author'] = '';
 $packageRecord['title'] = '';
 $packageRecord['publisher'] = '';
 $packageRecord['date'] = '';
	$packageRecord['id'] = '';
	$packageRecord['short'] = '';
	$packageRecord['collection'] = '';
 $packageRecord['full'] = '';
 $package_id = basename($thePath);
 $package_id = explode(DCTL_RESERVED_INFIX, $package_id);
 $collection_id = $package_id[0];
 $package_id = $package_id[1];
 $thePath = dirname($thePath).DB_PATH_SEP;
 $xml_resource = $thePath.$collection_id.DCTL_RESERVED_INFIX.$package_id;
	$ext = str_ireplace('.xml','', $package_id);
	$ext = substr($ext, -4, 4);
	$xquery = DCTL_XQUERY_BASE;
 $xquery .= ' let $node := doc("'.$xml_resource.'")/tei:TEI ';
 $xquery .= ' return ';
 $xquery .= ' <node';
 $xquery .= ' id="{$node/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n=\'id\']}"';
 $xquery .= ' short="{$node/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n=\'short\']}"';
 $xquery .= ' collection="{$node/tei:teiHeader/tei:encodingDesc/tei:projectDesc/tei:p[@n=\'short\']}"';
 $xquery .= ' ref="{$node/@xml:id}"';
 $xquery .= ' desc="{$node/@n}"';
 $xquery .= ' type="'.$ext.'"';
 $xquery .= ' author="{$node/tei:teiHeader/tei:fileDesc/tei:titleStmt/tei:author}"';
 $xquery .= ' title="{$node/tei:teiHeader/tei:fileDesc/tei:titleStmt/tei:title[@type=\'main\']}"';
 $xquery .= ' publisher="{$node/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:publisher}"';
 $xquery .= ' date="{$node/tei:teiHeader/tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,\'source\')]/tei:publicationStmt/tei:date}"';
 $xquery .= '> ';
 $xquery .= ' </node> ';
 $result = $exist->xquery($xquery) or dump($exist->getError());
 $resultXML = (array) $result["XML"];
 foreach ($resultXML as $node) {
  $xml_node = $node;
  $xml_node = simplexml_load_string($xml_node, 'SimpleXMLElement', DCTL_XML_LOADER);
  $namespaces = $xml_node->getDocNamespaces();
  foreach ($namespaces as $nsk=>$ns) {
   if ($nsk == '') $nsk = 'tei';
   $xml_node->registerXPathNamespace($nsk, $ns);
  };
  $packageRecord['path'] = $xml_resource;
  $packageRecord['ref'] = (string)$xml_node['ref'];
  $packageRecord['desc'] = (string)$xml_node['desc'];
  $packageRecord['type'] = (string)$xml_node['type'];
		$packageRecord['author'] = (string)$xml_node['author'];
		$packageRecord['title'] = (string)$xml_node['title'];
		$packageRecord['publisher'] = (string)$xml_node['publisher'];
		$packageRecord['date'] = (string)$xml_node['date'];
		$packageRecord['id'] = (string)$xml_node['id'];
		$packageRecord['short'] = (string)$xml_node['short'];
		$packageRecord['collection'] = (string)$xml_node['collection'];
		$packageRecord['full'] = cleanWebString($packageRecord['id'].': '.$packageRecord['short'], FIELD_STRING_LENGTH).SYS_DBL_SPACE;
 };
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
//        $data,  multidim array
//        $keys,  array(array(key=>col1, sort=>desc), array(key=>col2, type=>numeric))
// $res=php_multisort($_DATA['table1'], array(array('key'=>'name'),array('key'=>'age','sort'=>'desc')))

function php_multisort($data,$keys){
$sort = '';
// List As Columns
foreach ($data as $key => $row) {
	foreach ($keys as $k){
		$cols[$k['key']][$key] = $row[$k['key']];
	};
};
// List original keys
$idkeys=array_keys($data);
// Sort Expression
$i=0;
foreach ($keys as $k){
if($i>0){
$sort.=',';
};
$sort.='$cols["'.$k['key'].'"]';
if(isset($k['sort'])){
$sort.=',SORT_'.strtoupper($k['sort']);
};
if(isset($k['type'])){
$sort.=',SORT_'.strtoupper($k['type']);
};
$i++;
};
$sort.=',$idkeys';
// Sort Funct
$sort='array_multisort('.$sort.');';
eval($sort);
// Rebuild Full Array
foreach($idkeys as $idkey){
$result[$idkey]=$data[$idkey];
};
return $result;
};
/* - - - - - - - - - - - - - - - - - */

/* NO ?> IN FILE .INC */
