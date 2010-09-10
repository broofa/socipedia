<div class="sidebar">
  <? if ($user->canEdit($currentUser)) {?>
  <?= link_to($user, 'edit', 'Account Settings ...', 'class="button"') ?>
  <? } ?>
</div>

<h2><?= $user->html_hame ?></h2>
<h3>Entries by <?= $user->html_hame ?></h3>
<ul>
<?
$user->entry->order_by('name');
$user->entry->get();
foreach ($user->entry->all as $entry) {
?>
  <li><?= link_to($entry, 'show', $entry->html_name) ?></li>
<?
}
?>
</ul>

<h3>Recent Comments</h3>
<?= $cmarkup ?>
