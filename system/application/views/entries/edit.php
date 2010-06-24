<script>
var pp = {
  load: function() {
    // $('.tab').click(pp.showPane);

    //pp.showPane(location.hash || 0);

    setInterval(function() {
      pp.limitDescription();

      /*
      if (location.hash != pp.hash) {
        pp.showPane(location.hash)
      }
       */
    }, 500);
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
  }
};

$(window).load(pp.load);
</script>

<style>
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
#entry_form INPUT[type=text] {
  width: 250px;
}
#entry_form TEXTAREA {
  width: 320px;
}
#entry_form LABEL.left {
  padding-top: .3em;
  padding-right: 1em;
  text-align:right;
  white-space: nowrap;
  float:left;
  width:80px;
}
#entry_form LABEL.required {
  width: 64px;
}
LABEL IMG {
  vertical-align: middle;
  height: 24px;
}
.required {
  padding-left: 16px;
  background: url(<?= site_url('/static/images/required.png') ?>) no-repeat left 2px;
}
</style>

<? if ($entry->id) { ?>
  <h2><?= htmlify("$entry->name") ?></h2>
<? } else { ?>
  <h2>New Entry</h2>
<? } ?>

<form id="entry_form" enctype="multipart/form-data" method="POST" action="<?= $entry->id ? site_url("/entries/update/$entry->id") : site_url("/entries/create") ?>">
  <p class="required"> = required field</p>

  <label class="left required">Entry type</label>
  <label>
    <input name="type" type="radio" value="individual" <?= $entry->type == 'individual' ? 'checked' : '' ?>/>
    Individual
    <img src="http://icons2.iconarchive.com/icons/deleket/sleek-xp-basic/48/Administrator-icon.png" />
  </label>

  <label style="padding-left: 40px">
    <input name="type" type="radio" value="organization" <?= $entry->type != 'individual' ? 'checked' : '' ?>/>
    Organization
    <img src="http://icons2.iconarchive.com/icons/deleket/sleek-xp-basic/48/Clients-icon.png" />
  </label>
  <span class="hint">Use 'Individual' if this is a specific person, use 'Organization' if more than one person is involved</span>

  <div class="sep"></div>
  <label class="left required" for="name">Name</label>
  <input name="name" type="text" value="<?= $entry->name ?>" />
  <span class="hint">E.g. Fred Smith or GloSoft, Inc.</span>

  <div class="sep"></div>
  <label class="left" for="email">Email</label>
  <input name="email" type="text" value="<?= $entry->email ?>" />
  <span class="hint">E.g. info@glosoft.com</span>

  <div class="sep"></div>
  <label class="left" for="url">Website</label>
  <input name="url" type="text" value="<?= $entry->url ?>" />
  <span class="hint">E.g. http://www.glosoft.com</span>

  <div class="sep"></div>
  <label class="left" for="phone">Phone</label>
  <input name="phone" type="text" value="<?= $entry->phone ?>" />
  <span class="hint">E.g. 541-555-1212</span>

  <div class="sep"></div>
  <label class="left" for="address">Address</label>
  <textarea name="address" rows="4"><?= $entry->address ?></textarea>
  <span class="hint">Does Google know where this is?  (To find out, <a href="javascript:void 0" onclick="pp.verifyAddress()">click here</a>)  If not, we won't be able to include it in our map pages.</span>

  <div class="sep"></div>
  <label class="left" for="image">Logo/Photo</label>
  <? if ($entry->has_image) { ?><img style="float: left; margin-right: 10px;" src="<?= $entry->thumbURL() ?>" /><? } ?>
  <input name="image" type="file" />
  <span class="hint">Use jpg, gif or png image (smaller than 1MB, please)</span>

  <div class="sep"></div>
  <label class="left" for="description">Description</label>
  <span class="hint">(<span id="description_left">1000</span> characters left)</span>
  <span class="hint" style="float:right; width:200px">Hint: Add <a href="http://help.twitter.com/entries/49309-what-are-hashtags-symbols">#hashtags</a> to have your affiliations and areas of expertise listed on the <?= anchor('/entries/tags', 'Tags page') ?>. E.g. "We partner with <strong>#techalliance</strong> members to build <strong>#java</strong> solutions for the <strong>#biotech</strong> industry."</span>
  <textarea id="description" name="description" rows="16"><?= $entry->description ?></textarea>

  <div style="text-align: center">
    <input type="submit" value="<?= $entry->id ? 'Save Entry' : 'Add Entry' ?>" />
  </div>
</form>


