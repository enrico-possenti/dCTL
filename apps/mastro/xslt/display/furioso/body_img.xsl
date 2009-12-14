<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
 xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
 xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
 xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
 extension-element-prefixes="tei dctl php exslt dyn str">
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:import href="body_txt.xsl" />
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
         <xsl:value-of select="concat($wherePrefix,$thisID)" />
        </xsl:attribute>
        <xsl:attribute name="class">
         <xsl:text>dctl_img_</xsl:text>
         <xsl:value-of select="local-name(.)" />
        </xsl:attribute>
        <xsl:text> </xsl:text>
        <xsl:value-of select="@class" />
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
           <!-- dctl:object -->
           <xsl:if test="string-length(./@xml:id) = 4">
            <!-- link: ALLEGORIE -->
            <xsl:call-template name="putCorresp">
             <xsl:with-param name="blockID" select="string(./@xml:id)" />
             <xsl:with-param name="label" select="'Allegoria'" />
             <xsl:with-param name="packType" select="'_ptx'" />
             <xsl:with-param name="packBase" select="substring-before($doc, '_img')" />
            </xsl:call-template>
            <!-- link: IMG -->
            <xsl:call-template name="putCorresp">
             <xsl:with-param name="blockID" select="string(./@xml:id)" />
             <xsl:with-param name="label" select="'Altre edizioni:'" />
             <xsl:with-param name="packType" select="'_img'" />
             <xsl:with-param name="packBase" select="'_img'" />
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
           <li class="widget" id="{concat($wherePrefix,'w2',$thisID)}">
            <div class="widget_head">
             <div class="align_left">
              <a class="collapsible_handle" title="{$tooltip_toggle}">&#160;</a>
             </div>
             <div class="align_left widget_name">Scheda</div>
             <div class="align_right">
              <a class="reservation_handle"
               onclick="$(this).reservation('{$docExt}{$distinctSep}Scheda{$distinctSep}{$theBlock/@rend}');"
               title="{$tooltip_addtobasket}">&#160;</a>
              <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
             </div>
            </div>
            <div class="widget_body collapsible_body">
             <!-- titolo, autore, data -->
             <xsl:variable name="title">
              <xsl:apply-templates select="tei:bibl/tei:publisher" />
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:apply-templates select="tei:bibl/tei:pubPlace" />
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:apply-templates select="tei:bibl/tei:date" />
             </xsl:variable>
             <xsl:variable name="title2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$title" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$title2 != ''">
              <span class="widget_label">Edizione:</span>
              <span class="widget_field">
               <xsl:value-of select="$title2" />
              </span>
             </xsl:if>
             <!-- collocazione, provenienza, committenza -->
             <xsl:apply-templates select="tei:bibl/tei:distributor" />
             <!-- tecnica e misure -->
             <xsl:variable name="tech">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:typology) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:typology" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:material) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:material" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:technique) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:technique" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:measure) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:measure" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
             </xsl:variable>
             <xsl:variable name="tech2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$tech" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$tech2 != ''">
              <span class="widget_label">
               <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:technique) != ''">Tecnica e
               </xsl:if>Misure:</span>
              <span class="widget_field">
               <xsl:value-of select="$tech2" />
              </span>
             </xsl:if>
             <!-- cornice -->
             <xsl:variable name="frame">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:typology) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:typology" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:material) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:material" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:technique) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:technique" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:measure) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:measure" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:position) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:position" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:frame/dctl:desc) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:frame/dctl:desc" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
             </xsl:variable>
             <xsl:variable name="frame2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$frame" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$frame2 != ''">
              <span class="widget_label">Cornice:</span>
              <span class="widget_field">
               <xsl:value-of select="$frame2" />
              </span>
             </xsl:if>
             <!-- supporto -->
             <xsl:variable name="support">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <xsl:if
               test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:typology) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:typology" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if
               test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:material) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:material" />
               <xsl:text> </xsl:text>
              </xsl:if>
              <xsl:if
               test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:technique) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:technique" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:measure) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:measure" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if
               test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:position) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:position" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:support/dctl:desc) != ''">
               <xsl:apply-templates select="tei:figure/dctl:studio/dctl:support/dctl:desc" />
               <xsl:value-of select="concat($distinctSep, ' ')" />
              </xsl:if>
             </xsl:variable>
             <xsl:variable name="support2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$support" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$support2 != ''">
              <span class="widget_label">Cornice:</span>
              <span class="widget_field">
               <xsl:value-of select="$support2" />
              </span>
             </xsl:if>
             <!-- iscrizioni -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:inscription" />
             <!-- posizione -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:position" />
             <!-- note -->
             <xsl:apply-templates select="tei:note[@type='desc']" />
            </div>
           </li>
           <!-- (TAB) -->
           <li class="widget" id="{concat($wherePrefix,'w3',$thisID)}">
            <div class="widget_head">
             <div class="align_left">
              <a class="collapsible_handle" title="{$tooltip_toggle}">&#160;</a>
             </div>
             <div class="align_left widget_name">Studio</div>
             <div class="align_right">
              <a class="reservation_handle"
               onclick="$(this).reservation('{$docExt}{$distinctSep}Studio{$distinctSep}{$theBlock/@rend}');"
               title="{$tooltip_addtobasket}">&#160;</a>
              <a class="drag_handle" title="{$tooltip_drag}">&#160;</a>
             </div>
            </div>
            <div class="widget_body collapsible_body">
             <!-- lista scene -->
             <span class="widget_label" />
             <span class="widget_field">
              <xsl:apply-templates select="tei:figure/dctl:studio/dctl:sceneList" />
             </span>
             <!-- descrizione -->
             <xsl:if test="not(tei:figure/dctl:studio//dctl:studio)">
              <!-- ULTIMO LIVELLO -->
              <xsl:if test="normalize-space(tei:figure/dctl:studio/dctl:desc//text()) != ''">
               <span class="widget_label">Descrizione:</span>
               <span class="widget_field">
                <xsl:apply-templates select="tei:figure/dctl:studio/dctl:desc" />
               </span>
              </xsl:if>
             </xsl:if>
             <!-- link: TXT -->
             <xsl:call-template name="putCorresp">
              <xsl:with-param name="blockID" select="string(ancestor-or-self::*/@xml:id)" />
              <xsl:with-param name="packType" select="'_txt'" />
              <xsl:with-param name="label" select="'Ottave di riferimento:'" />
              <xsl:with-param name="class" select="'widget_label'" />
             </xsl:call-template>
             <!-- link: IMG -->
             <xsl:call-template name="putCorresp">
              <xsl:with-param name="blockID" select="string(ancestor-or-self::*/@xml:id)" />
              <xsl:with-param name="packType" select="'_img'" />
              <xsl:with-param name="packBase" select="$doc" />
              <xsl:with-param name="label" select="'Confronta con:'" />
              <xsl:with-param name="class" select="'widget_label'" />
             </xsl:call-template>
             <!-- personaggi -->
             <xsl:variable name="character">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <!-- select all -->
              <xsl:for-each select="tei:figure/dctl:studio/dctl:sceneList//dctl:character">
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:apply-templates select="." />
              </xsl:for-each>
             </xsl:variable>
             <xsl:variable name="character2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$character" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$character2 != '' or tei:figure/dctl:studio/dctl:character">
              <span class="widget_label">Personaggi:</span>
              <span class="widget_field">
               <xsl:value-of select="$character2" disable-output-escaping="yes" />
               <!-- select locals -->
               <xsl:for-each select="tei:figure/dctl:studio/dctl:character">
                <xsl:if test="position() > 1">
                 <xsl:text>, </xsl:text>
                </xsl:if>
                <xsl:apply-templates select="." />
               </xsl:for-each>
              </span>
             </xsl:if>
             <!-- ambientazioni -->
             <xsl:variable name="settings">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <!-- select all -->
              <xsl:for-each select="tei:figure/dctl:studio/dctl:sceneList//dctl:settings">
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:apply-templates select="." />
              </xsl:for-each>
             </xsl:variable>
             <xsl:variable name="settings2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="translate($settings, ',', $distinctSep)" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$settings2 != '' or tei:figure/dctl:studio/dctl:settings">
              <span class="widget_label">Ambientazioni:</span>
              <span class="widget_field">
               <xsl:value-of select="$settings2" disable-output-escaping="yes" />
               <!-- select locals -->
               <xsl:for-each select="tei:figure/dctl:studio/dctl:settings">
                <xsl:if test="position() > 1">
                 <xsl:text>, </xsl:text>
                </xsl:if>
                <xsl:apply-templates select="." />
               </xsl:for-each>
              </span>
             </xsl:if>
             <!-- pattern -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:pattern" />
             <!-- iconclass -->
             <xsl:variable name="iconTerm">
              <xsl:value-of select="concat($distinctSep, ' ')" />
              <!-- select all -->
              <xsl:for-each select="tei:figure/dctl:studio/dctl:sceneList//dctl:iconTerm">
               <xsl:value-of select="concat($distinctSep, ' ')" />
               <xsl:text>&lt;a href="http://www.iconclass.nl/libertas/ic?style=notationbb.xsl&amp;task=getnotation&amp;taal=it&amp;datum=</xsl:text>
               <xsl:value-of select="@iconclass" />
               <xsl:text>" title="</xsl:text>
               <xsl:value-of select="$tooltip_goto" />
               <xsl:text>" target="_new" class="external"&gt;</xsl:text>
               <xsl:apply-templates select="@iconclass" />
               <xsl:text>&lt;/a&gt;</xsl:text>
               <xsl:text>: </xsl:text>
               <xsl:apply-templates select="@n" />
              </xsl:for-each>
             </xsl:variable>
             <xsl:variable name="iconTerm2">
              <xsl:call-template name="getDistinct">
               <xsl:with-param name="text" select="$iconTerm" />
              </xsl:call-template>
             </xsl:variable>
             <xsl:if test="$iconTerm2 != '' or tei:figure/dctl:studio/dctl:iconTerm">
              <span class="widget_label">Iconclass:</span>
              <span class="widget_field">
               <xsl:value-of select="$iconTerm2" disable-output-escaping="yes" />
               <!-- select locals -->
               <xsl:for-each select="tei:figure/dctl:studio/dctl:iconTerm">
                <xsl:if test="position() > 1">
                 <xsl:text>, </xsl:text>
                </xsl:if>
                <a
                 href="http://www.iconclass.nl/libertas/ic?style=notationbb.xsl&amp;task=getnotation&amp;taal=it&amp;datum={@iconclass}"
                 title="{$tooltip_goto}" target="_new" class="external">
                 <xsl:apply-templates select="@iconclass" />
                </a>
                <xsl:text>: </xsl:text>
                <xsl:apply-templates select="@n" />
               </xsl:for-each>
              </span>
             </xsl:if>
             <!-- extra -->
             <xsl:apply-templates select="tei:figure/dctl:studio/dctl:extra" />
             <!-- note -->
             <xsl:apply-templates select="tei:note[@type='studio']" />
            </div>
           </li>
          </ul>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : STUDIO -->
        <xsl:when test="local-name(.)='studio'" />
        <!-- # DCTL : INSCRIPTION -->
        <xsl:when test="local-name(.)='inscription'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">Iscrizioni:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : TECHNIQUE -->
        <xsl:when test="local-name(.)='technique'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : TYPOLOGY -->
        <xsl:when test="local-name(.)='typology'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : MATERIAL -->
        <xsl:when test="local-name(.)='material'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : MEASURE -->
        <xsl:when test="local-name(.)='measure'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : POSITION -->
        <xsl:when test="local-name(.)='position'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">Posizione:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : DESC -->
        <xsl:when test="local-name(.)='desc'">
         <xsl:if test="local-name(parent::node()) = 'frame'">
          <xsl:value-of select="concat($distinctSep, ' ')" />
         </xsl:if>
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : CHARACTERS -->
        <xsl:when test="local-name(.)='character'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : SETTINGS -->
        <xsl:when test="local-name(.)='settings'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : ICONTERM -->
        <xsl:when test="local-name(.)='iconTerm'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : EXTRA -->
        <xsl:when test="local-name(.)='extra'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">Materiale extra-testuale:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : SCENELIST -->
        <xsl:when test="local-name(.)='sceneList'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <ul class="scene_loader">
           <xsl:for-each select="dctl:scene">
            <xsl:if test="normalize-space(.//text()) != ''">
             <li>
              <a class="collapsible_handle2" title="{$tooltip_toggle}">
               <xsl:apply-templates select="." />&#160; </a>
              <xsl:if test="count(tei:div) > 0">
               <ul class="collapsible_body">
                <xsl:variable name="putRiproduzione">
                 <xsl:call-template name="putRiproduzione" />
                </xsl:variable>
                <xsl:if test="$putRiproduzione != ''">
                 <li>
                  <xsl:copy-of select="$putRiproduzione" />
                 </li>
                </xsl:if>
                <xsl:for-each select="./tei:div">
                 <li>
                  <xsl:variable name="link">
                   <xsl:call-template name="putCorresp">
                    <xsl:with-param name="blockID" select="string(./@xml:id)" />
                    <xsl:with-param name="thisID" select="$high" />
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
        <!-- # DCTL : SCENE -->
        <xsl:when test="local-name(.)='scene'">
         <xsl:apply-templates select="dctl:sequence" />: <xsl:apply-templates select="dctl:desc" />
         <xsl:apply-templates select="dctl:meaning" />
        </xsl:when>
        <!-- # DCTL : PATTERN -->
        <xsl:when test="local-name(.)='pattern'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">Percorso di lettura:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : FRAME -->
        <xsl:when test="local-name(.)='frame'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : SUPPORT -->
        <xsl:when test="local-name(.)='support'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # DCTL : SEQUENCE -->
        <xsl:when test="local-name(.)='sequence'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <xsl:apply-templates />
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : TRANSCRIPTION -->
        <xsl:when test="local-name(.)='transcription'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">
           <xsl:value-of select="./@n" />
          </span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # DCTL : MEANING -->
        <xsl:when test="local-name(.)='meaning'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label2">significato:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # TEI : FIGURE -->
        <xsl:when test="local-name(.)='figure'">
         <xsl:apply-imports />
        </xsl:when>
        <!-- # TEI : BIBL -->
        <xsl:when test="local-name(.)='bibl'" />
        <!-- # TEI : TITLE -->
        <xsl:when test="local-name(.)='title'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : AUTHOR -->
        <xsl:when test="local-name(.)='author'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : PUBLISHER -->
        <xsl:when test="local-name(.)='publisher'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : PUBPLACE -->
        <xsl:when test="local-name(.)='pubPlace'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : DATE -->
        <xsl:when test="local-name(.)='date'">
         <xsl:apply-templates />
        </xsl:when>
        <!-- # TEI : DISTRIBUTOR -->
        <xsl:when test="local-name(.)='distributor'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">
           <xsl:choose>
            <xsl:when test="@role='location'">Collocazione:</xsl:when>
            <xsl:when test="@role='provenance'">Provenienza:</xsl:when>
            <xsl:when test="@role='commissioner'">Committenza:</xsl:when>
            <xsl:otherwise>?<xsl:value-of select="@role" />?:</xsl:otherwise>
           </xsl:choose>
          </span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
        </xsl:when>
        <!-- # TEI : NOTE -->
        <xsl:when test="local-name(.)='note'">
         <xsl:if test="normalize-space(.//text()) != ''">
          <span class="widget_label">Note:</span>
          <span class="widget_field">
           <xsl:apply-templates />
          </span>
         </xsl:if>
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
      <xsl:if test="normalize-space(.//text()) != ''">
       <div class="error"> ?[IMG] <xsl:value-of select="name(.)" />
        <xsl:for-each select="@*[local-name(.) != 'id']">
         <xsl:text> </xsl:text>
         <xsl:value-of select="name()" /> = <xsl:value-of select="." />
        </xsl:for-each> ? </div>
      </xsl:if>
     </xsl:otherwise>
    </xsl:choose>
   </xsl:otherwise>
  </xsl:choose>
 </xsl:template>
 <!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
