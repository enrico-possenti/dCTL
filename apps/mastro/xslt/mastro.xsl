<?xml version="1.0" encoding="UTF-8"?>
<!-- 
	/**
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| A digital tale (C) 2009 Enrico Possenti :: dCTL                     |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>            |
	| License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike     |
	|          http://creativecommons.org/licenses/by-nc-sa/3.0/          |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| A main file for "commodoro"                                         |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
 xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
 xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
 xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
 extension-element-prefixes="tei dctl php exslt dyn str">
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:import href="../../_shared/functions.inc.xsl" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes" />
 <xsl:strip-space elements="*" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:variable name="tooltip_goto" select="php:function('dctl_getPHPvar', 'TOOLTIP_GOTO')" />
 <xsl:variable name="tooltip_select" select="php:function('dctl_getPHPvar', 'TOOLTIP_SELECT')" />
 <xsl:variable name="tooltip_toggle" select="php:function('dctl_getPHPvar', 'TOOLTIP_TOGGLE')" />
 <xsl:variable name="tooltip_drag" select="php:function('dctl_getPHPvar', 'TOOLTIP_DRAG')" />
 <xsl:variable name="tooltip_zoom" select="php:function('dctl_getPHPvar', 'TOOLTIP_ZOOM')" />
 <xsl:variable name="tooltip_addtobasket"
  select="php:function('dctl_getPHPvar', 'TOOLTIP_ADDTOBASKET')" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- PARAMETRI -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:param name="doc" />
 <xsl:param name="block" />
 <xsl:param name="at" />
 <xsl:param name="high" />
 <!-- <xsl:param name="where" /> già in functions.inc.xsl-->
 <xsl:param name="label" />
 <xsl:param name="mode" />
 <xsl:variable name="next">
  <xsl:choose>
   <xsl:when test="$where  = 'navigator'">
    <xsl:text>1</xsl:text>
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="number($where) + 1" />
   </xsl:otherwise>
  </xsl:choose>
 </xsl:variable>
 <xsl:variable name="wherePrefix">
  <xsl:choose>
   <xsl:when test="$where  = 'navigator'">
    <xsl:text>p_</xsl:text>
   </xsl:when>
   <xsl:otherwise>
    <xsl:text>x</xsl:text>
    <xsl:value-of select="$where" />
    <xsl:text>_</xsl:text>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:variable>
 <xsl:variable name="nextPrefix">
  <xsl:choose>
   <xsl:when test="$next  = 'navigator'">
    <xsl:text>p_</xsl:text>
   </xsl:when>
   <xsl:otherwise>
    <xsl:text>x</xsl:text>
    <xsl:value-of select="$where" />
    <xsl:text>_</xsl:text>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:variable>
 <xsl:variable name="goto">
  <xsl:choose>
   <xsl:when test="$block = ''">
    <xsl:value-of select="//tei:div[.//text() != ''][1]/@xml:id" />
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="$block" />
   </xsl:otherwise>
  </xsl:choose>
 </xsl:variable>
 <xsl:variable name="theBlock" select="id($goto)" />
 <xsl:variable name="docExt"
  select="concat('_',substring-after(substring-before($doc, '.xml'), '_'))" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="*" mode="formatRetrieveData">
  <xsl:choose>
   <xsl:when test="local-name(.)='lb'">
    <xsl:choose>
     <xsl:when test="./preceding-sibling::tei:milestone[@unit='wb']" />
     <xsl:when test=".//text()[1]= ' '" />
     <xsl:otherwise>
      <xsl:text> </xsl:text>
     </xsl:otherwise>
    </xsl:choose>
   </xsl:when>
  </xsl:choose>
  <xsl:apply-templates mode="formatRetrieveData" />
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- - - - - - - - - - - - - - - - -->
 <!-- RETRIEVE FORMATTER -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="formatRetrieveData">
  <xsl:param name="data" />
  <xsl:variable name="length" select="150" />
  <xsl:call-template name="stripString">
   <xsl:with-param name="length" select="$length" />
   <xsl:with-param name="text">
    <xsl:apply-templates select="$data" mode="formatRetrieveData" />
   </xsl:with-param>
  </xsl:call-template>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- FIGURE @ WIDGET_BOX -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="tei:figure" mode="widget_box">
  <img alt="(preview icon)">
   <xsl:attribute name="src">
    <xsl:value-of
     select="concat(php:function('dctl_getPHPvar', 'WEB_PUBLISH'), php:function('dctl_getPHPvar', 'DCTL_MEDIA_SML'), '')" />
    <xsl:value-of select="substring-after(tei:graphic/@url, 'img://')" />
    <xsl:value-of select="concat('?', generate-id(.))" />
   </xsl:attribute>
  </img>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- GET REFS -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="getRefs">
  <xsl:param name="blockID" />
  <xsl:param name="length" />
  <xsl:param name="index" />
  <xsl:variable name="theBlock" select="id($blockID)" />
  <xsl:choose>
   <xsl:when test="normalize-space($theBlock/@rend) != ''">
    <xsl:value-of
     select="php:function('dctl_putRefs', string($theBlock/@rend), $distinctSep, $doc, $where, $index)"
     />
   </xsl:when>
   <xsl:otherwise>
    <xsl:call-template name="getIndex">
     <xsl:with-param name="blockID" select="$blockID" />
     <xsl:with-param name="length" select="$length" />
    </xsl:call-template>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- @CORRESP INLINE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="hasCorresp">
  <xsl:param name="label" />
  <xsl:param name="node" />
  <!-- # TEI : @CORRESP -->
  <xsl:if test="attribute::corresp">
   <xsl:variable name="link">
    <xsl:text>$().mastro('display', '</xsl:text>
    <xsl:value-of select="@corresp" />
    <xsl:text>', '</xsl:text>
    <xsl:value-of select="$next" />
    <xsl:text>', '', '', '', '', '</xsl:text>
    <xsl:value-of
     select="concat($docExt, $distinctSep, $label, $distinctSep, string($theBlock/@rend))" />
    <xsl:text>');</xsl:text>
   </xsl:variable>
   <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
    <xsl:copy-of select="$node" />
   </a>
  </xsl:if>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- @CORRESP INLINE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="putRiproduzione">
  <!-- # TEI : @CORRESP -->
  <xsl:if test="attribute::corresp">
   <xsl:variable name="link">
    <xsl:text>$().mastro('display', '</xsl:text>
    <xsl:value-of select="@corresp" />
    <xsl:text>', '</xsl:text>
    <xsl:value-of select="$next" />
    <xsl:text>', '', '', '', '', '</xsl:text>
    <xsl:value-of
     select="concat($docExt, $distinctSep, 'Immagine della scena', $distinctSep, string($theBlock/@rend))" />
    <xsl:text>');</xsl:text>
   </xsl:variable>
   <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">Immagine della scena</a>
  </xsl:if>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- @CORRESP BLOCK  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="putAnastatica">
  <!-- # TEI : @CORRESP -->
  <xsl:if test="attribute::corresp">
   <xsl:variable name="link">
    <xsl:text>$().mastro('display', '</xsl:text>
    <xsl:value-of select="@corresp" />
    <xsl:text>', '</xsl:text>
    <xsl:value-of select="$next" />
    <xsl:text>', '', '', '', '', '</xsl:text>
    <xsl:value-of
     select="concat($docExt, $distinctSep, 'Anastatica', $distinctSep, string($theBlock/@rend))" />
    <xsl:text>');</xsl:text>
   </xsl:variable>
   <div class="box_head_extend_item">
    <a class="box_head_extend_field" title="{$tooltip_goto}" href="javascript:void(0);"
     onclick="{$link}">Anastatica</a>
   </div>
  </xsl:if>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- PUTCORRESP -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="putCorresp">
  <xsl:param name="blockID" />
  <xsl:param name="thisID" />
  <xsl:param name="packType" />
  <xsl:param name="packBase" />
  <xsl:param name="label" />
  <xsl:param name="high" />
  <xsl:param name="class" />
  <xsl:variable name="corresp" select="substring-after($packType, $distinctSep) != ''" />
  <xsl:variable name="link">
   <xsl:choose>
    <xsl:when
     test="(not($corresp) and $packType != '' and substring-after($doc, $packType) = '') or ($packBase != '')">
     <!--  -->
     <!--  test="(not($corresp) and $packType != '' and substring-after($doc, $packType) = '') or ($packBase != '')" -->
     <!-- diverso package -->
     <xsl:value-of disable-output-escaping="yes"
      select="php:function('dctl_putLink', $blockID, $doc, $next, $packType, $label, $packBase, $class)"
      />
    </xsl:when>
    <xsl:otherwise>
     <xsl:variable name="doc">
      <xsl:choose>
       <xsl:when test="$corresp">
        <xsl:value-of
         select="concat(substring-before($doc, substring-before($packType, $distinctSep)), substring-after($packType, $distinctSep), '.xml')"
         />
       </xsl:when>
       <xsl:otherwise>
        <xsl:value-of select="$doc" />
       </xsl:otherwise>
      </xsl:choose>
     </xsl:variable>
     <xsl:text>$().mastro('display', '</xsl:text>
     <xsl:value-of select="$doc" />
     <xsl:text>', '</xsl:text>
     <xsl:value-of select="$next" />
     <xsl:text>', '', '</xsl:text>
     <xsl:value-of select="$blockID" />
     <xsl:text>', '</xsl:text>
     <xsl:value-of select="$thisID" />
     <xsl:text>', '</xsl:text>
     <xsl:value-of select="$high" />
     <xsl:text>', '</xsl:text>
     <xsl:value-of select="$label" />
     <xsl:text>');</xsl:text>
    </xsl:otherwise>
   </xsl:choose>
  </xsl:variable>
  <xsl:choose>
   <xsl:when test="$corresp">
    <div class="box_head_extend_item">
     <a class="box_head_extend_field {$class}" title="{$tooltip_goto}" href="javascript:void(0);"
      onclick="{$link}">
      <xsl:value-of select="$label" />
     </a>
    </div>
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="$link" disable-output-escaping="yes" />
   </xsl:otherwise>
  </xsl:choose>
  <!--entag(1, this, true);entag(2, $('</xsl:text>
   <xsl:value-of select="$thisID" />
   <xsl:text>), false);-->
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
