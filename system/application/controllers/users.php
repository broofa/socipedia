<?
require_once('basecontroller.php');

class Users extends BaseController {
  function Users() {
    parent::__construct();	
  }

  function do_show() {
    $this->template->write('content', $this->currentUser->display_name);
    $this->template->render();
  }
  
  function do_create() {
    if (isPost()) {
      $user = User::find('email', $_POST['email']);
      if ($user) {
        $this->template->write('content', 'sorry, that email address has already been registered');
        $this->template->render();
      } else {
        $user = new User();
        $user->email = $_POST['email'];
        $user->display_name = $_POST['display_name'];
        $user->password = $_POST['password'];
        $user->save();
        redirect(url_to($user, 'show'));
      }
    }
  }

  function do_login() {
    if (isGet()) {
      $this->render();
    } else if (isPost()) {
      $user = User::find('email', $_POST['email']);
      if ($user->isPassword($_POST['password'])) {
        $this->session->set_userdata('user', $user->id);
        redirect(url_to($user, 'show'));
      } else {
        $this->template->write('content', 'sorry, login failed');
        $this->template->render();
      }
    }
  }

  function do_logout() {
    $this->session->sess_destroy();
    redirect(url_to());
  }

  function recover_password($id) {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST') {
      $entry = $this->currentEntry($id);
      $entry->recoverPassword();
      redirect($entry->url('recover_password'));
    } else {
      $this->render();
    }
  }
}
