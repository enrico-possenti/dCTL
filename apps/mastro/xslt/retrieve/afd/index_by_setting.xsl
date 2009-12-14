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
 <xsl:template match="/">
  <ul>
   <xsl:variable name="values_concat">
    <xsl:for-each select="//dctl:settings[@ana != '']">
     <xsl:for-each select="str:split(@ana)">
      <xsl:value-of
       select="concat(substring(php:function('dctl_getValueFromClass', string(.)), 1, 4), $distinctSep2, string(.), $distinctSep, ' ')"
       />
     </xsl:for-each>
    </xsl:for-each>
   </xsl:variable>
   <xsl:variable name="values_distinct">
    <xsl:call-template name="getDistinct">
     <xsl:with-param name="text" select="$values_concat" />
    </xsl:call-template>
   </xsl:variable>
   <xsl:for-each select="str:split($values_distinct, ', ')">
    <xsl:sort />
    <xsl:variable name="settings" select="substring-after(string(.), $distinctSep2)" />
    <li class="line">
     <xsl:variable name="settingName" select="php:function('dctl_getValueFromClass', $settings)" />
     <xsl:variable name="link">
      <xsl:text>$().mastro('retrieve', '</xsl:text>
      <xsl:value-of select="$doc" />
      <xsl:text>', '1', 'setting_by_block', '</xsl:text>
      <xsl:value-of select="$settings" />
      <xsl:text>', '', '', '</xsl:text>
      <xsl:value-of select="$settingName" />
      <xsl:text>');</xsl:text>
     </xsl:variable>
     <a href="javascript:void(0);" title="{$tooltip_goto}" onclick="{$link}">
      <xsl:value-of select="$settingName" />
     </a>
    </li>
   </xsl:for-each>
  </ul>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
