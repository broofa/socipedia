<style>
#entry_form {
  width: 400px;
  text-align: right;
}
#entry_form label {
  margin: 10px 0px;
  display: block;
}
.hint {
  color: #642;
  font-size:10px;
  display:block;
  padding-top:2px;
}
</style>
  <h2><?= $user->id ? "Settings for $user->html_hame" : "Create New Account" ?></h2>
<p>
</p>
<form id="entry_form" method="POST" action="<?= $user->id ? url_to($user, 'update') : url_to('users', 'create') ?>">
  <label>
    Email
    <input name="email" type="text" value="<?= $user->email ?>" />
    <div class="hint">Private. Not shared. No spam.</div> 
  </label>
  <label>
    Name 
    <input name="name" type="text" value="<?= $user->html_name ?>" />
    <div class="hint">public name shown to other users</div> 
  </label>
  <label>
    <?= $user->id ? "Change password to " : "Password" ?>
    <input name="password" type="password" />
    <? if ($user->id) { ?>
      <div class="hint">leave blank to keep current password</div> 
    <? } ?>
  </label>
  <? if ($currentUser && $currentUser->is_admin) { ?>
    <label>
      Administrator?
      <input name="is_admin" type="checkbox" <?= $user->id == $currentUser->id ? "disabled" : "" ?> <?= $user->is_admin ? 'checked' : "" ?> style="margin-right: 112px;"/>
    </label>
  <? } ?>

  <input type="submit" value="<?= $user-> id ? "Update" : "Create Account" ?>" />
</form>
