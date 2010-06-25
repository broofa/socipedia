<?
abstract class BaseController extends Controller {
  function __construct() {
    parent::__construct();	

    $uid = $this->session->userdata('user');
    $this->currentUser = new User(); //User::find('id', $uid);

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
    $flash = $this->session->flashdata('flash');
    if ($flash) {
      $this->template->write('flash', "<div id=\"flash\">$flash</div>");
    }

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

  function show_error($msg, $code=404) {
    show_error($msg, $code);
    die();
  }

  function flash($msg) {
    $this->session->set_flashdata('flash', $msg);
  }

  function setReturnTo($url) {
    $this->load->helper('cookie');

    set_cookie('return_to', $url, 0);
  }

  function returnTo($url) {
    $this->load->helper('cookie');

    $rt = get_cookie('return_to');
    delete_cookie('return_to');
    redirect($rt ? $rt : $url);
  }
}
