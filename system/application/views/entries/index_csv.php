<?
function escapeField($field) {
  if (preg_match('/[",\n]/', $field)) {
    $field = preg_replace('/"/', "\\\"", $field);
    //$field = preg_replace("/\n/", "\\n", $field);
    $field = '"'.$field.'"';
  }
  return $field;
}

function writeRow($row) {
  $fields=array();
  foreach($row as $field) {
    $fields[] = escapeField($field);
  }
  echo implode(",", $fields)."\n";
}

$keys = explode(' ', "address company created description email geocode has_image name phone updated url");
writeRow($keys);
foreach ($entries->all as $entry) {
  $row = array();
  foreach ($keys as $key) {
    $row[] = $entry->$key;
  }
  writeRow($row);
}
?>
