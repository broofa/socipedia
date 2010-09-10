<style>
ul {
  list-style: none;
}
.updated {
  font-family: monospace;
  font-weight: normal;
  color: #888;
}
</style>

<ul>
  <? foreach($activities->all as $activity) { ?>
    <li>
      <span class="updated"><?= $activity->created ?></span>
      <?= link_to($activity->target_class, "show/$activity->target_id", $activity->body); ?>
    </li>
  <? } ?>
</ul>
