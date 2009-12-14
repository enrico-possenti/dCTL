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
 <xsl:variable name="depth" select="1" />
 <xsl:variable name="length" select="80" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <xsl:choose>
   <xsl:when test="count(//tei:figure[tei:graphic])>0">
    <xsl:for-each
     select="//tei:figure[count(ancestor::tei:div) &lt;= $depth][./tei:graphic/@url]">
     <xsl:variable name="thisID">
      <xsl:call-template name="get_id" />
     </xsl:variable>
     <xsl:variable name="thisLevel" select="count(ancestor::tei:div)" />
     <xsl:variable name="blockID">
      <xsl:value-of
       select="ancestor::tei:div[count(ancestor::tei:div) &lt;= ($thisLevel - 1)]/@xml:id" />
     </xsl:variable>
     <xsl:variable name="link">
      <xsl:call-template name="putCorresp">
       <xsl:with-param name="blockID" select="$blockID" />
      </xsl:call-template>
     </xsl:variable>
     <div class="line">
       <div class="sidebar_box_title">
      <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
        <xsl:call-template name="getIndex">
         <xsl:with-param name="length" select="$length" />
         <xsl:with-param name="blockID" select="$blockID" />
         <xsl:with-param name="xpath" select="tei:figDesc" />
        </xsl:call-template>
      </a>
       </div>
       <div class="sidebar_box_image">
      <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
        <img alt="(preview icon)">
         <xsl:attribute name="src">
          <xsl:value-of
           select="concat(php:function('dctl_getPHPvar', 'WEB_PUBLISH'), php:function('dctl_getPHPvar', 'DCTL_MEDIA_SML'), '')" />
          <xsl:value-of select="substring-after(tei:graphic/@url, 'img://')" />
          <xsl:value-of select="concat('?', generate-id(.))" />
         </xsl:attribute>
        </img>
      </a>
       </div>
     </div>
    </xsl:for-each>
   </xsl:when>
   <xsl:otherwise>
    <div class="line">- - -</div>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
