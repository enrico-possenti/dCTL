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
	<!-- ANY -->
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template match="@* | node()">
		<xsl:choose>
			<xsl:when test="ancestor::tei:text">
				<xsl:choose>
					<xsl:when test="(local-name(.)='id') and not(node())">
						<xsl:attribute name="xml:id">
							<xsl:value-of
								select="php:function('getNiceId', string(.), concat('x', string(generate-id(.))))" />
						</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:copy>
							<!-- @XML:ID -->
							<xsl:if
								test="(self::tei:div) or (self::tei:bibl) or (self::tei:pb) or (self::tei:figure) or (self::tei:p) or (self::tei:seg) or (self::tei:name) or (self::tei:rs)  or (@ana != '') or (@rend != '')">
								<xsl:if test="not(@xml:id)">
									<xsl:attribute name="xml:id">
										<xsl:text>x</xsl:text>
										<xsl:choose>
											<xsl:when test="self::tei:div">
												<xsl:text>dv</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:pb">
												<xsl:text>pb</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:p">
												<xsl:text>pf</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:bibl">
												<xsl:text>bl</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:figure">
												<xsl:text>fg</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:seg">
												<xsl:text>sg</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:name">
												<xsl:text>nm</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:when test="self::tei:rs">
												<xsl:text>rs</xsl:text>
												<xsl:number format="000001" level="any" />
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="generate-id(.)" />
											</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
								</xsl:if>
							</xsl:if>
							<!-- P -->
							<xsl:if test="local-name(.) = 'p'">
								<xsl:if test="not(@n)">
									<xsl:attribute name="n">
										<xsl:number level="any" count="tei:p" from="tei:div" />
									</xsl:attribute>
								</xsl:if>
							</xsl:if>
							<!-- LB -->
							<xsl:if test="local-name(.) = 'lb'">
								<xsl:if test="not(@n)">
									<xsl:attribute name="n">
										<xsl:number level="any" count="tei:lb" from="tei:p" />
									</xsl:attribute>
								</xsl:if>
							</xsl:if>
							<!-- - - - - - - - - - - - - - - - -->
							<!-- ADD FAKE PB -->
							<xsl:choose>
								<xsl:when test="self::tei:div and not(child::tei:pb)">
									<xsl:apply-templates select="@*" />
									<pb ed="fake">
										<xsl:choose>
											<xsl:when test="(preceding::tei:pb) ">
												<xsl:for-each select="preceding::tei:pb[1]/@*">
													<xsl:if test="local-name(.) != 'ed'">
														<xsl:attribute name="{local-name(.)}">
															<xsl:value-of select="." />
														</xsl:attribute>
													</xsl:if>
												</xsl:for-each>
											</xsl:when>
											<xsl:when test="(descendant::tei:pb)">
												<xsl:for-each select="descendant::tei:pb[1]/@*">
													<xsl:if test="local-name(.) != 'ed'">
														<xsl:attribute name="{local-name(.)}">
															<xsl:value-of select="." />
														</xsl:attribute>
													</xsl:if>
												</xsl:for-each>
											</xsl:when>
											</xsl:choose>
									</pb>
									<xsl:apply-templates select="node()" />
								</xsl:when>
								<xsl:otherwise>
									<xsl:apply-templates select="@* | node()" />
								</xsl:otherwise>
							</xsl:choose>
							<!-- - - - - - - - - - - - - - - - -->
							<!-- ADD FAKE PB -->
							<xsl:if test="local-name(.) = 'div'">
								<xsl:if test="not(parent::tei:div) and (.//tei:pb)">
									<pb ed="fake">
										<xsl:for-each select="(.//tei:pb)[position()=last()]/@*">
											<xsl:if test="local-name(.) != 'ed'">
												<xsl:attribute name="{local-name(.)}">
													<xsl:value-of select="." />
												</xsl:attribute>
											</xsl:if>
										</xsl:for-each>
									</pb>
								</xsl:if>
							</xsl:if>
							<!-- - - - - - - - - - - - - - - - -->
						</xsl:copy>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:copy>
					<xsl:apply-templates select="@* | node()" />
				</xsl:copy>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
