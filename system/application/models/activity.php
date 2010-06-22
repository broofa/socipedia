<?
class Activity extends DataMapper {
  function Activity($entry_id = null) {
    parent::__construct();
    $this->entry_id = $entry_id;
  }
}
?>
