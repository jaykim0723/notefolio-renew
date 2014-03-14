<?php echo '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>'; ?>
<urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php
	foreach($list as $key=>$val) {
?>
    <url>
        <loc><?$this->config->item('base_url')?><?=$val->loc?></loc>
        <lastmod><?=$val->lastmod?></lastmod>
        <changefreq><?=$val->changefreq?></changefreq>
        <priority><?=$val->priority?></priority>
    </url>
<?
	}
?>
</urlset>