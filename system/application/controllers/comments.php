<?
require_once('basecontroller.php');

class Comments extends BaseController {

  function requireComment($id, $editable = true) {
    $this->comment = null;

    $cu = $this->currentUser;
    $comment = new Comment();
    $comment->where('id', $id)->get();

    if (!$editable) {
      $this->comment = $comment;
    } else {
      $comment->user->get();
      if ($cu && ($cu->is_admin || $cu->id == $comment->user->id)) {
        $this->comment = $comment;
      }
    }

    if (!$this->comment) {
      $this->show_error('Entry not found');
    }
  }

  function do_delete($id) {
    if (isPost()) {
      $this->requireComment($id);
      $this->comment->entry->get();
      $this->comment->delete();

      redirect_back(url_to($this->comment->entry, 'show'));
    }
  }
}
