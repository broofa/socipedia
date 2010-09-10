<h2>Entry Tags</h2>
<?
// Inspired by http://www.bytemycode.com/snippets/snippet/415/
$font_min = 12;
$font_max = 20;

$count_min = min(array_values($tags));
$count_max = max(array_values($tags));

$spread = min(1, $count_max - $count_min);
$step = ($font_max - $font_min) / ($spread);
$tag_names = array_keys($tags);
sort($tag_names);
foreach ($tag_names as $tag) {
  $count = $tags[$tag];
  $size = round($font_min + (($count - $count_min) * $step));
  $url = url_to('entries', 'index', 'q='.hashquery($tag));

  echo anchor($url, $tag." ", array(
    'title' => "$tag appears in $count entries",
    'class' => "taglink",
    'style' => "font-size:${size}px;"
  ));
}
?>
