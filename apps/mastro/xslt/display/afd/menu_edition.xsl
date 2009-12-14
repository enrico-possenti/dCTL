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
 <xsl:variable name="curr_language" select="php:function('dctl_getCurrentLanguage')" />
 <!-- - - - - - - - - - - - - - - - -->
 <!-- ROOT :: SPECIFIC FOR PACKAGE  -->
 <!-- - - - - - - - - - - - - - - - -->
 <xsl:template match="/">
  <h3>
   <xsl:apply-templates
    select="//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:titleStmt/tei:title[@type='main']" />
   <br />
  </h3>
  <div class="h4">
   <xsl:if test="normalize-space(//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:titleStmt/tei:editor[1]) != ''">
   a cura di <xsl:apply-templates
   select="//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:titleStmt/tei:editor" /><br />
   </xsl:if>
   <xsl:apply-templates
    select="//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:publicationStmt/tei:publisher" />
   <xsl:text>, </xsl:text>
   <xsl:apply-templates
    select="//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:publicationStmt/tei:pubPlace" />
   <xsl:text>, </xsl:text>
   <xsl:apply-templates
    select="//tei:fileDesc/tei:sourceDesc/tei:biblFull[contains(@n,'source')]/tei:publicationStmt/tei:date" />
  </div>
  <div class="h2_detail">
   <xsl:value-of
    select="php:function('dctl_getMediaPath4', string(//tei:encodingDesc/tei:editorialDecl/tei:p[@xml:lang='it']))"
    disable-output-escaping="yes" />
  </div>
 </xsl:template>
 <!--  -->
</xsl:stylesheet>
