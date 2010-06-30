
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
label IMG {
  height: 24px;
  vertical-align: middle;
}
</style>

<? if ($entry->id) { ?>
  <h2><?= htmlify("$entry->name") ?></h2>
<? } else { ?>
  <h2>New Entry</h2>
<? } ?>

<form id="entry_form" class="basic_form" enctype="multipart/form-data" method="POST" action="<?= $entry->id ? site_url("/entries/update/$entry->id") : site_url("/entries/create") ?>">
  <p class="required"> = required</p>
  <?
  if ($currentUser->is_admin) {
    // The current owner (defaults to current user if not specified)
    $owner_id = $entry->user->id;

    // Render drop down of current users
    $users = new User();
    $users->order_by('name');
    $users->get();
    ?>
    <div class="label">Entry Owner</div>
    <select name="owner_id">
    <option>Choose an owner ...</option>
    <?  foreach ($users as $user) { ?>
    <option value="<?= $user->id ?>" <?= $user->id == $owner_id ? "selected" : "" ?>>
        <?= $user->html_name ?>
      </option>
    <? } ?>
    </select>
    <?= cleer() ?>
  <? } ?>

  <div class="label required">Entry type</div>
  <label>
    <input name="type" type="radio" value="individual" <?= $entry->type == 'individual' ? 'checked' : '' ?>/>
    Individual
    <img style="height: 24px;" src="<?= site_url('static/images/type_individual.png') ?>" />
  </label>

  <label style="padding-left: 40px">
    <input name="type" type="radio" value="organization" <?= $entry->type != 'individual' ? 'checked' : '' ?>/>
    Organization
    <img style="height: 24px;" src="<?= site_url('static/images/type_organization.png') ?>" />
  </label>
  <div class="hint">Use 'Individual' if this is a specific person, use 'Organization' if more than one person is involved</div>

  <?= cleer() ?>

  <div class="label required">Name</div>
  <input class="wide" name="name" type="text" value="<?= $entry->name ?>" />
  <div class="hint">E.g. Fred Smith or GloSoft, Inc.</div>

  <?= cleer() ?>

  <div class="label">Email</div>
  <input class="wide" name="email" type="text" value="<?= $entry->email ?>" />
  <div class="hint">E.g. info@glosoft.com</div>

  <?= cleer() ?>

  <div class="label">Website</div>
  <input class="wide" name="url" type="text" value="<?= $entry->url ?>" />
  <div class="hint">E.g. http://www.glosoft.com</div>

  <?= cleer() ?>

  <div class="label">Phone</div>
  <input class="wide" name="phone" type="text" value="<?= $entry->phone ?>" />
  <div class="hint">E.g. 541-555-1212</div>

  <?= cleer() ?>

  <div class="label">Address</div>
  <textarea name="address" rows="4"><?= $entry->address ?></textarea>
  <div class="hint">Does Google know where this is?  (To find out, <a href="javascript:void 0" onclick="pp.verifyAddress()">click here</a>)  If not, we won't be able to include it in our map pages.</div>

  <?= cleer() ?>

  <div class="label">Logo/Photo</div>
  <? if ($entry->has_image) { ?><img style="float: left; margin-right: 10px;" src="<?= $entry->thumbURL() ?>" /><? } ?>
  <input name="image" type="file" />
  <div class="hint">Use jpg, gif or png image (smaller than 1MB, please)</div>

  <?= cleer() ?>

  <div class="label">Description</div>
  <textarea id="description" name="description" rows="16"><?= $entry->description ?></textarea>
  <div class="hint">(<span id="description_left">1000</span> characters left)</div>
  <div class="hint">Hint: Add <a href="http://help.twitter.com/entries/49309-what-are-hashtags-symbols">#hashtags</a> to have your affiliations and areas of expertise listed on the <?= anchor('/entries/tags', 'Tags page') ?>. E.g. "We partner with <strong>#techalliance</strong> members to build <strong>#java</strong> solutions for the <strong>#biotech</strong> industry."</div>

  <?= cleer() ?>

  <input type="submit" value="<?= $entry->id ? 'Save Entry' : 'Add Entry' ?>" />
</form>
<?= cleer() ?>


