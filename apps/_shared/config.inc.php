<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

/**
 *  Common config file for dCTL
 *  @package    dCTL
 *  @subpackage common
 *  @author    NoveOPiu di Enrico Possenti
 *  @version   $Id: config.inc.php 2009-12-05 noveopiu $
 */
// |
// + - - - - - - - - - - - - - - - - - -
$v = (array)@strtok(@file_get_contents(dirname(__FILE__).'/../VERSION'), "\n\r");
define('APPS_VERSION', $v[0]); // current dCTL version
// + - - - - - - - - - - - - - - - - - -
// | INIT
$init = dirname(__FILE__).'/../config.inc.php';
if (!is_file($init)) die('ERROR: '.dirname(dirname(__FILE__)).'/config.inc.php" not found... Fix it. Me, i abort.');
require_once($init);
if (!defined('NOVEOPIU')) define('NOVEOPIU', false);
if (!defined('PRIVATE_ONLY')) define('PRIVATE_ONLY', false);
// + - - - - - - - - - - - - - - - - - -
// | PHP ENVIRONMENT
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
if (!defined('ENGINE_TIMEOUT_MULTIPLIER')) define('ENGINE_TIMEOUT_MULTIPLIER', 0.5);
ini_set('max_execution_time', 60 * ENGINE_TIMEOUT_MULTIPLIER);
ini_set('max_input_time', 60 * ENGINE_TIMEOUT_MULTIPLIER);
ini_set('memory_limit', -1);
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
define('CHMOD', 0775);
ini_set('mbstring.language', 'Neutral');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.detect_order', 'auto');
ini_set('mbstring.strict_detection', true);
define('DCTL_XML_LOADER', LIBXML_DTDLOAD | LIBXML_DTDATTR | LIBXML_NOENT | LIBXML_NSCLEAN | LIBXML_COMPACT | LIBXML_NOCDATA); //  LIBXML_DTDVALID |
define('WHITESPACES', '[ \t\r\n]');
// + - - - - - - - - - - - - - - - - - -
// | SYS ENVIRONMENT
define('SYS_PATH_SEPARATOR', '/');
define('SYS_PATH_SEPARATOR_DOUBLE', SYS_PATH_SEPARATOR.SYS_PATH_SEPARATOR);
// + - - - - - - - - - - - - - - - - - -
// | PATH SETUP (custom)
// | customize here location of folders
if (!isset($_DCTL_TOOL_FOLDER)) $_DCTL_TOOL_FOLDER = ''; // empty for default or full path to folder containing eXist installation
if (!isset($_DCTL_DATA_FOLDER)) $_DCTL_DATA_FOLDER = ''; // empty for default or full path to folder containing "dctlProject"
if (!isset($_DCTL_REPO_FOLDER)) $_DCTL_REPO_FOLDER = ''; // empty for default or full path to folder for repository
// + - - - - - - - - - - - - - - - - - -
// | VERIFY FOLDER EXISTENCE
if (!is_dir($_DCTL_TOOL_FOLDER)) die('ERROR: $_DCTL_TOOL_FOLDER ('.$_DCTL_TOOL_FOLDER.') not found... Fix it in "'.dirname(dirname(__FILE__)).'/config.inc.php". Me, i abort.');
if (!is_dir($_DCTL_DATA_FOLDER)) die('ERROR: $_DCTL_DATA_FOLDER ('.$_DCTL_DATA_FOLDER.') not found... Fix it in "'.dirname(dirname(__FILE__)).'/config.inc.php". Me, i abort.');
if (!is_dir($_DCTL_REPO_FOLDER)) die('ERROR: $_DCTL_REPO_FOLDER ('.$_DCTL_REPO_FOLDER.') not found... Fix it in "'.dirname(dirname(__FILE__)).'/config.inc.php". Me, i abort.');
// + - - - - - - - - - - - - - - - - - -
// | MYSQL CONFIG
if (!defined('MYSQL_HOST_PN')) define('MYSQL_HOST_PN', $_SERVER['SERVER_NAME']); // MySQL host x db NAME
if (!defined('MYSQL_PORT_PN')) define('MYSQL_PORT_PN', 3306); // MYSQL port x db NAME
if (!defined('MYSQL_HOST_IC')) define('MYSQL_HOST_IC', $_SERVER['SERVER_NAME']); // MySQL host x db ICONCLASS
if (!defined('MYSQL_PORT_IC')) define('MYSQL_PORT_IC', 3306); // MYSQL port x db ICONCLASS
define('DCTL_DB_NAME', 'dctl_name');
define('DCTL_DB_ICONCLASS', 'dctl_iconclass');
if (!defined('DCTL_SQL_USER')) define('DCTL_SQL_USER', 'ctl');
if (!defined('DCTL_SQL_PSWD')) define('DCTL_SQL_PSWD', 'sirena');
// | PATH CONFIG
define('FS_BASE_PATH', str_replace(SYS_PATH_SEPARATOR_DOUBLE, SYS_PATH_SEPARATOR, dirname(dirname(dirname(__FILE__))).SYS_PATH_SEPARATOR));
define('DCTL_VERSION', ''); // folder extension after "dctl"
define('DCTL_APPS_PATH', FS_BASE_PATH.'apps'.SYS_PATH_SEPARATOR);
define('DCTL_TOOL_PATH', ($_DCTL_TOOL_FOLDER ? $_DCTL_TOOL_FOLDER : (FS_BASE_PATH.'tool'.SYS_PATH_SEPARATOR.'exist'.SYS_PATH_SEPARATOR)));
define('DCTL_REPO_PATH', ($_DCTL_REPO_FOLDER ? $_DCTL_REPO_FOLDER : (FS_BASE_PATH.'data'.SYS_PATH_SEPARATOR)));
define('DCTL_DATA_PATH', ($_DCTL_DATA_FOLDER ? $_DCTL_DATA_FOLDER : (FS_BASE_PATH.'data'.SYS_PATH_SEPARATOR)));
// + - - - - - - - - - - - - - - - - - -
// | DATA (SOURCE) ENVIRONMENT
define('DCTL_PROJECT_PATH', DCTL_DATA_PATH.'dctl-project'.SYS_PATH_SEPARATOR);
// + - - - - - - - - - - - - - - - - - -
// | REPO (TARGET) ENVIRONMENT
// data/db/
define('DCTL_DBCTL_MAINPATH', DCTL_REPO_PATH.'db'.SYS_PATH_SEPARATOR);
if(!is_dir(DCTL_DBCTL_MAINPATH)) mkdir(DCTL_DBCTL_MAINPATH, CHMOD);
@chmod(DCTL_DBCTL_MAINPATH, CHMOD);
// data/db/version/; optional
define('DCTL_DBCTL_BASEPATH', DCTL_DBCTL_MAINPATH.(DCTL_VERSION?DCTL_VERSION.SYS_PATH_SEPARATOR:''));
if(!is_dir(DCTL_DBCTL_BASEPATH)) mkdir(DCTL_DBCTL_BASEPATH, CHMOD);
@chmod(DCTL_DBCTL_BASEPATH, CHMOD);
// reserved or public?
define('TEMPORARY_SYSTEM', PRIVATE_ONLY || ((isset($_REQUEST['temp']) ? (($_REQUEST['temp'] == 'true') || ($_REQUEST['temp'] == '1')) : false)));
define('DCTL_TMP_NAME', 'dctl-temp');
define('DCTL_PUB_NAME', 'dctl-pub');
// reserved or public?
// data/db/dctl-pub/
define('DCTL_DBCTL_PUB', DCTL_DBCTL_BASEPATH.DCTL_PUB_NAME.SYS_PATH_SEPARATOR);
if(!is_dir(DCTL_DBCTL_PUB)) mkdir(DCTL_DBCTL_PUB, CHMOD);
@chmod(DCTL_DBCTL_PUB, CHMOD);
// data/db/dctl-temp/
define('DCTL_DBCTL_TMP', DCTL_DBCTL_BASEPATH.DCTL_TMP_NAME.SYS_PATH_SEPARATOR);
if(!is_dir(DCTL_DBCTL_TMP)) mkdir(DCTL_DBCTL_TMP, CHMOD);
@chmod(DCTL_DBCTL_TMP, CHMOD);
// data/db/dctl-???/
if (TEMPORARY_SYSTEM) {
 define('DCTL_PUBLISH', DCTL_DBCTL_TMP);
} else {
 define('DCTL_PUBLISH', DCTL_DBCTL_PUB);
};
// + - - - - - - - - - - - - - - - - - -
// | COMMODORO ENVIRONMENT
define('COMMODORO', 'commodoro');
define('DCTL_IMAGES', '..'.SYS_PATH_SEPARATOR.'img'.SYS_PATH_SEPARATOR);
if (!defined('DCTL_USER_ID')) define('DCTL_USER_ID', 'guest');
if (!defined('DCTL_USER_NAME')) define('DCTL_USER_NAME', 'Sconosciuto');
if (!defined('DCTL_USER_KIND')) define('DCTL_USER_KIND', 0);
if (!defined('DCTL_USER_IS_SUPER')) define('DCTL_USER_IS_SUPER', false);
define('MAIL_TO', 'info@noveopiu.com');
$curr_lang = 'it';
define('SYS_DBL_SPACE', '&#160;&#160;&#160;');
define('FIELD_CODE_LENGTH', 27);
define('FIELD_DATE_LENGTH', 10);
define('FIELD_STRING_LENGTH', 80);
define('FIELD_STRING_MAXLENGTH', 255);
define('FIELD_TEXT_LENGTH', FIELD_STRING_LENGTH-2);
define('FIELD_TEXT_HEIGHT', 5);
define('FIELD_SELECT_HEIGHT', 15);
define('DISTINCT_SEP', '◊');
define('DISTINCT_SEP2', '≈');
define('DCTL_RESERVED_PREFIX', '_');
define('DCTL_RESERVED_INFIX', '-');
define('DCTL_TEXTCLASS', 'teiHeader.profileDesc.textClass.xml');
define('CODE_LENGTH', 8);
define('DCTL_TMP', 'temp'.SYS_PATH_SEPARATOR);
define('DCTL_DTD', 'dtd'.SYS_PATH_SEPARATOR);
define('DCTL_XSLT', 'xslt'.SYS_PATH_SEPARATOR);
define('DCTL_COLLECTION', 'collection'.SYS_PATH_SEPARATOR);
define('DCTL_PACKAGE', 'package'.SYS_PATH_SEPARATOR);
define('DCTL_MEDIA', DCTL_RESERVED_PREFIX.'media'.SYS_PATH_SEPARATOR);
define('DCTL_MEDIA_SML', DCTL_MEDIA.'sml'.SYS_PATH_SEPARATOR);
define('DCTL_MEDIA_MED', DCTL_MEDIA.'med'.SYS_PATH_SEPARATOR);
define('DCTL_MEDIA_BIG', DCTL_MEDIA.'big'.SYS_PATH_SEPARATOR);
define('DCTL_COLLECTION_XSLT', DCTL_RESERVED_PREFIX.'xslt'.SYS_PATH_SEPARATOR);
define('DCTL_ADDONS', 'add-ons'.SYS_PATH_SEPARATOR);
define('DCTL_COMMON', DCTL_RESERVED_PREFIX.'common'.SYS_PATH_SEPARATOR);
define('DCTL_TEMPLATES', 'templates'.SYS_PATH_SEPARATOR);
define('DCTL_FILE_HEADER', DCTL_RESERVED_PREFIX.'header.ent');
define('DCTL_FILE_BUILDER', DCTL_RESERVED_PREFIX.'builder.xml');
define('DCTL_FILE_LINKER', DCTL_RESERVED_PREFIX.'linker.xml');
define('DCTL_FILE_MAPPER', DCTL_RESERVED_PREFIX.'mapper.xml');
define('DCTL_PACKAGE_FRONT', 'p000.xml');
define('DCTL_PACKAGE_BODY', 'p$.xml');
define('DCTL_PACKAGE_BACK', 'p999.xml');
define('DCTL_PACKAGE_BODY_REGEXP1','p$.');
define('DCTL_PACKAGE_BODY_REGEXP2','p\d+\.');
define('DCTL_PACKAGE_BODY_REGEXP3','p(\d+)\.');
define('DCTL_SETTINGS', DCTL_PROJECT_PATH.DCTL_COMMON);
define('DCTL_SETTINGS_TEMPLATES', DCTL_SETTINGS.DCTL_TEMPLATES);
define('DCTL_SETTINGS_TEMPLATES_COLLECTION', DCTL_SETTINGS_TEMPLATES.DCTL_COLLECTION);
define('DCTL_SETTINGS_TEMPLATES_PACKAGE', DCTL_SETTINGS_TEMPLATES.DCTL_PACKAGE);
define('DCTL_SETTINGS_DTD', DCTL_SETTINGS.DCTL_DTD);
define('DCTL_SETTINGS_ADDONS', DCTL_SETTINGS.DCTL_ADDONS);
define('DCTL_SETTINGS_TEXTCLASS', DCTL_SETTINGS_ADDONS.DCTL_TEXTCLASS);
define('DCTL_LIST_RESP', 'dCTL-Responsible');
define('DCTL_LIST_LANG', 'dCTL-Language');
define('DCTL_LIST_GENRE', 'dCTL-Genre');
define('DCTL_APPS_XSLT', DCTL_APPS_PATH.DCTL_XSLT);
define('DCTL_COMMODORO_XSLT', DCTL_XSLT);
// + - - - - - - - - - - - - - - - - - -
define('DCTL_PUBLISH_MEDIA', DCTL_PUBLISH.DCTL_MEDIA);
define('DCTL_PUBLISH_TEXTCLASS', DCTL_PUBLISH.DCTL_TEXTCLASS);
// + - - - - - - - - - - - - - - - - - -
// | XML:DB ENVIRONMENT
define('DB_PATH_SEPARATOR', '/');
if (!defined('XMLDB_HOST')) define('XMLDB_HOST', 'http://'.$_SERVER['SERVER_NAME']); // eXist host
if (!defined('XMLDB_PORT')) define('XMLDB_PORT', 8080); // eXist port
if (!defined('XMLDB_TIMEOUT_MULTIPLIER')) define('XMLDB_TIMEOUT_MULTIPLIER', 1);
define('XMLDB_TIMEOUT', 60 * 1000 * XMLDB_TIMEOUT_MULTIPLIER);
define('XMLDB_MAXCOUNT', -1);
if (!defined('DCTL_XMLDB_USER')) define('DCTL_XMLDB_USER', 'guest');
if (!defined('DCTL_XMLDB_PSWD')) define('DCTL_XMLDB_PSWD', 'guest');
if (!defined('DCTL_XMLDB_USER_ADMIN')) define('DCTL_XMLDB_USER_ADMIN', DCTL_XMLDB_USER);
if (!defined('DCTL_XMLDB_PSWD_ADMIN')) define('DCTL_XMLDB_PSWD_ADMIN', DCTL_XMLDB_PSWD);
if (!defined('DCTL_XMLDB_GROUP_ADMIN')) define('DCTL_XMLDB_GROUP_ADMIN', 'dba');
if (!defined('DCTL_XMLDB_PERMISSIONS_ADMIN')) define('DCTL_XMLDB_PERMISSIONS_ADMIN', 508); // rwurwur--
define('XMLDB_CATALOG', DCTL_TOOL_PATH.'webapp'.SYS_PATH_SEPARATOR.'WEB-INF'.SYS_PATH_SEPARATOR.'catalog.xml');
define('XMLDB_ENTITIES', DCTL_TOOL_PATH.'webapp'.SYS_PATH_SEPARATOR.'WEB-INF'.SYS_PATH_SEPARATOR.'entities');
define("SAXON_DIR", DCTL_TOOL_PATH.'lib'.SYS_PATH_SEPARATOR.'endorsed'.SYS_PATH_SEPARATOR);
// /db/
define('XMLDB_DBCTL_MAINPATH', DB_PATH_SEPARATOR.'db'.DB_PATH_SEPARATOR);
// /db/version/; optional
define('XMLDB_DBCTL_BASEPATH', XMLDB_DBCTL_MAINPATH.(DCTL_VERSION?DCTL_VERSION.DB_PATH_SEPARATOR:''));
// /db/dctl-pub/
define('XMLDB_DBCTL_PUB', XMLDB_DBCTL_BASEPATH.DCTL_PUB_NAME.DB_PATH_SEPARATOR);
// /db/dctl-temp/
define('XMLDB_DBCTL_TMP', XMLDB_DBCTL_BASEPATH.DCTL_TMP_NAME.DB_PATH_SEPARATOR);
// /db/dctl-???/
if (TEMPORARY_SYSTEM) {
 define('XMLDB_PATH_BASE', XMLDB_DBCTL_TMP);
} else {
 define('XMLDB_PATH_BASE', XMLDB_DBCTL_PUB);
};
// + - - - - - - - - - - - - - - - - - -
// | WEB ENVIRONMENT
define('WEB_PATH_SEPARATOR', '/');
if (!defined('WWW_PORT')) define('WWW_PORT', '80');
if (!defined('WWW_NAME')) define('WWW_NAME', 'http://'.$_SERVER['SERVER_NAME'].':'.WWW_PORT);
if (!defined('WWW_HOST')) define('WWW_HOST', WWW_NAME.'');
define('HOST_BASE_PATH', dirname(dirname(dirname($_SERVER['PHP_SELF']))).WEB_PATH_SEPARATOR);
define('DCTL_QUERY_STRING', htmlspecialchars($_SERVER['QUERY_STRING']));
define('DCTL_REQUEST_URI', htmlspecialchars($_SERVER['REQUEST_URI']));
define('DCTL_FORM_METHOD', 'post');
define('DCTL_FORM_ENCTYPE', 'application/x-www-form-urlencoded');
define('DCTL_FORM_METHOD_POST', 'post');
define('DCTL_FORM_ENCTYPE_POST', 'multipart/form-data');
// data/db/version?/
define('HOST_DATA_PATH', dirname(HOST_BASE_PATH).WWW_DATA.'db'.WEB_PATH_SEPARATOR.(DCTL_VERSION?DCTL_VERSION.WEB_PATH_SEPARATOR:''));
// data/db/dctl-pub/
define('WEB_DBCTL_PUB', HOST_DATA_PATH.DCTL_PUB_NAME.WEB_PATH_SEPARATOR);
// data/db/dctl-temp/
define('WEB_DBCTL_TMP', HOST_DATA_PATH.DCTL_TMP_NAME.WEB_PATH_SEPARATOR);
// data/db/dctl-???/
if (TEMPORARY_SYSTEM) {
 define('WEB_PUBLISH', WEB_DBCTL_TMP);
} else {
 define('WEB_PUBLISH', WEB_DBCTL_PUB);
};
// + - - - - - - - - - - - - - - - - - -
// | DCTL:MASTRO SETUP
define('TOOLTIP_DRAG', 'Trascina');
define('TOOLTIP_GOTO', 'Accedi');
define('TOOLTIP_SELECT', 'Vedi');
define('TOOLTIP_ZOOM', 'Lente d\'ingrandimento');
define('TOOLTIP_TOGGLE', 'Apri/chiudi');
define('TOOLTIP_ADDTOBASKET', 'Aggiungi al basket');
define('MASTRO', 'mastro');
define('MASTRO_RETRIEVE', 'retrieve');
define('MASTRO_DISPLAY', 'display');
define('DCTL_MASTRO_XSLT', DCTL_XSLT);
define('DCTL_MASTRO_RETRIEVE_XSLT', DCTL_MASTRO_XSLT.MASTRO_RETRIEVE.SYS_PATH_SEPARATOR);
define('DCTL_MASTRO_DISPLAY_XSLT', DCTL_MASTRO_XSLT.MASTRO_DISPLAY.SYS_PATH_SEPARATOR);
// + - - - - - - - - - - - - - - - - - -
// | EXIST-XQUERY
define('XMLDB_XML_N', 'xmlns');
define('XMLDB_TEI_N', 'tei');
define('XMLDB_DCTL_N', 'dctl');
define('XMLDB_TEI_S', 'http://www.tei-c.org/ns/1.0');
define('XMLDB_DCTL_S', 'http://www.ctl.sns.it/ns/1.0');
define('XMLDB_TEI_NS', XMLDB_TEI_N.'="'.XMLDB_TEI_S.'"');
define('XMLDB_TEI_NS2', XMLDB_XML_N.'="'.XMLDB_TEI_S.'"');
define('XMLDB_DCTL_NS', XMLDB_DCTL_N.'="'.XMLDB_DCTL_S.'"');
define('XMLDB_DCTL_NS2', XMLDB_XML_N.'="'.XMLDB_DCTL_S.'"');
define('XMLDB_EXSLT_NS', 'exslt="http://exslt.org/common"');
define('XMLDB_DYN_NS', 'dyn="http://exslt.org/dynamic"');
define('XMLDB_STR_NS', 'str="http://exslt.org/strings"');
define('XMLDB_PHP_NS', 'php="http://php.net/xsl"');
define('XMLDB_UTIL_NS', 'util="http://exist-db.org/xquery/util"');
define('XMLDB_XMLDB_NS', 'xmldb="http://exist-db.org/xquery/xmldb"');
define('XMLDB_EXIST_NS', 'exist="http://exist.sourceforge.net/NS/exist"');
define('XMLDB_TRANSFORM_NS', 'transform="http://exist-db.org/xquery/transform"');
define('XMLDB_FUNCTX_NS', 'functx = "http://www.functx.com"');
$xquery_lib = ($xquery_lib = @file_get_contents(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'..'.SYS_PATH_SEPARATOR.'_shared'.SYS_PATH_SEPARATOR.'functions.inc.xq')) ? $xquery_lib : '';
//declare default element namespace "'.XMLDB_TEI_S.'";  // NON IMPORTA
define('DCTL_XQUERY_BASE',
'xquery version "1.0";
declare namespace '.XMLDB_TEI_NS.';
declare namespace '.XMLDB_DCTL_NS.';
declare namespace '.XMLDB_UTIL_NS.';
declare namespace '.XMLDB_EXIST_NS.';
declare namespace '.XMLDB_TRANSFORM_NS.';
declare namespace '.XMLDB_FUNCTX_NS.';
declare option exist:timeout "' . XMLDB_TIMEOUT . '";
declare option exist:output-size-limit "'.XMLDB_MAXCOUNT.'";
declare option exist:serialize "method=xhtml";
declare option exist:serialize "highlight-matches=both";
'.$xquery_lib);
// + - - - - - - - - - - - - - - - - - -
// | ADD-ONS
define('DCTL_STATS_PATH', '..'.SYS_PATH_SEPARATOR.'..'.SYS_PATH_SEPARATOR.'..'.SYS_PATH_SEPARATOR.'slimstat/inc.stats.php');
if (is_file(DCTL_STATS_PATH)) {
 require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).DCTL_STATS_PATH);
};
// + - - - - - - - - - - - - - - - - - -
// | REQUIRE NEXT SETUP
require_once(str_replace(SYS_PATH_SEPARATOR_DOUBLE,SYS_PATH_SEPARATOR,dirname(__FILE__).SYS_PATH_SEPARATOR).'..'.SYS_PATH_SEPARATOR.'_shared'.SYS_PATH_SEPARATOR.'functions.inc.php');
// + - - - - - - - - - - - - - - - - - -
// | MORE SETUP
$EXTENSION_PACKAGE = array();
$EXTENSION_PACKAGE['Illustrazioni']        = '_img';
$EXTENSION_PACKAGE['Paratesti']            = '_ptx';
$EXTENSION_PACKAGE['Testo']                = '_txt';
$EXTENSION_PACKAGE['Galleria di immagini'] = '_gal';
$EXTENSION_PACKAGE['Scheda di testo']      = '_txr';
$EXTENSION_PACKAGE['Citazioni']            = '_cit';
// + - - - - - - - - - - - - - - - - - -
$EXTENSION_PREVIEW = array('jpg', 'gif', 'png');
$EXTENSION_GRAPHIC = array('jpg', 'gif', 'png', 'pdf', 'mov');
$EXTENSION_TEXT = array('rtf', 'txt', 'xml', 'doc', 'pdf', 'ppt');
$EXTENSION_ALLOWED = array_merge_recursive($EXTENSION_GRAPHIC, $EXTENSION_TEXT);
// + - - - - - - - - - - - - - - - - - -
$COLLECTION_FIELDS = array();
$item = 'collection_id';
$COLLECTION_FIELDS['label'][$item] = 'Codice';
$COLLECTION_FIELDS['type'][$item] = 'id';
$item = 'collection_short';
$COLLECTION_FIELDS['label'][$item] = 'Nome breve';
$COLLECTION_FIELDS['type'][$item] = 'string';
$item = 'collection_work';
$COLLECTION_FIELDS['label'][$item] = 'Nome completo';
$COLLECTION_FIELDS['type'][$item] = 'string';
$item = 'collection_year_created';
$COLLECTION_FIELDS['label'][$item] = 'Creato il';
$COLLECTION_FIELDS['type'][$item] = 'date';
$item = 'collection_year_updated';
$COLLECTION_FIELDS['label'][$item] = 'Ultimo aggiornamento';
$COLLECTION_FIELDS['type'][$item] = 'date';
$item = 'collection_resp';
$COLLECTION_FIELDS['label'][$item] = 'Responsabile';
$COLLECTION_FIELDS['type'][$item] = 'list';
$item = 'collection_packageCount';
$COLLECTION_FIELDS['label'][$item] = 'Num. Package';
$COLLECTION_FIELDS['type'][$item] = 'auto';
$item = 'collection_summary_it';
$COLLECTION_FIELDS['label'][$item] = 'Sommario (IT)';
$COLLECTION_FIELDS['type'][$item] = 'string';
$item = 'collection_summary_en';
$COLLECTION_FIELDS['label'][$item] = 'Sommario (EN)';
$COLLECTION_FIELDS['type'][$item] = 'string';
$item = 'collection_desc_it';
$COLLECTION_FIELDS['label'][$item] = 'Descrizione (IT)';
$COLLECTION_FIELDS['type'][$item] = 'text';
$item = 'collection_desc_en';
$COLLECTION_FIELDS['label'][$item] = 'Descrizione (EN)';
$COLLECTION_FIELDS['type'][$item] = 'text';
$item = 'collection_editorial_it';
$COLLECTION_FIELDS['label'][$item] = 'Editoriale (IT)';
$COLLECTION_FIELDS['type'][$item] = 'text';
$item = 'collection_editorial_en';
$COLLECTION_FIELDS['label'][$item] = 'Editoriale (EN)';
$COLLECTION_FIELDS['type'][$item] = 'text';
// + - - - - - - - - - - - - - - - - - -
$PACKAGE_FIELDS = array();
$item = 'package_id';
$PACKAGE_FIELDS['label'][$item] = 'Codice';
$PACKAGE_FIELDS['type'][$item] = 'id';
$item = 'package_short';
$PACKAGE_FIELDS['label'][$item] = 'Nome breve';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'package_work';
$PACKAGE_FIELDS['label'][$item] = 'Nome completo';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'package_type';
$PACKAGE_FIELDS['label'][$item] = 'Tipo di accesso';
$PACKAGE_FIELDS['type'][$item] = 'radio';
$PACKAGE_FIELDS['opts'][$item] = ' Search+Browse+Display =0| Browse+Display =1| Display =2';
$item = 'package_year_created';
$PACKAGE_FIELDS['label'][$item] = 'Creato il';
$PACKAGE_FIELDS['type'][$item] = 'date';
$item = 'package_year_updated';
$PACKAGE_FIELDS['label'][$item] = 'Ultimo aggiornamento';
$PACKAGE_FIELDS['type'][$item] = 'date';
$item = 'package_encoder';
$PACKAGE_FIELDS['label'][$item] = 'Codificatore';
$PACKAGE_FIELDS['type'][$item] = 'list';
$item = 'package_fileSize';
$PACKAGE_FIELDS['label'][$item] = 'Dimensione';
$PACKAGE_FIELDS['type'][$item] = 'auto';
$item = 'package_summary_it';
$PACKAGE_FIELDS['label'][$item] = 'Sommario (IT)';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'package_summary_en';
$PACKAGE_FIELDS['label'][$item] = 'Sommario (EN)';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'package_desc_it';
$PACKAGE_FIELDS['label'][$item] = 'Descrizione (IT)';
$PACKAGE_FIELDS['type'][$item] = 'text';
$item = 'package_desc_en';
$PACKAGE_FIELDS['label'][$item] = 'Descrizione (EN)';
$PACKAGE_FIELDS['type'][$item] = 'text';
$item = 'package_editorial_it';
$PACKAGE_FIELDS['label'][$item] = 'Editoriale (IT)';
$PACKAGE_FIELDS['type'][$item] = 'text';
$item = 'package_editorial_en';
$PACKAGE_FIELDS['label'][$item] = 'Editoriale (EN)';
$PACKAGE_FIELDS['type'][$item] = 'text';
$item = 'source_title_main';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Titolo Principale';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_title_sub';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Titolo Secondario';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_title_parallel';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Titolo Parallelo';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_author';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Autore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_edition_editor';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Curatore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_edition_publisher';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Editore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_edition_place';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Luogo Pubbl.';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_edition_year';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Anno Pubbl.';
$PACKAGE_FIELDS['type'][$item] = 'date';
$item = 'source_edition_pages';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Num. Pagine';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'source_lang';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Lingua';
$PACKAGE_FIELDS['type'][$item] = 'list';
$item = 'source_genre';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Genere';
$PACKAGE_FIELDS['type'][$item] = 'list';
$item = 'source_note';
$PACKAGE_FIELDS['label'][$item] = 'Fonte: Note';
$PACKAGE_FIELDS['type'][$item] = 'text';
$item = 'reference_title_main';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Titolo Principale';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_title_sub';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Titolo Secondario';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_title_parallel';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Titolo Parallelo';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_author';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Autore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_edition_editor';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Curatore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_edition_publisher';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Editore';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_edition_place';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Luogo Pubbl.';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_edition_year';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Anno Pubbl.';
$PACKAGE_FIELDS['type'][$item] = 'date';
$item = 'reference_edition_pages';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Num. Pagine';
$PACKAGE_FIELDS['type'][$item] = 'string';
$item = 'reference_lang';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Lingua';
$PACKAGE_FIELDS['type'][$item] = 'list';
$item = 'reference_genre';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Genere';
$PACKAGE_FIELDS['type'][$item] = 'list';
$item = 'reference_note';
$PACKAGE_FIELDS['label'][$item] = 'Originale: Note';
$PACKAGE_FIELDS['type'][$item] = 'text';
// + - - - - - - - - - - - - - - - - - -

/* NO ?> IN FILE .INC */
