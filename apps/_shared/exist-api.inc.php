<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

		// +----------------------------------------------------------------------+
		// | Mastered by (C) 2009 NoveOPiu di Enrico Possenti                     |
		// | Author: Enrico Possenti <info@noveopiu.com>                          |
		// +----------------------------------------------------------------------+
		// | Code for querying and manipulating exist XML:DB via SOAP interface.
		// | from an original idea of:
		// | @author     Oscar Celma <ocelma@iua.upf.edu> Original Author
		// | @author     Francois-Xavier Prunayre <fx.prunayre@gmail.com> Author
		// | @license    GPL
		// | @link       http://query-exist.sourceforge.net
		// | @version    v0.5 2007-04-05
		// + - - - - - - - - - - - - - - - - - -
		// |
		// +----------------------------------------------------------------------+
		// | EXIST
		// +----------------------------------------------------------------------+
class exist {
	protected $_wsdl = "";
	protected $_user = "guest";
	protected $_password = "guest";
	protected $_soapClient = null;
	protected $_session = null;
	protected $_error = "";
	protected $_debug = false;
	protected $_highlight = true;
	protected $_pidfn = NULL;
	// +----------------------------------------------------------------------+
	public $pid = null;
	// +----------------------------------------------------------------------+
 public function __construct($user="guest", $password="guest", $wsdl="http://localhost:8080/exist/services/Query?wsdl", $persistentConnection=FALSE) {
	  if ( $this->getError() ) return false;
	  try {
				$this->_user = $user;
				$this->_password = $password;
				$this->_wsdl = $wsdl;
	   $this->_pidfn = str_replace('//','/',dirname(__FILE__).'/')."exist.pid";
	   $this->_session = NULL;
	   $this->_pSession = $persistentConnection;
	   if ($this->_pSession) {
	    if (!is_file($this->_pidfn)) {
	     @touch($this->_pidfn);
	     @chmod($this->_pidfn, 0644);
     };
     $this->_session = @file_get_contents($this->_pidfn);
	   };
				$this->_soapClient = new SoapClient ($this->_wsdl, array("user_agent"=>""));
	  } catch( SoapFault $e ) {
		  $this->setError($e->faultstring);
	  }
  }
		// +----------------------------------------------------------------------+
  public function __destruct() {
  }
		// +----------------------------------------------------------------------+
  public function getError() {
			$error = '';
			if ( $this->_error != "") {
				$error = $this->_error;
			};
			$this->_error = '';
			return $error;
  }
		// +----------------------------------------------------------------------+
  protected function setError($error) {
    $this->_error = $error;
  }
		// +----------------------------------------------------------------------+
  public function setHighlight($highlight) {
	  $this->_highlight = $highlight ? 'both' : FALSE;
  }
		// +----------------------------------------------------------------------+
  public function setDebug($debug=true) {
    $this->_debug = $debug;
  }
		// +----------------------------------------------------------------------+
  public function setUser($user) {
    $this->_user = $user;
  }
		// +----------------------------------------------------------------------+
  public function setPassword($passwd) {
    $this->_password = $passwd;
  }
		// +----------------------------------------------------------------------+
  public function setWSDL($wsdl) {
    $this->_wsdl = $wsdl;
  }
		// +----------------------------------------------------------------------+
		// +----------------------------------------------------------------------+
		// | Commmon
		// +----------------------------------------------------------------------+
  protected function soapCall($function, $params) {
	  $return = $this->_soapClient->__soapCall($function, array('parameters'=>$params));
	  $output = $function . "Return";
	  return isset($return->$output) ? $return->$output : 0;
  }
		// +----------------------------------------------------------------------+
  public function connect($mode) {
	  if ( $this->getError() ) return false;
   if ($this->_session) {
   // try a call to see if pid is still valid
				try {
				 if ($mode == 'admin') {
				  $call = 'getCollectionDesc';
 				 $parameters = array('sessionId' => $this->_session, 'collectionName' => '/');
				 } else {
				  $call = 'listCollection';
 				 $parameters = array('sessionId' => $this->_session, 'path' => '/');
				 };
				 $chk = $this->soapCall($call, $parameters);
				} catch( SoapFault $e ) {
					$chk = false;
				};
    if ($chk) {
				 $this->pid = $this->_session;
				} else {
				 $this->_session = NULL;
				};
   };
   if (!$this->_session) {
				try {
					$parameters = array('userId' => $this->_user, 'password' => $this->_password );
					$this->_session = $this->soapCall('connect', $parameters);
					if ($this->_session) {
					 if ($this->_pSession) {
							@touch($this->_pidfn);
							@chmod($this->_pidfn, 0644);
							@file_put_contents($this->_pidfn, $this->_session);
						};
					};
				 $this->pid = $this->_session;
				} catch( SoapFault $e ) {
					$this->setError($e->faultstring);
					return false;
				};
	  };
		 return true;
  }
		// +----------------------------------------------------------------------+
  public function disconnect($forceClose = false) {
	  if ( $this->getError() ) return false;
				$forceClose = ($this->_pSession) ? $forceClose : true;
				if ($forceClose) {
					try {
						$parameters = array('sessionId' => $this->_session);
						$this->_session = $this->soapCall('disconnect', $parameters);
		    if ($this->_pSession) {
	      if (is_file($this->_pidfn)) {
						  if (@file_get_contents($this->_pidfn) == $this->pid) unlink($this->_pidfn);
	 					};
						};
				 $this->pid = $this->_session;
					} catch( SoapFault $e ) {
						$this->setError($e->faultstring);
					};
				};
  }
		// +----------------------------------------------------------------------+
		// | Query.wsdl (http://exist.sourceforge.net/api/org/exist/soap/Query.html)
		// +----------------------------------------------------------------------+
		// | String 	connect(String userId, String password)
		// ; Create a new user session.
		// +----------------------------------------------------------------------+
		// | void 	disconnect(String sessionId)
		// ; Release a user session.
		// +----------------------------------------------------------------------+
		// | String  getResource(String sessionId, String path, boolean indent, boolean xinclude)
		// ; Retrieve a document from the database.
	public function getResource($document, $indent = false, $xinclude = false) {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'path' => $document, 'indent' => $indent, 'xinclude' => $xinclude);
		return $this->soapCall('getResource', $parameters);
	}
		// +----------------------------------------------------------------------+
		// | byte[] getResourceData(String sessionId, String path, boolean indent, boolean xinclude, boolean processXSLPI)
		// ; Retrieve a document from the database.
	public function getResourceData() {
	 return $this->setError('unimplemented: exist::getResourceData()');
	}
	// +----------------------------------------------------------------------+
		// | Collection  listCollection(String sessionId, String path)
		// ; Get information on the specified collection.
	public function listCollections($path = '') {
	 return $this->getCollections($path);
	}
	public function getCollections($path = '') {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'path' => $path);
		return $this->soapCall('listCollection', $parameters);
	}
		// +----------------------------------------------------------------------+
		// | String[] 	retrieve(String sessionId, int start, int howmany, boolean indent, boolean xinclude, String highlight)
		// ; Retrieve a set of query results from the last query executed within the current session.
 	// +----------------------------------------------------------------------+
		// | String[] 	retrieveByDocument(String sessionId, int start, int howmany, String path, boolean indent, boolean xinclude, String highlight)
		// ; For the specified document, retrieve a set of query results from the last query executed within the current session.
		// +----------------------------------------------------------------------+
		// | Base64BinaryArray 	retrieveData(String sessionId, int start, int howmany, boolean indent, boolean xinclude, String highlight)
		// ; Retrieve a set of query results from the last query executed within the current session.
		// +----------------------------------------------------------------------+
		// | QueryResponse 	xquery(String sessionId, byte[] xquery)
		// ; Execute an XQuery.
  public function xquery($query) {
   if ( $this->getError() ) return false;
	  if (empty($query)) {
		  $this->setError("dctl.exist-api.dbException: query execution failed: query is empty");
		  return false;
	  };
	  try {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			  $query = base64_encode($query);
		  };
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'xquery' => $query );
		  $queryResponse = $this->soapCall('xquery', $parameters);
	  } catch( SoapFault $e ) {
		  $this->setError($e->faultstring);
		  return false;
	  };
	  if (is_object($queryResponse) && $queryResponse->hits > 0) {
		  //$xml = $this->_soapClient->retrieve($this->_session, 1, $queryResponse->hits, true, true, "both");
		  /*
		  <element name="sessionId" type="xsd:string"/>
		  <element name="start" type="xsd:int"/>
		  <element name="howmany" type="xsd:int"/>
		  <element name="indent" type="xsd:boolean"/>
		  <element name="xinclude" type="xsd:boolean"/>
		  <element name="highlight" type="xsd:string"/>
		  */
		  $parameters = array(
		  'sessionId' => $this->_session,
		  'start' => 1,
				'howmany' => $queryResponse->hits,
				'indent' => TRUE,
				'xinclude' => TRUE,
				'highlight' => $this->_highlight
				);
		  $xml = $this->soapCall('retrieve', $parameters);
	  } else {
//		  $this->_error = "dctl.exist-api.dbException: query execution warning: no data found";
		  return false;
	  };
	  $result = array(
	      "HITS" => $queryResponse->hits,
	      "COLLECTIONS" => $queryResponse->collections,
	      "QUERY_TIME" => $queryResponse->queryTime,
	      "XML" => $xml
	    );
	 return $result;
  }
		// +----------------------------------------------------------------------+
		// +----------------------------------------------------------------------+
		// | Admin.wsdl (http://exist.sourceforge.net/api/org/exist/soap/Admin.html)
		// +----------------------------------------------------------------------+
		// | String 	connect(String userId, String password)
		// ; Create a new user session.
		// +----------------------------------------------------------------------+
		// | void 	disconnect(String sessionId)
		// ; Release a user session.
		// +----------------------------------------------------------------------+
		// | void 	copyCollection(String sessionId, String collectionPath, String destinationPath, String newName)
		// ; Copy a collection to the destination collection and rename it.
		// +----------------------------------------------------------------------+
		// | void 	copyResource(String sessionId, String docPath, String destinationPath, String newName)
		// ; Copy a resource to the destination collection and rename it.
		// +----------------------------------------------------------------------+
		// | boolean 	createCollection(String sessionId, String path)
		// ; Create a new collection using the specified path.
		// +----------------------------------------------------------------------+
		// | byte[] 	getBinaryResource(String sessionId, String name)
		// ; Retrieve a binary resource from the database
		// +----------------------------------------------------------------------+
		// | CollectionDesc 	getCollectionDesc(String sessionId, String collectionName)
		// ; Obtain a description of the specified collection.
		// +----------------------------------------------------------------------+
		// | Strings 	getGroups(String sessionId)
		// ; Obtain a list of the defined database groups
		// +----------------------------------------------------------------------+
		// | IndexedElements 	getIndexedElements(String sessionId, String collectionName, boolean inclusive)
		// ; Return a list of Indexed Elements for a collection
		// +----------------------------------------------------------------------+
		// | Permissions 	getPermissions(String sessionId, String resource)
		// ; Return the permissions of the specified collection/document
		// +----------------------------------------------------------------------+
		// | UserDesc 	getUser(String sessionId, String user)
		// ; Obtain information about an eXist user.
		// +----------------------------------------------------------------------+
		// | UserDescs 	getUsers(String sessionId)
		// ; Get an list of users
		// +----------------------------------------------------------------------+
		// | String 	hasUserLock(String sessionId, String path)
		// ; Return the name of the user owning the lock on the specified resource
		// +----------------------------------------------------------------------+
		// | EntityPermissionsList 	listCollectionPermissions(String sessionId, String name)
		// ; Return a list of the permissions of the child collections of the specified parent collection
		// +----------------------------------------------------------------------+
		// | EntityPermissionsList 	listDocumentPermissions(String sessionId, String name)
		// ; Return a list of the permissions of the child documents of the specified parent collection
		// +----------------------------------------------------------------------+
		// | void 	lockResource(String sessionId, String path, String userName)
		// ; Place a write lock on the specified resource
		// +----------------------------------------------------------------------+
		// | void 	moveCollection(String sessionId, String collectionPath, String destinationPath, String newName)
		// ; Move a collection and its contents.
		// +----------------------------------------------------------------------+
		// | void 	moveResource(String sessionId, String docPath, String destinationPath, String newName)
		// ; Move a resource.
		// +----------------------------------------------------------------------+
		// | boolean 	removeCollection(String sessionId, String path)
		// ; Remove the specified collection.
		// +----------------------------------------------------------------------+
		// | boolean 	removeDocument(String sessionId, String path)
		// ; Remove the specified document.
		// +----------------------------------------------------------------------+
		// | void 	removeUser(String sessionId, String name)
		// ; Remove an eXist user account.
		// +----------------------------------------------------------------------+
		// | void 	setPermissions(String sessionId, String resource, String owner, String ownerGroup, int permissions)
		// ; Set the owner, group and access permissions for a document or collection
		// +----------------------------------------------------------------------+
		// | void 	setUser(String sessionId, String name, String password, Strings groups, String home)
		// ; Create a new user.
		// +----------------------------------------------------------------------+
		// | void 	store(String sessionId, byte[] data, String encoding, String path, boolean replace)
		// ; Store a new document into the database.
		// +----------------------------------------------------------------------+
		// | void 	storeBinary(String sessionId, byte[] data, String path, String mimeType, boolean replace)
		// ; Store a binary resource in the database
		// +----------------------------------------------------------------------+
		// | void 	unlockResource(String sessionId, String path)
		// ; Release the lock on the specified resource
		// +----------------------------------------------------------------------+
		// | int 	xupdate(String sessionId, String collectionName, String xupdate)
		// ; Apply a set of XUpdate modifications to a collection.
		// +----------------------------------------------------------------------+
		// | int 	xupdateResource(String sessionId, String documentName, String xupdate)
		// ; Apply a set of XUpdate modifications to the specified document.
		// +----------------------------------------------------------------------+
		// +----------------------------------------------------------------------+


	// GET RESOURCE INFO
	public function getCollectionDesc($document) {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'collectionName' => $document);
		return $this->soapCall('getCollectionDesc', $parameters);
	}

	// GET PERMISSIONS
	public function getPermissions($document) {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'resource' => $document);
		return $this->soapCall('getPermissions', $parameters);
	}

	// SET PERMISSIONS
	public function setPermissions($document, $owner, $group, $permissions) {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'resource' => $document, 'owner' => $owner, 'ownerGroup' => $group, 'permissions' => $permissions);
		return $this->soapCall('setPermissions', $parameters);
	}

	// CREATE COLLECTION
	public function createCollection($path = '') {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'path' => $path);
		return $this->soapCall('createCollection', $parameters);
	}

	// REMOVE COLLECTION
	public function removeCollection($path = '') {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'path' => $path);
		return $this->soapCall('removeCollection', $parameters);
	}

	// REMOVE DOCUMENT
	public function removeDocument($path = '') {
  if ($this->getError()) return false;
		$parameters = array('sessionId' => $this->_session, 'path' => $path);
		return $this->soapCall('removeDocument', $parameters);
	}

	// UPLOAD RESOURCE
	public function uploadResource($location, $document) {
  if ($this->getError()) return false;
	$handle = fopen($document, "rb");
		$document_content = fread($handle, filesize($document));
		fclose($handle);
  $encoding = 'UTF-8';

	 if (empty($document_content)) {
		 $this->_error = "dctl.exist-api.dbException: document upload failed: document is empty";
		 return false;
	 }
		try {
			// encode only to base64 if php version lesser than 5.1
			// patch by Bastian Gorke bg/at\ipunkt/dot\biz
			if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
				$document_content = base64_encode($document_content);
			}
	 	$parameters = array('sessionId' => $this->_session, 'data' => $document_content, 'encoding' => $encoding, 'path' => $location, 'replace' => TRUE);
			return $this->soapCall('store', $parameters);
		}
		catch( SoapFault $e) {
			$this->setError($e->faultstring);
			return false;
		}
	}


}



/**
 * existAdmin Class
 *
 * This class is the main interface for manipulating the collections and documents into the exist XML:DB.
 *
 * basic usage:<code>
 * include "existAdmin.php";
 * $db = new existAdmin('guest', 'guest', 'http://127.0.0.1:8080/exist/services/Admin?wsdl');
 * $db->connect() or die ($db->getError());
 *
 * // Store Document
 * echo $db->store('<simple><fxp>franois</fxp></simple>',
 * 				'UTF-8',
 * 				'/db/test.xml', true);
 * echo $db->store('<simple><fxp>franois</fxp></simple>',
 * 				'UTF-8',
 * 				'/db/test2suppr.xml', true);
 * // Remove Document
 * echo $db->removeDocument('/db/test2suppr.xml');
 *
 * // Create collection
 * echo $db->createCollection('/db/existAdminDemo');
 * echo $db->createCollection('/db/existAdminDemo2supp');
 *
 * // Remove collection
 * echo $db->removeCollection('/db/existAdminDemo2supp');
 *
 * // XupdateResource
 * $xupdate = "<xupdate:modifications version='1.0' xmlns:xupdate='http://www.xmldb.org/xupdate'>".
 * 		"<xupdate:update select='/simple/fxp'>TITI</xupdate:update></xupdate:modifications>";
 *
 * echo $db->xupdateResource('/db/test.xml', $xupdate);
 *
 * $db->disconnect() or die ($db->getError());
 * </code>
 *
 * @access   public
 */

class existAdmin extends  exist
{
  public function __construct($user="guest", $password="guest", $wsdl="http://localhost:8080/exist/services/Admin?wsdl")
  {
  $this->_user = $user;
	  $this->_password = $password;
	  $this->_wsdl = $wsdl;

	  $this->_soapClient = new SoapClient ($this->_wsdl, array("user_agent"=>""));
  }

  public function __destruct() {
  }

/*
 * Store
 *    <element name="store">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="data" type="xsd:base64Binary"/>
 *      <element name="encoding" type="xsd:string"/>
 *      <element name="path" type="xsd:string"/>
 *      <element name="replace" type="xsd:boolean"/>
 *     </sequence>
 *    </complexType>
 *   </element>
 */
  public function store($data, $encoding = "UTF-8", $path = "/db", $replace = false)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($data) )
	  {
		  $this->_error = "ERROR: No data to load !";
		  return false;
	  }

	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$data = base64_encode($data);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session ,
							'data' => $data,
							'encoding' => $encoding,
							'path' => $path,
							'replace' => $replace );

		  $queryResponse = $this->soapCall('store', $parameters);

	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }

	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return true;
  }

/*
 * createCollection
 *
 *  <element name="createCollection">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="path" type="xsd:string"/>
 *     </sequence>
 *   </complexType>
 */
  public function createCollection($path)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($path) )
	  {
		  $this->_error = "ERROR: path is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'path' => $path);

		  $queryResponse = $this->soapCall('createCollection', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }

	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }
 	  return $queryResponse->createCollectionReturn;
  }

/*
 * describeCollection
 *
 *  <element name="describeCollection">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="path" type="xsd:string"/>
 *     </sequence>
 *   </complexType>
 */
  public function describeCollection ($path)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($path) )
	  {
		  $this->_error = "ERROR: path is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'path' => $path);

		  $queryResponse = $this->soapCall('describeCollection', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }


	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return $queryResponse->describeCollectionReturn;
  }

/*
 * removeCollection
 *
 *  <element name="removeCollection">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="path" type="xsd:string"/>
 *     </sequence>
 *   </complexType>
 */
  public function removeCollection($path)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($path) )
	  {
		  $this->_error = "ERROR: path is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'path' => $path);

		  $queryResponse = $this->soapCall('removeCollection', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }


	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return $queryResponse->removeCollectionReturn;
  }

/*
 * removeDocument
 *
 *  <element name="removeDocument">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="path" type="xsd:string"/>
 *     </sequence>
 *   </complexType>
 */
  public function removeDocument($path)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($path) )
	  {
		  $this->_error = "ERROR: path is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  $parameters = array('sessionId' => $this->_session , 'path' => $path);

		  $queryResponse = $this->soapCall('removeDocument', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }


	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return $queryResponse->removeDocumentReturn;
  }

/*
 * xupdateResource
 *
 *   <element name="xupdate">
 *    <complexType>
 *     <sequence>
 *      <element name="sessionId" type="xsd:string"/>
 *      <element name="collectionName" type="xsd:string"/>
 *      <element name="xupdate" type="xsd:string"/>
 *     </sequence>
 *    </complexType>
 */
  public function xupdate($collectionName, $xupdate)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($xupdate) )
	  {
		  $this->_error = "ERROR: Xupdate query is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'collectionName' => $collectionName, 'xupdate' => $xupdate );

		  $queryResponse = $this->soapCall('xupdate', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }

	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return $queryResponse->xupdateReturn;
  }

/*
 * xupdateResource
 *
 * <element name="xupdateResource">
 *   <complexType>
 *    <sequence>
 *     <element name="sessionId" type="xsd:string"/>
 *     <element name="documentName" type="xsd:string"/>
 *     <element name="xupdate" type="xsd:string"/>
 *    </sequence>
 *   </complexType>
 *   </element>
 */
  public function xupdateResource($documentName, $xupdate)
  {
	  if ( $this->getError() )
	  	return false;
	  if ( empty($xupdate) )
	  {
		  $this->_error = "ERROR: Xupdate query is empty!";
		  return false;
	  }
	  try
	  {
		  // encode only to base64 if php version lesser than 5.1
		  // patch by Bastian Gorke bg/at\ipunkt/dot\biz
		  if (!version_compare(PHP_VERSION, '5.1.0', 'ge')) {
			$xupdate = base64_encode($xupdate);
		  }
		  //$queryResponse = $this->_soapClient->xquery($this->_session, $queryBase64);
		  $parameters = array('sessionId' => $this->_session , 'documentName' => $documentName, 'xupdate' => $xupdate );

		  $queryResponse = $this->soapCall('xupdateResource', $parameters);
	  }
	  catch( SoapFault $e )
	  {
		  $this->setError($e->faultstring);
		  return false;
	  }

	  if ( $this->_debug && is_object($queryResponse) )
	  {
		  // xquery call Result
		  print "===========================================================================";
		  print "<p><b>Result of the <i>store</i> SOAP call (in PHP array format)</b></p>";
		  print "===========================================================================";
		  print "<p>\$queryResponse:<p><pre>";
		  print_r($queryResponse);
		  print "</pre>";
		  print "===========================================================================";
	  }

 	  return $queryResponse->xupdateResourceReturn;
  }

  // TODO : getCollectionDesc
  // TODO : getBinaryResource
}
?>
