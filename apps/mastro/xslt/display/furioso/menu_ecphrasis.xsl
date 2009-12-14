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
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <xsl:variable name="thisLevel" select="1" />
  <xsl:choose>
   <xsl:when test="count(//*[contains(@ana,'verbfig_ecphrasis')])>0">
    <xsl:for-each
     select="//*[contains(@ana,'verbfig_ecphrasis')][ substring-after(@xml:id, '.')= '' or substring-after(@xml:id, '.')= '001']">
     <div class="line">
      <xsl:variable name="item">
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
      </xsl:variable>
      <xsl:value-of select="$item" disable-output-escaping="yes" />
     </div>
    </xsl:for-each>
   </xsl:when>
   <xsl:otherwise>
    <div class="line">- - -</div>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!--  -->
</xsl:stylesheet>
