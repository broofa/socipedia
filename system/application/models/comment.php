<?
class Comment extends BaseModel {
  var $has_one = array('user', 'entry');

  function __get($k) {
    if ($k == 'friendly_created') {
      $ts = strtotime($this->created);
      return strftime('%b %e, %Y - %H:%M%P', $ts);
    }
    return parent::__get($k);
  }
}
?>
