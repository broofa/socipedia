<?
class Activity extends DataMapper {
  function Activity($target = null) {
    parent::__construct();
    if ($target) {
      $this->target_class = get_class($target);
      $this->target_id = $target->id;
    }
  }
}
?>
