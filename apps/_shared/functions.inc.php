<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

/* - - - - - - - - - - - - - - - - - */
function xml_character_encode($string, $trans='') {
	$trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
	foreach ($trans as $k=>$v)
		$trans[$k]= "&#".ord($k).";";
	return strtr($string, $trans);
};
/* - - - - - - - - - - - - - - - - - */
function fixLabel($label) {
 return trim(addslashes(preg_replace('/'.WS.'+/', ' ',$label)));
};
/* - - - - - - - - - - - - - - - - - */
function loadXML($thePath) {
 if (is_file($thePath)) {
		forceUTF8($thePath);
  $simplexml = simplexml_load_file($thePath, 'SimpleXMLElement', DCTL_XML_LOADER);
  $namespaces = $simplexml->getDocNamespaces();
  foreach ($namespaces as $nsk=>$ns) {
   if ($nsk == '') $nsk = 'tei';
   $simplexml->registerXPathNamespace($nsk, $ns);
  };
  $xml_resource = simplexml_load_string($simplexml->asXML()); // NO => , 'SimpleXMLElement', DCTL_XML_LOADER
  //$xml_resource = str_ireplace('xml:id','id',$xml_resource);
 } else {
  die('<div class="error">! cannot SIMPLEXML load resource: '.$thePath.'</div>');
 };
 return $xml_resource;
};
/* - - - - - - - - - - - - - - - - - */
function checkUTF8Encoding ( $string, $string_encoding ) {
	$fs = $string_encoding == 'UTF-8' ? 'UTF-32' : $string_encoding;
	$ts = $string_encoding == 'UTF-32' ? 'UTF-8' : $string_encoding;
	return $string === mb_convert_encoding ( mb_convert_encoding ( $string, $fs, $ts ), $ts, $fs );
};
/* - - - - - - - - - - - - - - - - - */
function encodeToUTF8($string) {
 return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
};
/* - - - - - - - - - - - - - - - - - */
function forceUTF8 ($fullsrc, $fPath="", $dumpIt=true) {
	if (is_file($fullsrc)) {
	 $fPath = $fullsrc;
	 $content = file_get_contents($fullsrc);
	 $src = dirname($fullsrc).SYS_PATH_SEP.basename($fullsrc);
	} else {
		$content = $fullsrc;
	 $src = substr($fullsrc, 0, 30);
	};
	$encoding = mb_detect_encoding($content, 'UTF-8, ISO-8859-1, ASCII, UTF-7', true);
 $contentUTF8 = $content;
 // TEST #1
 switch ($encoding) {
		case 'UTF-8':
		 break;
		default:
			$contentUTF8 = iconv($encoding, "UTF-8", $content);
// 			if (is_file($fullsrc)) {
   if ($dumpIt) dump('#1) UTF-8 warning in "'.$src.'" '.($fPath?('('.basename($fPath).')'):'').': seems to be '.$encoding.'... please check and fix!');
// 				$chown = fileowner($fullsrc);
// 				$chgrp = filegroup($fullsrc);
// 				$@chmod = fileperms($fullsrc);
// 				file_put_contents($fullsrc, $content);
// 				chown($fullsrc, $chown);
// 				chgrp($fullsrc, $chgrp);
// 				@chmod($fullsrc, $CHMOD);
// 			};
			break;
	};
// TEST #2
// 	if (! checkUTF8Encoding($content, 'UTF-8')) {
// 		dump('#2) UTF-8 warning in "'.$src.'" '.($fPath?('('.basename($fPath).')'):'').': seems to be '.$encoding.'... please check and fix!');
//  }

	return $contentUTF8;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function checkEncoding ( $string, $string_encoding ) {
    $fs = $string_encoding == 'UTF-8' ? 'UTF-32' : $string_encoding;
    $ts = $string_encoding == 'UTF-32' ? 'UTF-8' : $string_encoding;
    return $string === mb_convert_encoding ( mb_convert_encoding ( $string, $fs, $ts ), $ts, $fs );
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function charset_decode_utf_8 ($string) {
      /* Only do the slow convert if there are 8-bit characters */
    /* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
    if (! ereg("[\200-\237]", $string) and ! ereg("[\241-\377]", $string))
        return $string;

    // decode three byte unicode characters
    $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
    $string);

    // decode two byte unicode characters
    $string = preg_replace("/([\300-\337])([\200-\277])/e",
    "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
    $string);

    return $string;
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function uc_first($string) {
// if (preg_match('/abitator/', $string)) dump($string);
 return strval(ucfirst($string));
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function untag($string, $tag) {
	$tmpval = array();
	$preg = "|<$tag( .*?)?>(.*?)</$tag>|s";
	preg_match_all($preg, $string, $tags);
	foreach ($tags[2] as $tmpcont){
		$tmpval[] = $tmpcont;
	};
	return $tmpval;
};
/* - - - - - - - - - - - - - - - - - */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// * ARCHIVES *
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

/* - - - - - - - - - - - - - - - - - */
function stripNamespaces (&$theText) {
	$theText = str_ireplace('xmlns:'.XMLDB_TEI_NS, '', $theText);
	$theText = str_ireplace(XMLDB_TEI_NS, '', $theText);
	$theText = str_ireplace(XMLDB_TEI_NS2, '', $theText);
	$theText = str_ireplace('xmlns:'.XMLDB_DCTL_NS, '', $theText);
	$theText = str_ireplace(XMLDB_DCTL_NS, '', $theText);
	$theText = str_ireplace(XMLDB_DCTL_NS2, '', $theText);
	$theText = str_ireplace('xmlns:'.XMLDB_DYN_NS, '', $theText);
	$theText = str_ireplace(XMLDB_DYN_NS, '', $theText);
	$theText = str_ireplace('xmlns:'.XMLDB_EXSLT_NS, '', $theText);
	$theText = str_ireplace(XMLDB_EXSLT_NS, '', $theText);
	$theText = str_ireplace('xmlns:'.XMLDB_PHP_NS, '', $theText);
	$theText = str_ireplace(XMLDB_PHP_NS, '', $theText);
	$theText = str_ireplace('xmlns:'.XMLDB_STR_NS, '', $theText);
	$theText = str_ireplace(XMLDB_STR_NS, '', $theText);
};
/* - - - - - - - - - - - - - - - - - */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function dctl_xmldb_disconnect ($exist = FALSE, $forceClose = false) {
 if ($exist) {
  $exist->disconnect($forceClose);
 };
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function dctl_xmldb_connect ($mode = 'query', $persistent = false) {
//
$persistent = false; // force to check if eXist works fine without pid
//
$exist = FALSE;
require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).'..'.SYS_PATH_SEP.'_shared'.SYS_PATH_SEP.'exist-api.inc.php');
 try {
		if ($mode == 'admin') {
			$wsdl_url=XMLDB_HOST.':'.XMLDB_PORT.'/exist/services/Admin?wsdl';
			$u = DCTL_XMLDB_USER_ADMIN;
			$p = DCTL_XMLDB_PSWD_ADMIN;
		} else {
			$wsdl_url=XMLDB_HOST.':'.XMLDB_PORT.'/exist/services/Query?wsdl';
			$u = DCTL_XMLDB_USER;
			$p = DCTL_XMLDB_PSWD;
		};
  $exist = false;
		if (url_exists($wsdl_url)) {
/* 			if ($mode == 'admin') { */
/* 				$exist = new existAdmin(DCTL_XMLDB_USER, DCTL_XMLDB_PSWD, $wsdl_url); */
/* 			} else { */
 		 $exist = new exist($u, $p, $wsdl_url, $persistent);
/* 			}; */
			$exist->setDebug(false);
			$exist->connect($mode);
		} else {
			echo('<span class="error">Can\'t find URL {'.$wsdl_url.'}</span><br />');
		};
 } catch (Exception $e) {
  echo('<span class="error">Can\'t connect to xmldb engine... {'.$e.'}</span><br />');
 };
 return $exist;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function url_exists($url) {
if($url == NULL) return false;
         if (function_exists('curl_init')) {
										$ch = curl_init($url);
										curl_setopt($ch, CURLOPT_TIMEOUT, 5);
										curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										$data = curl_exec($ch);
										$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
										curl_close($ch);
         } else {
          $httpcode = 200 * (file_get_contents($url,null,null,0,10) != false);
         };
         if($httpcode>=200 && $httpcode<300){
             return true;
         } else {
             return false;
         }
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function dctl_sql_connect ($db_name, $new=true) {
	$URL = ''; // default
	switch($db_name) {
		case DCTL_DB_NAME:
		 $URL = MYSQL_HOST_PN.':'.MYSQL_PORT_PN;
			break;
		case DCTL_DB_ICONCLASS:
		 $URL = MYSQL_HOST_IC.':'.MYSQL_PORT_IC;
			break;
	};
	try {
  $connection = mysql_connect($URL, DCTL_SQL_USER, DCTL_SQL_PSWD, $new);
 } catch (Exception $e) {
  die('<span class="error">Can\'t connect to sql engine... {'.$e.'}</span><br />');
 };
 try {
  mysql_select_db($db_name, $connection);
 } catch (Exception $e) {
  die('<span class="error">Can\'t connect to database "'.$db_name.'"... {'.$e.'}</span><br />');
 };
 return $connection;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
// * GENERAL PURPOSES *
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function doBackup ($fPath) {
 if (is_file($fPath)) {
  $dPath = dirname($fPath).SYS_PATH_SEP.DCTL_RESERVED_PREFIX.'bkp';
  if(!is_dir($dPath)) mkdir($dPath, CHMOD);
  @chmod($dPath, CHMOD);
  $fName = basename($fPath);
  $fExt = substr($fName, -4, 4);
  $fBase = substr($fName, 0, strlen($fName)-4);
  $fPath2 = $dPath.SYS_PATH_SEP.$fBase.'.'.date('ymdhms').'.'.DCTL_USER_ID.$fExt;
		copy($fPath, $fPath2);
		@chmod($fPath2, CHMOD);
 };
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function checkIfLocked ($fPath, &$who, &$content) {
	$isLocked = FALSE;
	$who = '';
	$content = array();
	$dPath = dirname($fPath);
	$handle = opendir($dPath);
	while ($entry = readdir($handle)) {
		if (substr($entry, 0, 1) != '.') {
			$content[] = $dPath.SYS_PATH_SEP.$entry;
		};
	};
	$content = array_values(preg_grep('/x-.*'.basename($fPath).'/', $content));
	$isLocked = count($content) == 1;
	if ($isLocked) {
		$who = preg_split('/-/', basename($content[0]));
		$who = $who[1];
	};
	return $isLocked;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function makePreview ($theLocation, $g_srcfile, $overwrite=TRUE, $forcedsize=200, $imgcomp=0) {
	 if (file_exists($g_srcfile)) {
			global $EXTENSION_PREVIEW;
			$g_imgcomp = 100-$imgcomp;
			$g_fw = $forcedsize;
			$g_fh = $forcedsize;
	  $g_srcpath = dirname(dirname(dirname($g_srcfile))).SYS_PATH_SEP.$theLocation;
   if(!is_dir($g_srcpath)) mkdir($g_srcpath, CHMOD);
   @chmod($g_srcpath, CHMOD);
	  $g_srcname = basename($g_srcfile);
			$g_dstfile = $g_srcpath.SYS_PATH_SEP.$g_srcname;
			if ($overwrite || (!file_exists($g_dstfile))) {
				$ext = strtolower(substr($g_srcname, -3, 3));
				if (in_array($ext, $EXTENSION_PREVIEW)) {
					$g_is=getimagesize($g_srcfile);
					if (($g_is[0]>$g_fw) || ($g_is[1]>$g_fh)) {
						if (($g_is[0]-$g_fw) >= ($g_is[1]-$g_fh)) {
							$g_iw=$g_fw;
							$g_ih=($g_fw/$g_is[0])*$g_is[1];
						} else {
							$g_ih=$g_fh;
							$g_iw=($g_ih/$g_is[1])*$g_is[0];
						};
						switch ($ext) {
							case 'jpg':
								$img_src=imagecreatefromjpeg($g_srcfile);
								break;
							case 'gif':
								$img_src=imagecreatefromgif($g_srcfile);
								break;
							case 'png':
								$img_src=imagecreatefrompng($g_srcfile);
								break;
						};
						$img_dst=imagecreatetruecolor($g_iw,$g_ih);
						imagecopyresampled($img_dst, $img_src, 0, 0, 0, 0, $g_iw, $g_ih, $g_is[0], $g_is[1]);
						imagejpeg($img_dst, $g_dstfile, $g_imgcomp);
						imagedestroy($img_dst);
					} else {
						copy($g_srcfile, $g_dstfile);
						@chmod($g_dstfile, CHMOD);
					};
				} else {
					$file_icon = DCTL_IMAGES.'file-'.$ext.'.gif';
					if (!is_file($file_icon)) $file_icon = DCTL_IMAGES.'file-unknown.gif';
					copy($file_icon, $g_dstfile.'.gif');
					@chmod($g_dstfile, CHMOD);
				};
			};
 	};
	};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function createPreview ($g_srcfile, $overwrite=TRUE, $forcedsize=200, $imgcomp=0) {
	if (is_dir($g_srcfile)) {
		$handle = opendir($g_srcfile);
		while ($entry = readdir($handle)) {
 	 if (substr($entry, 0, 1) != '.') {
 	  if (($entry != basename(DCTL_MEDIA_SML)) && ($entry != basename(DCTL_MEDIA_MED))) {
				 createPreview ($g_srcfile.SYS_PATH_SEP.$entry, $overwrite, $forcedsize, $imgcomp);
				};
	  };
	 };
	} else {
		makePreview (DCTL_MEDIA_SML, $g_srcfile, $overwrite, $forcedsize, $imgcomp);
		makePreview (DCTL_MEDIA_MED, $g_srcfile, $overwrite, $forcedsize*2.5, $imgcomp);
	};
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function dircopy ($srcdir, $dstdir, &$files=array()) {
	if(!is_dir($dstdir)) mkdir($dstdir, CHMOD);
 @chmod($dstdir, CHMOD);
	$handle = opendir($srcdir);
	while ($entry = readdir($handle)) {
	 if (substr($entry, 0, 1) != '.') {
	  $fullsrc = $srcdir.SYS_PATH_SEP.$entry;
	  $fulldst = $dstdir.SYS_PATH_SEP.$entry;
	  if (is_dir ($fullsrc)) {
				if(!is_dir($fulldst)) mkdir($fulldst, CHMOD);
    @chmod($fulldst, CHMOD);
	   dircopy ($fullsrc, $fulldst, &$files);
	  } else {
	   if (is_file($fullsrc)) {
	    copy($fullsrc, $fulldst);
					@chmod($fulldst, CHMOD);
				};
	   $files[] = $entry;
	  };
	 };
	};
	@chmod($dstdir, CHMOD);
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function require_here ($file) {
	if(is_file($file)) {
		$phpCode=implode("", file($file));
		ob_start();
		eval('?>'.$phpCode);
		$returnText = ob_get_contents();
		ob_end_clean();
	} else {
	 $returnText = '<span class="error">File not found: '.basename($file).'</span>';
	};
 return $returnText;
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function hardFlush(&$text = '') {
 if ($text != '') {
		if (strlen($text)<256) $text = str_pad($text,256);
		// make sure output buffering is off before we start it
		// this will ensure same effect whether or not ob is enabled already
		while (ob_get_level()) {
						ob_end_flush();
		};
		// start output buffering
		if (ob_get_length() === false) {
						ob_start();
		};
		echo '<li>'.$text.'</li>';
		ob_flush();
		flush();
		$text = '';
	};
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function cleanUpIndentation($theText) {
 $theText = preg_replace('/<(\w+)>('.WS.'*)<\/(\w+)>/', '<$1 />', $theText);
 $theText = preg_replace('/('.WS.'{2,})/', '$1', $theText);
 $theText = preg_replace('/'.WS.'*\/>/', ' />', $theText);
 $theText = preg_replace('/'.WS.'+/', ' ', $theText);
 $theText = preg_replace('/>'.WS.'*</', '> <', $theText);
 return $theText;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function normalize($string) {
 $n_string = $string;// $n_string = strtolower($string);
 $n_string = preg_replace('/[^a-zA-Z0-9._\-]/', '', $n_string);// remove all unwanted chars
	return trim($n_string);
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function my_strtoupper ($output)
{
 // Convert accented characters to their ASCII counterparts...
	$output = strtr(utf8_decode($output),
																	"\xA1\xAA\xBA\xBF".
																	"\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF".
																	"\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD".
																	"\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF".
																	"\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF",
																	"!ao?AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyy");
	// ...and ligatures too
	$output = makeUTF8(strtr($output, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe",
																																												"\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae",
																																												"\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th")));

	$output = strtoupper($output);

	return $output;
}
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function my_soundex ($WordString, $LengthOption=0)
{

	$SoundExLen = $LengthOption;

	if($SoundExLen > 10) {
		$SoundExLen = 10;
	}
	if($SoundExLen < 4) {
		$SoundExLen = 4;
	}

	if(!$WordString) {
		return "";
	}

# Clean and tidy
#
	// $WordString = strtoupper($WordString);
	$WordString = my_strtoupper($WordString);
	$WordStr = $WordString;
	$WordStr = preg_replace ('/[^A-Z]/si', ' ', $WordStr);   # replace non-chars with space
		$WordStr = preg_replace ('/^'.WS.'/s', '', $WordStr);        # remove leading space
			$WordStr = preg_replace ('/'.WS.'$/s', '', $WordStr);        # remove trailing space

# Some of our own improvements
				$WordStr = preg_replace ('/DG/s', '', $WordStr);          # Change DG to G
					$WordStr = preg_replace ('/GH/s', 'H', $WordStr);          # Change GH to H
						$WordStr = preg_replace ('/KN/s', 'N', $WordStr);          # Change KN to N
							$WordStr = preg_replace ('/GN/s', 'N', $WordStr);          # Change GN to N
								$WordStr = preg_replace ('/MB/s', 'M', $WordStr);          # Change MB to M
									$WordStr = preg_replace ('/PH/s', 'F', $WordStr);          # Change PH to F
          $WordStr = preg_replace ('/TCH/s', 'CH', $WordStr);        # Change TCH to CH
											$WordStr = preg_replace ('/MP([STZ])/s', 'M$1', $WordStr); # MP if follwd by S|T|Z
												$WordStr = preg_replace ('/^PS/s', 'S', $WordStr);         # Change leading PS to S
													$WordStr = preg_replace ('/^PF/s', 'F', $WordStr);         # Change leading PF to F

# Done here because the
# above improvements could
# change this first letter
#
														$FirstLetter = substr($WordStr,0,1);

# in case 1st letter is
# an H or W and we're in
# CensusOption = 1
#  (add test for 'H'/'W' v1.0c djr)
#
													if($FirstLetter == "H" | $FirstLetter == "W") {
														$TmpStr = substr($WordStr,1);
														$WordStr = "-$TmpStr";
													}

# Begin Classic SoundEx
#
													$WordStr = preg_replace ('/[AEIOUYHW]/s', '0', $WordStr);
													$WordStr = preg_replace ('/[BPFV]/s' ,'1', $WordStr);
													$WordStr = preg_replace ('/[CSGJKQXZ]/s', '2', $WordStr);
													$WordStr = preg_replace ('/[DT]/s', '3', $WordStr);
													$WordStr = preg_replace ('/L/s', '4', $WordStr);
													$WordStr = preg_replace ('/[MN]/s', '5', $WordStr);
													$WordStr = preg_replace ('/R/s', '6', $WordStr);

# Remove extra equal adjacent digits
#
													$WSLen = strlen($WordStr);
													$LastChar = '';
# v1.0c rmv: $TmpStr = "-";    # rplc skipped 1st char
													$TmpStr = '';
													for($i = 0; $i < $WSLen; $i++) { # v1.0c now org-0
														$CurChar = substr($WordStr, $i, 1);
														if($CurChar == $LastChar) {
															$TmpStr .= ' ';
														}
														else {
															$TmpStr .= $CurChar;
															$LastChar = $CurChar;
														}
													}
													$WordStr = $TmpStr;

													$WordStr = substr($WordStr,1);      # Drop first ltr code
														$WordStr = preg_replace ('/'.WS.'/s', '', $WordStr);               # remove spaces
															$WordStr = preg_replace ('/0/s', '', $WordStr);                # remove zeros
																$WordStr .= "0000000000";           # pad w/0s on rght

																	$WordStr = "$FirstLetter$WordStr";  # Add 1st ltr of wrd

																		$WordStr = substr($WordStr,0,$SoundExLen);  # size to taste

																			return $WordStr;

}
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

/* - - - - - - - - - - - - - - - - - */
function cleanWebString ($theMixed, $theLength=0, $theSpacer='') {
 $theResult = '';
 $theMixed = (array) $theMixed;
 foreach ($theMixed as $theString) {
  $theString = (string) $theString;
  if ($theString != '') {
			$theString = strip_html($theString);
			$theString = preg_replace('/'.WS.''.WS.'+/', ' ', $theString);

			$theString = preg_replace('/\"/', '&quot;', $theString);
			$theString = preg_replace('/\'/', '&apos;', $theString);
			$theString = preg_replace('/\>/', '&gt;', $theString);
			$theString = preg_replace('/\</', '&lt;', $theString);
			$theString = trim($theString);
			if ($theLength > 0) {
				$add = '';
				if ($theLength < strlen($theString)) {
					$add = '...';
				};
				$theString = substr($theString, 0, $theLength).$add;
			};
   if ($theString != '') {
	 		if ($theResult != '') {
	 		 $theResult .= '. ';
	 		};
			 $theString = makeSafeEntities($theString);
				//
				// 			 $theString = str_ireplace('<','&lt;',$theString);
				// 			 $theString = str_ireplace('>','&gt;',$theString);
				//
	 		$theResult .= $theString;
  	};
 	};
	};
 return $theResult;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function strip_html($text, $theSpacer='') {
 $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
                 '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                 '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                 '@<!['.WS.'\S]*?--[ \t\n\r]*>@'        // Strip multi-line comments including CDATA
                 );
 $text = preg_replace($search, $theSpacer, $text);
 return $text;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Convert str to UTF-8 (if not already), then convert that to HTML named entities.
// and numbered references. Compare to native htmlentities() function.
// Unlike that function, this will skip any already existing entities in the string.
// mb_convert_encoding() doesn't encode ampersands, so use makeAmpersandEntities to convert those.
// mb_convert_encoding() won't usually convert to illegal numbered entities (128-159) unless
// there's a charset discrepancy, but just in case, correct them with correctIllegalEntities.
function makeSafeEntities($str, $convertTags = 0, $encoding = "") {
	if (is_array($arrOutput = $str)) {
		foreach (array_keys($arrOutput) as $key)
		$arrOutput[$key] = makeSafeEntities($arrOutput[$key],$encoding);
		return $arrOutput;
	}
	else if (strlen($str)>0) {
		$str = makeUTF8($str,$encoding);
		$str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
		$str = makeAmpersandEntities($str);
		if ($convertTags)
			$str = makeTagEntities($str);
		$str = correctIllegalEntities($str);
		return $str;
	}
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Compare to native utf8_encode function, which will re-encode text that is already UTF-8
function makeUTF8($str,$encoding = "") {
	if (strlen($str)>0) {
		if (empty($encoding) && isUTF8($str))
			$encoding = "UTF-8";
		if (empty($encoding))
			$encoding = mb_detect_encoding($str,'UTF-8, ISO-8859-1');
		if (empty($encoding))
			$encoding = "ISO-8859-1"; //  if charset can't be detected, default to ISO-8859-1
		return $encoding == "UTF-8" ? $str : @mb_convert_encoding($str,"UTF-8",$encoding);
	};
	return '';
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Much simpler UTF-8-ness checker using a regular expression created by the W3C:
// Returns true if $string is valid UTF-8 and false otherwise.
// From http://w3.org/International/questions/qa-forms-UTF-8.html
function isUTF8($str) {
	return preg_match('%^(?:
                       [\x09\x0A\x0D\x20-\x7E]           // ASCII
                       | [\xC2-\xDF][\x80-\xBF]            // non-overlong 2-byte
                       | \xE0[\xA0-\xBF][\x80-\xBF]        // excluding overlongs
                       | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} // straight 3-byte
                       | \xED[\x80-\x9F][\x80-\xBF]        // excluding surrogates
                       | \xF0[\x90-\xBF][\x80-\xBF]{2}     // planes 1-3
                       | [\xF1-\xF3][\x80-\xBF]{3}         // planes 4-15
                       | \xF4[\x80-\x8F][\x80-\xBF]{2}     // plane 16
                       )*$%xs', $str);
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Convert ampersands to named or numbered entities.
// Use regex to skip any that might be part of existing entities.
function makeAmpersandEntities($str, $useNamedEntities = 1) {
	return preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", $useNamedEntities ? "&amp;" : "&#38;", $str);
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Convert illegal HTML numbered entities in the range 128 - 159 to legal couterparts
function correctIllegalEntities($str) {
	$chars = array(
                128 => '&#8364;',
                130 => '&#8218;',
                131 => '&#402;',
                132 => '&#8222;',
                133 => '&#8230;',
                134 => '&#8224;',
                135 => '&#8225;',
                136 => '&#710;',
                137 => '&#8240;',
                138 => '&#352;',
                139 => '&#8249;',
                140 => '&#338;',
                142 => '&#381;',
                145 => '&#8216;',
                146 => '&#8217;',
                147 => '&#8220;',
                148 => '&#8221;',
                149 => '&#8226;',
                150 => '&#8211;',
                151 => '&#8212;',
                152 => '&#732;',
                153 => '&#8482;',
                154 => '&#353;',
                155 => '&#8250;',
                156 => '&#339;',
                158 => '&#382;',
                159 => '&#376;');
	foreach (array_keys($chars) as $num)
		$str = str_replace("&#".$num.";", $chars[$num], $str);
	return $str;
}
/* - - - - - - - - - - - - - - - - - */

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
function dump ($text='** HERE **', $class='') {
 $returnText = '';
 $returnText .= '<div style="background:white;color:red;border:1px dotted red;';
 $returnText .= 'right:1.0em;z-index:10000;width:98%;overflow:auto;';
 $returnText .= 'word-wrap:break-word;height:auto;background:transparent;';
 $returnText .= 'padding:3px;font-size:9px;position:relative;top:0;left:1.0em">';
 $backtrace = debug_backtrace();
 $text = preg_replace('/\n/', '<br />', htmlentities(((string) serialize($text)), ENT_QUOTES, "utf-8")).'<br />';//htmlentities(nl2br((string) serialize($text))); // var_export($text,true) | print_r($text,true) | serialize($text)
 if ($class != '') {
  $returnText = '<span class="'.$class.'">'.$returnText.'</span>';
 };
 $returnText .= $text;
 foreach ($backtrace as $k=>$v) {
  $file = (isset($backtrace[$k]['file'])) ? $backtrace[$k]['file']: '???';
		$returnText .= basename(dirname($file)).SYS_PATH_SEP.basename($file).' :: ';
		if (isset($backtrace[$k+1])) $returnText .= $backtrace[$k+1]['function'];
  $line = (isset($backtrace[$k]['line'])) ? $backtrace[$k]['line']: '???';
		$returnText .= ' @ '.$line.'<br />';
 };
 $returnText .= '</div>';
 echo $returnText;
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

/* - - - - - - - - - - - - - - - - - */
function dctl_getPHPvar($theVar) {
 $thePHP = 'echo '.$theVar.';';
 ob_start();
 eval($thePHP);
	$returnText = ob_get_contents();
	ob_end_clean();
 return $returnText;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_getCurrentLanguage () {
	global $curr_lang;
	return strval($curr_lang);
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function dctl_getLocalized($item) {
	global $curr_lang;
	global $string;
	if (isset($string[$curr_lang][$item])) {
		return strval($string[$curr_lang][$item]);
	} else {
	 return strval('[?'.$item.'?]');
	};
};
/* - - - - - - - - - - - - - - - - - */



// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
class zipfile  {

	var $datasec = array(); // array to store compressed data
	var $ctrl_dir = array(); // central directory
	var $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00"; //end of Central directory record
	var $old_offset = 0;

	function add_dir($name)

		// adds "directory" to archive - do this before putting any files in directory!
		// $name - name of directory... like this: "path/"
		// ...then you can add files using add_file with names like "path/file.txt"
	{
		$name = str_replace("\\", "/", $name);

		$fr = "\x50\x4b\x03\x04";
		$fr .= "\x0a\x00";    // ver needed to extract
		$fr .= "\x00\x00";    // gen purpose bit flag
		$fr .= "\x00\x00";    // compression method
		$fr .= "\x00\x00\x00\x00"; // last mod time and date

		$fr .= pack("V",0); // crc32
		$fr .= pack("V",0); //compressed filesize
		$fr .= pack("V",0); //uncompressed filesize
		$fr .= pack("v", strlen($name) ); //length of pathname
		$fr .= pack("v", 0 ); //extra field length
		$fr .= $name;
		// end of "local file header" segment

		// no "file data" segment for path

		// "data descriptor" segment (optional but necessary if archive is not served as file)
		$fr .= pack("V",0); //crc32
		$fr .= pack("V",0); //compressed filesize
		$fr .= pack("V",0); //uncompressed filesize

		// add this entry to array
		$this->datasec[] = $fr;

		$new_offset = strlen(implode("", $this->datasec));

		// ext. file attributes mirrors MS-DOS directory attr byte, detailed
		// at http://support.microsoft.com/support/kb/articles/Q125/0/19.asp

		// now add to central record
		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .="\x00\x00";    // version made by
		$cdrec .="\x0a\x00";    // version needed to extract
		$cdrec .="\x00\x00";    // gen purpose bit flag
		$cdrec .="\x00\x00";    // compression method
		$cdrec .="\x00\x00\x00\x00"; // last mod time & date
		$cdrec .= pack("V",0); // crc32
		$cdrec .= pack("V",0); //compressed filesize
		$cdrec .= pack("V",0); //uncompressed filesize
		$cdrec .= pack("v", strlen($name) ); //length of filename
		$cdrec .= pack("v", 0 ); //extra field length
		$cdrec .= pack("v", 0 ); //file comment length
		$cdrec .= pack("v", 0 ); //disk number start
		$cdrec .= pack("v", 0 ); //internal file attributes
		$ext = "\x00\x00\x10\x00";
		$ext = "\xff\xff\xff\xff";
		$cdrec .= pack("V", 16 ); //external file attributes  - 'directory' bit set

		$cdrec .= pack("V", $this->old_offset ); //relative offset of local header
		$this->old_offset = $new_offset;

		$cdrec .= $name;
		// optional extra field, file comment goes here
		// save to array
		$this->ctrl_dir[] = $cdrec;


	}


	function add_file($data, $name)

		// adds "file" to archive
		// $data - file contents
		// $name - name of file in archive. Add path if your want

	{
		$name = str_replace("\\", "/", $name);
		//$name = str_replace("\\", "\\\\", $name);

		$fr = "\x50\x4b\x03\x04";
		$fr .= "\x14\x00";    // ver needed to extract
		$fr .= "\x00\x00";    // gen purpose bit flag
		$fr .= "\x08\x00";    // compression method
		$fr .= "\x00\x00\x00\x00"; // last mod time and date

		$unc_len = strlen($data);
		$crc = crc32($data);
		$zdata = gzcompress($data);
		$zdata = substr( substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
		$c_len = strlen($zdata);
		$fr .= pack("V",$crc); // crc32
		$fr .= pack("V",$c_len); //compressed filesize
		$fr .= pack("V",$unc_len); //uncompressed filesize
		$fr .= pack("v", strlen($name) ); //length of filename
		$fr .= pack("v", 0 ); //extra field length
		$fr .= $name;
		// end of "local file header" segment

		// "file data" segment
		$fr .= $zdata;

		// "data descriptor" segment (optional but necessary if archive is not served as file)
		$fr .= pack("V",$crc); //crc32
		$fr .= pack("V",$c_len); //compressed filesize
		$fr .= pack("V",$unc_len); //uncompressed filesize

		// add this entry to array
		$this->datasec[] = $fr;

		$new_offset = strlen(implode("", $this->datasec));

		// now add to central directory record
		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .="\x00\x00";    // version made by
		$cdrec .="\x14\x00";    // version needed to extract
		$cdrec .="\x00\x00";    // gen purpose bit flag
		$cdrec .="\x08\x00";    // compression method
		$cdrec .="\x00\x00\x00\x00"; // last mod time & date
		$cdrec .= pack("V",$crc); // crc32
		$cdrec .= pack("V",$c_len); //compressed filesize
		$cdrec .= pack("V",$unc_len); //uncompressed filesize
		$cdrec .= pack("v", strlen($name) ); //length of filename
		$cdrec .= pack("v", 0 ); //extra field length
		$cdrec .= pack("v", 0 ); //file comment length
		$cdrec .= pack("v", 0 ); //disk number start
		$cdrec .= pack("v", 0 ); //internal file attributes
		$cdrec .= pack("V", 32 ); //external file attributes - 'archive' bit set

		$cdrec .= pack("V", $this->old_offset ); //relative offset of local header
		$this->old_offset = $new_offset;

		$cdrec .= $name;
		// optional extra field, file comment goes here
		// save to central directory
		$this->ctrl_dir[] = $cdrec;
	}

	function file() { //
		$data = implode("", $this->datasec);
		$ctrldir = implode("", $this->ctrl_dir);

		return
			$data.
			$ctrldir.
			$this->eof_ctrl_dir.
			pack("v", sizeof($this->ctrl_dir)).     // total # of entries "on this disk"
			pack("v", sizeof($this->ctrl_dir)).     // total # of entries overall
			pack("V", strlen($ctrldir)).             // size of central dir
			pack("V", strlen($data)).                 // offset to start of central dir
			"\x00\x00";                             // .zip file comment length
	}
}
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *


// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function addToZip($item, &$zip, $zipdir="") {
	if (is_dir($item)) {
		$zipdir .= basename($item).SYS_PATH_SEP;
		$zip->add_dir($zipdir);
		$handle = opendir($item);
		while ($entry = readdir($handle)) {
			if (substr($entry, 0, 1) != '.') {
				addToZip($item.SYS_PATH_SEP.$entry, &$zip, $zipdir);
			};
		};
	} else {
		if (is_file($item)) {
		 if (strtolower(substr($item, -4, 4)) != '.zip') {
				$handle = fopen($item, "r");
				$contents = fread($handle, filesize($item));
				fclose($handle);
    $zip->add_dir($zipdir);
				$zip->add_file($contents,$zipdir.basename($item));
  	};
 	};
	};
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //
class asPrettyXMLElement extends SimpleXMLElement {
    /**
     * Outputs this element as pretty XML to increase readability.
     *
     * @param   int     $level      (optional) The number of spaces to use for
     *                              indentation, defaults to 1
     * @return  string              The XML output
     * @access  public
     */
    public function asPrettyXML($level = 1)    {
        // get an array containing each XML element
        $xml = explode("\n", preg_replace('/>'.WS.'*</', ">\n<", preg_replace('/('.WS.')'.WS.'+/','$1',$this->asXML())));
        // hold current indentation level
        $indent = 0;
        // hold the XML segments
        $pretty = array();
        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?'.WS.'*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        };
        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    // closing tag, decrease indent
                    $indent -= $level;
                };
                if ($indent<0) $indent = 0;
                $pretty[] = str_repeat(' ', $indent) . $el;
            };
        };
        return implode("\n", $pretty);
    }
};
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - //

/* - - - - - - - - - - - - - - - - - */
function get_currentpath() {
 global $area;
 global $curr_area;
 global $curr_lang;
 global $other_lang;
 global $string;

 $location = "";
 $prev_step = "";
 $steps = explode (".", $curr_area);
 foreach ($steps as $this_step) {
  if ($location != '') {
   $location .= ":";
   $prev_step .= ".";
  };
  if (stripos($area['url'][$prev_step.$this_step], '.php') !==FALSE) {
   $php_script = $area['url'][$prev_step.$this_step];
  } else {
   $php_script = 'index.php';
  };
  $prev_step .= $this_step;
		$thisURL = DCTL_REQUEST_URI;
		$thisURL = str_ireplace(basename($_SERVER['PHP_SELF']),$php_script,$thisURL);
		$thisURL = str_ireplace('area='.$curr_area,'area='.$prev_step,$thisURL);
	if (stripos($thisURL,'.php?') === false) {
   $thisURL = str_ireplace('.php', '.php?', $thisURL);
  };
	if (XMLDB_PATH_BASE_TMP) {
			if (stripos($thisURL,'temp=') === false) {
				$thisURL .= "&amp;temp=true";
			};
		};
  $otherURL = str_ireplace('lang='.$curr_lang,'lang='.$other_lang,$thisURL);
  $location .= "<a href=\"".$thisURL."\" class=\"small\" title=\"".strtolower($area[$curr_lang][$prev_step])."\">".strtolower($area[$curr_lang][$prev_step])."</a>";
 };
 if ($curr_lang != $other_lang) {
  $location .= " <a href=\"".$otherURL."\" class=\"small\" title=\"".$string[$other_lang]['language_version']."\">";
  $location .= "(".strtoupper($other_lang).")</a>";
 } else {
  $location .= " ()";
 };
 return $location;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function wctl_loader(&$text_result, $f_content, $option="", $putBR=TRUE, $DEBUG=FALSE) {
 global $string;
	$text_done = false;
	if ($f_content != '') {
		if (stripos($f_content, 'sitemap') !== false) {
			global $area;
			global $curr_area;
			global $curr_lang;
			$content = '';
			$content .= '<?xml version="1.0" encoding="UTF-8" ?>';
			$content .= '<!DOCTYPE wctl SYSTEM "'.DCTL_SITEMAP_DTDS.'">';
			$content .= '<wctl>';
			$content .= '<wctl_page>';
			$content .= '<wctl_caption>'.$area[$curr_lang]['00'].'</wctl_caption>';
			$content .= '<wctl_title>www.ctl.sns.it</wctl_title>';
			$content .= '<wctl_summary />';
			$content .= '<wctl_text>';
			foreach($area[$curr_lang] as $key=>$what) {
				if ($key != '00') {
					$length = strlen($key);
					$level = intval($length/2);
					for ($i=1; $i<=$length; $i++) $content .= "&#160;";
					$tag_b ='';
					$tag_e ='';
					// $what = ucwords($what);
					switch ($level) {
						case '1':
							$tag_b ='<br /><strong>';
							$tag_e ='</strong>';
							break;
						case '2':
							$tag_b ='<em>';
							$tag_e ='</em>';
							break;
						default:
							$level = 0;
							break;
					};
					$content .= $tag_b."<a href=\"".$_SERVER['PHP_SELF']."?lang=".$curr_lang."&amp;area=".$key."\" title=\"\">";
					$content .= '<img src="'.DCTL_IMAGES.'map_l'.$level.'.gif" alt="(open level)" />'.$what.'</a>'.$tag_e.'<br />';
				};
			};
			$content .= '</wctl_text>';
			$content .= '<wctl_note />';
			$content .= '<wctl_icon />';
			$content .= '</wctl_page>';
			$content .= '</wctl>';
			$f_handle = fopen($f_content, "wb");
			fwrite($f_handle, $content);
			fclose($f_handle);
			@chmod($f_content, CHMOD);
		};
		forceUTF8($f_content);
  if (!($xml = simplexml_load_file($f_content, 'SimpleXMLElement', DCTL_XML_LOADER))) {
			$text_result .= "<b class='wctl_error'>".$f_content."</b> : not valid or couldn't open xml file...";
			$text_done = false;
		};
		// hack to work around php 5.0's problems with default namespaces and xpath()
		$xml['xmlns'] = '';
		$htop = 16;
		switch ($option) {
			case CMS_VIEWMODE_PROJ: // PROGETTI
				$text_result .=  '<div id="wctl_icon01"><img id="wctl_preview01" src="'.WEB_WCTL_ICONS.'camillo.gif" alt="(preview icon)" height="250" /></div>';
				$text_result .=  "<div id=\"wctl_summary01\">&#160;</div>";
				$text_result .=  '<a class="go_top" href="#top" title=""><img src="'.DCTL_IMAGES.'up.gif" alt="(go top icon)" height="'.$htop.'" /></a>';
				break;
			case CMS_VIEWMODE_LIST: // ATTIVITA
				$text_result .=  '<a class="go_top" href="#top" title=""><img src="'.DCTL_IMAGES.'up.gif" alt="(go top icon)" height="'.$htop.'" /></a>';
				break;
			default: // PAGE
				break;
		};
		if ($putBR) $text_result .=  '<br />';
		$text_result .= wctl_loader_recurse ($option, $xml, '');
		if ($putBR) $text_result .=  '<br />';
  $text_done = true;
	};
	return $text_done;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function wctl_loader_recurse ($option, $child, $name_father='') {
 global $curr_area;
 $text2parse = '';

 $icon = array();
 foreach($child->children() as $name=>$children) {
		$id = $children['id'];
  switch ($option) {

			case CMS_VIEWMODE_PROJ:
				switch ($name) {
					case 'wctl_list':
						$text2parse .= "<div class=\"wctl_section01\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_head':
						$text2parse .= "<div class=\"wctl_head01\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_item':
						$text2parse .= "<div class=\"wctl_item01\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_title':
						$title = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_summary':
						$summary = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_icon':
						if ($children !='') $icon = $children;
						break;
					case 'wctl_link':
						$link = $children;
						$text2parse .= "<div class=\"wctl_title01\">";
						$text2parse .= "<a href=\"".$link."\" ";
						$text2parse .= "onmouseover=\"javascript: document.getElementById('wctl_preview01').src='".WEB_WCTL_ICONS.$icon."'; ";
						$text2parse .= "document.getElementById('wctl_summary01').firstChild.nodeValue='".makeSafeEntities($summary)."';\" ";
						$text2parse .= "onmouseout=\"javascript:document.getElementById('wctl_preview01').src='".WEB_WCTL_ICONS."camillo.gif'; ";
						$text2parse .= "document.getElementById('wctl_summary01').firstChild.nodeValue='&#160;';\" ";
						$text2parse .= "title=\"".$icon."\">";
						$text2parse .= $title;
						$text2parse .= "</a>";
						$text2parse .= "</div>";
						break;
					default:
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
				};
				$text2parse .= "\n";
				break;


			case CMS_VIEWMODE_LIST:
				switch ($name) {
					case 'wctl_list':
						$text2parse .= "<div ";
						if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_section02\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_head':
						$text2parse .= "<div class=\"wctl_head02\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_item':
						$text2parse .= "<div ";
						if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_item02\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_caption':
						$caption = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_title':
						$title = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_summary':
						$summary = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_text':
						$text = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_note':
						$note = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_icon':
						if ($children !='') $icon[] = $children;
						break;
					case 'wctl_summary':
						$summary = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_link':
						$link = node_copy ($children->asXML(), $name);
						if ($caption!='') {
							$text2parse .= "<div class=\"wctl_caption02\">";
							$text2parse .= $caption;
							$text2parse .= "</div>";
						};
						for ($ic=0; $ic<count($icon); $ic++) {
							$text2parse .= "<div class=\"wctl_icon02\">";
							$f_hires = str_ireplace("_pw","", $icon[$ic]);
							if (is_file(WEB_WCTL_ICONS.$f_hires) && ($f_hires != $icon[$ic])) {
								$text2parse .= "<a class=\"link_pop\" title=\"(open image in a new window)\"
href=\"".WEB_WCTL_ICONS.$f_hires."\">";
							} else {
								$f_hires = '';
							};
							$text2parse .= '<img src="'.WEB_WCTL_ICONS.$icon[$ic].'" alt="(preview)" />';
							if ($f_hires) {
								$text2parse .= "</a>";
							};
							$text2parse .= "</div>";
						};
						$text2parse .= "<div class=\"wctl_title02\">";
						if ($children != '' ) {
							$text2parse .= "<a class=\"link_int\" href=\"".$link."\" >";
							$text2parse .= $title;
							$text2parse .= "</a>";
						} else {
							$text2parse .= $title;
						};
						$text2parse .= "</div>";
						if ($summary!='') {
							$text2parse .= "<div class=\"wctl_summary02\">";
							$text2parse .= $summary;
							$text2parse .= "</div>";
						};
						if ($text!='') {
							$text2parse .= "<div class=\"wctl_text02\">";
							$text2parse .= $text;
							$text2parse .= "</div>";
						};
						if ($note!='') {
							$text2parse .= "<div class=\"wctl_note02\">";
							$text2parse .= $note;
							$text2parse .= "</div>";
						};
						break;
					default:
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
				};
				$text2parse .= "\n";
				break;


			case CMS_VIEWMODE_THUMB:
				switch ($name) {
					case 'wctl_list':
						$text2parse .= "<div class=\"spacer\"> </div>";
						$text2parse .= "<div ";
						if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_section03\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						$text2parse .= "<div class=\"spacer\"> </div>";
						break;
					case 'wctl_head':
						$text2parse .= "<div class=\"wctl_head03\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_item':
						$text2parse .= "<div ";
						if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_item03\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_caption':
						$caption = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_icon':
						if ($children !='') $icon[] = $children;
						break;
					case 'wctl_link':
						$link = node_copy ($children->asXML(), $name);
						for ($ic=0; $ic<count($icon); $ic++) {
							$text2parse .= "<div class=\"wctl_icon03\">";
							$f_hires = str_ireplace("_pw","", $icon[$ic]);
							if (is_file(WEB_WCTL_ICONS.$f_hires) && ($f_hires != $icon[$ic])) {
								$text2parse .= "<a class=\"link_pop\" title=\"(open image in a new window)\"
href=\"".WEB_WCTL_ICONS.$f_hires."\">";
							} else {
								$f_hires = '';
							};
							$text2parse .= '<img src="'.WEB_WCTL_ICONS.$icon[$ic].'" alt="(preview)" />';
							if ($f_hires) {
								$text2parse .= "</a>";
							};
							$text2parse .= "<br /><span class=\"wctl_caption03\">";
							$text2parse .= $caption;
							$text2parse .= "</span>";
						};
						$text2parse .= "</div>";
						break;
					default:
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
				};
				$text2parse .= "\n";
				break;


   case CMS_VIEWMODE_PAGE: // PAGE
				switch ($name) {
					case 'wctl_page':
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
					case 'wctl_caption':
						if ($children) {
							$text2parse .= "<div class=\"wctl_caption99\">";
							$text2parse .= node_copy ($children->asXML(), $name);
							$text2parse .= "</div>";
						};
						break;
					case 'wctl_title':
						if ($children) {
							$text2parse .= "<div class=\"wctl_title99\">";
							$text2parse .= node_copy ($children->asXML(), $name);
							$text2parse .= "</div>";
						};
						break;
					case 'wctl_summary':
						if ($children) {
							$text2parse .= "<div class=\"wctl_summary99\">";
							$text2parse .= node_copy ($children->asXML(), $name);
							$text2parse .= "</div>";
						};
						break;
					case 'wctl_text':
						$text = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_note':
						$note = "";
						if ($children) {
							$note = node_copy ($children->asXML(), $name);
						};
						break;
					case 'wctl_icon':
						if ($children) {
							$text2parse .= '<div class="wctl_icon99"><img src="'.WEB_WCTL_ICONS.$children.'" alt="(preview)" />';
							$text2parse .= "</div>";
						};
						if ($text != '') {
							$text2parse .= "<div class=\"wctl_text99\">";
							$text2parse .= $text;
							$text2parse .= "</div>";
						};
						if ($note != '') {
							$text2parse .= "<div class=\"wctl_note99\">";
							$text2parse .= $note;
							$text2parse .= "</div>";
						};
						break;
				};
				$text2parse .= "\n";
				break;


   case CMS_VIEWMODE_NEWS: // BACHECA
				switch ($name) {
					case 'wctl_list':
						$text2parse .= "<div ";
						if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_section04\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_head':
						$text2parse .= "<div class=\"wctl_head04\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_item':
						$text2parse .= "<div ";
						if ($id != '') {
							switch ($id)  {
								case 'drag01':
									$text2parse .= "style=\"position:relative;top:0px;left:50px;\" ";
									break;
								case 'drag02':
									$text2parse .= "style=\"position:relative;top:-130px;left:320px;\" ";
									break;
								case 'drag03':
									$text2parse .= "style=\"position:relative;top:-200px;left:20px;\" ";
									break;
								case 'drag04':
									$text2parse .= "style=\"position:relative;top:-350px;left:280px;\" ";
									break;
								case 'drag05':
									$text2parse .= "style=\"position:relative;top:-400px;left:50px;\" ";
									break;
								case 'drag06':
									$text2parse .= "style=\"position:relative;top:-550px;left:320px;\" ";
									break;
							};
							if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_item04_drag\">";
						} else {
							if ($id != '') $text2parse .= "id=\"".$id."\" ";
							$text2parse .= "class=\"wctl_item02\">";
						};
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_title':
						$title = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_text':
						$text = node_copy ($children->asXML(), $name);
						break;
					case 'wctl_link':
						$link = node_copy ($children->asXML(), $name);
						$text2parse .= "<div class=\"wctl_title04\">";
						if ($children != '' ) {
							$text2parse .= "<a class=\"link_int\" href=\"".$link."\" >";
							$text2parse .= $title;
							$text2parse .= "</a>";
						} else {
							$text2parse .= $title;
						};
						$text2parse .= "<div class=\"wctl_text04\">";
						$text2parse .= $text;
						$text2parse .= "</div>";
						$text2parse .= "</div>";
						break;
					default:
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
				};
				$text2parse .= "\n";
				break;


   default: // ...
				switch ($name) {
					case 'wctl_list':
						$text2parse .= "<div class=\"wctl_section\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_head':
						$text2parse .= "<div class=\"wctl_head\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_item':
						$text2parse .= "<div class=\"wctl_item\">";
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_title':
						$text2parse .= "<div class=\"wctl_title\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_summary':
						$text2parse .= "<div class=\"wctl_summary\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_text':
						$text2parse .= "<div class=\"wctl_text\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_note':
						$text2parse .= "<div class=\"wctl_note\">";
						$text2parse .= node_copy ($children->asXML(), $name);
						$text2parse .= "</div>";
						break;
					case 'wctl_icon':
						if ($children !='') $text2parse .= '<div class="wctl_icon"><img src="'.WEB_WCTL_ICONS.$children.'" alt="(preview)" />';
						$text2parse .= "</div>";
						break;
					case 'wctl_link':
						$text2parse .= "<a href=\"".$children."\">";
						$text2parse .= "&gt;&gt;";
						$text2parse .= "</a>";
						break;
					case 'wctl_page':
						$text2parse .= wctl_loader_recurse ($option, $children, $name);
						break;
					default:
						wctl_loader_recurse ($option, $children, $name);
						break;
				};
				$text2parse .= "\n";
				break;

		};
	};
	return parse_command($text2parse);
};
/*           */


/* - - - - - - - - - - - - - - - - - */
function array_multi_search($p_needle, $p_haystack) {
	if (! is_array ($p_haystack)) {
		return false;
	};
	$key = array_search ($p_needle, $p_haystack) ;
	if (! ($key === false)) {
		return $key;
	};
	foreach ($p_haystack as $row) {
		$key = array_multi_search ($p_needle, $row);
		if (! ($key === false)) {
			return $key;
		};
	};
	return false;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function array_multi_search_getKey($p_needle, $p_haystack, $key1=0) {
	if (! is_array ($p_haystack)) {
		return false;
	};
	$key = array_search ($p_needle, $p_haystack) ;
	if ($key !== false) {
		return $key1;
	};
	foreach ($p_haystack as $key1=>$row) {
		$key = array_multi_search_getKey ($p_needle, $row, $key1);
		if ($key !== false) {
			return $key1;
		};
	};
	return false;
};
/* - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - */
function parse_command ($text2parse) {
 global $area;
 global $area_c;
 global $area_a;
 global $area;
 global $curr_area;
 global $curr_lang;
 global $string;
 // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 do {
  $PHP_SELF = $_SERVER['PHP_SELF'];
  $has_command1 = stripos($text2parse, '$[');
if ($has_command1 !== false) {
	$has_command2 = stripos($text2parse, ']$');
if ($has_command2 !== false) {
	$token = substr($text2parse, $has_command1, $has_command2-$has_command1+2);
	$what = substr($token,2,strlen($token)-4);
	$prefix0 = explode(':', $what);
	$what = $prefix0[count($prefix0)-1];
	$prefix = $prefix0[0];
	if ($prefix != $what) {
		$prev_prefix = basename(dirname($PHP_SELF));
		$PHP_SELF = str_ireplace($prev_prefix.SYS_PATH_SEP, $prefix.SYS_PATH_SEP, $PHP_SELF);
		require_once(str_replace(SYS_PATH_SEP_DOUBLE,SYS_PATH_SEP,dirname(__FILE__).SYS_PATH_SEP).DCTL_DATA_PATH.$prefix.SYS_PATH_SEP.'config.inc.php');
		switch ($prefix) {
			case WEB_WCTL_NAME:
				$key = array_multi_search ($what, $area_c);
				break;
			case WEB_DCTL_NAME:
				$key = array_multi_search ($what, $area_a);
				break;
		};
	} else {
		$key = array_multi_search ($what, $area);
	};
	if (!(stripos($what, '.php')) === false) {
		$replace = $what;
	} else {
		$replace = $PHP_SELF;
	};
	$replace .= "?lang=".$curr_lang."&amp;area=".$key;
	if (XMLDB_PATH_BASE_TMP) {
		$replace .= "&amp;temp=true";
	};
	$text2parse = str_replace($token, $replace, $text2parse);
};
  };
 } while ($has_command1 && $has_command2);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
do {
	$has_command1 = stripos($text2parse, '${');
if (!($has_command1 === false)) {
	$has_command2 = stripos($text2parse, '}$');
if (!($has_command2 === false)) {
	$token = substr($text2parse, $has_command1, $has_command2-$has_command1+2);
	$what = substr($token,2,strlen($token)-4);
	$replace = '';
	switch ($what) {
		case 'newsletter':
			$email = '';
			if (isSet($_REQUEST['email'])) {
				$email = $_REQUEST['email'];
			};
			if (!(stripos($email, '@')) === false) {
				require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared'.SYS_PATH_SEP.'config.inc.php');
				require_once(str_replace(SYS_PATH_SEP_DOUBLE, SYS_PATH_SEP, dirname(__FILE__).SYS_PATH_SEP).'./config.inc.php');
				$connection = dctl_sql_connect(WEB_NEWSLETTER);
				if ($connection) {
					$email = addslashes($email);
					$email = trim($email);
					$query = "SELECT * FROM news_address WHERE email = '$email'";
					$result = mysql_query($query);
					if (mysql_num_rows($result) != false) {
						$replace .= "<br /><strong class=\"thankyou\">".$string[$curr_lang]['mail_yetrecorded']." (".$email.")...</strong>";
					} else {
						$query = "INSERT INTO news_address (id, email) VALUES (NULL, '$email')";
						$result = mysql_query($query);
						if (mysql_affected_rows() == 1) {
							$replace .= "<br /><strong class=\"thankyou\">".$string[$curr_lang]['mail_ok']."...</strong>";
						} else {
							$replace .= "<br /><strong class=\"thankyou\">".$string[$curr_lang]['mail_not']." (".$email.")...</strong>";
						};
					};
					mysql_close($connection);
				} else {
					$replace .= "<br /><strong class=\"thankyou\">".$string[$curr_lang]['mail_nosrv']."...</strong></em>";
				};
			} else {
				$replace .= '<form id="newsletter" action="'.$_SERVER['PHP_SELF'].'" method="'.DCTL_FORM_METHOD.'" enctype="'.DCTL_FORM_ENCTYPE.'">';
				$replace .= "<fieldset>";
				$replace .= "<input class=\"small\" type=\"text\" size=\"20\" name=\"email\" value=\"(e-mail)\" />";
				$replace .= "<label class=\"small\">";
				$replace .= "<a class=\"small\" href=\"javascript:submitform('newsletter');\"> -&gt; ".$string[$curr_lang]['mail_sign']."</a>";
				$replace .= "</label>";
				$replace .= "<input type=\"hidden\" name=\"area\" value=\"".$curr_area."\" />";
				$replace .= "<input type=\"hidden\" name=\"lang\" value=\"".$curr_lang."\" />";
				$replace .= "</fieldset></form>";
			};
			break;
	};
	$text2parse = str_replace($token, $replace, $text2parse);
};
   };
 } while ($has_command1 && $has_command2);
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
return $text2parse;
};
/* - - - - - - - - - - - - - - - - - */


/* - - - - - - - - - - - - - - - - - */
function node_copy ($value, $strip) {
 $strip_s = "<".$strip.">";
 $strip_e1 = "<".$strip."/>";
 $strip_e2 = "<".$strip." />";
 $strip_e3 = "</".$strip.">";
 $strip_e4 = "</ ".$strip.">";
 $text = $value;
 $text = str_ireplace($strip_s, "", $text);
 $text = str_ireplace($strip_e1, "", $text);
 $text = str_ireplace($strip_e2, "", $text);
 $text = str_ireplace($strip_e3, "", $text);
 $text = str_ireplace($strip_e4, "", $text);
 return $text;
};
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Convert str to UTF-8 (if not already), then convert to HTML numbered decimal entities.
// If selected, it first converts any illegal chars to safe named (and numbered) entities
// as in makeSafeEntities(). Unlike mb_convert_encoding(), mb_encode_numericentity() will
// NOT skip any already existing entities in the string, so use a regex to skip them.
function makeAllEntities($str, $useNamedEntities = 0, $encoding = "") {
	if (is_array($str)) {
		foreach ($str as $s)
		$arrOutput[] = makeAllEntities($s,$encoding);
		return $arrOutput;
	}
	else if (strlen($str)>0) {
		$str = makeUTF8($str,$encoding);
		if ($useNamedEntities)
			$str = mb_convert_encoding($str,"HTML-ENTITIES","UTF-8");
		$str = makeTagEntities($str,$useNamedEntities);
		// Fix backslashes so they don't screw up following mb_ereg_replace
		// Single quotes are fixed by makeTagEntities() above
		$str = mb_ereg_replace('\\\\',"&#92;", $str);
		mb_regex_encoding("UTF-8");
		$str = mb_ereg_replace("(?>(&(?:[a-z]{0,4}\w{2,3};|#\d{2,5};)))|(\S+?)",
                         "'\\1'.mb_encode_numericentity('\\2',array(0x0,0x2FFFF,0,0xFFFF),'UTF-8')", $str, "ime");
		$str = correctIllegalEntities($str);
		return $str;
	}
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
// Convert common characters to named or numbered entities
function makeTagEntities($str, $useNamedEntities = 1) {
	// Note that we should use &apos; for the single quote, but IE doesn't like it
	$arrReplace = $useNamedEntities ? array('&#39;','&quot;','&lt;','&gt;') : array('&#39;','&#34;','&#60;','&#62;');
	return str_replace(array("'",'"','<','>'), $arrReplace, $str);
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function fixID ($n_string) {
 $n_string = preg_replace('/[^a-zA-Z0-9_\-]/', '', $n_string);// remove all unwanted chars
 return trim($n_string);

};
/* - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - */
/**
* Perform a simple text replace
	* This should be used when the string does not contain HTML
	* (off by default)
	*/
define('STR_HIGHLIGHT_SIMPLE', 1);

/**
* Only match whole words in the string
	* (off by default)
	*/
define('STR_HIGHLIGHT_WHOLEWD', 2);

/**
* Case sensitive matching
	* (off by default)
	*/
define('STR_HIGHLIGHT_CASESENS', 4);

/**
* Overwrite links if matched
	* This should be used when the replacement string is a link
	* (off by default)
	*/
define('STR_HIGHLIGHT_STRIPLINKS', 8);

/**
* Highlight a string in text without corrupting HTML tags
	*
	* @author      Aidan Lister <aidan@php.net>
	* @version     3.1.1
	* @link        http://aidanlister.com/repos/v/function.str_highlight.php
	* @param       string          $text           Haystack - The text to search
	* @param       array|string    $needle         Needle - The string to highlight
	* @param       bool            $options        Bitwise set of options
	* @param       array           $highlight      Replacement string
	* @return      Text with needle highlighted
	*/
function str_highlight($text, $needle, $options = null, $highlight = null, $start = "<strong>", $end = "</strong>", &$refs) {
 $text = preg_replace('/'.WS.''.WS.'+/', ' ', $text);
 $refs = array();
 // Default highlighting
 if ($highlight === null) {
  $highlight = $start.'\1'.$end;
 }
 // Select pattern to use
 if ($options & STR_HIGHLIGHT_SIMPLE) {
  $pattern = '#(%s)#';
  $sl_pattern = '#(%s)#';
 } else {
  $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
  $sl_pattern = '#<a'.WS.'(?:.*?)>(%s)</a>#';
 }
 // Case sensitivity
 if (!($options & STR_HIGHLIGHT_CASESENS)) {
  $pattern .= 'i';
  $sl_pattern .= 'i';
 }
 $needle = $needle;
 foreach ($needle as $needle_s) {
  $needle_s = preg_quote($needle_s);
  // Escape needle with optional whole word check
  if ($options & STR_HIGHLIGHT_WHOLEWD) {
   $needle_s = '\b' . $needle_s . '\b';
  }
  // Strip links
  if ($options & STR_HIGHLIGHT_STRIPLINKS) {
   $sl_regex = sprintf($sl_pattern, $needle_s);
   $text = preg_replace($sl_regex, '\1', $text);
  }
  $regex = sprintf($pattern, $needle_s);
  $highlight2 = $highlight;
  $highlight2 = str_ireplace('_TITLE_', $needle_s, $highlight2);
  $text = preg_replace($regex, $highlight2, $text);
  while (stripos($text, '_ID_') !== FALSE) {
   $thisID = 'H-'.md5(uniqid(rand(), true));
   $text = preg_replace('/_ID_/', fixID($thisID), $text, 1);
   $refs[$needle_s][] = $thisID;
  };
	}
 return $text;
}
/* - - - - - - - - - - - - - - - - - */

/* - - - - - - - - - - - - - - - - - */
function replaceValueInURLajax ($theItem, $theNext, $theAnchor='') {
	$URLs['doc'] = $_REQUEST['doc'];
	$URLs[$theItem] = $theNext;
 return http_build_query($URLs);
};
/* - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - */
function replaceValueInURL ($theURL, $theItem, $theNext, $theAnchor='') {
if(true) {
 return ($theURL);
} else {
if (DVLP_USE_PAGINATION) {
		$params0 = explode('&',$theURL);

		$params = preg_grep('/'.$theItem.'=(.*)/', $params0, PREG_GREP_INVERT);

 		$params[] = $theItem.'='.$theNext;

 		$params[] = 'posx='.$theAnchor;

		$thisURL = implode('&',$params);
		$load = array_values(preg_grep('/doc=(.*)/', $params));
  $load2 = str_ireplace(XMLDB_PATH_BASE, '', $load[0]);
		$load2 = str_ireplace('doc=','',$load2);

		$collection_id = dirname($load2);
		$package_id = str_ireplace('.xml', '', basename($load2));

		$result = '';
		$result .='javascript: indexAjax(\'load_package\', \''.DCTL_EXPLORER_1.'\', \''.$collection_id.'\', \''.$package_id.'\'';

		$result .=', \'url='.$theNext.SYS_PATH_SEP.$theAnchor.'\'';

		foreach ($params as $param) {
			$result .= ', \''.$param.'\'';
		};

		$result .= ');';

		return strval($result);
	} else {
	 return '#'.$theNext;
	};
	};
};
/* - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - */
function get_value_from_url($curr_what, $where) {
 $result = false;
 foreach($where as $key => $value) {
  if(is_array($value)) {
   $result = array_get_value($curr_what, $value);
  } else {
   if (!(stripos($value, $curr_what) === false))  {
    $result = str_ireplace($curr_what.'=', '', $value);
   };
  };
 };
 if ($result) {
  return (string) $result;
 };
};
/* - - - - - - - - - - - - - - - - - */
/* - - - - - - - - - - - - - - - - - */
function multexplode($spacer,$string) {
 if (strlen($spacer)<1) {
  return($string);
 } else {
  $trenn=array_shift($spacer);
  $string=explode($trenn,$string);
  while (list($key,$val) = each($string)) {
   $string[$key]=multexplode($spacer,$val);
  };
  return($string);
 };
};
/* - - - - - - - - - - - - - - - - - */

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
function validate_xml($xml_path) {
 $message = true;
	if (is_file($xml_path)) {
		libxml_use_internal_errors(true);
		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->resolveExternals = true;
		$dom->substituteEntities = true;
		$dom->validateOnParse = true;
		$dom->load($xml_path);
		$errors = libxml_get_errors();
		if (strlen($errors)<1) {
			return true;
		} else {
			$message = '';
			foreach($errors as $error) {
				$message .= $error->message.' at line '.$error->line.':<br />';
			};
		};
		return $message;
	};
	return $message;
};
// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

// * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
/* NO ?> IN FILE .INC */
