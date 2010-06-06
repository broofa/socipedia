<?
if (!$entry) {
  show_404($this->router->uri);
} else {
?>
  <style>
  h2,
  .description,
  .contact_info {
    width: 550px;
  }
  .description {
    position: relative;
    text-align:justify;
  }
  img {
    float: right;
    padding: 10px;
  }
  .contact_info {
    padding:6px 0px;
    margin:6px 0px;
    border:solid 1px #ccc;
    border-width: 1px 0px;
    list-style:none;
  }
  dt {
    color:#669;
    margin-top: 6px;
    font-weight:bold;
    padding-right:10px;
    text-align:right;
    width:65px;
    height:20px;
  }
  dd {
    margin-top:-20px;
    padding-left: 40px;
  }
  .avatar {
    float: right;
    clear: both;
    padding: 4px;
    border: solid 1px #eee;
    background-color: #fff;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius: 4px;
    margin: 0px 0px 10px 10px;
    -moz-box-shadow: inset 2px 2px 2px rgba(0,0,0,.5);
    -webkit-box-shadow: inset 1px 1px 1px rgba(0,0,0,.5);
  }
  </style>

  <div class="sidebar">
    <?= anchor('/entries/edit/'.$entry->id, 'Edit this entry', 'class="button"') ?>
  </div>
  <? if ($entry->has_image) { ?>
    <img class="avatar large" src="<?= $entry->imageURL() ?>" />
  <? } ?>

  <h2><?= $entry->getDisplayName() ?></h2>

  <div class="description">

<?= $entry->getDescription() ?>
</div>

  <dl class="contact_info">
    <? if ($entry->email) { ?>
      <dt class="label">email</dt>
      <dd class="email"><a href="mailto:<?= htmlify($entry->email) ?>"><?= htmlify($entry->email) ?></a></dd>
    <? } ?>
    <? if ($entry->url) { ?>
      <dt class="label">www</dt>
      <dd class="url"><a rel="nofollow" target="_blank" href="<?= htmlify($entry->url) ?>"><?= htmlify($entry->url) ?></a></dd>
    <? } ?>
    <? if ($entry->phone) { ?>
      <dt class="label">phone</dt>
      <dd class="phone"><?= htmlify($entry->phone) ?></dd>
    <? } ?>
    <? if ($entry->address) { ?>
      <dt class="label">address</dt>
      <dd class="address"><?= htmlify($entry->address) ?></dd>
    <? } ?>
  </dl>

  <p style="font-size: 10px; color: #666;">
  This entry was last updated on <?= $entry->updated ?>
  </p>
<? } ?>
