<?php
 if (!defined('_INCLUDE')) die('"'.__FILE__.'" not executable... me, i abort.');
 $returnText = '';
 define('AJAX', 'api.php');
 // +----------------------------------------------------------------------+
 $returnText .= '<script type="text/javascript">';
 // | debug=1, no debug=0
 $returnText .= 'var debug = 1;';
 // | private=1, public=0
 $returnText .= 'var private = 1;';
 $returnText .= '</script>';
 // +----------------------------------------------------------------------+
 $returnText .= '<style>';
 $returnText .= '.sh_hide { display: none !important; } ';
 $returnText .= '</style>';
 $returnText .= '<script type="text/javascript" language="javascript">';
 $returnText .= '$().ready(function () {';
 $returnText .= ' $("input:text").change(function () { ';
 $returnText .= '  idx = "b"+$(this).attr("id").replace("uri","").replace("xpath","") ;';
 $returnText .= '  $("#"+idx).click();';
 $returnText .= ' });';
 $returnText .= '});';
 $returnText .= ' function refreshMe() { ';
 $returnText .= ' window.location="#s";
 SyntaxHighlighter.all();
 $("h3.ok").next(".syntaxhighlighter").addClass("sh_hide").end().click(function(){$(this).next().toggleClass("sh_hide");});killProgress(); ';
 $returnText .= '};';
 $returnText .= '</script>';
 // | end JS
 // +----------------------------------------------------------------------+
	$returnText .='<div id="manager_repo" class="layout clearfix">';
 // +----------------------------------------------------------------------+
 $returnText .= '<h2>dCTL API :: try me</h2>';
	$returnText .= '<div>';
	$returnText .= '<p>Documentation available at: <a target="wiki" href="http://net7sviluppo.com/trac/ctl/wiki" title="wiki">http://net7sviluppo.com/trac/ctl/wiki</a></p>';
	$returnText .= '<p>Read carefully: <a target="wiki" href="http://net7sviluppo.com/trac/ctl/wiki/dCTL-ENG" title="wiki">http://net7sviluppo.com/trac/ctl/wiki/dCTL-ENG</a> to know what the "core" is</p>';
	$returnText .= '</div>';
	$returnText .= '<br />';

 $returnText .= '<form>';

 $returnText .= '<label>flags:</label>';
 $returnText .= '<input type="checkbox" name="private" id="private" value="1" checked="checked" disabled="disabled" /><span>private</span>';
 $returnText .= '&#160;';
 $returnText .= '<input type="checkbox" name="debug" id="debug" value="1" checked="checked" disabled="disabled" /><span>debug</span>';
 $returnText .= '<br />';
 $returnText .= '<br />';
 $returnText .= 'Try: <a href="#unitTest" title="jump to">unitTest()</a><br />';
 $returnText .= 'Try: <a href="#getStructure" title="jump to">getStructure()</a><br />';
 $returnText .= 'Try: <a href="#getOptions" title="jump to">getOptions()</a><br />';
 $returnText .= 'Try: <a href="#getBlock" title="jump to">getBlock()</a><br />';
 $returnText .= 'Try: <a href="#getLinks" title="jump to">getLinks()</a><br />';
 $returnText .= 'Try: <a href="#getMaps" title="jump to">getMaps()</a><br />';
 // |
 $returnText .= '<hr />';
  $returnText .= '<div id="s" class="code"></div>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h2>Check suite</h2>';
 $returnText .= '<h3 id="unitTest">unitTest()</h3><em>launch tests</em><br />';
 $returnText .= '<input id="b0" type="button" value="Run the test" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'unitTest', debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h2>Browsing/Searching</h2>';
 $returnText .= '<h3 id="getStructure">getStructure()</h3><em>returns description records for each resource as a structure</em><br />';
 $returnText .= '<label class="label">rsrc:</label>&#160;<input type="text" id="uri1" size="80"/><br/>';
 $returnText .= '<input id="b1" type="button" value="Try" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'getStructure', rsrc: $('#uri1').val(), debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h3 id="getOptions">getOptions()</h3><em>returns description records of retrieved values for each resource</em><br />';
 $returnText .= '<label>rsrc:</label>&#160;<input type="text" id="uri3" size="80"/><br/>';
 $returnText .= '<label>xpath:</label>&#160;<input type="text" id="xpath3" size="80"/><br/>';
 $returnText .= '<input id="b3" type="button" value="Try" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'getOptions', rsrc: $('#uri3').val(), xpath: $('#xpath3').val(), debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h2>Visualizing</h2>';
 $returnText .= '<h3 id="getBlock">getBlock()</h3><em>returns description records for each resource as a single block</em><br />';
 $returnText .= '<label>rsrc:</label>&#160;<input type="text" id="uri2" size="80"/><br/>';
 $returnText .= '<input id="b2" type="button" value="Try" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'getBlock', rsrc: $('#uri2').val(), debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h3 id="getLinks">getLinks()</h3><em>returns description records of retrieved values for each resource as a link</em><br />';
 $returnText .= '<label>rsrc:</label>&#160;<input type="text" id="uri4" size="80"/><br/>';
 $returnText .= '<input id="b4" type="button" value="Try" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'getLinks', rsrc: $('#uri4').val(), debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '<p>';
 $returnText .= '<hr />';
 $returnText .= '<h3 id="getMaps">getMaps()</h3><em>returns description records of retrieved values for each resource as a map</em><br/>';
 $returnText .= '<label>rsrc:</label>&#160;<input type="text" id="uri5" size="80"/><br/>';
 $returnText .= '<input id="b5" type="button" value="Try" onclick="doProgress();';
 $returnText .= "$('#s').html('loading...');$('#s').load('".AJAX."', { method: 'getMaps', rsrc: $('#uri5').val(), debug: debug, private: private}, function() { refreshMe(); } );";
 $returnText .= ' return false;"/>';
 $returnText .= '</p>';
 // |
 $returnText .= '</form>';
 // | end BODY
 // +----------------------------------------------------------------------+
 echo $returnText;

/* NO ?> IN FILE .INC */
