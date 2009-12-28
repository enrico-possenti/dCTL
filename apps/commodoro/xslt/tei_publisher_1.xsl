<?xml version="1.0" encoding="UTF-8"?>
<!-- 
	/**
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| A digital tale (C) 2009 Enrico Possenti :: dCTL                     |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>            |
	| License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike     |
	|          http://creativecommons.org/licenses/by-nc-sa/3.0/          |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	| A main file for "commodoro"                                         |
	+ - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - +
	*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
	xmlns:tei="http://www.tei-c.org/ns/1.0" xmlns:dctl="http://www.ctl.sns.it/ns/1.0"
	xmlns:php="http://php.net/xsl" xmlns:exslt="http://exslt.org/common"
	xmlns:dyn="http://exslt.org/dynamic" xmlns:str="http://exslt.org/strings"
	extension-element-prefixes="tei dctl php exslt dyn str">
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:output omit-xml-declaration="no" version="1.0" method="xml" indent="yes" encoding="UTF-8" />
	<xsl:strip-space elements="*" />
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:variable name="basepath" />
	<!-- - - - - - - - - - - - - - - - -->
	<!-- ROOT -->
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template match="/">
		<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE TEI SYSTEM "</xsl:text>
		<xsl:value-of select="$basepath" />
		<xsl:text disable-output-escaping="yes">dctl.core.dtd" [
			&lt;!ENTITY % TEI.extension.dtd SYSTEM "</xsl:text>
		<xsl:value-of select="$basepath" />
		<xsl:text disable-output-escaping="yes">dctl.extension.dtd"&gt;
			%TEI.extension.dtd;
			]&gt;
		</xsl:text>
		<xsl:apply-templates />
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
	<!-- PROCESSING-INSTRUCTION -->
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template match="processing-instruction()">
		<xsl:value-of select="php:function(name(), string(/*/@xml:id), string(.))"
			disable-output-escaping="yes" />
		<xsl:apply-templates select="@* | node()" />
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
	<!-- ANY -->
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template match="@* | node()">
		<xsl:copy>
			<xsl:apply-templates select="@* | node()" />
		</xsl:copy>
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
