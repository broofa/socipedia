<?
if (param('string')) {
  $hash = Entry::hash_password(param('string'));
?>
  <p>Here's your password hash:</p>
  <textarea cols=40 rows=4 onclick="this.select()"><?= $hash ?></textarea>
<?  } else { ?>
  Enter the password you'd like to generate a hash for:
  <form action="" method="POST">
  <input name="string" type="text" />
  <input type="submit">
</form>
<? } ?>
