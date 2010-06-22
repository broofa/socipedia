<?
abstract class BaseController extends Controller {
  function BaseController() {
    parent::__construct();	

    $uid = $this->session->userdata('user');
    $this->currentUser = User::find('id', $uid);

    $vars = array(
      'currentUser' => $this->currentUser
    );
    $this->load->vars($vars);
  }

  /*
   * Default action is to invoke the method if it exists, otherwise render the 
   * template
   */
  function _remap($method) {
    $handler = "do_".$method;
    if (method_exists($this, $handler)) {
      call_user_func_array(array(&$this, $handler), array_slice($this->uri->rsegments, 2));
    } else {
      $this->render();
    }
  }

  // Do tasks common to all actions.  We may want to refactor this into a parent
  // controller class?
  function render($view=null, $data=null) {
    $cn = strtolower(get_class($this));
    if (!$view) $view = "$cn/".$this->router->method;

    // Apply data to the appropriate view, and render it
    $this->template->write_view('content', $view, $data);
    $this->template->render();
  }
}
