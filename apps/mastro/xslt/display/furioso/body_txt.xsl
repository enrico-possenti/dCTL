<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
 xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
 xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
 xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
 extension-element-prefixes="tei dctl php exslt dyn str">
 <!-- - - - - - - - - - - - - - - - -->
 <!-- <xsl:import href="mastro_body_header.xsl" /> -->
 <xsl:import href="../../mastro.xsl" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes" />
 <xsl:strip-space elements="*" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="*">
  <xsl:choose>
   <!-- # # SKIP THESE CONTENTS -->
   <xsl:when test="local-name(.)='figDesc'" />
   <xsl:when test="local-name(.)='graphic'" />
   <!-- # # RECORD UI -->
   <xsl:otherwise>
    <!-- # # FLOW UI -->
    <xsl:choose>
     <!-- # FOLLOW IN -->
     <xsl:when test="local-name(.)='index' or local-name(.)='term'">
      <xsl:apply-templates />
     </xsl:when>
     <!-- # TEI : PB -->
     <xsl:when test="local-name(.)='pb'">
      <span>
       <xsl:attribute name="class">
        <xsl:text>tei_</xsl:text>
        <xsl:value-of select="local-name(.)" />
        <xsl:text> </xsl:text>
        <xsl:value-of select="@class" />
       </xsl:attribute>
       <xsl:text>p.</xsl:text>
       <xsl:value-of select="@n" />
       <xsl:if test="@ed != 'ctl'">
        <xsl:text> (</xsl:text>
        <xsl:value-of select="@ed" />
        <xsl:text>)</xsl:text>
       </xsl:if>
      </span>
     </xsl:when>
     <!-- # TEI : LB -->
     <xsl:when test="local-name(.)='lb'">
      <span>
       <xsl:attribute name="class">
        <xsl:text>tei_</xsl:text>
        <xsl:value-of select="local-name(.)" />
        <xsl:text> </xsl:text>
        <xsl:value-of select="@class" />
       </xsl:attribute>
       <br />
      </span>
     </xsl:when>
     <!-- # TEI : MILESTONE -->
     <xsl:when test="local-name(.)='milestone' and @unit='wb'">
      <span class="tei_hyphen" />
     </xsl:when>
     <!-- # # ANY OTHER -->
     <xsl:otherwise>
      <!-- # # ASSIGN ID TO THESE ELEMENTS-->
      <xsl:variable name="thisID">
       <xsl:call-template name="get_id" />
      </xsl:variable>
      <!-- PROCESS ELEMENT -->
      <xsl:element name="span">
       <xsl:attribute name="id">
        <xsl:value-of select="concat($wherePrefix,$thisID)" />
       </xsl:attribute>
       <xsl:attribute name="class">
        <xsl:text>tei_</xsl:text>
        <xsl:value-of select="local-name(.)" />
        <xsl:text> </xsl:text>
        <xsl:value-of select="@class" />
       </xsl:attribute>
       <!-- PROCESS ELEMENT -->
       <xsl:choose>
        <!-- # TEI : DIV -->
        <xsl:when test="local-name(.)='div'">
         <!-- 1st DIV (BLOCK) -->
         <xsl:choose>
          <xsl:when test=". = /">
           <!-- LINKS (BLOCK) -->
           <div class="box_head_extend">
            <!-- link: ANASTATICA -->
            <xsl:call-template name="putAnastatica" />
           </div>
           <ul class="collapsible sortable">
            <!-- IMMAGINE (BLOCK) -->
            <li class="widget" id="{concat($wherePrefix,'w1',$thisID)}">
             <div class="widget_head">
              <div class="align_left">
               <a class="">&#160;</a>
              </div>
              <div class="align_left widget_name">
               <xsl:value-of select="$label" />
              </div>
              <div class="align_right">
               <a class="reservation_handle"
                onclick="$(this).reservation('{$docExt}{$distinctSep}{$label}{$distinctSep}{$theBlock/@rend}');"
                title="{$tooltip_addtobasket}">&#160;</a>
               <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
              </div>
             </div>
             <div class="widget_body">
              <xsl:apply-templates select="./*" />
             </div>
            </li>
           </ul>
          </xsl:when>
          <xsl:otherwise>
           <xsl:apply-templates />
          </xsl:otherwise>
         </xsl:choose>
        </xsl:when>
        <!-- # TEI : REF -->
        <xsl:when test="local-name(.)='ref'">
         <a class="link_ref_internal" title="{$tooltip_goto}" href="#{$wherePrefix}@target">???</a>
        </xsl:when>
        <!-- # TEI : FIGURE -->
        <xsl:when test="local-name(.)='figure'">
         <xsl:if test="tei:graphic">
          <img class="magnify fancyzoom widget_image" alt="{tei:figDesc}"
           src="{concat(php:function('dctl_getPHPvar', 'WEB_PUBLISH'), php:function('dctl_getPHPvar', 'DCTL_MEDIA_MED'), substring-after(tei:graphic/@url, 'img://'), '?', generate-id(.))}"
           rel="{concat(php:function('dctl_getPHPvar', 'WEB_PUBLISH'), php:function('dctl_getPHPvar', 'DCTL_MEDIA_BIG'), substring-after(tei:graphic/@url, 'img://'), '?', generate-id(.))}"
           />
         </xsl:if>
        </xsl:when>
        <!-- # TEI : NOTE -->
        <xsl:when test="local-name(.)='note'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : HEAD -->
        <xsl:when test="local-name(.)='head'">
         <xsl:if test="../@type != 'canto'">
          <xsl:apply-templates />
         </xsl:if>
        </xsl:when>
        <!-- # TEI : P -->
        <xsl:when test="local-name(.)='p'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : NAME -->
        <xsl:when test="local-name(.)='name'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : RS -->
        <xsl:when test="local-name(.)='rs'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : SEG -->
        <xsl:when test="local-name(.)='seg'">
         <xsl:choose>
          <xsl:when test="attribute::corresp">
           <xsl:call-template name="hasCorresp">
            <xsl:with-param name="label" select="@n" />
            <xsl:with-param name="node" select="." />
           </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
           <xsl:apply-templates />
          </xsl:otherwise>
         </xsl:choose>
        </xsl:when>
        <xsl:otherwise>
         <xsl:if test="normalize-space(.//text()) != ''">
          <div class="error"> ?[TXT_1] <xsl:value-of select="name(.)" />
           <xsl:for-each select="@*[local-name(.) != 'id']">
            <xsl:text> </xsl:text>
            <xsl:value-of select="name()" /> = <xsl:value-of select="." />
           </xsl:for-each> ? </div>
         </xsl:if>
        </xsl:otherwise>
       </xsl:choose>
      </xsl:element>
     </xsl:otherwise>
    </xsl:choose>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
