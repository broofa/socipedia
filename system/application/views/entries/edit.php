<script>
var pp = {
  load: function() {
    setInterval(pp.limitDescription, 500);
  },

  getForm: function() {
    return document.forms.entry_form;
  },

  limitDescription: function() {
    var max = 1000;
    var el = document.getElementById('description');
    if (el) {
      var v = el.value;
      if (v.length > max) {
        el.value = v.substr(0,max);
      }
      document.getElementById('description_left').innerHTML = Math.max(0, max - v.length);
    }
  },

  verifyAddress: function () {
    var v = pp.getForm().address.value;
    //v = v.replace(/\n/g, ', ');
    var url = 'http://maps.google.com/maps?q='
    window.open(url + escape(v), 'gverify', 'resizable,width=700,height=500');
  },

  toggleDelete: function() {
    $('#delete_ui0').slideToggle();
    $('#delete_ui1').slideToggle();
  },
};

pp.load();
</script>

<?
if (!$entry) {
  class DummyEntry {
    var $id = null;
    var $address = null;
    var $auth = null;
    var $company = null;
    var $description = null;
    var $email = null;
    var $geocode = null;
    var $has_image = null;
    var $name = null;
    var $owner = null;
    var $phone = null;
    var $url = null;
  }
  $entry = new DummyEntry();
}
?>

<style>
fieldset {
  border-radius: 8px;
  border: solid 1px #ccc;
  margin: 10px 0px;
}
fieldset legend {
  color: #555;
  font-weight: bold;
  font-size: 12pt;
}

 
/* edit form */
#entry_form .hint {
  color: #642;
  font-size:10px;
  display:block;
  width: 400px;
  margin-left: 12em;
  padding-top:2px;
}
#entry_form .sep {
  display:block;
  height:12px;
  clear:both;
}
#entry_form input[type=text] {
  width: 250px;
}
#entry_form textarea {
  width: 320px;
}
#entry_form label {
  padding-top: .3em;
  padding-right: 1em;
  text-align:right;
  white-space: nowrap;
  float:left;
  width:8em;
}
#entry_form .form_controls input {
  width: 80px;
  margin-right: 100px;
}

</style>
  <? if ($entry->id) {
    form_open('form', array(

    ));
  ?>
    <form method="POST" action="<?= site_url('/entries/delete/'.$entry->id) ?>"
        class="sidebar"
        style="border-color: #900; text-align: left">
      <div id="delete_ui0">
        Don't want to be in the directory anymore?
        <br/>
        <br/>
        <a class="button" onclick="pp.toggleDelete()">Delete this entry</a>
      </div>
      <div id="delete_ui1" style="display:none">
        This can not be undone.  To delete this entry for good, re-enter the password:
        <input name="password" type="password" />
        <input type="button" value="Cancel" onclick="pp.toggleDelete()" />
        <input type="submit" value="Delete Now" />
      </div>
    </form>
  <? } ?>
<form id="entry_form" enctype="multipart/form-data" method="POST" action="<?= $entry->id ? site_url("/entries/update/$entry->id") : site_url("/entries/create") ?>">
  <input name="id" type="hidden" value="<?= $entry->id ?>" />
  <input name="auth" type="hidden" value="<?= $entry->auth ?>" />
  <input name="doDelete" type="hidden" value="0" />

  <fieldset>
  <legend>Private Info (required)</legend>
  <p>For quality and security purposes we need to have the following info.</p>
  <label for="owner">Contact Email</label>
  <input name="owner" type="text" value="<?= $entry->owner ?>" />
  <span class="hint">Who we should contact if there are any questions with this entry. Not shared. Not published. No spam.</span>

  <div class="sep"></div>
  <label for="password">Password</label>
  <input name="password" type="password" />
  <span class="hint">The password to require before allowing changes to be made to this entry.</span>

  <div class="sep"></div>
  </fieldset>
  <fieldset>
  <legend>Public Profile (optional)</legend>
  <p>Yup, all these fields are optional. Of course, if you don't fill any of them out... well... there's not much point in this then, is there.</p>

  <div class="sep"></div>
  <label for="name">Individual Name</label>
  <input name="name" type="text" value="<?= $entry->name ?>" />
  <span class="hint">E.g John Smith. (Leave blank if this entry is for a company)</span>

  <div class="sep"></div>
  <label for="company">Company Name</label>
  <input name="company" type="text" value="<?= $entry->company ?>" />
  <span class="hint">E.g. GloSoft Technologies. (Leave blank if no company affiliation)</span>

  <div class="sep"></div>
  <label for="email">Email</label>
  <input name="email" type="text" value="<?= $entry->email ?>" />
  <span class="hint">E.g. info@glosoft.com</span>

  <div class="sep"></div>
  <label for="url">Website</label>
  <input name="url" type="text" value="<?= $entry->url ?>" />
  <span class="hint">E.g. http://www.glosoft.com</span>

  <div class="sep"></div>
  <label for="phone">Phone</label>
  <input name="phone" type="text" value="<?= $entry->phone ?>" />
  <span class="hint">E.g. 541-555-1212</span>

  <div class="sep"></div>
  <span class="hint" style="float:right; width:200px">Please <a href="javascript:void 0" onclick="pp.verifyAddress()">verify that Google recognizes this location</a> (opens in new window)</span>
  <label for="address">Address</label>
  <textarea name="address" rows="4"><?= $entry->address ?></textarea>
  <span class="hint">Please include city, state, and zip at a minimum.  E.g.:
  <br />&nbsp;&nbsp;&nbsp;&nbsp;123 Wall St.
  <br />&nbsp;&nbsp;&nbsp;&nbsp;Bend, OR 97701
  </span>

  <div class="sep"></div>
  <label for="image">Logo/Photo</label>
  <? if ($entry->has_image) { ?><img style="float: left; margin-right: 10px;" src="<?= $entry->thumbURL() ?>" /><? } ?>
  <input name="image" type="file" />
  <span class="hint">Use jpg, gif or png image (smaller than 1MB, please)</span>

  <div class="sep"></div>
  <label for="description">Description</label>
  <span class="hint">Additional info, if desired (<span id="description_left">1000</span> characters left)</span>
  <span class="hint" style="float:right; width:200px">Hint: Use <a href="http://help.twitter.com/entries/49309-what-are-hashtags-symbols" target="_blank">#hashtags</a> to have your entry appear in our <?= anchor('/entries/tags', 'Tags page') ?>.  Just add a '#' wherever you list an interest, skill, or relationship. E.g. "We partner with #bendresearch to build #java solutions for the #biotech industry."</span>
  <textarea id="description" name="description" rows="16"><?= $entry->description ?></textarea>
  </fieldset>

  <div class="form_controls">
  <input type="submit" value="<?= $entry->id ? 'Save Entry' : 'Add Entry' ?>" />
  </div>
</form>


