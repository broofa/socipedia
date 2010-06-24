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
  background-color: #fff;
  margin: 0px 0px 10px 10px;
}

#comments {
  margin: 10px 0px;
  padding: 0px;
  list-style: none;
}
#comments .comment {
  font-size: 90%;
}
#comments .comment .created {
  color: #666;
}
#comments .comment .body {
  margin-left: 10px;
}
#footer {
  color: #666;
  margin: 10px 0px;
}
#map {
  float: right;
  text-align: center;
  font-size: 10pt;
  clear: right;
}
</style>

<? if ($entry->canEdit($currentUser)) { ?>
  <div class="sidebar">
    <script>
    function confirmDelete() {
      return confirm('Are you sure?  This can not be undone.\nTo permanently delete this entry, click "OK"');
    }
    </script>
    <?= link_to($entry, 'edit', 'edit', 'class="button"') ?>
    <?= link_to($entry, 'delete', 'delete', 'class="button" onclick="return confirmDelete()"') ?>
  </div>
<? } ?>
<? if (false && $entry->geocode) { ?>
  <div id="map">
    <iframe
      <?
      $mapUrl = $entry->address;
      $mapUrl = preg_replace('/\s/', '+', $mapUrl);
      $mapUrl = "http://maps.google.com/maps?q=".htmlify($mapUrl);
      ?>
      width="200"
      height="200"
      frameborder="0"
      scrolling="no"
      marginheight="0"
      marginwidth="0"
      src="<?= $mapUrl."&output=embed&iwloc=near" ?>"></iframe>
      <br />
      <a href="<?= $mapUrl ?>">View Larger Map</a>
  </div>
<? } ?>

<? if ($entry->has_image) { ?>
  <img class="avatar large" src="<?= $entry->imageURL() ?>" />
<? } ?>

<h2 class="<?= $entry->stale ? "stale" : "" ?>">
  <?= $entry->name ?>
</h2>

<div class="description">

<?= $entry->descriptionHtml ?>
</div>

<dl class="contact_info">
  <? if ($entry->email) { ?>
    <dt class="label">email</dt>
    <dd class="email"><?= auto_link($entry->email, 'email', TRUE) ?></dd>
  <? } ?>
  <? if ($entry->url) { ?>
    <dt class="label">www</dt>
    <dd class="url"><?= weblink($entry->url, null, TRUE) ?></dd>
  <? } ?>
  <? if ($entry->phone) { ?>
    <dt class="label">phone</dt>
    <dd class="phone"><?= htmlify($entry->phone) ?></dd>
  <? } ?>
  <? if ($entry->address) { ?>
    <dt class="label">address
    </dt>
    <dd class="address">
    <a href="http://maps.google.com/maps?q=<?= urlencode($entry->address) ?>"><?= htmlify($entry->address) ?></a>
</dd>
  <? } ?>
</dl>

<?
$comments = $entry->getComments()->all;
if (count($comments) > 0) {
?>
  <h3>Comments</h3>
  <ul id="comments">
    <? foreach ($comments as $comment) { ?>
      <li class="comment"><span class="created"><?= $comment->created ?></span></span><span class="body"><?= linkify(htmlify($comment->body)) ?></span><span class="action"><?= $comment->action ? " ($comment->action)" : ''?></li>
    <? } ?>
  </ul>
<? } ?>
<p style="font-weight: bold">Add a comment / request action for this entry</p>
<form id="comment_ui" style="margin-bottom: 15px;" action="<?= url_to($entry, 'comment') ?>" method="POST">
  <textarea name="body" style="width: 400px; height: 80px"></textarea><br />
  <select name="action">
    <option value="" selected>Requested action ...</option>
    <option value="">None - 'just leaving a comment</option>
    <option value="update">Update - this needs some attention</option>
    <option value="delete">Remove - this should be deleted</option>
    <option value="claim">Claim - this should belong to me</option>
    <option value="other">Other - see comment for details</option>
  </select>
  <input style="margin-left: 40px;" type="submit" value="Submit" />
</form>

<p id="footer">
updated: <?= $entry->updated ?>
<? if ($entry->geocode) { ?>
  &bull; geocode: <?= htmlify($entry->geocode) ?>
<? } ?>
</p>
