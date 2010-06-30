<style>
  ul#comments {
    list-style: none;
    width: 500px;
  }
  ul#comments li {
    padding: 5px 0px;
  }
  ul#comments .meta {
  }
  ul#comments .body {
    color: #444;
    margin: 5px 0px 5px 30px;
  }
</style>
<ul id="comments">
<script>
function deleteComment(id) {
  if (confirm("Really delete comment #" + id + "?")) {
    postTo('<?= url_to('comments', 'delete') ?>/' + id);
  }
}
</script>
<?
foreach ($comments as $comment) {
    $comment->user->get();
    $comment->entry->get();
    if ($comment->user->id) {
      $utext = $comment->user->html_name;
      $ulink = url_to($comment->user, 'show');
    } else {
      $utext = $comment->email;
      $ulink = "mailto: $comment->email";
    }
  ?>
    <li class="comment">
      <div class="meta">
        <? if ($comment->action) { ?>
          <span class="action"><?= $comment->action ?> request by
        <? } ?>
        <a href="<?= $ulink ?>"><?= $utext ?></a>
        on <?= link_to($comment->entry, 'show', $comment->entry->html_name) ?>
        &mdash;
        <span class="created"><?= $comment->friendly_created ?></span>
        <? if (isset($edit_ui)) { ?>
        <a class="delete_button" class="button" href="#" onclick="deleteComment(<?= $comment->id ?>)">delete</a>
        <? } ?>
      </div>
      <div class="body"><?= linkify(htmlify($comment->body)) ?></div>
    </li>
  <? } ?>
</ul>
