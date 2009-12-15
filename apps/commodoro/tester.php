<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');

require_once(str_replace('//','/',dirname(__FILE__).'/').'../_shared/simpletest/web_tester.php');
	SimpleTest::prefer(new HtmlReporter());
 // |
	function simplexmlloadstring ($xml) {
	$xml = preg_replace('/xml\:id/', 'id', $xml);
	$simplexml = @simplexml_load_string($xml);
 $simplexml['xmlns'] = 'http://www.tei-c.org/ns/1.0';

	$simplexml->registerXPathNamespace('tei', 'http://www.tei-c.org/ns/1.0');

	$namespaces = $simplexml->getDocNamespaces();
	foreach ($namespaces as $nsk=>$ns) {
		if ($nsk == '') $nsk = 'tei';
		$simplexml->registerXPathNamespace($nsk, $ns);
	};
	return $simplexml;
	}
 // |
	function formatIt ($p, $c, $t, $x) {
		$returnText = '';
		$returnText .= '<span class="'.($t ? 'ok' : 'error').'">- '.$p.'("'.$c.'") = ';
		$returnText .= gettype($x).'#'.count($x);
//		$returnText .= htmlspecialchars(substr((string)$x,0,80),ENT_QUOTES);
		$returnText .= '</span><br />';
		echo $returnText;
	};
 // |
/**
// +----------------------------------------------------------------------+
// | Legenda
// +----------------------------------------------------------------------+
// | 1) record vuoto (impossibile identificare una risorsa XML):
// |  <?xml version="1.0" encoding="UTF-8"?>
// |  <dctl version="_VERSION_"/>
// |
// +----------------------------------------------------------------------+
// | 2) nessun risultato (identificata la risorsa XML, impossibile identificare un nodo che risponda ai criteri, anche id() ):
// |  <?xml version="1.0" encoding="UTF-8"?>
// |  <dctl version="0.6.03">
// |   <resource>
// |   <_CONTENT_ />
// |
// +----------------------------------------------------------------------+
// | 3) risultato (identificata la risorsa XML, identificato almeno un nodo che risponda ai criteri):
// |  <?xml version="1.0" encoding="UTF-8"?>
// |  <dctl version="0.6.03">
// |   <resource>
// |   <_CONTENT_>
// |    ...
// |   </_CONTENT_>
// |
// +----------------------------------------------------------------------+
// | valori per _CONTENT_
// +----------------------------------------------------------------------+
// | getStructure:
// | - "fragment" : per contenere nodi di risultato in formato TEI
// | - "error" : per errori di sintassi in xpath, con segnalazione
// | getOptions:
// | - "list" : per contenere nodi di risultato come lista di nodi <item> con contenuto TEI  e non
// | - "error" : per errori di sintassi in xpath, con segnalazione
// | getBlock:
// | - "fragment" : per contenere nodi di risultato in formato TEI
// | - "error" : per errori di sintassi in xpath, con segnalazione
// | getLinks:
// | - "link" : per contenere nodi di risultato in formato group/anchor
// | - "error" : per errori di sintassi in xpath, con segnalazione
// | getMaps:
// | - "link" : per contenere nodi di risultato in formato group/anchor
// | - "error" : per errori di sintassi in xpath, con segnalazione
// |
*/
// +----------------------------------------------------------------------+
// | START OF TEST
// +----------------------------------------------------------------------+
			class CoreTester_Repository extends WebTestCase {
 		 function __construct () { echo '<hr/><b>dCTL : repo status </b>'.'<br />'; }
				//
				function Test_RepoHasStructure() {
     global $dCTL;
     $tChk = $dCTL->__get('web_publish_path');
     $tRes = UnitTestCase::assertPattern('/http:\/\/.+\/db\/.+/', $tChk);
     echo '- [www] '.$tChk.'<br />';
				}
				//
				function Test_eXistHasStructure() {
     global $dCTL;
     $tChk = $dCTL->__get('db_publish_path');
     $tRes = UnitTestCase::assertPattern('/\/db\/.+/', $tChk);
     echo '- [exist] '.$tChk.'<br />';
				}
			}
// +----------------------------------------------------------------------+
		class CoreTester_getStructure extends WebTestCase {
		 public $testIt = FALSE;
			function __construct () {
				global $dCTL;
			 echo '<hr/><b>dCTL : AFD </b>'.'<br />';
				// 	• getStructure ("afd"); // collection esistente => risultato
				$tChk = 'afd';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = count($tRes);
				$this->testIt = $t;
    if (!$t) {
     echo '<span class="error">Collection "AFD" not published...untested!</span><br/>';
    };
			}
			public function __destruct() {
				unset($this);
			}
			// getStructure
			// > getStructure() vuole uno o piu URI del tipo xml://collection/ oppure  xml://collection/package/ oppure xml://collection/package/id
			function Test_getStructure() {
    if ($this->testIt) {
				global $dCTL;
				//
				echo '[1] "collection" level (xml://collection/) <br/>';

				// 	• getStructure (""); // nessuna collection => record vuoto
				$tChk = '';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("*"); // tutte le collection => risultato
				$tChk = '*';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("_ERR_"); // collection inesistente => record vuoto
				$tChk = '_ERR_';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd"); // collection esistente => risultato
				$tChk = 'afd';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//
				echo '[2.1] "package" level (xml://collection/package) <br/>';
				// afd/* equivale a afd/

				//  • getStructure ("afd/*"); // tutti i package => risultato
				$tChk = 'afd/*';
				$tXPath = 'resource[kind="collection"]/packages/resource';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//
				echo '[2.2] "package" level (xml://collection/package) <br/>';

				//  • getStructure ("afd/_ERR_"); // package inesistente => record vuoto
				$tChk = 'afd/_ERR_';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//  • getStructure ("afd/marmi_*"); // package esistente => risultato
				$tChk = 'afd/marmi_*';
				$tXPath = 'resource[kind="package"][contains(ref, "marmi_")]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//  • getStructure ("afd/marmo_*"); // package inesistente => record vuoto
				$tChk = 'afd/marm_ERR_*';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//  • getStructure ("afd/*_txt"); // package esistente => risultato
				$tChk = 'afd/*_txt';
				$tXPath = 'resource[kind="package"][contains(ref, "_txt")]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//  • getStructure ("afd/*_ERR"); // package inesistente => record vuoto
				$tChk = 'afd/*_ERR';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//
				echo '[3] "id" level (xml://collection/package/id) <br/>';

				// 	• getStructure ("afd/marmi_txt/*"); // id inesistente => nessun risultato in <fragment />
				$tChk = 'afd/marmi_txt/*';
				$tXPath = 'resource[kind="tei" and fragment]/fragment/*';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt/_ERR_"); // id inesistente => nessun risultato in <fragment />
				$tChk = 'afd/marmi_txt/_ERR_';
				$tXPath = 'resource[kind="tei" and fragment]/fragment/*';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt/xpb000001"); // id esistente => risultato in <fragment> ... </fragment>
				$tChk = 'afd/marmi_txt/xpb000001';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="xpb000001"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

    //
    echo '[4.1] "?query" level (xml://collection/package?query) <br/>';

				// 	• getStructure ("afd/marmi_txt?_ERR_"); // nodo non trovato  => no <fragment> ... </fragment>
				// segue...
				$tChk = 'afd/marmi_txt?_ERR_';
				$tXPath = 'resource[kind="package" and fragment]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// ...segue
				// 	• getStructure ("afd/marmi_txt?_ERR_"); // errore di sintassi in xpath => messaggio di errore in <error> ... </error>
				$tChk = 'afd/marmi_txt?_ERR_';
				$tXPath = 'resource[kind="package" and not(fragment)]/error';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

    // 	• getStructure ("afd/marmi_txt?//*[@ana &= "genre_short"]"); // nodo trovato  => risultato in <fragment> ... </fragment>
				$tChk = 'afd/marmi_txt?//*[@ana &= "genre_short"]';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[contains(@ana,"genre_short")]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//
				echo '[4.2] "?query" level (xml://collection/package/id?query) <br/>';

				// 	• getStructure ("afd/marmi_txt/xpb000001?_ERR_"); // errore di sintassi in xpath => messaggio di errore in <error> ... </error>
				$tChk = 'afd/marmi_txt/xpb000001?_ERR_';
				$tXPath = 'resource[kind="package" and not(fragment)]/error';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt/_ERR_?_ERR_"); // nodo non trovato  => nessun risultato in <fragment> ... </fragment>
				// segue...
				$tChk = 'afd/marmi_txt/_ERR_?_ERR_';
				$tXPath = 'resource[kind="package" and fragment]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// 	• getStructure ("afd/marmi_txt/_ERR_?_ERR_"); // errore di sintassi in xpath => messaggio di errore in <error> ... </error>
				$tChk = 'afd/marmi_txt/_ERR_?_ERR_';
				$tXPath = 'resource[kind="package" and not(fragment)]/error';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				//
    echo '[5] "#anchor" level (xml://collection/package/id#anchor / xml://collection/package/id?query#anchor) <br />';

				// 	• getStructure ("afd/marmi_txt#_ERR_"); // richiesta non riconosciuta => richiesta ignorata, equivale alla richiesta con #div
				$tChk = 'afd/marmi_txt#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#_ERR_';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#div"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#div1"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#div1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#div2"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#div2';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#div100"); // nodo non trovato => nessun risultato in <fragment />
				$tChk = 'afd/marmi_txt#div100';
				$tXPath = 'resource[kind="tei" and fragment]/fragment/*';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#div@1"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#div@1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#div@100"); // nodo non trovato => nessun risultato in <fragment />
				$tChk = 'afd/marmi_txt#div@8';
				$tXPath = 'resource[kind="tei" and fragment]/fragment/*';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#div1@3"); // richiesta non riconosciuta => richiesta ignorata, equivale alla richiesta senza @3
				$tChk = 'afd/marmi_txt#div1@3';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#div1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#div@1;3"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#div@1;3';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 3);
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#div@1;-1"); // equivale alla richiesta senza @1;-1
				$tChk = 'afd/marmi_txt#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#div@1;-1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#div@1;100"); // equivale alla richiesta senza @1;100
				$tChk = 'afd/marmi_txt#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#div@1;100';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#pb"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#pb1");  // richiesta non riconosciuta => richiesta ignorata, equivale alla richiesta senza #pb1 ma con #div
				$tChk = 'afd/marmi_txt#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#pb1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#pb@1"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#pb@1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

				// 	• getStructure ("afd/marmi_txt#pb@100000"); // nodo non trovato => nessun risultato in <fragment />
				$tChk = 'afd/marmi_txt#pb@100000';
				$tXPath = 'resource[kind="tei" and fragment]/fragment/*';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#pb@1;3"); // nodo trovato => risultato in <fragment />
				$tChk = 'afd/marmi_txt#pb@1;3';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 3);
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#pb@1;-1"); // equivale alla richiesta senza @1;-1
				$tChk = 'afd/marmi_txt#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#pb@1;-1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

			// 	• getStructure ("afd/marmi_txt#pb@1;100"); // equivale alla richiesta senza @1;100
				$tChk = 'afd/marmi_txt#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
// 				$t = true;
// 				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
// 				$t &= UnitTestCase::assertTrue(count($tRes));
// 				formatIt('getStructure', $tChk, $t, $tRes);
				$t2 = count($tRes);
				$tChk = 'afd/marmi_txt#pb@1;100000';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				$t1 = count($tRes);
				$t = true;
				$t &= UnitTestCase::assertTrue($t1 == $t2);
				$tRes = TRUE;
				formatIt('getStructure', $tChk, $t, $tRes);

/** MISSING
    "#" anchor "$(hier)" : returns all the nodes identified by anchor in their own <div> hierarchy
    "#" anchor "$(page)" : returns all the nodes identified by anchor with page reference in @synch
    "#" anchor "$(hier:page)" : returns all the nodes identified by anchor in their own <div> hierarchy with page reference in @synch
*/

   }
		}
		}
// +----------------------------------------------------------------------+

/** MISSING
    xloc: "#" attr "@" expr ";" x  : returns, from items evaluated via expr included, the x values of attr
*/

/**
			class CoreTester_getOptions extends WebTestCase {
 		 function __construct () { echo '<hr/><b>dCTL : AFD </b>'.'<br />'; }
// getOptions
// > getOptions() vuole uno o piu URI del tipo xml://collection/package/ oppure xml://collection/package/id
// > getOptions() vuole un XPath valido
// [1] "package" level (xml://collection/)
// 	• getOptions ("", ... ); // nessun package => record vuoto
// 	• getOptions ("afd/marmi_txt", "_ERR_" ); // errore di sintassi in xpath => messaggio di errore in <error> ... </error>
// 	• getOptions ("afd/marmi_txt", "@ana &= "_ERR_"" );  // nodo non trovato  => nessun risultato in <list />
// 	• getOptions ("afd/marmi_txt", "@ana &= "syntexp*"" );  // nodo trovato  => risultato in <list> ... </list>
			}
*/
// +----------------------------------------------------------------------+

/** MISSING
    "#div" : returns the upper ancestor <div> for the node (same as #div1) NOT YET IMPLEMENTED
    "#div" n : returns the ancestor <div> of absolute level n for the node NOT YET IMPLEMENTED
    "#pb" : returns the "containing" <pb> for the node NOT YET IMPLEMENTED
*/

/**
			class CoreTester_getBlock extends WebTestCase {
 		 function __construct () { echo '<hr/><b>dCTL : AFD </b>'.'<br />'; }
// getBlock
// > getBlock() vuole uno o piu URI del tipo xml://collection/package/id
// [1] "id" level (xml://collection/package/id)
// 	• getBlock ("afd/marmi_txt/_ERR_"); // id inesistente => nessun risultato in <fragment />
// 	• getBlock ("afd/marmi_txt/xpb000001"); // id esistente => risultato in <fragment> ... </fragment>
			}
*/
// +----------------------------------------------------------------------+

/** MISSING
    "#div" : returns the upper ancestor <div> for the node (same as #div1) NOT YET IMPLEMENTED
    "#div" n : returns the ancestor <div> of absolute level n for the node NOT YET IMPLEMENTED
    "#pb" : returns the "containing" <pb> for the node NOT YET IMPLEMENTED
*/

/**
			class CoreTester_getLinks extends WebTestCase {
 		 function __construct () { echo '<hr/><b>dCTL : AFD </b>'.'<br />'; }
// getLinks
// > getLinks() vuole uno o piu URI del tipo xml://collection/package/id
// 	• getLinks ("afd/marmi_txt/_ERR_"); // id inesistente => nessun risultato in <link />
// 	• getLinks ("afd/marmi_txt/p000f001"); // id esistente => risultato in <link> ... </link >
			}
*/
// +----------------------------------------------------------------------+

/** MISSING
    ref : one or more tailored URIs as string, CSV string or array
*/

/**
			class CoreTester_getMaps extends WebTestCase {
 		 function __construct () { echo '<hr/><b>dCTL : AFD </b>'.'<br />'; }
	// getMaps
// > getMaps() vuole uno o piu URI del tipo xml://collection/package/id
// 	• getMaps ("afd/marmi_txt/_ERR_"); // id inesistente => nessun risultato in <link />
// 	• getMaps ("afd/marmi_img/p004ki001"); // id esistente => risultato in <link> ... </link >
			}
*/
// +----------------------------------------------------------------------+
			class CoreTester_Simone extends WebTestCase {
 // |
			function __construct () {
				global $dCTL;
			 echo '<hr/><b>Simone : TEST </b>'.'<br />';
				// 	• getStructure ("afd"); // collection esistente => risultato
				$tChk = 'test';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = count($tRes);
				$this->testIt = $t;
    if (!$t) {
     echo '<span class="error">Collection "TEST" not published...untested!<br/>';
     $this->__destruct();
     unset($this);
    };
			}
			public function __destruct() {
				unset($this);
			}
 // |
				function Test_getStructure() {
    if ($this->testIt) {
				global $dCTL;
				// Lista delle risorse presenti nella collection di test ::: xml://test/_txt
				$tChk = 'xml://test/_txt';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 2);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Informazioni su due collezioni: marmi e mondi ::: xml://test/marmi_txt,mondi_txt
				$tChk = 'xml://test/marmi_txt,xml://test/mondi_txt';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 2);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Prima pb dei marmi ::: xml://test/marmi_txt/#pb@1
				$tChk = 'xml://test/marmi_txt/#pb@1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb[@id="xpb000001"]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Una immagine di una scheda immagine ::: img://afd-marmi_p1_03_pw.jpg
				$tChk = 'img://afd-marmi_p1_03_pw.jpg';
				$tXPath = 'resource[contains(./kind,"media") and ./ref and ./icon and ./file]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Una anastatica ::: img://afd-marmi_partei_p_007.jpg
				$tChk = 'img://afd-marmi_partei_p_007.jpg';
				$tXPath = 'resource[contains(./kind,"media") and ./ref and ./icon and ./file]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Primo livello dell\'indice dei marmi: parti in cui son divisi i marmi ::: xml://test/marmi_txt/#div
				$tChk = 'xml://test/marmi_txt/#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Secondo livello indice dei marmi: capitoli in cui e\' divisa la prima parte ::: xml://test/marmi_txt/xdv000003#div
				$tChk = 'xml://test/marmi_txt/xdv000003#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Terzo livello dell\'indice dei marmi: sezioni in cui e\' diviso il primo capitolo ::: xml://test/marmi_txt/xdv000006#div
				$tChk = 'xml://test/marmi_txt/xdv000006#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quarto livello (ed ultimo) dell\'indice dei marmi: cerco prima delle div, se non le trovo cerco dei pb ::: xml://test/marmi_txt/xdv000007#div
				$tChk = 'xml://test/marmi_txt/xdv000007#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(gettype($tRes), gettype(FALSE));
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quinto livello (foglie) dell\'indice dei marmi: non trovando div, cerco pb ::: xml://test/marmi_txt/xdv000007#pb
				$tChk = 'xml://test/marmi_txt/xdv000007#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Primo livello dell\'indice dei marmi per genere novella (short): lista delle parti ::: xml://test/marmi_txt/?//*[@ana &= "genre_short"]#div1
				$tChk = 'xml://test/marmi_txt/?//*[@ana &= "genre_short"]#div1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Secondo livello dell\'indice dei marmi per genere novella (short): lista dei capitoli della parte 1 ::: xml://test/marmi_txt/xdv000003?//*[@ana &= "genre_short"]#div2
				$tChk = 'xml://test/marmi_txt/xdv000003?//*[@ana &= "genre_short"]#div2';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Terzo livello dell\'indice dei marmi per genere novella (short): lista delle sezioni del capitolo 1 ::: xml://test/marmi_txt/xdv000006?//*[@ana &= "genre_short"]#div3
				$tChk = 'xml://test/marmi_txt/xdv000006?//*[@ana &= "genre_short"]#div3';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quarto livello dell\'indice dei marmi per genere novella (short): lista delle div dentro alla sezione 1.1. Non trovando piu\' div, si cercano i pb ::: xml://test/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#div4
				$tChk = 'xml://test/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#div4';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Ultimo livello indice dei marmi per genere novella: pagine dentro a parte1, cap 1, sez 1.1 ::: xml://test/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#pb
				$tChk = 'xml://test/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Indice dei marmi per genere facezia: vista appiattita ::: xml://test/marmi_txt?//*[@ana &= "genre_anecdote"]#pb$(hier)
				$tChk = 'xml://test/marmi_txt?//*[@ana &= "genre_anecdote"]#pb$(hier)';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//div[pb]/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
			}
 // |
			function Test_getBlock() {
				global $dCTL;
				global $resultXML;
				$resultXML .= '<?xml ?>';

				// La prima pagina dei marmi ::: xml://test/marmi_txt/xpb000001
				$tChk = 'xml://test/marmi_txt/xdv000099';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id = "xdv000099"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
    $tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// La prima pagina dei marmi ::: xml://test/marmi_txt/xpb000001
				$tChk = 'xml://test/marmi_txt/xpb000001';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id = "xpb000001"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Una scheda immagine ::: xml://test/marmi_img/p002pt001pg008
				$tChk = 'xml://test/marmi_img/p002pt001pg008';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p002pt001pg008"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
    $tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Scheda immagine Marmi, parte 1, pag 16 ::: xml://test/marmi_img/p003pt001pg016
				$tChk = 'xml://test/marmi_img/p003pt001pg016';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p003pt001pg016"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Una scheda immagine nei Mondi, collegata alla figura di marmi pag 16 ::: xml://test/mondi_img/p015mn001cc017
				$tChk = 'xml://test/mondi_img/p015mn001cc017';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p015mn001cc017"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				//
    }
			}
			}
// +----------------------------------------------------------------------+
			class CoreTester_SimoneFixed extends WebTestCase {
 // |
			function __construct () {
				global $dCTL;
			 echo '<hr/><b>dCTL : Simone Test => AFD </b>'.'<br />';
				// 	• getStructure ("afd"); // collection esistente => risultato
				$tChk = 'afd';
				$tXPath = 'resource[kind="collection"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = count($tRes);
				$this->testIt = $t;
    if (!$t) {
     echo '<span class="error">Collection "AFD" not published... untested!</span><br/>';
     $this->__destruct();
     unset($this);
    };
			}
			public function __destruct() {
				unset($this);
			}
 // |
				function Test_getStructure() {
    if ($this->testIt) {
				global $dCTL;
				// Lista delle risorse presenti nella collection di afd ::: xml://afd/_txt
				$tChk = 'xml://afd/_txt';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 2);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Informazioni su due collezioni: marmi e mondi ::: xml://afd/marmi_txt,mondi_txt
				$tChk = 'xml://afd/marmi_txt,xml://afd/mondi_txt';
				$tXPath = 'resource[kind="package"]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 2);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Prima pb dei marmi ::: xml://afd/marmi_txt/#pb@1
				$tChk = 'xml://afd/marmi_txt/#pb@1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb[@id="xpb000001"]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Una immagine di una scheda immagine ::: img://afd-marmi_p1_03_pw.jpg
				$tChk = 'img://afd-marmi_p1_03_pw.jpg';
				$tXPath = 'resource[contains(./kind,"media") and ./ref and ./icon and ./file]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Una anastatica ::: img://afd-marmi_partei_p_007.jpg
				$tChk = 'img://afd-marmi_partei_p_007.jpg';
				$tXPath = 'resource[contains(./kind,"media") and ./ref and ./icon and ./file]';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes) == 1);
				formatIt('getStructure', $tChk, $t, $tRes);
				// Primo livello dell\'indice dei marmi: parti in cui son divisi i marmi ::: xml://afd/marmi_txt/#div
				$tChk = 'xml://afd/marmi_txt/#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Secondo livello indice dei marmi: capitoli in cui e\' divisa la prima parte ::: xml://afd/marmi_txt/xdv000003#div
				$tChk = 'xml://afd/marmi_txt/xdv000003#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Terzo livello dell\'indice dei marmi: sezioni in cui e\' diviso il primo capitolo ::: xml://afd/marmi_txt/xdv000006#div
				$tChk = 'xml://afd/marmi_txt/xdv000006#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quarto livello (ed ultimo) dell\'indice dei marmi: cerco prima delle div, se non le trovo cerco dei pb ::: xml://afd/marmi_txt/xdv000007#div
				$tChk = 'xml://afd/marmi_txt/xdv000007#div';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
    $tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(gettype($tRes), gettype(FALSE));
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quinto livello (foglie) dell\'indice dei marmi: non trovando div, cerco pb ::: xml://afd/marmi_txt/xdv000007#pb
				$tChk = 'xml://afd/marmi_txt/xdv000007#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Primo livello dell\'indice dei marmi per genere novella (short): lista delle parti ::: xml://afd/marmi_txt/?//*[@ana &= "genre_short"]#div1
				$tChk = 'xml://afd/marmi_txt/?//*[@ana &= "genre_short"]#div1';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Secondo livello dell\'indice dei marmi per genere novella (short): lista dei capitoli della parte 1 ::: xml://afd/marmi_txt/xdv000003?//*[@ana &= "genre_short"]#div2
				$tChk = 'xml://afd/marmi_txt/xdv000003?//*[@ana &= "genre_short"]#div2';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Terzo livello dell\'indice dei marmi per genere novella (short): lista delle sezioni del capitolo 1 ::: xml://afd/marmi_txt/xdv000006?//*[@ana &= "genre_short"]#div3
				$tChk = 'xml://afd/marmi_txt/xdv000006?//*[@ana &= "genre_short"]#div3';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Quarto livello dell\'indice dei marmi per genere novella (short): lista delle div dentro alla sezione 1.1. Non trovando piu\' div, si cercano i pb ::: xml://afd/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#div4
				$tChk = 'xml://afd/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#div4';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/div';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertFalse(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Ultimo livello indice dei marmi per genere novella: pagine dentro a parte1, cap 1, sez 1.1 ::: xml://afd/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#pb
				$tChk = 'xml://afd/marmi_txt/xdv000007?//*[@ana &= "genre_short"]#pb';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
				// Indice dei marmi per genere facezia: vista appiattita ::: xml://afd/marmi_txt?//*[@ana &= "genre_anecdote"]#pb$(hier)
				$tChk = 'xml://afd/marmi_txt?//*[@ana &= "genre_anecdote"]#pb$(hier)';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//div[pb]/pb';
				$tCmd = $dCTL->getStructure($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getStructure', $tChk, $t, $tRes);
			}
			}
 // |
			function Test_getBlock() {
    if ($this->testIt) {
				global $dCTL;
				// La prima pagina dei marmi ::: xml://afd/marmi_txt/xpb000001
				$tChk = 'xml://afd/marmi_txt/xdv000099';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id = "xdv000099"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
    $tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// La prima pagina dei marmi ::: xml://afd/marmi_txt/xpb000001
				$tChk = 'xml://afd/marmi_txt/xpb000001';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id = "xpb000001"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Una scheda immagine ::: xml://afd/marmi_img/p002pt001pg008
				$tChk = 'xml://afd/marmi_img/p002pt001pg008';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p002pt001pg008"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
    $tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Scheda immagine Marmi, parte 1, pag 16 ::: xml://afd/marmi_img/p003pt001pg016
				$tChk = 'xml://afd/marmi_img/p003pt001pg016';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p003pt001pg016"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				// Una scheda immagine nei Mondi, collegata alla figura di marmi pag 16 ::: xml://afd/mondi_img/p015mn001cc017
				$tChk = 'xml://afd/mondi_img/p015mn001cc017';
				$tXPath = 'resource[kind="tei" and fragment/*]/fragment//*[@id="p015mn001cc017"][(descendant-or-self::pb) or (ancestor-or-self::pb or preceding::pb or preceding-sibling::pb)]';
				$tCmd = $dCTL->getBlock($tChk);
				$tXml = simplexmlloadstring($tCmd);
				$tRes = is_array($tRes=$tXml->xpath($tXPath)) ? $tRes : array();
				$t = true;
				$t &= UnitTestCase::assertIsA($tXml, 'SimpleXMLElement');
				$t &= UnitTestCase::assertTrue(count($tRes));
				formatIt('getBlock', $tChk, $t, $tRes);
				//
    }
			}
			}
// +----------------------------------------------------------------------+
?>
