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
   <xsl:variable name="prevID">
    <xsl:value-of select="string($theBlock/preceding-sibling::tei:div[1]/@xml:id)" />
   </xsl:variable>
   <xsl:variable name="nextID">
    <xsl:value-of select="string($theBlock/following-sibling::tei:div[1]/@xml:id)" />
   </xsl:variable>
   <xsl:if test="($prevID != '' or $nextID != '')">
    <!-- and ($where != '3') -->
    <xsl:if test="($prevID != '') and (substring($prevID,1,1) != 'x')">
     <xsl:variable name="link">$().mastro('display', '<xsl:value-of select="$doc" />',
       '<xsl:value-of select="$where" />', '', '<xsl:value-of select="$prevID" />');</xsl:variable>
     <a class="page_prev" href="javascript:void(0);" onclick="{$link}" title="{$tooltip_goto}"
       >«<xsl:call-template name="getIndex">
       <xsl:with-param name="blockID" select="$prevID" />
       <xsl:with-param name="length" select="$length" />
      </xsl:call-template>
     </a>
    </xsl:if>
    <xsl:if test="($nextID != '') and (substring($prevID,1,1) != 'x')">
     <xsl:variable name="link">$().mastro('display', '<xsl:value-of select="$doc" />',
       '<xsl:value-of select="$where" />', '', '<xsl:value-of select="$nextID" />');</xsl:variable>
     <a class="page_next" href="javascript:void(0);" onclick="{$link}" title="{$tooltip_goto}">
      <xsl:call-template name="getIndex">
       <xsl:with-param name="blockID" select="$nextID" />
       <xsl:with-param name="length" select="$length" />
      </xsl:call-template>»</a>
    </xsl:if>
   </xsl:if>
   <!--  -->
  </div>
  <!--  -->
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
