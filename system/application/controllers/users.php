<?
require_once('basecontroller.php');

class Users extends BaseController {
  function Users() {
    parent::__construct();	
  }

  function requireUser($id, $editable = true) {
    $cu = $this->currentUser;
    $user = User::find('id', $id);
    $this->user = null;

    if (!$editable || ($cu && ($cu->is_admin || $cu->id == $user->id))) {
      $this->user = $user;
    }

    if (!$this->user) {
      $this->show_error('User not found');
    }
  }
  
  function do_show($id) {
    $this->requireUser($id, false);

    $this->render(null, array('user' => $this->user));
  }

  function do_index() {
    $users = new User();
    $users->order_by('display_name');
    $users->get();
    $this->render(null, array('users' => $users));
  }

  function applyForm($user) {
    $user->email = trim($_POST['email']);
    $user->display_name = trim($_POST['display_name']);
    $passwd = trim($_POST['password']);
    if ($passwd) $user->password = $passwd;
  }

  function do_edit($id) {
    $this->requireUser($id, true);
    $this->render(null, array('user' => $this->user));
  }

  function do_update($id) {
    $this->requireUser($id, true);
    $user = $this->user;
    $this->applyForm($user);
    if ($user->save()) {
      redirect(url_to($user, 'show'));
    } else {
      $this->flash($user->error->string);
      redirect(url_to($user, 'edit'));
    };
  }

  function do_new() {
    $user = new User();
    $this->render('users/edit', array('user' => $user));
  }

  function do_create() {
    if (isPost()) {
      $user = User::find('email', $_POST['email']);
      if ($user) {
        $this->flash('An account for "'.$_POST['email'].'" has already exists.  You may either create an account using a different email address or login to the existing account.');
        redirect(url_to('users', 'new'));
      } else {
        $user = new User();
        $this->applyForm($user);
        $user->save();
        if ($user->valid) {
          redirect(url_to($user, 'show'));
        } else {
          $this->flash($user->error->string);
          redirect(url_to('users', 'new'));
        }
      }
    } else {
      redirect(url_to('users', 'new'));
    }
  }

  function do_login() {
    if (isGet()) {
      $this->render();
    } else if (isPost()) {
      $user = User::find('email', $_POST['email']);
      if ($user->isPassword($_POST['password'])) {
        $this->session->set_userdata('user', $user->id);

        $this->returnTo(url_to($user));
      } else {
        $this->flash('Login failed - please try again');
        redirect(url_to($user, 'login'));
      }
    }
  }

  function do_logout() {
    $this->session->sess_destroy();
    redirect(url_to());
  }

  function recover_password($id) {
    /*
    if (isPost()) {
      $entry = $this->currentEntry($id);
      $entry->recoverPassword();
      redirect($entry->url('recover_password'));
    } else {
      $this->render();
    }
    */
  }
}
