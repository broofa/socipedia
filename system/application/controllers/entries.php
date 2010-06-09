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

  function index_rss($entries) {
    header('Content-type:application/rss+xml');
    $entries->order_by('updated desc');
    $entries->limit(10);
    $entries = $entries->get();
    $this->load->view('entries/index_rss', array(
      'entries' => $entries
    ));
  }

  function index_csv($entries) {
    header('Content-type:text/csv');
    $entries->order_by('_sort');
    $entries = $entries->get();

    $this->load->view('entries/index_csv', array(
      'entries' => $entries
    ));
  }

  function index_kml($entries) {
    header('Content-type:text/plain');
    $entries->where('geocode !=', '');
    $entries->order_by('_sort');
    $entries = $entries->get();

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

    if ($unq) {
      $entries->like('company', $unq);
      $entries->or_like('name', $unq);
      $entries->or_like('description', $unq);
    }

    if ($format == 'rss') {
      return $this->index_rss($entries);
    } else if ($format == 'csv') {
      return $this->index_csv($entries);
    } else if ($format == 'kml') {
      return $this->index_kml($entries);
    }

    $entries->order_by('_sort');
    $entries = $entries->get();
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

    $this->template->write('title', $entry->displayName);

    $this->render(null, array(
      'entry' => $entry
    ));
  }

  function edit($id) {
    $entry = $this->currentEntry($id);

    if (!$entry->isAuthorized()) {
      $flash = isset($_POST['auth']) ? 'Sorry, that\'s not the right password <img src="'.site_url('/static/images/face-sad.png').'" />' : null;
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
    $fields = explode(' ', "name company description email private_email url phone address password");

    foreach($fields as $field) {
      if (isset($_POST[$field])) $entry->$field = trim($_POST[$field]);
    }

    $entry->save();

    // Do this after entry save since we need to know the entry ID
    if (isset($_FILES['image'])) {
      if ($_FILES['image']['size'] > 0 && $_FILES['image']['size'] < 1000000) {
        $entry->setImage($_FILES['image']['tmp_name']);
        $entry->save();
      }
    }
  }

  function create() {
    $entry = new Entry();

    // Record IP that created this record (for use in spam filtering)
    $entry->created_ip = $_SERVER['REMOTE_ADDR'];

    $this->applyFormToEntry($entry);

    redirect($entry->url('show'));
  }

  function update($id) {
    $entry = $this->currentEntry($id);
    if ($this->authCheck($entry)) {
      $this->applyFormToEntry($entry);
      redirect($entry->url('show'));
    }
  }

  function delete($id) {
    $entry = $this->currentEntry($id);
    if ($this->authCheck($entry)) {
      $entry->setImage(null); // Remove image files
      $entry->delete();
      redirect($entry->url());
    }
  }

  function tags() {
    $entries = new Entry();
    $entries = $entries->get();

    $tags = array();
    foreach ($entries->all as $entry) {
      preg_match_all('/(#\w+)/', $entry->description, $matches);
      $matches = array_slice($matches, 0, 8);
      foreach ($matches[0] as $match) {
        $match = strtolower($match);
        $tags[$match] = (isset($tags[$match]) ? $tags[$match] : 0) + 1;
      }
    }

    $this->render(null, array(
      'tags' => $tags
    ));
  }
}
