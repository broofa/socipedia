<?
class Entries extends Controller {
  var $data;

  function Entries() {
    parent::Controller();	
  }

  function _remap($method) {
    if (method_exists($this, $method)) {
      call_user_func_array(array(&$this, $method), array_slice($this->uri->rsegments, 2));
    } else {
      $this->render();
    }
  }

  // Do tasks common to all actions.  We may want to refactor this into a parent
  // controller class?
  function render($view=null, $data=null) {
    if (!$view) $view = "entries/".$this->router->method;

    // Apply data to the appropriate view, and render it
    $this->template->write_view('content', $view, $data);
    $this->template->render();
  }

  function currentEntry($id = null) {
    $entry = new Entry();
    $entry->where('id', $id)->get();
    if (!$entry->created) {
      show_404();
      die();
    }
    return $entry;
  }

  function authCheck($entry) {
    if (!$entry->isAuthorized()) {
      $this->render('entries/failed');
      return false;
    }

    return true;
  }

  function index_kml($entries) {
    //header('Content-type:text/plain');
    header('Content-type:text/plain');
    $entries->where('geocode !=', '');
    $entries = $entries->get()->all;

    $this->load->view('entries/index_kml', array(
      'entries' => $entries
      ));
  }

  function index() {
    $view = null;
    $params = queryParams();

    $q=isset($params['q']) ? $params['q'] : null;
    $unq=hashunquery($q);
    $format=isset($params['format']) ? $params['format'] : null;

    $entries = new Entry();
    $entries->select("*, CONCAT(name,company) as _sort", false);
    $entries->order_by('_sort');

    if ($unq) {
      $entries->like('company', $unq);
      $entries->or_like('name', $unq);
      $entries->or_like('description', $unq);
    }

    if ($format == 'kml') {
      return $this->index_kml($entries);
    }

    $entries = $entries->get()->all;
    $maplink = "http://maps.google.com/maps?q=".
      urlencode(site_url("/entries?format=kml&q=$q&ts=".time()));
    $this->render(null, array(
      'q' => $q,
      'entries' => $entries,
      'maplink' => $maplink
    ));
  }

  function show($id) {
    $entry = $this->currentEntry($id);

    $this->render(null, array(
      'entry' => $entry
    ));
  }

  function edit($id) {
    $entry = $this->currentEntry($id);

    if (!$entry->isAuthorized()) {
      $flash = isset($_POST['password']) ? 'Sorry, that\'s not the right password <img src="'.site_url('/static/images/face-sad.png').'" />' : null;
      return $this->render('entries/login', array(
        'flash' => $flash
        ));
    }

    $this->render(null, array(
      'entry' => $entry
    ));
  }

  function newAction() {
    $entry = new Entry();

    $this->render('entries/edit', array(
      'entry' => $entry
    ));
  }

  function applyFormToEntry($entry) {
    $fields = explode(' ', "name company description email owner url phone address password");

    foreach($fields as $field) {
      if (isset($_POST[$field])) $entry->$field = trim($_POST[$field]);
    }

    $entry->save();

    // Do this after entry save since we need to know the entry ID
    if (isset($_FILES['image'])) {
      if ($_FILES['image']['size'] > 0 && $_FILES['image']['size'] < 1000000) {
        $entry->setImage($_FILES['image']['tmp_name']);
      }
    }
  }

  function create() {
    $entry = new Entry();

    // Record IP that created this record (for use in spam filtering)
    $entry->created_ip = $_SERVER['REMOTE_ADDR'];

    $this->applyFormToEntry($entry);

    redirect("/entries/show/".$entry->id);
  }

  function update($id) {
    $entry = $this->currentEntry($id);
    if ($this->authCheck($entry)) {
      $this->applyFormToEntry($entry);
      redirect("/entries/show/".$entry->id);
    }
  }

  function delete($id) {
    $entry = $this->currentEntry($id);
    if ($this->authCheck($entry)) {
      $entry->setImage(null); // Remove image files
      $entry->delete();
      redirect('/entries');
    }
  }

  function tags() {
    $entries = new Entry();
    $entries = $entries->get()->all;

    $tags = array();
    foreach ($entries as $entry) {
      preg_match_all('/(#\w+)/', $entry->description, $matches);
      foreach ($matches[0] as $match) {
        $tags[$match] = (isset($tags[$match]) ? $tags[$match] : 0) + 1;
      }
    }

    $this->render(null, array(
      'tags' => $tags
    ));
  }
}
