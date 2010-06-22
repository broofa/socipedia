<?
require_once('basecontroller.php');

class Entries extends BaseController {
  var $data;

  function Entries() {
    parent::__construct();	
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
      die();
    }
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

  function do_index() {
    $view = null;
    $params = queryParams();

    $q=isset($params['q']) ? $params['q'] : null;
    $unq=hashunquery($q);
    $format=isset($params['format']) ? $params['format'] : null;

    $entries = new Entry();
    $entries->select("*, CONCAT(company,name) as _sort", false);

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

  function do_show($id) {
    $entry = $this->currentEntry($id);

    $this->template->write('title', $entry->displayName);

    $this->render(null, array(
      'entry' => $entry
    ));
  }

  function do_edit($id) {
    $params = queryParams();
    $entry = $this->currentEntry($id);
    $key = isset($params['key']) ? $params['key'] : null;

    if ($entry->isAuthorized() || $key == $entry->editKey) {
      $this->render(null, array(
        'entry' => $entry
      ));
    } else {
      $flash = isset($_POST['auth']) ? 'Sorry, that\'s not the right password <img src="'.site_url('/static/images/face-sad.png').'" />' : null;
      return $this->render('entries/login', array(
        'flash' => $flash,
        'entry' => $entry
      ));
    }
  }

  function do_new() {
    $entry = new Entry();

    $this->render('entries/edit', array(
      'entry' => $entry
    ));
  }

  function applyFormToEntry($entry) {
    $fields = explode(' ', "name company description email private_email url phone address password");

    foreach($fields as $field) {
      if (isset($_POST[$field])) {
        $value = trim($_POST[$field]);
        $entry->$field = $value;
      }
    }

    if (isSpam($entry->displayName, $entry->email, $entry->description)) {
      $this->template->write('content', 'Yuck, that really didn\'t taste very good!');
      $this->template->render();
      die();
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

  function do_create() {
    $entry = new Entry();

    // Record IP that created this record (for use in spam filtering)
    $entry->created_ip = $_SERVER['REMOTE_ADDR'];

    $this->applyFormToEntry($entry);

    redirect(url_to($entry, 'show'));
  }

  function do_update($id) {
    $entry = $this->currentEntry($id);

    $this->authCheck($entry);

    $this->applyFormToEntry($entry);
    redirect(url_to($entry, 'show'));
  }

  function do_delete($id) {
    $entry = $this->currentEntry($id);

    $this->authCheck($entry);
    
    $entry->setImage(null); // Remove image files
    $entry->delete();
    redirect(url_to('entries'));
  }

  function do_tags() {
    $entries = new Entry();
    $entries = $entries->get();

    $tags = array();
    foreach ($entries->all as $entry) {
      preg_match_all(TAG_REGEX, $entry->description, $matches);
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

  function do_geocode($id) {
    $entry = $this->currentEntry($id);
    $json = $entry->_geocode();
    dump($json);
  }

  function do_recache() {
    $params = queryParams();

    $entries = new Entry();
    $entries = $entries->get();

    foreach ($entries->all as $entry) {
      if (isset($params['state'])) {
        $entry->init();
        $entry->_geocode();
        $entry->save(false);
        $this->template->write('content', "Recached $entry->displayName<br />");
      }

      if (isset($params['thumb'])) {
        // Re-render thumbnails
        if ($entry->has_image) {
          $entry->renderThumb();
          $this->template->write('content', "Recached thumbnail for $entry->displayName<br />");
        }
      }

      // $this->template->write_view('content', 'entries/show', array(
      //   'entry' => $entry
      // ));
    }
    $this->template->render();
  }

  function do_recover_password($id) {
    if (isPost()) {
      $entry = $this->currentEntry($id);
      $entry->recoverPassword();
      redirect(url_to('entries', 'recover_password'));
    } else {
      $this->render();
    }
  }

  function do_comment($id) {
    $entry = $this->currentEntry($id);
    if (!$entry) {
      show_404();
    } else if (isPost()) {
      $params = $_POST;
      $action = isset($params['action']) ? $params['action'] : '';
      $comment = new Comment($entry->id, $action);
      $comment->body = isset($params['body']) ? $params['body'] : '';
      if (isSpam(null, null, $comment->body)) {
        $this->template->write('content', 'Yuck, that really didn\'t taste very good!');
        $this->template->render();
        die();
      }
      $comment->save();
    }
    redirect(url_to($entry, 'show'));
  }
}
