<?
define('CONVERT_CMD', '/usr/bin/convert -flatten SRC -background white -thumbnail "SIZE" DST');

class Entry extends BaseModel {
  var $has_one = array('user');
  var $has_many = array('comment');

  var $validation = array(
    array(
      'field' => 'address',
      'label' => 'Address',
      'rules' => array('geocode')
    )
  );
  
  static $PUBLIC_PROPERTIES = array(
    'has_image' => true,
    'name' => true,
    'email' => true,
    'url' => true,
    'phone' => true,
    'address' => true,
    'geocode' => true,
    'description' => true,
    '__end__' => true
  );

  function __construct() {
    parent::__construct();
    $this->init();
  }

  static public function hash_password($password, $nonce = 'master nonce') {
    return substr(hash_hmac('sha512', $password . $nonce, BARD_SITE_KEY), 0, 128);
  }

  function __get($k) {
    if ($k == 'editKey') {
      return substr($this->auth,20,10);
    } else if ($k == 'stale') {
      $cutoff = time() - 365*(24*60*60);
      $stamp = strtotime($this->updated);
      return $stamp < $cutoff;
    } else if ($k == 'descriptionHtml') {
      return hashlinks(linkify(htmlify($this->description)));
    } else if ($k == 'descriptionSummary') {
      return htmlify(substr_replace($this->description, ' ...', 140), false);
    } else if ($k == 'rssDate') {
      // TODO: Is there a better way to handle the timezone?  Mysql times are 
      // GMT and have to be parsed as such.
      $oldtz =date_default_timezone_get();
      date_default_timezone_set('GMT');
      return date('D, d M Y H:i:s T', strtotime($this->updated));
      date_default_timezone_set($oldtz);
    }
    return parent::__get($k);
  }

  function __set($k, $v) {
    if ($k == 'password') {
      // Don't allow blank passwords to be set
      if (!$v) return;
      $k = 'auth';
      $v = $v ? self::hash_password($v, $this->salt) : null;
    }
    $this->$k = $v;
  }

  function init() {
    // Initialize once-only fields
    if (!$this->created) {
      $this->created = date ("Y-m-d H:i:s");
    }
    if(!$this->salt) {
      $this->salt = uniqid(null, true);
    }
  }

  function delete() {
    $activity = new Activity($this->id);
    $name = $this->name;
    $activity->summary = "Deleted \"$name\"";
    $activity->save();

    parent::delete();
  }

  function url($type=null, $option='') {
    switch ($type) {
    case 'map':
      return "http://maps.google.com/maps?q=".
        urlencode($this->url('kml', $option)."&ts=".time());
    case 'kml':
      return $this->url()."?q=$option&format=kml";
    case 'rss':
      return $this->url()."?q=$option&format=rss";
    case 'csv':
      return $this->url()."?q=$option&format=csv";

    case 'show':
      return $this->url().'/'.$this->id;
    case 'edit':
      return $this->url().'/edit/'.$this->id.($option ? "#$option" : '');
    default:
    case 'delete':
      return $type ? $this->url()."/$type/".$this->id : site_url('/entries');
    }
  }

  public function canEdit($user=null) {
    if ($user) {
      if ($user->is_admin) return true;

      $this->user->get();
      if ($this->user && $this->user->id == $user->id) return true;
    }
    return false;
  }

  public function imageURL() {
    return site_url($this->imagePath());
  }

  public function thumbURL() {
    return site_url($this->thumbPath());
  }

  public function imagePath() {
    return BARD_UPLOAD_DIR . "/". $this->id . '.jpg';
  }

  public function thumbPath() {
    return BARD_UPLOAD_DIR . "/". $this->id . '_thumb.jpg';
  }

  public function _geocode($field = 'address') {
    $address = urlencode($this->$field);
    $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
    // Fetch url. We'd like to do:
    //    $response = file_get_contents($url);
    // ...but has this is disabled in PHP on Dreamhost.  Instead we use curl, 
    // ala http://wiki.dreamhost.com/index.php/CURL
    $ch = curl_init();
    $timeout = 5; // set to zero for no timeout
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $response = curl_exec($ch);
    $start = microtime(true);
    curl_close($ch);
    $stop = microtime(true);

    // Parse the returned XML file
    try {
      $json = json_decode($response);
      $json->url = $url;
      if ($json->status == 'OK') {
        $geom = $json->results[0]->geometry;
        // Compute diagonal distance across recommended viewport (in km)
        $span = earthDist($geom->viewport->northeast, $geom->viewport->southwest);
        $latlng = $geom->location;
        $this->geocode = $span <= 2 ? "$latlng->lng,$latlng->lat" : '';
        $json->geocode = $this->geocode;
      }
    } catch (Exception $e) {
      // Not much we can do here.  Fail silently
      return $e;
    }
    return $json;
  }

  function renderThumb() {
    $cmd = str_replace(array('SRC', 'DST', 'SIZE'),
      array($this->imagePath(), $this->thumbPath(), '48x48'), CONVERT_CMD);
    exec($cmd);
  }

  public function setImage($src) {
    if ($src) {
      // Create upload dir if necessary
      if (!file_exists(BARD_UPLOAD_DIR)) {
        mkdir(BARD_UPLOAD_DIR);
      }

      // Create 192px version
      $cmd = str_replace(array('SRC', 'DST', 'SIZE'),
        array($src, $this->imagePath(), '192x192>'), CONVERT_CMD);
      exec($cmd);

      $this->renderThumb();

      $this->has_image = true;
    } else {
      // Remove images
      foreach (array($this->imagePath(), $this->thumbPath()) as $path) {
        !file_exists($path) or unlink($path);
      }
      $this->has_image = false;
    }
  }

  public function recoverPassword() {
    // Send out a notification email
    $to = $this->private_email;
    $subject = "Access to ".PROJECT_NAME." '".$this->name."' entry";
    $body = "To access the '".$this->name."' entry, go to this URL:\n".$this->url('edit')."?key=".$this->editKey;
    mail($to, $subject, $body, "From: no-reply@no-reply.com");
  }
}

?>
