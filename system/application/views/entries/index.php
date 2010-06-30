<style>
UL {
  list-style: none;
  margin: 0px;
  padding: 0px;
}
.entry {
  position: relative;
  padding: 4px 18px 4px 52px;
}
.entry a {
  color: #333;
  display: block;
}
.entry:hover {
  background-color: #bdf;
}
.entry .name {
  float: left;
  font-weight: bold;
  width: 150px;
}
.entry .summary {
  width: 350px;
  float: right;
  font-size: 90%;
}
.entry.stale {
  background-position: right center;
}
.entry.has_image {
  padding-left: 52px;
  margin-left: 0px;
  background-repeat: no-repeat;
  background-position: left top;
  min-height: 48px;
}
.section {
  font-size: 18px;
  font-weight: bold;
  margin-top: 10px;
  color: #999;
  border-top: dotted 1px #ccc;
}
.section A {
  color: #999;
}
#index {
  font-size: 14pt;
  font-weight: bold;
  margin-right: 200px;
  text-align: center;
}
.index_char {
margin: 0px 4px;
text-decoration: underline;
}
</style>

<div class="sidebar">
  Get this search as a &hellip;
  <a class="button" href="<?= $entries->url('rss', $q) ?>">RSS Feed</a>
  <a class="button" href="<?= $entries->url('csv', $q) ?>">CSV File</a>
  <a class="button" href="<?= $entries->url('map', $q) ?>">Google Map</a>
  <p style="border-top: solid 1px #ccc; margin-top: 10px; padding-top: 5px;"><img src="<?= site_url('static/images/stale.png')?>" style="vertical-align: middle" /> = entry may not be current (last update more than 1 year ago)</p>
</div>

<? $count = count($entries->all) ?>
<h2><?= $q ? "$count entries matching $q" : "All $count entries" ?></h2>
<div id="index">
<?
$chars = array();

foreach($entries->all as $entry) {
  $char = strtoupper(substr($entry->name, 0,1));
  $chars[] = $char;
}
sort($chars);
$chars = array_unique($chars);
foreach($chars as $char) {
  echo "<a class=\"index_char\" href=\"#section_$char\">$char</a>";
}
?>
</div>

<ul>
  <?
  $lastchar = "";
  foreach($entries->all as $entry) {
    $dn = $entry->name;
    $char = strtoupper(substr($dn, 0,1));
    if ($char != $lastchar) {
      echo "<div class=\"section\"><a name=\"section_$char\">$char</a></div>";
    }
    $lastchar = $char;

    $cn = array('entry');
    if ($entry->has_image) $cn[] = 'has_image';
    if ($entry->stale) $cn[] = 'stale';
    $cn= implode(' ', $cn);

    if ($entry->has_image) {
      ?><li class="<?= $cn ?>" style="background-image: url(<?= $entry->thumbURL() ?>)"><?
    } else {
      ?><li class="<?= $cn ?>"><?
    }
    ?>
    <a href="<?= url_to($entry) ?>">
      <div class="name"><?= $dn ?></div>
      <div class="summary"><?= $entry->descriptionSummary ?></div>
      <?= cleer() ?>
    </a>
    </li>
  <? } ?>
</ul>
