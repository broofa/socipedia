<form id="login_form" class="basic_form" action="" method="POST">

  <input type="hidden" value="<?= isset($return_to) ? $return_to : '' ?>" />

  <div class="label">Email</div>
  <input name="email" type="text" />

  <?= cleer() ?>

  <div class="label">Password</div>
  <input name="password" type="password" />

  <?= cleer() ?>

  <input type="submit" value="Log in" />
</form>

