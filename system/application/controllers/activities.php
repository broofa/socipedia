<?
class Activities extends Controller {
  function Activities() {
    parent::Controller();	
  }

  function index() {
    $MAX = 200;
    $activities = new Activity();
    $activities->order_by('updated desc');
    $activities->limit(100);
    $activities = $activities->get();

    // Keep number of records from growing unbounded
    if ($activities->count() > $MAX) {
      $toDelete = new Activity();
      $toDelete->order_by('updated desc');
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
