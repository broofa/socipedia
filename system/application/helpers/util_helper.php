<?
include "app_config.php";

define('TAG_REGEX', '/(?:\s#)([a-zA-Z][\w-]*)/');

function dump($o) {
  echo "<h3>dump</h3>\n<pre>\n";
  print_r($o);
  echo "\n</pre>\n";
}

$marks = array();
function mark($s) {
  global $marks;
  $marks[$s] = isset($marks[$s]) ? ($marks[$s] += 1) : 0;
  echo "$s ".$marks[$s]."\n";
}

function unmark($s) {
  global $marks;
  unset($marks[$s]);
  echo "$s end\n";
}

function stack() {
  echo "<h3>Stack</h3><pre>";
  debug_print_backtrace();
  echo "</pre>";
}

function modelFind($c, $k, $v) {
  $o = new $c();
  $o->get_where(array($k => $v));
  return $o->id ? $o : null;
}


function redirect_back($url='') {
  if (isset($_SERVER['HTTP_REFERER'])) {
    redirect($_SERVER['HTTP_REFERER']);
  }
  redirect($url);
}

function url_to($model='', $action=null, $params = null) {
  if ($model instanceof DataMapper) {
    $class = strtolower(plural(get_class($model)));
    if (!$action) $action = 'show';
    $path = "$class/$action/$model->id";
  } else if ($model) {
    $path = $action ? "$model/$action" : "$model";
  } else {
    $path = "/";
  }
  if ($params) $path = $path."?$params";

  return site_url($path);
}

function link_to($model='', $action=null, $text, $atts=null) {
  return "<a href=\"".url_to($model, $action)."\"". ($atts ? " $atts" : "").">$text</a>";
}

function queryParams() {
  if (isset($GLOBALS['__query'])) {
    $params = $GLOBALS['__query'];
  } else {
    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $params);
  }

  return $params;
}

function param($name, $default='', $trim=true) {
  $p = queryParams();
  if (!isset($p->$name)) {
    if (isPost()) {
      $p = $_POST;
    } else {
      return $default;
    }
  }
  return isset($p[$name]) ? ($trim ? trim($p[$name]) : $p[name]) : $default;
}

function isGet() {return $_SERVER['REQUEST_METHOD'] == 'GET';}
function isPost() {return $_SERVER['REQUEST_METHOD'] == 'POST';}
function isPut() {return $_SERVER['REQUEST_METHOD'] == 'PUT';}
function isDelete() {return $_SERVER['REQUEST_METHOD'] == 'DELETE';}
function isHead() {return $_SERVER['REQUEST_METHOD'] == 'HEAD';}

function cleer() {
  return '<div class="cleer"></div>';
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

function linkify($s) {
  return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@', '<a href="$1" rel="nofollow">$1</a>', $s);
}

function weblink($url, $text=null) {
  if (!$text) $text = $url;
  if (!preg_match('|^http|', $url)) $url = "http://$url";
  return "<a href=\"$url\" rel=\"nofollow\"".">".htmlify($text)."</a>";
}

function htmlify($s, $escape=true) {
    $s = htmlspecialchars($s);
    return $escape ? preg_replace('/\n/', '<br />', $s) : $s;
}


// Check to see if Akismet thinks an entry is spam.  Note that this hits the 
// Akismet service every time it's called, so use sparingly!
//
// (Note: To test this, enter "viagra-test-123" in the name, company, or 
// description fields. See http://akismet.com/development/api/ for more info)
function isSpam($author = '', $email = '', $content = '') {
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
    $vars['comment_author']         = $author;
    $vars['comment_author_email']   = $email;
    $vars['comment_content']        = $content;

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
