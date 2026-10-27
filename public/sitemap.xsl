<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>Sơ đồ trang web XML</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
					body {
						font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
						color: #444;
						margin: 0;
					}
					#sitemap-header {
						background-color: #427ad1;
						padding: 30px 40px;
						color: #fff;
					}
					#sitemap-header h1 {
						margin: 0 0 10px 0;
						font-size: 32px;
					}
					#sitemap-header p {
						margin: 0;
						font-size: 14px;
						line-height: 1.5;
						opacity: 0.9;
						max-width: 800px;
					}
					#sitemap-header a {
						color: #fff;
						text-decoration: underline;
					}
					#sitemap-content {
						padding: 40px;
						max-width: 1000px;
						margin: 0 auto;
					}
					.sitemap-info {
						background: #f8f9fa;
						padding: 15px;
						border-radius: 4px;
						margin-bottom: 20px;
						font-size: 13px;
						color: #666;
						border: 1px solid #eee;
					}
					table {
						width: 100%;
						border-collapse: collapse;
						margin-top: 10px;
					}
					th {
						text-align: left;
						padding: 12px 15px;
						background-color: #427ad1;
						color: #fff;
						font-size: 14px;
						font-weight: 600;
					}
					td {
						padding: 12px 15px;
						border-bottom: 1px solid #eee;
						font-size: 14px;
					}
					tr:nth-child(even) {
						background-color: #fcfcfc;
					}
					tr:hover {
						background-color: #f5f8ff;
					}
					a {
						color: #427ad1;
						text-decoration: none;
					}
					a:hover {
						text-decoration: underline;
					}
					.lastmod {
						color: #888;
						white-space: nowrap;
					}
				</style>
			</head>
			<body>
				<div id="sitemap-header">
					<h1>Sơ đồ trang web XML</h1>
					<p>Sơ đồ trang web XML này được tạo bởi <strong>Hệ thống SEO VietTinMart</strong>. Đây là những gì công cụ tìm kiếm như Google sử dụng để thu thập dữ liệu và thu thập dữ liệu lại các bài viết/trang/sản phẩm/hình ảnh/lưu trữ trên trang web của bạn.</p>
					<p style="margin-top: 10px;"><a href="https://www.sitemaps.org/">Tìm hiểu thêm về Sơ đồ trang web XML</a></p>
				</div>
				<div id="sitemap-content">
					<xsl:if test="count(sitemap:sitemapindex/sitemap:sitemap) &gt; 0">
						<div class="sitemap-info">
							Tệp chỉ mục Sơ đồ trang web XML này chứa <strong><xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"/></strong> sơ đồ trang web.
						</div>
						<table id="sitemap-table">
							<thead>
								<tr>
									<th width="75%">Sơ đồ trang web</th>
									<th width="25%">Sửa đổi lần cuối</th>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="sitemap:sitemapindex/sitemap:sitemap">
									<xsl:variable name="sitemapURL">
										<xsl:value-of select="sitemap:loc"/>
									</xsl:variable>
									<tr>
										<td>
											<a href="{$sitemapURL}"><xsl:value-of select="sitemap:loc"/></a>
										</td>
										<td class="lastmod">
											<xsl:value-of select="sitemap:lastmod"/>
										</td>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</xsl:if>
					<xsl:if test="count(sitemap:urlset/sitemap:url) &gt; 0">
						<div class="sitemap-info">
							Sơ đồ trang web này chứa <strong><xsl:value-of select="count(sitemap:urlset/sitemap:url)"/></strong> URL.
						</div>
						<table id="sitemap-table">
							<thead>
								<tr>
									<th width="75%">URL</th>
									<th width="25%">Sửa đổi lần cuối</th>
								</tr>
							</thead>
							<tbody>
								<xsl:for-each select="sitemap:urlset/sitemap:url">
									<xsl:variable name="itemURL">
										<xsl:value-of select="sitemap:loc"/>
									</xsl:variable>
									<tr>
										<td>
											<a href="{$itemURL}"><xsl:value-of select="sitemap:loc"/></a>
										</td>
										<td class="lastmod">
											<xsl:value-of select="sitemap:lastmod"/>
										</td>
									</tr>
								</xsl:for-each>
							</tbody>
						</table>
					</xsl:if>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
