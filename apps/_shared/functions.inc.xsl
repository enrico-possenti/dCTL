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
 <xsl:output omit-xml-declaration="no" version="1.0" method="xml" indent="yes" encoding="UTF-8" />
 <xsl:strip-space elements="*" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:variable name="distinctSep" select="php:function('dctl_getPHPvar', 'DISTINCT_SEP')" />
 <xsl:variable name="distinctSep2" select="php:function('dctl_getPHPvar', 'DISTINCT_SEP2')" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- PARAMETRI -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:param name="where" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- STRIP STRING -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="stripString">
  <xsl:param name="length" />
  <xsl:param name="text" />
  <xsl:choose>
   <xsl:when test="string-length($text) > $length">
    <xsl:value-of select="concat(substring($text, 0, $length),'...')" />
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="$text" />
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- GET INDEX -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="getIndex">
  <xsl:param name="length" />
  <xsl:param name="blockID" />
  <xsl:param name="xpath" />
  <xsl:variable name="theBlock" select="id($blockID)" />
  <xsl:variable name="size">
   <xsl:choose>
    <xsl:when test="number($length) > 0">
     <xsl:value-of select="$length" />
    </xsl:when>
    <xsl:otherwise>
     <xsl:value-of select="'80'" />
    </xsl:otherwise>
   </xsl:choose>
  </xsl:variable>
  <xsl:variable name="pre_text">
   <xsl:choose>
    <xsl:when test="contains($xpath, 'figDesc')">
     <xsl:value-of select="$theBlock//tei:figDesc" />
    </xsl:when>
    <xsl:when test="contains($xpath, 'desc')">
     <xsl:value-of select="$theBlock/../dctl:desc" />
    </xsl:when>
   </xsl:choose>
  </xsl:variable>
  <xsl:variable name="text">
   <xsl:choose>
    <xsl:when test="normalize-space($pre_text) = ''">
     <xsl:choose>
      <xsl:when
       test="($theBlock/ancestor-or-self::tei:div[@type='dctlObject']) and (normalize-space($theBlock/@n) != '') and ($where != 1)">
       <xsl:if test="normalize-space($theBlock/../@n) != ''">
        <xsl:value-of select="$theBlock/../@n" />
        <xsl:text>, </xsl:text>
       </xsl:if>
       <xsl:value-of select="$theBlock/@n" />
      </xsl:when>
      <xsl:when test="normalize-space($theBlock//tei:index/tei:term) != ''">
       <xsl:value-of select="$theBlock//tei:index/tei:term" />
      </xsl:when>
      <xsl:when test="normalize-space($theBlock//tei:head) != ''">
       <xsl:value-of select="$theBlock//tei:head" />
      </xsl:when>
      <xsl:when test="normalize-space($theBlock/@n) != ''">
       <xsl:value-of select="$theBlock/@n" />
      </xsl:when>
      <xsl:otherwise>
       <xsl:value-of select="$theBlock//text()" />
      </xsl:otherwise>
     </xsl:choose>
    </xsl:when>
    <xsl:otherwise>
     <xsl:value-of select="$pre_text" />
    </xsl:otherwise>
   </xsl:choose>
  </xsl:variable>
  <xsl:call-template name="stripString">
   <xsl:with-param name="length" select="$size" />
   <xsl:with-param name="text" select="normalize-space($text)" />
  </xsl:call-template>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- DISTINCT -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="getDistinct">
  <xsl:param name="text" />
  <xsl:param name="withCount" />
  <xsl:value-of disable-output-escaping="yes"
   select="normalize-space(translate(php:function('dctl_getDistincts', $text, $distinctSep, $withCount), $distinctSep, ','))"
   />
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
 <!-- GET ID -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template name="get_id">
  <xsl:choose>
   <xsl:when test="not(@xml:id)">
    <xsl:value-of select="generate-id(.)" />
   </xsl:when>
   <xsl:otherwise>
    <xsl:value-of select="@xml:id" />
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
