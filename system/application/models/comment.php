<?
class Comment extends DataMapper {
  function Comment($entry_id=null, $action='') {
    parent::__construct();
    $this->entry_id = $entry_id;
    $this->action = $action;
  }

  function save($log = true) {
    parent::save();

    if ($log) {
      $activity = new Activity($this->entry_id);
      $activity->summary = "Comment on entry \"$this->entry_id\"".($this->action ? " (action=$this->action)" : '');
      $activity->body = $this->body;
      $activity->save();
    }
  }
}
?>
