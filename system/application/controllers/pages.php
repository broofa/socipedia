<?
class Pages extends Controller {
	function Pages() {
		parent::Controller();	
	}

  function _remap($method) {
    $method = $this->router->method;
    $this->template->write('title', ucwords("$method"));
    $this->template->write_view('content', 'pages/'.$method);
    $this->template->render();
  }
}

?>
