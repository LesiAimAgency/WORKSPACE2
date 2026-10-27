<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9">
<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>XML Sitemap Index</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;color:#333;max-width:1200px;margin:0 auto;padding:20px;background:#f5f5f5}
h1{color:#1e40af;font-size:24px;margin-bottom:10px}
#intro{background:#fff;padding:20px;border-radius:8px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.1)}
#intro p{margin:8px 0;line-height:1.6}
#content{background:#fff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,.1);overflow:hidden}
table{width:100%;border-collapse:collapse}
th{background:#1e40af;color:#fff;text-align:left;padding:12px 15px;font-weight:600;font-size:14px}
td{padding:12px 15px;border-bottom:1px solid #e5e7eb;font-size:14px}
tr:hover{background:#f9fafb}
tr:last-child td{border-bottom:none}
a{color:#2563eb;text-decoration:none}
a:hover{text-decoration:underline}
.url{color:#2563eb;word-break:break-all}
.date{color:#6b7280;font-size:13px}
</style>
</head>
<body>
<div id="intro">
<h1>XML Sitemap Index</h1>
<p>Đây là sitemap index chứa danh sách các sitemap con của website.</p>
<p>Tổng số sitemap: <strong><xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/></strong></p>
</div>
<div id="content">
<table>
<tr>
<th style="width:70%">Sitemap URL</th>
<th>Cập nhật lần cuối</th>
</tr>
<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
<tr>
<td><a class="url" href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
<td class="date"><xsl:value-of select="substring(sitemap:lastmod,0,11)"/></td>
</tr>
</xsl:for-each>
</table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
