<h2>Users</h2>
<ul>
<?
foreach ($users->all as $user) {
?>
  <li><a href="<?= url_to($user, 'show') ?>"><?= $user->display_name ?></a></li>
<?
}
?>
</ul>
