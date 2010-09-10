<?
require_once('basecontroller.php');

class Activities extends BaseController {
  function Activities() {
    parent::__construct();	
  }

  function do_index() {
    $MAX = 200;
    $activities = new Activity();
    $activities->order_by('created desc');
    $activities->limit(100);
    $activities = $activities->get();

    // Keep number of records from growing unbounded
    if ($activities->count() > $MAX) {
      $toDelete = new Activity();
      $toDelete->order_by('created desc');
      $toDelete->limit($MAX,$MAX/2);
      $toDelete->get();
      $toDelete->delete_all();
    }

    $this->template->write_view('content', 'activities/index', array(
      'activities' => $activities
    ));
    $this->template->render();
  }
}
