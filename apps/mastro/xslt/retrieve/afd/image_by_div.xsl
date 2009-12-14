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
  <div class="widget">
   <!--  <div class="widget_head">
    <xsl:value-of
     select="php:function('dctl_putRefs', string(*/*/@rend), $distinctSep, string($doc), $where, 2)"
     />
   </div>
-->
   <div class="widget_body">
    <ul>
     <xsl:for-each select="*/*">
      <li class="widget_box">
       <xsl:variable name="doc" select="./@doc" />
       <xsl:variable name="block" select="./tei:div/@xml:id" />
       <xsl:variable name="link">$().mastro('display', '<xsl:value-of select="$doc"
          />','','','<xsl:value-of select="$block" />');</xsl:variable>
       <xsl:variable name="blockID" select="tei:div/@xml:id" />
       <div class="widget_index">
        <xsl:value-of select="position()" />
       </div>
       <div class="widget_image">
        <xsl:apply-templates select="tei:div/tei:figure" mode="widget_box" />
       </div>
       <!--     <div class="widget_icon" />
   -->
       <div class="widget_text">
        <div class="widget_title">
         <a href="javascript:void(0);" title="{$tooltip_goto}" onclick="{$link}">
          <xsl:value-of
           select="php:function('dctl_putRefs', string(tei:div/@rend), $distinctSep, string(@doc), $where, 1)"
           />&#160;</a>
        </div>
       </div>
      </li>
     </xsl:for-each>
    </ul>
   </div>
  </div>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
