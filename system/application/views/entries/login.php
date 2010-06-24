<form method="POST" style="text-align: center;">
<?  if (isset($flash)) { ?><p class="message warn"><?= $flash ?><p><?  } ?>
<p>This entry is password protected.  Please provide the password:</p>
<input name="auth" type="password" />
<input type="submit" />
</form>
<form action="<?= url_to($entry, 'recover_password') ?>" method="POST" onsubmit="return confirm('This will send an email with password recovery instructions to the owner of this entry. Is that okay?')" style="text-align: center;">
<p style="color:#666; font-size: 10px;">forgot your password?  <input type="submit" value="click here" style="border: none; text-decoration: underline; color: #00a; background: none; padding: 0px; margin: 0px;" /></p>
</form>
