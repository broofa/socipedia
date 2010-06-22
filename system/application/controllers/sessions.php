<?
require_once('basecontroller.php');

class Sessions extends BaseController {
  
  function Session() {
    parent::__construct();	
  }

  function do_create() {
    $user = new User();

    $this->render(null, array('user' => $user));
  }

  function do_delete() {
  }
}
