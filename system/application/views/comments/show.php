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
  .action {
    color: #555;
    font-weight: bold;
  }
  .action.update {color: #464;}
  .action.claim {color: #446;}
  .action.delete {color: #644;}
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
    $comment->user->get();
    $comment->entry->get();
  ?>
    <li class="comment">
      <div class="meta">
        <a href="<?= $comment->from_link ?>"><?= $comment->html_from ?></a>
        on <?= link_to($comment->entry, 'show', $comment->entry->html_name) ?>
        &mdash;
        <span class="created"><?= $comment->friendly_created ?></span>
        <? if ($comment->action) { ?>
          <span class="action <?= $comment->action ?>">(<?= $comment->action ?> request)</span>
        <? } ?>
        <? if (isset($edit_ui)) { ?>
        <a class="delete_button" class="button" href="#" onclick="deleteComment(<?= $comment->id ?>)">delete</a>
        <? } ?>
      </div>
      <div class="body"><?= linkify(htmlify($comment->body)) ?></div>
    </li>
</ul>
