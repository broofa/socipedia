<?
require_once('basecontroller.php');

class Users extends BaseController {
  function Users() {
    parent::__construct();	
  }

  function requireUser($id, $editable = true) {
    $cu = $this->currentUser;
    $user = modelFind('User', 'id', $id);
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

    $comments = $this->user->comment;
    $comments->order_by('created');
    $comments->limit(10);
    $comments->get();
    $cmarkup = $this->load->view('comments/_list', array('comments' => $comments->all), true);

    $this->render(null, array('user' => $this->user, 'cmarkup' => $cmarkup));
  }

  function do_index() {
    $users = new User();
    $users->order_by('name');
    $users->get();
    $this->render(null, array('users' => $users));
  }

  function applyForm($user) {
    $user->email = param('email');
    $user->name = param('name');
    $passwd = param('password');
    if ($passwd) $user->password = $passwd;

    $cu = $this->currentUser;
    if ($cu->is_admin && $cu->id != $user->id) {
      $user->is_admin = param('is_admin', 0);
    }
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
      $user = modelFind('User', 'email', param('email'));
      if ($user) {
        $this->flash('An account for "'.param('email').'" has already exists.  You may either create an account using a different email address or login to the existing account.');
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
      $user = modelFind('User', 'email', param('email'));
      if ($user->isPassword(param('password'))) {
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
    
    redirect_back(url_to());
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
