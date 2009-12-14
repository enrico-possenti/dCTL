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
  <ul class="collapsible">
   <xsl:variable name="thisLevel" select="1" />
   <xsl:choose>
    <xsl:when test="count(//dctl:settings[@ana != ''])>0">
     <xsl:variable name="root" select="." />
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
      <li>
       <a class="collapsible_handle2 h3" title="{$tooltip_toggle}"><xsl:value-of
         select="php:function('dctl_getValueFromClass', $settings)" />&#160;</a>
       <ul class="collapsible_body">
        <xsl:variable name="item">
         <xsl:for-each select="$root//dctl:settings[contains(@ana, $settings)]">
          <xsl:text>&lt;li class="line"&gt;</xsl:text>
          <xsl:variable name="thisID">
           <xsl:call-template name="get_id" />
          </xsl:variable>
          <xsl:variable name="partID">
           <xsl:value-of select="ancestor-or-self::tei:div[1]/@xml:id" />
          </xsl:variable>
          <xsl:variable name="blockID">
           <xsl:value-of
            select="ancestor-or-self::tei:div[count(ancestor-or-self::tei:div[@xml:id and ancestor-or-self::tei:text]) = 1]/@xml:id"
            />
          </xsl:variable>
          <!-- <xsl:if test="position()>1">
             <xsl:value-of select="concat($distinctSep, ' ')" />
            </xsl:if> -->
          <xsl:variable name="link">
           <xsl:call-template name="putCorresp">
            <xsl:with-param name="blockID" select="$blockID" />
            <xsl:with-param name="thisID" select="$partID" />
            <xsl:with-param name="high" select="$thisID" />
           </xsl:call-template>
          </xsl:variable>
          <xsl:text>&lt;a href="javascript:void(0);" title="</xsl:text>
          <xsl:value-of select="$tooltip_goto" />
          <xsl:text>" onclick="</xsl:text>
          <xsl:value-of select="$link" />
          <xsl:text>"&gt;</xsl:text>
          <xsl:call-template name="getIndex">
           <xsl:with-param name="length" select="$length" />
           <xsl:with-param name="blockID" select="$blockID" />
          </xsl:call-template>
          <xsl:if test="$partID != $blockID">
           <xsl:text>, </xsl:text>
           <xsl:call-template name="getIndex">
            <xsl:with-param name="length" select="$length" />
            <xsl:with-param name="blockID" select="$partID" />
           </xsl:call-template>
          </xsl:if>
          <xsl:text>&lt;/a&gt; </xsl:text>
          <xsl:text>&lt;/li&gt;</xsl:text>
         </xsl:for-each>
        </xsl:variable>
        <!-- <xsl:call-template name="getDistinct">
          <xsl:with-param name="text" select="$item" />
          <xsl:with-param name="withCount" select="true()" />
         </xsl:call-template> -->
        <xsl:value-of select="$item" disable-output-escaping="yes" />
       </ul>
      </li>
     </xsl:for-each>
    </xsl:when>
    <xsl:otherwise>
     <li class="{concat('h', $thisLevel+2)}">- - - </li>
    </xsl:otherwise>
   </xsl:choose>
  </ul>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
