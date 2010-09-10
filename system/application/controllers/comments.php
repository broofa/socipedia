<?
require_once('basecontroller.php');

class Comments extends BaseController {
  function do_show($id) {
    $comment = $this->get('Comment', $id);
    $this->render(null, array('comment' => $comment));
  }

  function do_delete($id) {
    if (isPost()) {
      $comment = $this->get('Comment', $id);
      if ($comment && $comment->canEdit($this->currentUser)) {
        $comment->entry->get();
        $comment->delete();
      } else {
        $this->show_error('Permission denied');
      }

      redirect_back(url_to($comment->entry, 'show'));
    }
  }
}
