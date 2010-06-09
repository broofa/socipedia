<?

class Entry extends DataMapper {
  static $BLACKLIST = array(
    'updated' => true,
    'created' => true,
    'created_ip' => true,
    'auth' => true,
    'salt' => true
  );

  function Entry() {
    parent::DataMapper();

    // Initialize once-only fields
    if (!$this->created) {
      $this->created = date ("Y-m-d H:i:s");
    }
    if(!$this->salt) {
      $this->salt = uniqid(null, true);
    }
  }

  static public function hash_password($password, $nonce = 'master nonce') {
    return substr(hash_hmac('sha512', $password . $nonce, BARD_SITE_KEY), 0, 128);
  }

  function __get($k) {
    if ($k == 'displayName') {
      $c = $this->company;
      $n = $this->name;
      if ($n && $c) return "$n, $c";
      if ($n) return $n;
      return $c ? $c : '- No name provided -';
    } else if ($k == 'descriptionHtml') {
      return hashlinks(htmlify(parent::__get('description')));
    } else if ($k == 'descriptionSummary') {
      return htmlify(substr_replace(parent::__get('description'), ' ...', 140), false);
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
    if ($k == 'address') {
      $this->geocode($v);
    } if ($k == 'password') {
      // Don't allow blank passwords to be set
      if (!$v) return;
      $k = 'auth';
      $v = $v ? self::hash_password($v, $this->salt) : null;
    }
    parent::__set($k, $v);
  }

  function delete() {
    $activity = new Activity($this->id);
    $name = $this->displayName;
    $activity->summary = "Deleted \"$name\"";
    $activity->save();

    parent::delete();
  }

  function save() {
    $activity = new Activity($this->id);
    $stored = get_object_vars($this->stored);
    $isNew = !$this->id;

    parent::save();

    $details = array();
    $name = $this->displayName;
    if ($isNew) {
      $activity->entry_id = $this->id;
      $activity->summary = "Created \"$name\"";
      foreach ($stored as $key => $was) {
        if (isset(self::$BLACKLIST[$key]) || !$this->$key) continue;
        $now = $this->$key;
        $details[] = "$key: $now"; 
      }
    } else {
      $activity->summary = "Updated \"$name\"";
      foreach ($stored as $key => $was) {
        if (isset(self::$BLACKLIST[$key])) continue;
        $now = $this->$key;
        if ($was != $now) {
          $details[] = "$key was: $was"; 
          $details[] = "$key now: $now"; 
        }
      }
    }
    $activity->details = implode($details, "\n");
    $activity->save();
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
    case 'delete':
      return $this->url().'/delete/'.$this->id;
    case 'new':
      return $this->url().'/new/'.$this->id;
    case 'create':
      return $this->url().'/create/'.$this->id;
    }
    return site_url('/entries');
  }

  public function isAuthorized($passwd = null) {
    // No auth set == no password required
    if (!$this->auth) return true;

    // Pull password from POST field?
    if (!$passwd && isset($_POST['auth'])) {
      $passwd = $_POST['auth'];
    }

    // Compute auth token
    $hash = self::hash_password($passwd, $this->salt);
    $mhash = self::hash_password($passwd);

    return $passwd == $this->auth || $hash == $this->auth ||
      $passwd == BARD_MASTER_HASH || $mhash == BARD_MASTER_HASH ;
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

  public function geocode($address) {
    $key = BARD_GEOCODE_KEY;
    if (!$address || !$key) {
      return false;
    }

    $address = urlencode($address);
    $url = "http://maps.google.com/maps/geo?q=${address}&output=xml&key=${key}";

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
      $xml = new SimpleXMLElement($response);
      if ($xml->Response->Status->code == 200) {
        // Normal response
        $this->geocode = (string)$xml->Response->Placemark->Point->coordinates;
      } else {
        // For other codes, see
        // http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode
      }
    } catch (Exception $e) {
      // ??? - Not much we can do here.  Fail silently since this isn't a critical operation
    }
  }

  public function setImage($src) {
    $CONVERT = '/usr/bin/convert -flatten SRC -background white -thumbnail "SIZE" DST';

    if ($src) {
      // Create upload dir if necessary
      if (!file_exists(BARD_UPLOAD_DIR)) {
        mkdir(BARD_UPLOAD_DIR);
      }

      // Create 192px version
      $cmd = str_replace(array('SRC', 'DST', 'SIZE'),
        array($src, $this->imagePath(), '192x192>'), $CONVERT);
      exec($cmd);

      // Create 32px versior/bin/convert';
      $cmd = str_replace(array('SRC', 'DST', 'SIZE'),
        array($src, $this->thumbPath(), '96x48'), $CONVERT);
      exec($cmd);

      $this->has_image = true;
    } else {
      // Remove images
      foreach (array($this->imagePath(), $this->thumbPath()) as $path) {
        !file_exists($path) or unlink($path);
      }
      $this->has_image = false;
    }
  }
}

?>
