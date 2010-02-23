<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
 xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
 xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
 xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
 extension-element-prefixes="tei dctl php exslt dyn str">
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:import href="body_img.xsl" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes" />
 <xsl:strip-space elements="*" />
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="*">
  <xsl:choose>
   <xsl:when test="node()[ancestor-or-self::tei:div[@type='dctlObject']]">
    <xsl:choose>
     <!-- # FOLLOW IN -->
     <xsl:when test="local-name(.)='index' or local-name(.)='term'">
      <xsl:apply-imports />
     </xsl:when>
     <!-- # TEI:PB -->
     <xsl:when test="local-name(.)='pb'">
      <xsl:apply-imports />
     </xsl:when>
     <!-- # TEI : LB -->
     <xsl:when test="local-name(.)='lb'">
      <xsl:apply-imports />
     </xsl:when>
     <!-- # TEI : MILESTONE -->
     <xsl:when test="local-name(.)='milestone' and @unit='wb'">
      <xsl:apply-imports />
     </xsl:when>
     <!-- # # ANY OTHER -->
     <xsl:otherwise>
      <!-- # # ASSIGN ID TO THESE ELEMENTS-->
      <xsl:variable name="thisID">
       <xsl:call-template name="get_id" />
      </xsl:variable>
      <xsl:element name="span">
       <xsl:if test="substring-after(name(.), 'dctl:')">
        <xsl:attribute name="id">
         <xsl:value-of select="$wherePrefix" />
         <xsl:value-of select="$thisID" />
        </xsl:attribute>
        <xsl:attribute name="class">
         <xsl:text>dctl_ptx_</xsl:text>
         <xsl:value-of select="local-name(.)" />
         <xsl:text> </xsl:text>
         <xsl:value-of select="@class" />
        </xsl:attribute>
       </xsl:if>
       <!-- PROCESS ELEMENT -->
       <xsl:choose>
        <!-- # TEI : DIV -->
        <xsl:when test="local-name(.)='div'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <!-- LINKS (BLOCK) -->
          <div class="box_head_extend">
           <!-- link: ANASTATICA -->
           <xsl:call-template name="putAnastatica" />
           <xsl:if test="string-length(./@xml:id) = 4">
            <!-- link: ILLUSTRAZIONI -->
            <xsl:call-template name="putCorresp">
             <xsl:with-param name="blockID" select="string(./@xml:id)" />
             <xsl:with-param name="label" select="'Illustrazione'" />
             <xsl:with-param name="packType" select="'_img'" />
             <xsl:with-param name="packBase" select="substring-before($doc, '_ptx')" />
            </xsl:call-template>
            <!-- link: PTX -->
            <xsl:call-template name="putCorresp">
             <xsl:with-param name="blockID" select="string(./@xml:id)" />
             <xsl:with-param name="label" select="'Altre edizioni:'" />
             <xsl:with-param name="packType" select="'_ptx'" />
             <xsl:with-param name="packBase" select="'_ptx'" />
            </xsl:call-template>
           </xsl:if>
          </div>
          <ul class="collapsible sortable">
           <!-- IMMAGINE (BLOCK) -->
           <xsl:if test="count(tei:figure/tei:graphic)>0">
            <li class="widget" id="{concat($wherePrefix,'w1',$thisID)}">
             <div class="widget_head">
              <div class="align_left">
               <a class="collapsible_handle" title="{$tooltip_toggle}">&#160;</a>
              </div>
              <div class="align_left widget_name">Immagine</div>
              <div class="align_right">
               <a class="view_handle"
                onclick="$('.magnify:first', $(this).parents('.widget:first')).magnify();"
                title="{$tooltip_zoom}">&#160;</a>
               <a class="reservation_handle"
                onclick="$(this).reservation('{$docExt}{$distinctSep}Immagine{$distinctSep}{$theBlock/@rend}');"
                title="{$tooltip_addtobasket}">&#160;</a>
               <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
              </div>
             </div>
             <div class="widget_body collapsible_body">
              <xsl:apply-templates select="tei:figure" />
             </div>
            </li>
           </xsl:if>
           <!-- (TAB) -->
           <xsl:if test="tei:figure/dctl:studio/dctl:transcription[1] != ''">
            <li class="widget" id="{concat($wherePrefix,'w2',$thisID)}">
             <div class="widget_head">
              <div class="align_left">
               <a class="collapsible_handle" title="{$tooltip_toggle}">&#160;</a>
              </div>
              <div class="align_left widget_name">Trascrizione</div>
              <div class="align_right">
               <a class="reservation_handle"
                onclick="$(this).reservation('{$docExt}{$distinctSep}Trascrizione{$distinctSep}{$theBlock/@rend}');"
                title="{$tooltip_addtobasket}">&#160;</a>
               <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
              </div>
             </div>
             <div class="widget_body collapsible_body">
              <!-- trascrizione -->
              <xsl:apply-templates select="tei:figure/dctl:studio/dctl:transcription" />
             </div>
            </li>
           </xsl:if>
           <!-- (TAB) -->
           <li class="widget" id="{concat($wherePrefix,'w3',$thisID)}">
            <div class="widget_head">
             <div class="align_left">
              <a class="collapsible_handle" title="{$tooltip_toggle}">&#160;</a>
             </div>
             <div class="align_left widget_name">Allegoria</div>
             <div class="align_right">
              <a class="reservation_handle"
               onclick="$(this).reservation('{$docExt}{$distinctSep}Allegoria{$distinctSep}{$theBlock/@rend}');"
               title="{$tooltip_addtobasket}">&#160;</a>
              <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
             </div>
            </div>
            <div class="widget_body collapsible_body">
             <!-- titolo, autore, data -->
             <xsl:if test="normalize-space(tei:bibl/tei:author) != ''">
              <span class="widget_label">Autore:</span>
              <span class="widget_field">
               <xsl:apply-templates select="tei:bibl/tei:author" />
              </span>
             </xsl:if>
             <!-- lista scene -->
             <xsl:if test="tei:figure/dctl:studio//dctl:desc != '' or tei:figure/dctl:studio//tei:head != ''"><!-- almeno del contenuto! -->
              <span class="widget_label">Episodi e Significati:</span>
             <span class="widget_field">&#160;</span>
             <span class="widget_field">
              <xsl:apply-templates select="tei:figure/dctl:studio/dctl:list" />
             </span>
              <!-- descrizione -->
             <xsl:if test="not(tei:figure/dctl:studio//dctl:studio)">
              <!-- ULTIMO LIVELLO -->
              <ul>
               <xsl:variable name="putRiproduzione">
                <xsl:call-template name="putRiproduzione" />
               </xsl:variable>
               <xsl:if test="$putRiproduzione != ''">
                <li>
                 <xsl:value-of select="$putRiproduzione" />
                </li>
               </xsl:if>
               <xsl:for-each select=".">
                <li>
                 <xsl:variable name="link">
                  <xsl:call-template name="putCorresp">
                   <xsl:with-param name="packType" select="'_txt'" />
                   <xsl:with-param name="blockID" select="string(./@xml:id)" />
                   <xsl:with-param name="label" select="'Ottave di riferimento:(_remove_)'" />
                  </xsl:call-template>
                 </xsl:variable>
                 <span class="widget_field">
                  <a title="{$tooltip_goto}" href="javascript:void(0);">
                   <xsl:attribute name="id">
                    <xsl:value-of select="concat($wherePrefix,./@xml:id)" />
                   </xsl:attribute>
                   <xsl:attribute name="onclick">
                    <xsl:value-of select="$link" />
                   </xsl:attribute>
                   <xsl:apply-templates select="@n" />: <xsl:apply-templates
                    select="tei:head//text()" />
                  </a>
                 </span>
                 <xsl:apply-templates select="tei:figure/dctl:studio/dctl:meaning" />
                 <!-- link: PTX -->
                 <xsl:call-template name="putCorresp">
                  <xsl:with-param name="blockID" select="string(./@xml:id)" />
                  <xsl:with-param name="packType" select="'_ptx'" />
                  <xsl:with-param name="packBase" select="$doc" />
                  <xsl:with-param name="label" select="'confronta con:'" />
                  <xsl:with-param name="class" select="'widget_label2'" />
                 </xsl:call-template>
                </li>
               </xsl:for-each>
              </ul>
              <!--             <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:desc//text()) != ''">
               <span class="widget_label">Descrizione:</span>
               <span class="widget_field">
                <xsl:apply-templates select="tei:figure/dctl:studio/dctl:desc" />
               </span>
              </xsl:if>
-->
             </xsl:if>
             </xsl:if>
             <!-- personaggi -->
             <xsl:variable name="character">
              <!-- select all -->
              <xsl:for-each
               select="tei:figure/dctl:studio/dctl:transcription//*[contains(@ana, 'func_character')]">
               <xsl:sort select="@ana" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:apply-templates select="@n" />
              </xsl:for-each>
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:for-each
               select="tei:figure/dctl:studio/dctl:transcription//*[contains(@ana, 'func_object')]">
               <xsl:sort select="@ana" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:apply-templates select="@n" />
              </xsl:for-each>
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:for-each
               select="tei:figure/dctl:studio/dctl:transcription//*[contains(@ana, 'func_place')]">
               <xsl:sort select="@ana" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:apply-templates select="@n" />
              </xsl:for-each>
             </xsl:variable>
             <xsl:variable name="character2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$character" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$character2 != ''">
              <span class="widget_label">Personaggi, oggetti, luoghi:</span>
              <span class="widget_field">
               <xsl:value-of select="$character2" disable-output-escaping="yes" />
               <!-- select locals -->
               <xsl:for-each select="tei:figure/dctl:studio/dctl:character">
                <xsl:if test="position() > 1">
                 <xsl:text>, </xsl:text>
                </xsl:if>
                <xsl:apply-templates select="@n" />
               </xsl:for-each>
              </span>
             </xsl:if>
             <!-- ambientazioni -->
             <xsl:variable name="settings">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <!-- select locals -->
              <xsl:apply-templates select="tei:figure/dctl:studio/dctl:settings" />
              <!-- select all -->
              <xsl:apply-templates select="tei:figure/dctl:studio/dctl:list//dctl:settings" />
             </xsl:variable>
             <xsl:variable name="settings2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="translate($settings, ',', $distinctSep)" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$settings2 != ''">
              <span class="widget_label">Ambientazioni:</span>
              <span class="widget_field">
               <xsl:value-of select="$settings2" />
              </span>
             </xsl:if>
             <!-- pattern -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:pattern" />
             <!-- iconclass -->
             <xsl:variable name="topic">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <!-- select all -->
              <xsl:for-each select="tei:figure/dctl:studio/dctl:list//dctl:topic">
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:text>&lt;a href="http://www.iconclass.nl/libertas/ic?style=notationbb.xsl&amp;task=getnotation&amp;taal=it&amp;datum=</xsl:text>
               <xsl:value-of select="@iconclass" />
               <xsl:text>" title="</xsl:text>
               <xsl:value-of select="$tooltip_goto" />
               <xsl:text>" target="_new" class="external"&gt;</xsl:text>
               <xsl:apply-templates />
               <xsl:text>&lt;/a&gt;</xsl:text>
              </xsl:for-each>
             </xsl:variable>
             <xsl:variable name="topic2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$topic" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$topic2 != '' or tei:figure/dctl:studio/dctl:topic">
              <span class="widget_label">Iconclass:</span>
              <span class="widget_field">
               <xsl:value-of select="$topic2" disable-output-escaping="yes" />
               <!-- select locals -->
               <xsl:for-each select="tei:figure/dctl:studio/dctl:topic">
                <xsl:if test="position() > 1">
                 <xsl:text>, </xsl:text>
                </xsl:if>
                <a
                 href="http://www.iconclass.nl/libertas/ic?style=notationbb.xsl&amp;task=getnotation&amp;taal=it&amp;datum={@iconclass}"
                 title="{$tooltip_goto}" target="_new" class="external">
                 <xsl:apply-templates select="." />
                </a>
               </xsl:for-each>
              </span>
             </xsl:if>
             <!-- extra -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:extra" />
             <!-- note -->
             <xsl:apply-templates select="tei:note" />
            </div>
           </li>
          </ul>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : list -->
        <xsl:when test="local-name(.)='list'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <ul class="scene_loader">
           <xsl:for-each select="dctl:item">
            <xsl:if test="normalize-space(.//text()) != ''">
             <li>
              <a class="collapsible_handle2" title="{$tooltip_toggle}">
               <xsl:apply-templates select="." />&#160;</a>
              <xsl:if test="count(tei:div) > 0">
               <ul class="collapsible_body">
                <xsl:variable name="putRiproduzione">
                 <xsl:call-template name="putRiproduzione" />
                </xsl:variable>
                <xsl:if test="$putRiproduzione != ''">
                 <li>
                  <xsl:value-of select="$putRiproduzione" />
                 </li>
                </xsl:if>
                <xsl:for-each select="./tei:div">
                 <li>
                  <xsl:variable name="link">
                   <xsl:call-template name="putCorresp">
                    <xsl:with-param name="packType" select="'_txt'" />
                    <xsl:with-param name="blockID" select="string(./@xml:id)" />
                    <xsl:with-param name="label" select="'Ottave di riferimento:(_remove_)'" />
                   </xsl:call-template>
                  </xsl:variable>
                  <span class="widget_field">
                   <a title="{$tooltip_goto}" href="javascript:void(0);">
                    <xsl:attribute name="id">
                     <xsl:value-of select="concat($wherePrefix,./@xml:id)" />
                    </xsl:attribute>
                    <xsl:attribute name="onclick">
                     <xsl:value-of select="$link" />
                    </xsl:attribute>
                    <xsl:apply-templates select="@n" />: <xsl:apply-templates
                     select="tei:head//text()" />
                   </a>
                  </span>
                  <xsl:apply-templates select="tei:figure/dctl:studio/dctl:meaning" />
                  <!-- link: PTX -->
                  <xsl:call-template name="putCorresp">
                   <xsl:with-param name="blockID" select="string(./@xml:id)" />
                   <xsl:with-param name="packType" select="'_ptx'" />
                   <xsl:with-param name="packBase" select="$doc" />
                   <xsl:with-param name="label" select="'confronta con:'" />
                   <xsl:with-param name="class" select="'widget_label2'" />
                  </xsl:call-template>
                 </li>
                </xsl:for-each>
               </ul>
              </xsl:if>
             </li>
            </xsl:if>
           </xsl:for-each>
          </ul>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : @ANA = FUNC_CHARACTER -->
        <xsl:when
         test="*[contains(./@ana, 'func_character') or contains(./@ana, 'func_object') or contains(./@ana, 'func_place')]">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # OTHERWISE -->
        <xsl:otherwise>
         <xsl:apply-imports />
        </xsl:otherwise>
       </xsl:choose>
      </xsl:element>
     </xsl:otherwise>
    </xsl:choose>
   </xsl:when>
   <!-- - - - - - - - - - - - - - - - -->
   <xsl:when test="node()[descendant::tei:div[@type='dctlObject']]">
    <xsl:apply-templates select="//tei:div[@type='dctlObject'][1]" />
   </xsl:when>
   <!-- - - - - - - - - - - - - - - - -->
   <xsl:otherwise>
    <xsl:choose>
     <xsl:when test="dctl:*">
      <xsl:apply-templates />
     </xsl:when>
     <xsl:otherwise>
      <xsl:apply-imports />
     </xsl:otherwise>
    </xsl:choose>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
