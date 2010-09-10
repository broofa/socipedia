<?
abstract class BaseModel extends DataMapper {
  function __construct() {
    parent::__construct();
  }

  function logActivity($action, $body=null) {
    $activity = new Activity($this);
    $name = $this->logName();
    $activity->body = "$action $activity->target_class $activity->target_id".($name ? ", \"$name\"" : "");
    $activity->save();
  }

  function logName() {
    return isset($this->name) ? $this->name : "";
  }

  function __get($k) {
    if (strpos($k, 'html_') === 0) {
      $k = substr($k, 5);
      return htmlify($this->$k);
    }
    return parent::__get($k);
  }

  function __set($k, $v) {
    $this->$k = $v;
  }

  function canEdit($user) {
    return $user && $user->is_admin;
  }

  function delete($o=null, $log=true) {
    if ($log) $this->logActivity('deleted');
    parent::delete();
  }

  function save($o=null, $log=true) {
    $action = $this->id ? 'updated' : 'inserted';
    parent::save($o);
    if ($log) $this->logActivity($action);
  }

}
?>
