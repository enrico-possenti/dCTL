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
 <xsl:variable name="depth" select="9999" />
 <xsl:variable name="length" select="80" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:key name="groupByCharacter" match="tei:*[contains(@ana, 'func_character')]" use="@n" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <!-- NAME -->
  <ul>
   <xsl:for-each
    select="//tei:*[contains(@ana, 'func_character')][count(. | key('groupByCharacter', @n)[1]) = 1]">
    <xsl:sort select="@n" />
    <li class="line">
     <xsl:variable name="link">
      <xsl:text>$().mastro('retrieve', '</xsl:text>
      <xsl:value-of select="$doc" />
      <xsl:text>', '1', 'character_by_block', '</xsl:text>
      <xsl:choose>
       <xsl:when test="@key">
        <xsl:value-of select="@key" />
       </xsl:when>
       <xsl:otherwise>
        <xsl:value-of select="php:function('fixLabel', string(@n))" />
       </xsl:otherwise>
      </xsl:choose>
      <xsl:text>', '', '', '</xsl:text>
      <xsl:value-of select="php:function('fixLabel', string(@n))" />
      <xsl:text>');</xsl:text>
     </xsl:variable>
     <a href="javascript:void(0);" title="{$tooltip_goto}" onclick="{$link}">
      <xsl:call-template name="formatRetrieveData">
       <xsl:with-param name="data" select="@n" />
      </xsl:call-template>
     </a>
    </li>
   </xsl:for-each>
  </ul>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
