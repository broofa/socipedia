<h2>Entry Tags</h2>
<?
// Inspired by http://www.bytemycode.com/snippets/snippet/415/
$font_min = 12;
$font_max = 30;

$count_min = min(array_values($tags));
$count_max = max(array_values($tags));

$spread = min(1, $count_max - $count_min);
$step = ($font_max - $font_min) / ($spread);

foreach ($tags as $tag => $count) {
  $size = round($font_min + (($count - $count_min) * $step));
  $url = '/entries/?q='.hashquery($tag);

  echo anchor($url, $tag." ", array(
    'title' => "$tag appears in $count entries",
    'class' => "taglink",
    'style' => "font-size:${size}px;"
  ));
}
?>
