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
.sidebar .owner {
  font-size: 10px;
  opacity: .7;
  text-align: center;
  margin-top: 20px;
}
</style>

  <div class="sidebar">
    <? if ($entry->canEdit($currentUser)) { ?>
        <script>
        function confirmDelete() {
          if (confirm('Are you sure?  This can not be undone.\nTo permanently delete this entry, click "OK"')) {
            postTo('<?= url_to($entry, 'delete') ?>');
          }
        }
        </script>
        <?= link_to($entry, 'edit', 'edit', 'class="button"') ?>
        <a href="#" class="button" onclick="return confirmDelete()">delete</a>
    <? } ?>
    <? if ($entry->user->id) { ?>
      <p class="owner">(entry by <?= link_to($entry->user, 'show', $entry->user->html_name) ?>)</p>
    <? } ?>
  </div>

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
<?= $entry->html_name ?>
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
    <dd class="phone"><?= $entry->html_phone ?></dd>
  <? } ?>
  <? if ($entry->address) { ?>
    <dt class="label">address
    </dt>
    <dd class="address">
    <a href="http://maps.google.com/maps?q=<?= urlencode($entry->address) ?>"><?= $entry->html_address ?></a>
</dd>
  <? } ?>
</dl>

<? if ($entry->comment->count() > 0) { ?>
<h3>Comments</h3>
<?= $cmarkup; ?>
<? } ?>

<p style="font-weight: bold">Add a comment / request action for this entry</p>

<form id="comment_ui" class="basic_form" action="<?= url_to($entry, 'comment') ?>" method="POST">
  <? if (!$currentUser) { ?>
    <div class="label">Name</div>
    <input name="name" type="text" value="" />
    <?= cleer() ?>

    <div class="label">Email</div>
    <input name="email" type="text" value="" />
    <?= cleer() ?>
  <? } ?>

  <div class="label">Comment</div>
  <textarea name="body" style="width: 400px; height: 80px"></textarea>
  <?= cleer() ?>

  <select name="action">
    <option value="" selected>Requested action ...</option>
    <option value="">None - 'just leaving a comment</option>
    <option value="update">Update - this needs some attention</option>
    <option value="delete">Remove - this should be deleted</option>
    <option value="claim">Claim - I'd like to be responsible for this</option>
    <option value="other">Other - see comment for details</option>
  </select>

  <input style="margin-left: 40px;" type="submit" value="Submit" />
</form>

<p id="footer">
updated: <?= $entry->updated ?>
<? if ($entry->geocode) { ?>
  &bull; geocode: <?= $entry->html_geocode ?>
<? } ?>

</p>
