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
	<xsl:import href="../../_shared/functions.inc.xsl" />
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
		<xsl:copy>
			<xsl:if test="ancestor-or-self::tei:text">
				<!-- (self::tei:div[count(ancestor::tei:div) &lt;= 1 ]) -->
				<!-- div | @ana | @rend | @n -->
				<xsl:variable name="force"
					select="(self::tei:div[child::tei:pb]) or (self::tei:pb[not(@ed='fake')]) or ((@ana != '') and (not(contains(@xml:id, '.')) or (contains(@xml:id, '.001')))) or (@rend != '') or (@corresp != '')" />
				<!-- ID x LINK -->
				<xsl:if test="(@xml:id and not(starts-with(@xml:id, 'x'))) or ($force)">
					<xsl:if test="not(contains(@xml:id, '.')) or (contains(@xml:id, '.001')) or ($force)">
						<xsl:variable name="aContent">
							<xsl:choose>
								<xsl:when test="normalize-space(@rend)!=''">
									<xsl:value-of select="normalize-space(@rend)" />
								</xsl:when>
								<xsl:otherwise>
									<!-- RISORSA -->
									<xsl:value-of
										select="/tei:TEI/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n='short']" />
									<xsl:for-each select="ancestor-or-self::tei:div[./tei:head//text() != '']">
										<xsl:value-of select="$distinctSep" />
										<xsl:call-template name="getIndex">
											<xsl:with-param name="blockID" select="./@xml:id" />
										</xsl:call-template>
									</xsl:for-each>
									<xsl:value-of select="$distinctSep" />
									<xsl:choose>
										<xsl:when test="self::tei:div">
											<xsl:value-of select="child::tei:pb[1]/@label" />
											<xsl:text>.</xsl:text>
											<xsl:value-of select="child::tei:pb[1]/@n" />
										</xsl:when>
										<xsl:when test="self::tei:pb">
											<xsl:value-of select="@label" />
											<xsl:text>.</xsl:text>
											<xsl:value-of select="@n" />
										</xsl:when>
										<xsl:otherwise>
											<xsl:value-of select="preceding::tei:pb[1]/@label" />
											<xsl:text>.</xsl:text>
											<xsl:value-of select="preceding::tei:pb[1]/@n" />
										</xsl:otherwise>
									</xsl:choose>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:variable>
						<xsl:if test="$aContent != ''">
							<xsl:attribute name="rend">
								<xsl:value-of select="normalize-space($aContent)" />
							</xsl:attribute>
						</xsl:if>
					</xsl:if>
				</xsl:if>
			</xsl:if>
			<xsl:choose>
				<!-- @REND pre-assegnato -->
				<xsl:when test="attribute::rend">
					<xsl:attribute name="rend">
						<xsl:value-of
							select="concat(/tei:TEI/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n='short'], ', ', @rend)"
						 />
					</xsl:attribute>
					<xsl:apply-templates select="@*[local-name(.) != 'rend'] | node()" />
				</xsl:when>
				<!-- RS -->
				<xsl:when test="self::tei:rs">
					<xsl:choose>
						<xsl:when test="normalize-space(@n) != ''">
							<xsl:value-of select="normalize-space(php:function('uc_first', string(@n)))" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="n">
								<xsl:value-of select="normalize-space(php:function('uc_first',  str:concat(.//text())))" />
							</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:apply-templates select="@xml:id" />
					<xsl:apply-templates select="@ana" />
					<xsl:apply-templates select="@type" />
					<xsl:apply-templates select="node()" />
				</xsl:when>
				<!-- NAME -->
				<xsl:when test="self::tei:name">
					<xsl:attribute name="key">
						<xsl:value-of select="@key" />
					</xsl:attribute>
					<xsl:variable name="regName">
						<xsl:value-of select="php:function('get_dbName', string(@key), 'name')" />
					</xsl:variable>
					<xsl:if test="$regName != ''">
						<xsl:attribute name="n">
							<xsl:value-of select="$regName" />
						</xsl:attribute>
					</xsl:if>
					<xsl:variable name="regType">
						<xsl:value-of select="php:function('get_dbName', string(@key), 'type')" />
					</xsl:variable>
					<xsl:if test="$regType != ''">
						<xsl:attribute name="type">
							<xsl:value-of select="$regType" />
						</xsl:attribute>
					</xsl:if>
					<xsl:variable name="regSubtype">
						<xsl:value-of select="php:function('get_dbName', string(@key), 'subtype')" />
					</xsl:variable>
					<xsl:if test="$regSubtype != ''">
						<xsl:attribute name="subtype">
							<xsl:value-of select="$regSubtype" />
						</xsl:attribute>
					</xsl:if>
					<xsl:apply-templates select="@xml:id" />
					<xsl:apply-templates select="@ana" />
					<xsl:apply-templates select="@corresp" />
					<xsl:apply-templates select="node()" />
				</xsl:when>
				<!-- TOPIC -->
				<xsl:when test="self::dctl:topic">
					<xsl:attribute name="key">
						<xsl:value-of select="@key" />
					</xsl:attribute>
					<xsl:variable name="iconclass">
						<xsl:value-of select="php:function('get_dbIconclass', string(@key), 'iconclass')" />
					</xsl:variable>
					<xsl:if test="$iconclass != ''">
						<xsl:attribute name="iconclass">
							<xsl:value-of select="$iconclass" />
						</xsl:attribute>
					</xsl:if>
					<xsl:variable name="regName">
						<xsl:value-of select="php:function('get_dbIconclass', string(@key), 'name')" />
					</xsl:variable>
					<xsl:if test="$regName != ''">
						<xsl:attribute name="n">
							<xsl:value-of select="$regName" />
						</xsl:attribute>
					</xsl:if>
					<xsl:apply-templates select="@xml:id" />
					<xsl:apply-templates select="@ana" />
					<xsl:apply-templates select="node()" />
				</xsl:when>
					<!-- ANY OTHER -->
				<xsl:otherwise>
					<xsl:apply-templates select="@* | node()" />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:copy>
		<!-- * * * * * * * * * * * * * -->
		<!-- VALIDO SOLO PER IL FURIOSO -->
		<!-- * * * * * * * * * * * * * -->
		<xsl:if test="true()=false()">
			<xsl:copy>
				<xsl:if test="ancestor-or-self::tei:text">
					<xsl:variable name="force"
						select="(self::tei:div[count(ancestor::tei:div) &lt;= 1 ]) or ((@ana != '') and (not(contains(@xml:id, '.')) or (contains(@xml:id, '.001')))) or (@rend != '')" />
					<!-- ID x LINK -->
					<xsl:if test="(@xml:id and not(starts-with(@xml:id, 'x'))) or ($force)">
						<xsl:if test="not(contains(@xml:id, '.')) or (contains(@xml:id, '.001')) or ($force)">
							<xsl:variable name="aContent">
								<xsl:choose>
									<xsl:when test="normalize-space(@rend)!=''">
										<xsl:value-of select="normalize-space(@rend)" />
									</xsl:when>
									<xsl:otherwise>
										<!-- RISORSA -->
										<xsl:value-of
											select="/tei:TEI/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n='short']" />
										<!-- DIV(s) -->
										<xsl:variable name="blockID-block"
											select="ancestor-or-self::tei:div[tei:head//text() != ''][@xml:id][last()]/@xml:id" />
										<xsl:variable name="blockID-node"
											select="ancestor-or-self::tei:div[tei:head//text() != ''][@xml:id][1]/@xml:id" />
										<xsl:variable name="blockID-span"
											select="ancestor-or-self::tei:div[@type and @type != 'dctlObject']" />
										<!-- DIV 1 livello -->
										<xsl:if test="($blockID-block != '')">
											<xsl:value-of select="$distinctSep" />
											<xsl:call-template name="getIndex">
												<xsl:with-param name="blockID" select="$blockID-block" />
											</xsl:call-template>
										</xsl:if>
										<!-- DIV padre -->
										<xsl:if test="not($blockID-span)">
											<xsl:if test="($blockID-node != '') and ($blockID-block != $blockID-node)">
												<xsl:value-of select="$distinctSep" />
												<xsl:call-template name="getIndex">
													<xsl:with-param name="blockID" select="$blockID-node" />
												</xsl:call-template>
											</xsl:if>
										</xsl:if>
										<!-- ??? -->
										<xsl:if test="$blockID-span">
											<xsl:choose>
												<xsl:when test="contains(@xml:id, '.')">
													<xsl:if test="contains(@xml:id, '.001')">
														<xsl:variable name="stem" select="concat(substring-before(@xml:id, '.'), '.')" />
														<xsl:variable name="blocks" select="/tei:TEI/tei:text//*[starts-with(@xml:id, $stem)]" />
														<xsl:call-template name="getSpan">
															<xsl:with-param name="stem" select="$stem" />
															<xsl:with-param name="blocks" select="$blocks" />
														</xsl:call-template>
													</xsl:if>
												</xsl:when>
												<xsl:otherwise>
													<xsl:if test="count(ancestor::tei:div) > 0">
														<xsl:call-template name="getSpan">
															<xsl:with-param name="blocks" select="id(@xml:id)" />
														</xsl:call-template>
													</xsl:if>
												</xsl:otherwise>
											</xsl:choose>
										</xsl:if>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:variable>
							<xsl:if test="$aContent != ''">
								<xsl:attribute name="rend">
									<xsl:value-of select="normalize-space($aContent)" />
								</xsl:attribute>
							</xsl:if>
						</xsl:if>
					</xsl:if>
				</xsl:if>
				<xsl:choose>
					<!-- @REND pre-assegnato -->
					<xsl:when test="attribute::rend">
						<xsl:attribute name="rend">
							<xsl:value-of
								select="concat(/tei:TEI/tei:teiHeader/tei:encodingDesc/tei:samplingDecl/tei:p[@n='short'], ', ', @rend)"
							 />
						</xsl:attribute>
						<xsl:apply-templates select="@*[local-name(.) != 'rend'] | node()" />
					</xsl:when>
					<!-- RS -->
					<xsl:when test="local-name(.)='rs'">
						<xsl:choose>
							<xsl:when test="normalize-space(@n) != ''">
								<xsl:value-of select="normalize-space(php:function('uc_first', string(@n)))" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:attribute name="n">
									<xsl:value-of select="normalize-space(php:function('uc_first',  str:concat(.//text())))" />
								</xsl:attribute>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:apply-templates select="@xml:id" />
						<xsl:apply-templates select="@ana" />
						<xsl:apply-templates select="@type" />
						<xsl:apply-templates select="node()" />
					</xsl:when>
					<!-- NAME -->
					<xsl:when test="local-name(.)='name'">
						<xsl:attribute name="key">
							<xsl:value-of select="@key" />
						</xsl:attribute>
						<xsl:variable name="regName">
							<xsl:value-of select="php:function('get_dbName', string(@key), 'name')" />
						</xsl:variable>
						<xsl:if test="$regName != ''">
							<xsl:attribute name="n">
								<xsl:value-of select="$regName" />
							</xsl:attribute>
						</xsl:if>
						<xsl:variable name="regType">
							<xsl:value-of select="php:function('get_dbName', string(@key), 'type')" />
						</xsl:variable>
						<xsl:variable name="subtype">
							<xsl:value-of select="php:function('get_dbName', string(@key), 'subtype')" />
						</xsl:variable>
						<xsl:if test="$regType != ''">
							<xsl:attribute name="type">
								<xsl:value-of select="$regType" />
								<xsl:if test="$subtype != ''">
									<xsl:text>, </xsl:text>
									<xsl:value-of select="$subtype" />
								</xsl:if>
							</xsl:attribute>
						</xsl:if>
						<xsl:apply-templates select="@xml:id" />
						<xsl:apply-templates select="@ana" />
						<xsl:apply-templates select="node()" />
					</xsl:when>
					<!-- TOPIC -->
					<xsl:when test="local-name(.)='topic'">
						<xsl:attribute name="key">
							<xsl:value-of select="@key" />
						</xsl:attribute>
						<xsl:variable name="iconclass">
							<xsl:value-of select="php:function('get_dbIconclass', string(@key), 'iconclass')" />
						</xsl:variable>
						<xsl:if test="$iconclass != ''">
							<xsl:attribute name="iconclass">
								<xsl:value-of select="$iconclass" />
							</xsl:attribute>
						</xsl:if>
						<xsl:variable name="regName">
							<xsl:value-of select="php:function('get_dbIconclass', string(@key), 'name')" />
						</xsl:variable>
						<xsl:if test="$regName != ''">
							<xsl:attribute name="n">
								<xsl:value-of select="$regName" />
							</xsl:attribute>
						</xsl:if>
						<xsl:apply-templates select="@xml:id" />
						<xsl:apply-templates select="@ana" />
						<xsl:apply-templates select="node()" />
					</xsl:when>
					<!-- ANY OTHER -->
					<xsl:otherwise>
						<xsl:apply-templates select="@* | node()" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:copy>
		</xsl:if>
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template name="getSpan2">
		<xsl:param name="span" />
		<xsl:variable name="block" select="$span/ancestor-or-self::tei:div[1]" />
		<xsl:call-template name="getIndex">
			<xsl:with-param name="blockID" select="$block/@xml:id" />
		</xsl:call-template>
		<xsl:variable name="start" select="$span/descendant::tei:lb[1]/@n" />
		<xsl:variable name="end" select="$span/descendant::tei:lb[last()]/@n" />
		<xsl:variable name="count" select="count($block/descendant::tei:lb)" />
		<xsl:choose>
			<xsl:when test="($start = 1) and ($end = $count)" />
			<xsl:when test="($start = $end)">
				<xsl:text>, v. </xsl:text>
				<xsl:value-of select="$start" />
			</xsl:when>
			<xsl:when test="$start and not($end)">
				<xsl:text>, v. </xsl:text>
				<xsl:value-of select="$start" />
			</xsl:when>
			<xsl:when test="$end and not($start)">
				<xsl:text>, v. </xsl:text>
				<xsl:value-of select="$end" />
			</xsl:when>
			<xsl:when test="$end and $start">
				<xsl:text>, vv. </xsl:text>
				<xsl:value-of select="$start" />
				<xsl:text>-</xsl:text>
				<xsl:value-of select="$end" />
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
	<xsl:template name="getSpan">
		<xsl:param name="stem" />
		<xsl:param name="blocks" />
		<xsl:if test="count($blocks) > 0">
			<xsl:value-of select="$distinctSep" />
			<xsl:call-template name="getSpan2">
				<xsl:with-param name="span" select="$blocks[1]" />
			</xsl:call-template>
			<xsl:if test="(count($blocks) > 1)">
				<!-- has stem  -->
				<xsl:choose>
					<xsl:when
						test="($blocks[last()]/descendant::tei:lb[1]/@n != 1) or ($blocks[1]/descendant::tei:lb[last()]/@n != count($blocks[1]/ancestor-or-self::tei:div[1]/descendant::tei:lb))">
						<xsl:text>; </xsl:text>
					</xsl:when>
					<xsl:otherwise>
						<xsl:text> - </xsl:text>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:call-template name="getSpan2">
					<xsl:with-param name="span" select="$blocks[last()]" />
				</xsl:call-template>
			</xsl:if>
		</xsl:if>
	</xsl:template>
	<!-- - - - - - - - - - - - - - - - -->
</xsl:stylesheet>
