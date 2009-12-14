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
 <xsl:variable name="limit" select="10" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <xsl:choose>
   <xsl:when test="count(//tei:div[count(ancestor::tei:div) &lt; $depth])>0">
    <xsl:for-each select="//tei:div[count(ancestor::tei:div) &lt; $depth]">
     <!-- [.//text() != ''] -->
     <xsl:variable name="thisLevel" select="count(ancestor::tei:div)" />
     <xsl:variable name="count" select="count(parent::*[1]/tei:div)" />
     <xsl:choose>
      <xsl:when test="($count &gt; $limit) and ($thisLevel &gt; 0)">
       <xsl:if test="position() = 1">
        <div class="line">- - -</div>
       </xsl:if>
      </xsl:when>
      <xsl:otherwise>
       <xsl:if test="($thisLevel &lt; $depth) and (. != '')">
        <xsl:variable name="thisID">
         <xsl:call-template name="get_id" />
        </xsl:variable>
        <div class="line">
         <xsl:variable name="blockID">
          <xsl:choose>
           <xsl:when test="$thisLevel = 0">
            <xsl:value-of select="$thisID" />
           </xsl:when>
           <xsl:otherwise>
            <xsl:value-of
             select="ancestor::tei:div[count(ancestor::tei:div) &lt;= ($thisLevel - 1)]/@xml:id"
             />
           </xsl:otherwise>
          </xsl:choose>
         </xsl:variable>
         <xsl:variable name="link">
          <xsl:call-template name="putCorresp">
           <xsl:with-param name="blockID" select="$blockID" />
           <xsl:with-param name="thisID" select="$thisID" />
          </xsl:call-template>
         </xsl:variable>
         <a title="{$tooltip_goto}" href="javascript:void(0);" onclick="{$link}">
          <xsl:call-template name="getIndex">
           <xsl:with-param name="length" select="$length - $thisLevel" />
           <xsl:with-param name="blockID" select="$thisID" />
          </xsl:call-template>
         </a>
         <!--
          <xsl:if test="@type != 'dctlObject'">
           <xsl:variable name="pageN" select=".//tei:pb[position()=1]/@n" />
           <div class="align_right">
            <xsl:choose>
             <xsl:when test="string-length($pageN)"> p.<xsl:value-of select="$pageN" />
             </xsl:when>
             <xsl:otherwise>&#160;</xsl:otherwise>
            </xsl:choose>
           </div>
           </xsl:if>
         -->
        </div>
       </xsl:if>
      </xsl:otherwise>
     </xsl:choose>
    </xsl:for-each>
   </xsl:when>
  </xsl:choose>
  <!--  <xsl:choose>
   <xsl:when test="count(//tei:div)>0">
    <xsl:for-each select="//tei:div[count(ancestor::tei:div) &lt; $depth][.//text() != '']">
     <xsl:variable name="thisID">
      <xsl:call-template name="get_id" />
     </xsl:variable>
     <xsl:variable name="thisLevel" select="count(ancestor::tei:div)" />
     <xsl:variable name="blockID">
      <xsl:value-of select="./@xml:id" />
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
        </xsl:call-template>
      </a>
       </div>
     </div>
    </xsl:for-each>
   </xsl:when>
   <xsl:otherwise>
    <div class="line">- - -</div>
   </xsl:otherwise>
  </xsl:choose>
-->
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
