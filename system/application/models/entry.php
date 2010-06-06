<?

class Entry extends DataMapper {
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

  public function isAuthorized($passwd = null) {
    // No auth set == no password required
    if (!$this->auth) return true;

    // Pull password from POST field?
    if (!$passwd && isset($_POST['password'])) {
      $passwd = $_POST['password'];
    }

    // Compute auth token
    $hash = self::hash_password($passwd, $this->salt);
    $mhash = self::hash_password($passwd);

    return $passwd == $this->auth || $hash == $this->auth ||
      $passwd == BARD_MASTER_HASH || $mhash == BARD_MASTER_HASH ;
  }

  public function getDisplayName() {
    $c = $this->company;
    $n = $this->name;
    if ($n && $c) return "$n, $c";
    if ($n) return $n;
    return $c ? $c : '(unnamed)';
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

  public function getDescription($max=0) {
    $d = $this->description;
    if ($max && strlen($d) > $max) {
      return htmlify(substr_replace($d, ' ...', $max), false);
    }
    return $d = hashlinks(htmlify($d));
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

    $this->save();
  }
}

?>
