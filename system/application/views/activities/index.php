<style>
dl dt {
  margin-top: 5px;
  font-weight: bold;
}
dl dd {
  font-size: 10px;
  margin-left: 150px;
}
.updated {
  font-family: monospace;
  font-weight: normal;
  color: #888;
}
</style>

<dl>
  <? foreach($activities->all as $activity) { ?>
    <dt>
      <span class="updated"><?= $activity->updated ?></span>
      <?= anchor('/entries/show/'.$activity->entry_id, $activity->summary); ?>
    </dt>
    <dd>
      <?= htmlify($activity->body, true) ?>
    </dd>
  <? } ?>
</dl>
