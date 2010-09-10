<?
require_once('basecontroller.php');

class Entries extends BaseController {
  var $data;

  function Entries() {
    parent::__construct();	
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
    $entries->order_by('name');
    $entries = $entries->get();

    $this->load->view('entries/index_csv', array(
      'entries' => $entries
    ));
  }

  function index_kml($entries) {
    header('Content-type:text/plain');
    $entries->where('geocode !=', '');
    $entries->order_by('name');
    $entries = $entries->get();
    $this->load->view('entries/index_kml', array(
      'entries' => $entries
    ));
  }

  function do_index() {
    $view = null;

    $q= param('q', null);
    $unq=hashunquery($q);
    $format= param('format', null);

    $entries = new Entry();

    if ($unq) {
      $entries->group_start();
      $entries->or_like('name', $unq);
      $entries->or_like('description', $unq);
      $entries->group_end();
    }

    if ($format == 'rss') {
      return $this->index_rss($entries);
    } else if ($format == 'csv') {
      return $this->index_csv($entries);
    } else if ($format == 'kml') {
      return $this->index_kml($entries);
    }

    $entries->order_by('name');
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
    $entry = $this->get('Entry', $id);

    $entry->user->get();

    $comments = $entry->comment;
    $comments->order_by('created');
    $comments->limit(10);
    $comments->get();
    $data = array(
      'comments'=> $comments->all,
      'edit_ui' => $entry->canEdit($this->currentUser)
    );
    $cmarkup = $this->load->view('comments/_list', $data, true);

    $this->template->write('title', $entry->name);

    $this->render(null, array(
      'entry' => $entry,
      'cmarkup' => $cmarkup
    ));
  }

  function do_edit($id) {
    $params = queryParams();

    $entry = $this->getEditable('Entry', $id);

    if ($entry->canEdit($this->currentUser)) {
      $entry->user->get();
      $this->render(null, array(
        'entry' => $entry
      ));
    } else {
      $this->flash('Login as the owner of this entry to edit it.');
      $this->setReturnTo(url_to($entry, 'edit'));
      redirect(url_to('users', 'login'));
    }
  }

  function do_new() {
    if (!$this->currentUser) {
      $this->flash("You must be logged in to create an entry.<br />(don't have an account? ".link_to("users", "new", "create one")." - it's easy)");
      $this->setReturnTo(url_to('entries', 'new'));
      return redirect(url_to('users', 'login'));
    }

    $entry = new Entry();
    $this->render('entries/edit', array(
      'entry' => $entry
    ));
  }

  function applyForm($entry, $owner = null) {
    $fields = explode(' ', "type name description email private_email url phone address password");

    foreach($fields as $field) {
      $entry->$field = trim(param($field, $entry->$field));
    }

    if ($this->currentUser->is_admin) {
      if (is_numeric(param('owner_id'))) {
        $new_owner = modelFind('User', 'id', param('owner_id'));
        if ($new_owner->id) $owner = $new_owner;
      }
    }

    if (isSpam($entry->name, $entry->email, $entry->description)) {
      $this->template->write('content', 'Yuck, that really didn\'t taste very good!');
      $this->template->render();
      die();
    }

    $entry->save($owner);

    // Do this after entry save since we need to know the entry ID
    if (isset($_FILES['image'])) {
      if ($_FILES['image']['size'] > 0 && $_FILES['image']['size'] < 1000000) {
        $entry->setImage($_FILES['image']['tmp_name']);
        $entry->save(null, false);
      }
    }
  }

  function do_create() {
    if (isPost()) {
      if (!$this->currentUser) {
        $this->show_error("Not logged in", 401);
      }
      $entry = new Entry();

      // Record IP that created this record.  We don't currently do anything with 
      // this, but it may prove useful later on
      $entry->created_ip = $_SERVER['REMOTE_ADDR'];

      $this->applyForm($entry, $this->currentUser);

      redirect(url_to($entry, 'show'));
    }
  }

  function do_update($id) {
    if (isPost()) {
      $entry = $this->getEditable('Entry', $id);

      $this->applyForm($entry);

      redirect(url_to($entry, 'show'));
    }
  }

  function do_delete($id) {
    if (isPost()) {
      $entry = $this->getEditable('Entry', $id);

      $entry->setImage(null); // Remove image files
      $entry->delete();
      redirect(url_to('entries'));
    }
  }

  function do_tags() {
    $entries = new Entry();
    $entries = $entries->get();

    $tags = array();
    foreach ($entries->all as $entry) {
      preg_match_all(TAG_REGEX, $entry->description, $matches);

      // Limit to 8 tags per entry
      $matches = array_slice($matches[1], 0, 8);

      // process each tag
      foreach ($matches as $match) {
        $match = strtolower($match);
        $tags[$match] = (isset($tags[$match]) ? $tags[$match] : 0) + 1;
      }
    }

    $this->render(null, array(
      'tags' => $tags
    ));
  }

  function do_geocode($id) {
    $entry = $this->getEditable('Entry', $id);

    $json = $entry->_geocode();
    dump($json);
  }

  function do_recache() {
    $entries = new Entry();
    $entries = $entries->get();

    foreach ($entries->all as $entry) {
      if (param('state')) {
        $entry->init();
        $entry->_geocode();
        $entry->save(null, false);
        $this->template->write('content', "Recached $entry->name<br />");
      }

      if (param('thumb')) {
        // Re-render thumbnails
        if ($entry->has_image) {
          $entry->renderThumb();
          $this->template->write('content', "Recached thumbnail for $entry->name<br />");
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
      $entry = $this->getEditable('Entry', $id);

      $entry->recoverPassword();
      redirect(url_to('entries', 'recover_password'));
    } else {
      $this->render();
    }
  }

  function do_comment($id) {
    $entry = $this->get('Entry', $id);

    if (isPost()) {
      $comment = new Comment();
      $comment->name = param('name');
      $comment->email = param('email');
      $comment->body = param('body');
      $comment->action = param('action');
      if (isSpam(null, null, $comment->body)) {
        $this->show_error('Yuck, that didn\'t taste very good!');
      }
      // Gather up relationships to save
      $rels = array($entry);
      if ($this->currentUser) {
        $rels[] = $this->currentUser;
      }

      // Save the entry
      $comment->save($rels);
    }

    redirect(url_to($entry, 'show'));
  }
}
