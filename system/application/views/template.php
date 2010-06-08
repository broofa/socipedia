<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title><?= $title ? $title : PROJECT_NAME ?></title>
  <?= $_scripts ?>
  <?= $_styles ?>
  <link href="<?= site_url('/static/css/common.css') ?>" media="screen" rel="stylesheet" type="text/css" /> 
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
</head>
<body>
  <div id="page">
  <h1 id="banner"><?= PROJECT_NAME ?></h1>
    <div id="navbar">
    <?= anchor("/", 'Home', 'class="button"') ?>
    <?= anchor("/entries", 'Browse', 'class="button left_cap"') ?><?= anchor("./entries/tags", 'Tags', 'class="button no_cap"') ?><?= anchor("/activities", 'Activity', 'class="button right_cap"') ?>
    <?= anchor("/entries/new", 'Add an Entry', 'class="button"') ?>
    <form action="<?= site_url('/entries') ?>" method="GET">
    <input type="text" name="q" onready="this.focus()" />
    </form>
    <?= anchor("/pages/contact", 'Contact', 'class="button" title="Contact COTR admin"') ?>
    </div>
    <div id="content">
      <?= $content ?>
    </div>
    <?= cleer() ?>
  </div>
  <a id="by_link" target="_blank" href="http://github.com/broofa/socipedia">powered by socipedia</a>
</body>
</html>

