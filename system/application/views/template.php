<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?= $title ? $title : PROJECT_NAME ?></title>
    <?= $_scripts ?>
    <?= $_styles ?>
    <link href="<?= site_url('/static/css/common.css') ?>" media="screen" rel="stylesheet" type="text/css" /> 
    <script>
      var SITE_ROOT = '<?= site_url('') ?>';
    </script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
  </head>
  <body>
    <div id="page">
      <h1 id="banner"><?= PROJECT_NAME ?></h1>
      <div id="session_controls">
        <? if (isset($currentUser)) { ?>
          welcome,
          <a href="<?= url_to('users', 'show') ?>"><?= $currentUser->display_name ?></a>
          <a href="<?= url_to('users', 'edit') ?>">settings</a>
          <a href="<?= url_to('users', 'logout') ?>">logout</a>
        <? } else { ?>
          <a href="<?= url_to('users', 'login') ?>">login</a>
        <? } ?>
      </div>

      <div id="navbar">
        <a href="<?= url_to() ?>" class="button left_cap">Home</a><a href="<?= url_to('pages', 'contact') ?>" class="button right_cap">Contact</a>
        <a href="<?= url_to('entries') ?>" class="button left_cap">Browse</a><a href="<?= url_to('entries', 'tags') ?>" class="button right_cap">Tags</a>

        <a href="<?= url_to('entries', 'new') ?>" class="button">Add an Entry</a>
        <form action="<?= url_to('entries') ?>" method="GET">
        <input type="text" name="q" onready="this.focus()" />
        </form>
      </div>

      <div id="content">
        <?= $content ?>
        <?= cleer() ?>
      </div>

      <div id="footer">
        <?= anchor("/activities", 'activity') ?>
        &bull;
        <a href="http://github.com/broofa/socipedia">powered by socipedia</a>
        &bull;
        photo credit: <a href="http://www.flickr.com/photos/alyssssyla/3588629512/">alyssssyla</a>
      </div>
    <div>
  </body>
  <script src="<?= site_url('static/js/common.js') ?>"></script>
</html>

