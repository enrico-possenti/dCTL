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
 <xsl:variable name="depth" select="2" />
 <xsl:variable name="limit" select="10" />
 <xsl:variable name="length" select="80" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <xsl:variable name="thisLevel" select="0" />
  <xsl:choose>
   <xsl:when test="count(//dctl:item) > 0">
    <xsl:for-each select="//dctl:item">
     <div class="line">
      <xsl:variable name="thisID">
       <xsl:call-template name="get_id" />
      </xsl:variable>
      <xsl:variable name="blockID">
       <xsl:value-of
        select="ancestor-or-self::tei:div[@type='dctlObject'][position()=last()]/@xml:id" />
      </xsl:variable>
      <xsl:variable name="link">
       <xsl:call-template name="putCorresp">
        <xsl:with-param name="blockID" select="$blockID" />
        <xsl:with-param name="thisID" select="$thisID" />
       </xsl:call-template>
      </xsl:variable>
      <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
       <xsl:value-of select="dctl:desc" />
      </a>
     </div>
    </xsl:for-each>
   </xsl:when>
   <xsl:otherwise>
    <xsl:for-each select="//tei:div[@type='dctlObject']">
     <div class="line">
      <xsl:variable name="thisID">
       <xsl:call-template name="get_id" />
      </xsl:variable>
      <xsl:variable name="blockID">
       <xsl:value-of
        select="ancestor-or-self::tei:div[@type='dctlObject'][position()=last()]/@xml:id" />
      </xsl:variable>
      <xsl:variable name="link">
       <xsl:call-template name="putCorresp">
        <xsl:with-param name="blockID" select="$blockID" />
        <xsl:with-param name="thisID" select="$thisID" />
       </xsl:call-template>
      </xsl:variable>
      <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
       <xsl:value-of select=".//dctl:desc" />
      </a>
     </div>
    </xsl:for-each>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
