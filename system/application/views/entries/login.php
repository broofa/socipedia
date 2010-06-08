<form method="POST" style="text-align: center;">
<?  if (isset($flash)) { ?><p class="message warn"><?= $flash ?><p><?  } ?>
<p>This entry is password protected.  Please provide the password:</p>
<input name="auth" type="password" />
<input type="submit" />
</form>
