<?
class Comment extends BaseModel {
  var $has_one = array('user', 'entry');

  function logName() {
    return $this->body;
  }

  function __get($k) {
    if ($k == 'friendly_created') {
      $ts = strtotime($this->created);
      return strftime('%b %e, %Y - %H:%M%P', $ts);
    }  else if ($k == 'from') {
        return $this->user->id ? $this->user->name : $this->name;
    }  else if ($k == 'from_link') {
        return $this->user->id ? url_to($this->user, 'show') : "";
    }
    return parent::__get($k);
  }

  function canEdit($user) {
    if (parent::canEdit($user)) return true;
    if ($user) {
      // User created comment
      $this->user->get();
      if ($this->user) {
        if ($this->user->id == $user->id) return true;
      }

      // User owns comment's entry
      $this->entry->get();
      if ($this->entry) {
        $this->entry->user->get();
        if ($this->entry->user) {
          if ($user->id == $this->entry->user->id) return true;
        }
      }
    }

    return false;
  }
}
?>
