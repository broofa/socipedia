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
          <?= link_to($currentUser, 'show', htmlify($currentUser->display_name)) ?>
          <?= link_to($currentUser, 'edit', 'settings') ?>
          <?= link_to('users', 'logout', 'logout') ?>
        <? } else { ?>
          <?= link_to('users', 'new', 'register') ?>
          <?= link_to('users', 'login', 'login') ?>
        <? } ?>
      </div>

      <div id="navbar">
        <?= link_to(null, null, 'Home', 'class="button left_cap"') ?><?= link_to('pages', 'contact', 'Contact', 'class="button right_cap"') ?>
        <?= link_to('entries', null, 'Browse', 'class="button left_cap"') ?><?= link_to('entries', 'tags', 'Tags', 'class="button right_cap"') ?>

        <?= link_to('entries', 'new', 'Add an Entry', 'class="button"') ?>
        <form action="<?= url_to('entries') ?>" method="GET">
        <input type="text" name="q" onready="this.focus()" />
        </form>
      </div>


      <div id="content">
        <?= $flash ?>
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

