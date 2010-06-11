<?
include "app_config.php";

define('TAG_REGEX', '/#([a-zA-Z][\w-]*)/');

function dump($o) {
  echo "<h3>dump</h3><pre>";
  print_r($o);
  echo "</pre>";
}

function stack() {
  echo "<h3>Stack</h3><pre>";
  debug_print_backtrace();
  echo "</pre>";
}

function queryParams() {
  parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $params);
  return $params;
}

function cleer() {
  echo '<div class="cleer"></div>';
}

function hashquery($s) {
  return $s ? preg_replace('/^#/', 'tag:', $s) : null;
}
function hashunquery($s) {
  return $s ? preg_replace('/^tag:/', '#', $s) : null;
}

function _hashlinks($matches) {
  $s=$matches[0];
  return "<a class=\"hashtag\" href=\"./?q=".hashquery($s)."\">".$s."</a>";
}

function hashlinks($s) {
  return preg_replace_callback(TAG_REGEX, '_hashlinks', $s);
}

function weblink($url, $text=null, $blank=false) {
  if (!$text) $text = $url;
  if (!preg_match('|^http|', $url)) $url = "http://$url";
  return "<a href=\"$url\" rel=\"nofollow\"".($blank ? ' target="_blank"' : '').">".htmlify($text)."</a>";
}

function htmlify($s, $escape=true) {
    $s = htmlspecialchars($s);
    return $escape ? preg_replace('/\n/', '<br />', $s) : $s;
}


// Check to see if Akismet thinks an entry is spam.  Note that this hits the 
// Akismet service every time it's called, so use sparingly!
//
// (Note: To test this, enter "viagra-test-123" the name, company, or 
// description fields. See http://akismet.com/development/api/ for more info)
function entryIsSpam($entry) {
  $spam = false;

  if (isset($GLOBALS['akismet_key'])) {
    $vars = array();
    // Uncomment to mix in $_SERVER properties, which may or may not improve 
    // spam detection(???)
    // $vars = array_merge($vars, $_SERVER);
    $vars['user_ip']                = $_SERVER['REMOTE_ADDR'];
    $vars['user_agent']             = $_SERVER['HTTP_USER_AGENT'];

    // The body of the message to check, the name of the person who
    // posted it, and their email address
    $vars['comment_content']        = $entry->description;
    $vars['comment_author']         = $entry->name . ' ' . $entry->company;
    $vars['comment_author_email']   = $entry->email;

    // ... Add more fields if you want

    // Check if it's spam

    if (akismet_check($vars)) $spam = true;
  }

  return $spam;
}

/*
 * Compute distance between two points (lat,lng) on the surface of the earth
 */
define('RAD_PER_DEG', 0.017453293);
define('RAD_EARTH', 6371);
function earthDist($pnt1, $pnt2) {
  $lat1 = $pnt1->lat*RAD_PER_DEG;
  $lng1 = $pnt1->lng*RAD_PER_DEG;
  $lat2 = $pnt2->lat*RAD_PER_DEG;
  $lng2 = $pnt2->lng*RAD_PER_DEG;

  $dlng = $lng2-$lng1;
  $dlat = $lat2-$lat1;

  $a = pow(sin($dlat/2),2) + cos($lat1) * cos($lat2) * pow(sin($dlng/2),2);
  $c = 2 * atan2(sqrt($a), sqrt(1-$a));

  return $c*RAD_EARTH;
}
?>
