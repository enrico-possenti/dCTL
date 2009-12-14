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
// | INIT
// + - - - - - - - - - - - - - - - - - -
// | YOUR PATH CONFIG
// + - - - - - - - - - - - - - - - - - -
// | url(?) / path to existing folder containing your eXist installation
$_DCTL_TOOL_FOLDER = '';
// | url(?) / path to existing folder containing your "dctl-project" data sources
$_DCTL_DATA_FOLDER = '';
// | url(?) / path to existing folder where your Commodoro will publish your repo data
$_DCTL_REPO_FOLDER = '';
// + - - - - - - - - - - - - - - - - - -
// + - - - - - - - - - - - - - - - - - -
// | YOUR WWW CONFIG
// + - - - - - - - - - - - - - - - - - -
// | your www domain URL & port
if (!defined('WWW_HOST')) define('WWW_HOST', 'http://'.$_SERVER['SERVER_NAME']); // Apache host
if (!defined('WWW_PORT')) define('WWW_PORT', 80); // Apache port
if (!defined('WWW_DATA')) define('WWW_DATA', ''); // "repo" relative url
// + - - - - - - - - - - - - - - - - - -
// + - - - - - - - - - - - - - - - - - -
// | YOUR XMLDB CONFIG
// + - - - - - - - - - - - - - - - - - -
// | your eXist-db servlet URL & port
if (!defined('XMLDB_HOST')) define('XMLDB_HOST', 'http://'.$_SERVER['SERVER_NAME']); // eXist host
if (!defined('XMLDB_PORT')) define('XMLDB_PORT', 8080); // eXist port
// + - - - - - - - - - - - - - - - - - -
// + - - - - - - - - - - - - - - - - - -
// | YOUR MYSQL CONFIG
// + - - - - - - - - - - - - - - - - - -
// | your MySQL URL & port for "dctl_name"
if (!defined('MYSQL_HOST_PN')) define('MYSQL_HOST_PN', $_SERVER['SERVER_NAME']); // MySQL host x db NAME
if (!defined('MYSQL_PORT_PN')) define('MYSQL_PORT_PN', 3306); // MYSQL port x db NAME
// | your MySQL URL & port for "dctl_iconclass"
if (!defined('MYSQL_HOST_IC')) define('MYSQL_HOST_IC', $_SERVER['SERVER_NAME']); // MySQL host x db ICONCLASS
if (!defined('MYSQL_PORT_IC')) define('MYSQL_PORT_IC', 3306); // MYSQL port x db ICONCLASS
// + - - - - - - - - - - - - - - - - - -
// + - - - - - - - - - - - - - - - - - -
