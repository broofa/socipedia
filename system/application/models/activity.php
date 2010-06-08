<?
class Activity extends DataMapper {
  function Activity($entry_id = null) {
    parent::DataMapper();
    $this->entry_id = $entry_id;
  }
}
?>
