<h2>Entries by <?= $user->display_name ?></h2>
<ul>
<?
$user->entry->order_by('name');
$user->entry->get();
foreach ($user->entry->all as $entry) {
?>
  <li><?= link_to($entry, 'show', htmlify($entry->name)) ?></li>
<?
}
?>
</ul>
