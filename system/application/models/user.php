<?
class User extends BaseModel {
  var $has_many = array('entry');

  var $validation = array(
    array(
      'field' => 'password',
      'label' => 'Password',
      'rules' => array('required')
    )
  );

  function User() {
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

  static public function find($k, $v) {
    $user = new User();
    $user->get_where(array($k => $v));
    return $user->id ? $user : null;
  }

  function __get($k) {
    if ($k == 'logName') {
      return $this->display_name;
    }
    return parent::__get($k);
  }

  function __set($k, $v) {
    if ($k == 'password') {
      // Don't allow blank passwords to be set
      if (!$v) throw new Exception('Password must not be blank');
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
