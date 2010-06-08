<?
include "app_config.php";

function dump($o) {
  echo "<pre>";
  print_r($o);
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
  return preg_replace_callback('/#(\w*)/', '_hashlinks', $s);
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
?>
