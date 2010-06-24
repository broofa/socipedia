<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	> 
  <channel> 
    <title><?= PROJECT_NAME ?></title> 
    <description>Recent Activity</description> 
    <atom:link href="<?= url_to('entries', null, 'format=rss') ?>" rel="self" type="application/rss+xml" /> 
    <link><?= site_url('') ?></link> 
    <lastBuildDate><?= $entries->rssDate ?></lastBuildDate> 
    <generator>http://github.com/broofa/socipedia</generator> 
    <language>en</language> 
    <sy:updatePeriod>daily</sy:updatePeriod> 
    <sy:updateFrequency>6</sy:updateFrequency> 

    <? foreach ($entries->all as $entry) { ?>
      <item> 
        <title><?= $entry->name ?></title> 
        <guid><?= url_to($entry, 'show') ?></guid>
        <link><?= url_to($entry, 'show') ?></link> 
        <pubDate><?= $entry->rssDate ?></pubDate> 
        <description><![CDATA[<?= $entry->descriptionHtml ?>]]></description> 
      </item> 
    <? } ?>

  </channel> 
</rss> 
