<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
 xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
 xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
 xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
 extension-element-prefixes="tei dctl php exslt dyn str">
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:import href="../../mastro.xsl" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes" />
 <xsl:strip-space elements="*" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:variable name="length" select="80" />
 <!-- - - - - - - - - - - - - - - - -->
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
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <div class="box_head">
   <h2 class="page_curr" rel="{$doc}-{$goto}">
    <xsl:variable name="title">
     <xsl:call-template name="getRefs">
      <xsl:with-param name="blockID" select="$goto" />
      <xsl:with-param name="length" select="$length * 2" />
     </xsl:call-template>
    </xsl:variable>
    <xsl:value-of select="$title" />
    <xsl:value-of select="php:function('generateVerticalString', $title, $goto, $doc)" />
   </h2>
   <xsl:copy-of select="." />
   <!--  -->
  </div>
  <!--  -->
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
