<?
abstract class BaseModel extends DataMapper {
  function __construct() {
    parent::__construct();
  }

  function logName() {
    return $this->id;
  }

  function logActivity($action, $body=null) {
    $cn = get_class($this);
    $name = $this->logName;

    $activity = new Activity();
    $activity->summary = "$action $cn \"$name\"";
    $activity->body = $body;
    $activity->save();
  }

  function delete($log = true) {
    if ($log) {
      $this->logActivity('deleted');
    }

    parent::delete();
  }

  /*
  function save($object='', $log = true) {
    $action = $this->id ? 'updated' : 'inserted';

    $args = func_get_args();
    call_user_func_array(array('parent', 'save'), $args);

    if ($log) {
      $this->logActivity($action);
    }
  }
     */
}
?>

