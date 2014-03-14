<?php echo '<?'.'xml version="1.0" encoding="UTF-8"'.'?>'; ?>
<sitemapindex
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php
	foreach($list as $loc=>$lastmod) {
?>
    <sitemap>
        <loc><?$this->config->item('base_url')?>sitemap/<?=$loc?></loc>
        <lastmod><?=$lastmod?></lastmod>
    </sitemap>
<?
	}
?>
</sitemapindex>