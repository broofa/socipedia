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

  showPane: function (showTab) {
    var newtab = null;
    
    // If tab explicitely specified (by pane-name or index)
    if (typeof(showTab) == 'number') {
      newtab = $('.tab')[showTab];
    } else if (typeof(showTab) == 'string') {
      showTab = showTab.replace(/^#/, '');
      $('.tab').each(function(i, tab) {
        var tabPane = tab['data-pane'] || tab.getAttribute("data-pane");
        if (tabPane == showTab) newtab  = tab
      });
    } else {
      newtab = this;
    }

    $('.tab').removeClass('active');
    $(newtab).addClass('active');

    var showId = newtab['data-pane'] || newtab.getAttribute("data-pane");
    $('.tab_pane').each(function(i, pane) {
      if (pane.id == showId) {
        $(pane).slideDown();
        pp.hash = location = '#' + showId;
      } else {
        $(pane).slideUp();
      }
    });
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


$(window).load(pp.load);
</script>

<style>
fieldset {
  border-radius: 8px;
  -moz-border-radius: 8px;
  -webkit-border-radius: 8px;
  border: solid 1px #ccc;
  margin: 10px 0px;
}
FIELDSET LEGEND {
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
#entry_form INPUT[type=text] {
  width: 250px;
}
#entry_form TEXTAREA {
  width: 320px;
}
#entry_form LABEL {
  padding-top: .3em;
  padding-right: 1em;
  text-align:right;
  white-space: nowrap;
  float:left;
  width:8em;
}

.tab_bar {
  display: none;
  width: 560px;
  border-bottom: solid 1px #ccc;
  position: relative;
}
.tab_bar #submit {
  position: absolute;
  right: 20px;
}
.tab_bar LI {
  display: inline-block;
  padding: 2px 10px;
}
.tab_bar .tab {
  color: #777;
  background-color: #eee;
  font-size: 12pt;
  font-weight: bold;
  position: relative;
  top: 1px;
  border: solid 1px #ccc;
  border-radius: 10px 10px 0px 0px;
  -moz-border-radius: 10px 10px 0px 0px;
  -webkit-border-radius: 10px 10px 0px 0px;
  margin-left: -8px;
}
.tab_bar .active {
  color: #555;
  background-color: #fff;
  border-bottom-color: #fff;
  z-index: 2;
}
</style>

<? if ($entry->id) {
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
    <div id="delete_ui1" style="display: none">
      This can not be undone.  To delete this entry for good, re-enter the password:
      <input name="auth" type="password" />
      <input type="button" value="Cancel" onclick="pp.toggleDelete()" />
      <input type="submit" value="Delete Now" />
    </div>
  </form>
<? } ?>

<form id="entry_form" enctype="multipart/form-data" method="POST" action="<?= $entry->id ? site_url("/entries/update/$entry->id") : site_url("/entries/create") ?>">
  <ul class="tab_bar">
    <li class="tab" data-pane="required">Required</li>
    <li class="tab" data-pane="public">Public</li>
    <li class="tab" data-pane="optional">Optional</li>
  </ul>

  <fieldset id="required" class="tab_pane">
    <input name="auth" type="hidden" value="<?= $entry->auth ?>" />

    <legend>Private Information (required)</legend>
    <p>These fields are not made public.  They are required for quality and security purposes.</p>
    <label for="private_email">Contact Email</label>
    <input name="private_email" type="text" value="<?= $entry->private_email ?>" />
    <span class="hint">Who we should contact if there are any issues with this entry. Not shared. Not published. No spam.</span>

    <div class="sep"></div>
    <label for="password">Password</label>
    <input name="password" type="password" />
    <span class="hint">The password to require before allowing changes to be made to this entry.</span>
  </fieldset>

  <fieldset id="public" class="tab_pane mode_business">
    <legend>Public Information (optional)</legend>
    <p>The following fields <em>will</em> be public.  Each one is optional, but if you don't fill any of them out... well... there's not much point to this then is there?</p>

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
    <span class="hint" style="float:right; width:200px">Hint: Add <a href="http://help.twitter.com/entries/49309-what-are-hashtags-symbols" target="_blank">#hashtags</a> to have your affiliations and areas of expertise listed on the <?= anchor('/entries/tags', 'Tags page') ?>. E.g. "We partner with <strong>#techalliance</strong> members to build <strong>#java</strong> solutions for the <strong>#biotech</strong> industry."</span>
    <textarea id="description" name="description" rows="16"><?= $entry->description ?></textarea>
  </fieldset>

  <div style="text-align: center">
    <input type="submit" value="<?= $entry->id ? 'Save Entry' : 'Add Entry' ?>" />
  </div>
</form>


