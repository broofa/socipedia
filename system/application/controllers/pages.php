<?
require_once('basecontroller.php');

class Pages extends BaseController {
	function Pages() {
		parent::__construct();	
	}

  function _remap($method) {
    $method = $this->router->method;
    $this->template->write('title', ucwords("$method"));
    $this->template->write_view('content', 'pages/'.$method);
    $this->template->render();
  }
}

?>
