<style>
#login_form {
  width: 400px;
  text-align: right;
  margin-top: 20px;
}
</style>

<form id="login_form" action="" method="POST">
  <input type="hidden" value="<?= isset($return_to) ? $return_to : '' ?>" />
  <label>Email: <input name="email" type="text" /></label>
  <br />
  <label>Password: <input name="password" type="password" /></label>
  <br />
  <input type="submit" value="Log in" />
</form>

