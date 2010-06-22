<style>
form {
  width: 400px;
  text-align: right;
}
label {
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
<h2>Create New Account</h2>
<p>
</p>
<form id="entry_form" method="POST" action="<?= url_to('users', 'create') ?>">
  <label>
    Email
    <input name="email" type="text" />
    <div class="hint">Private. Not shared. No spam.</div> 
  </label>
  <label>
    Name 
    <input name="display_name" type="text" />
    <div class="hint">public name shown to other users</div> 
  </label>
  <label>
    Password
    <input name="password" type="password" />
  </label>
  <input type="submit" value="Create Account" />
</form>
