<?
class User extends BaseModel {
  var $has_many = array('entry', 'comment');

  var $validation = array(
    array(
      'field' => 'email',
      'label' => 'email address',
      'rules' => array('required', 'valid_email')
    ),
    array(
      'field' => 'name',
      'label' => 'name',
      'rules' => array('required')
    ),
    array(
      'field' => 'salted_passwd',
      'label' => 'password',
      'rules' => array('required')
    )
  );

  function __construct() {
    parent::__construct();

    // Initialize once-only fields
    if (!$this->created) {
      $this->created = date("Y-m-d H:i:s");
    }

    if(!$this->salt) $this->salt = uniqid(null, true);
  }

  static public function hashPassword($password, $nonce = 'master nonce') {
    return substr(hash_hmac('sha512', $password . $nonce, BARD_SITE_KEY), 0, 128);
  }

  function __get($k) {
    if ($k == 'logName') {
      return $this->name;
    }
    return parent::__get($k);
  }

  function __set($k, $v) {
    if ($k == 'password') {
      // Don't allow blank passwords to be set
      $k = 'salted_passwd';
      $v = $v ? self::hashPassword($v, $this->salt) : null;
    }
    parent::__set($k, $v);
  }

  function isPassword($passwd) {
    return self::hashPassword($passwd, $this->salt) == $this->salted_passwd;
  }
}
?>
