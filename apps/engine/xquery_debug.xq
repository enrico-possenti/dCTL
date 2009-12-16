xquery version "1.0";
declare namespace tei="http://www.tei-c.org/ns/1.0";
declare namespace dctl="http://www.ctl.sns.it/ns/1.0";
declare namespace util="http://exist-db.org/xquery/util";
declare namespace exist="http://exist.sourceforge.net/NS/exist";
declare namespace transform="http://exist-db.org/xquery/transform";
declare namespace functx = "http://www.functx.com";
declare option exist:timeout "60000";
declare option exist:output-size-limit "-1";
declare option exist:serialize "method=xhtml";
declare option exist:serialize "highlight-matches=both";

(: http://wiki.tei-c.org/index.php/Milestone-chunk.xquery :)

declare function tei:milestone-chunk(
		$ms1 as element(),
		$ms2 as element(),
		$node as node()*
) as node()* {
		typeswitch ($node)
				case element() return
						if ($node is $ms1) then $node
						else if ( some $n in $node/descendant::* satisfies ($n is $ms1 or $n is $ms2) )
						 then
element { node-name($node) }
							{ for $i in ( $node/node() | $node/@* )
return tei:milestone-chunk($ms1, $ms2, $i) }
						else if ( $node >> $ms1 and $node << $ms2 ) then $node
						else ()
				case attribute() return $node (: will never match attributes outside non-returned elements :)
				default return
						if ( $node >> $ms1 and $node << $ms2 ) then $node
						else ()
};

(: http://www.ctl.sns.it/dctl :)

declare function tei:getFullPage($ms1 as node(), $parent as node()) as node()* {
		let $ms2 := subsequence($parent/descendant::tei:pb, 1)[. >> $ms1][not(string(attribute::ed) = "fake")][1]
		let $ms2 := if ($ms2) then $ms2 else subsequence($parent/descendant::tei:pb, 1)[. >> $ms1][(string(attribute::ed) = "fake")][1]
  let $node := tei:milestone-chunk($ms1, $ms2, $parent)
		return $node
 };

declare function tei:getPage($node as node(), $justRefs as xs:integer) as node()* {
		let $node :=
		(: se è una div allora deve contenere un pb :)
		 if ($node[self::tei:div]) (: * :)
		 then
		  if ($node[child::tei:pb])
     then $node
     else $node/child::*[. >> $node/child::tei:pb[1]][1]
   (: se è un pb allora ha un figlio successivo :)
		 else
		  if ($node[self::tei:pb])
  		 then $node/following-sibling::*[. >> $node][1]
	   	(: oppure è lui :)
		   else $node
		return
		(: becca il blocco container :)
			let $parent := tei:getBlock($node)
		 (: in questo blocco ci sono altri nodi pb dopo questo? :)
			let $parent :=
			 if (count($parent/descendant::tei:pb[. >> $node]) > 0)
		   (: si, buono :)
				 then $parent
		   (: no, becca il blocco del blocco :)
				 else $parent/ancestor::tei:div[count((./descendant::tei:pb[. >> $node])) > 0][1]
			let $ms1 := (subsequence($parent/descendant::tei:pb, 1)[. << $node])[position()=last()]
			let $ms1 := if ($ms1) then $ms1 else $parent/descendant::tei:pb[1]
			return
			if ($justRefs = 1)
				then $ms1/@xml:id
				else tei:getFullPage($ms1, $parent)
};

declare function tei:getBlock($node as node()) as node() {
  let $block :=
 		if ($node/@type="dctlObject") then
	   $node
		  else
				$node/ancestor-or-self::tei:div[./@xml:id and ./ancestor-or-self::tei:text][position()=last()]
		  return $block
};

declare function tei:getTree ( $node as node(), $embed as node()* ) as node()* {
		let $nodeP := $node/ancestor::tei:div[1]
	 let $block :=
	  if (empty($nodeP)) then
	   $embed
			 else
	   tei:getTree($nodeP, element {node-name($nodeP)} {$nodeP/@*, $embed } ) (: MUST BE NAME AND NOT NODE-NAME :)
		 return $block
};


declare function tei:getParent($node as node()) as node() {
  let $block :=
	   $node/ancestor-or-self::tei:div[position()=last()]
		  return $block
};

declare function tei:handleID($node as node()) as node()* {
	let $id := string($node/ancestor-or-self::tei:div[@xml:id and ancestor-or-self::tei:text][1]/@xml:id)
	return
		if (number(substring-after($id, ".")) > 0) then
			$node/ancestor-or-self::*[@xml:id=concat(substring-before($id, "."), ".001")]
		else
			$node
};

declare function tei:highlight($term as xs:string, $node as text(), $args as item()*) as node()* {
	<span>{$term}</span>
};

declare function tei:shrink($nodes as node()*, $width as xs:integer, $args as item()*) as node()* {
	<span>{$nodes}</span>
};

(: http://www.functx.com :)

declare function functx:add-attributes
  ( $elements as element()* ,
    $attrNames as xs:QName* ,
    $attrValues as xs:anyAtomicType* )  as element()? {

   for $element in $elements
   return element { node-name($element)}
                  { for $attrName at $seq in $attrNames
                    return if ($element/@*[node-name(.) = $attrName])
                           then ()
                           else attribute {$attrName}
                                          {$attrValues[$seq]},
                    $element/@*,
                    $element/node() }
 } ;

declare function functx:distinct-nodes
		( $nodes as node()* )  as node()* {
				for $seq in (1 to count($nodes))
				return $nodes[$seq][not(functx:is-node-in-sequence(
					.,$nodes[position() < $seq]))]
	} ;

declare function functx:distinct-deep ( $nodes as node()* )  as node()* {
				for $seq in (1 to count($nodes))
				return $nodes[$seq][not(functx:is-node-in-sequence-deep-equal(
.,$nodes[position() < $seq]))]
};

declare function functx:is-node-in-sequence ( $node as node()?, $seq as node()* )  as xs:boolean {
	some $nodeInSeq in $seq satisfies $nodeInSeq is $node
};

declare function functx:is-node-in-sequence-deep-equal
		( $node as node()? ,
				$seq as node()* )  as xs:boolean {
			some $nodeInSeq in $seq satisfies deep-equal($nodeInSeq,$node)
	} ;

let $highlight := util:function(xs:QName("tei:highlight"), 3)
let $shrink := util:function(xs:QName("tei:shrink"), 3)

 let $base := xmldb:document("/db/dctl-temp/test/test-marmi_txt.xml")//tei:text 
 for $node in 
 $base/*/id("xdv000028") 
 let $kwic := if ($node//text() != "") then text:kwic-display($node//text(), 80, $highlight, ()) else text:kwic-display(subsequence($node/parent::*/descendant::text(), 1)[. >> $node][position() < 5], 80, $highlight, ()) 
 let $nodeT := element {node-name($node)} {$node/@*, text {$kwic}} 
 let $nodeT := functx:add-attributes($nodeT, xs:QName("synch"), tei:getPage($node, 1)) 
 return 
 $nodeT 
 