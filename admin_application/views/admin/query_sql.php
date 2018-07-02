<?php $insert = "Insert into  $table (" . implode(",", $cols) . ") values \n"; ?>
<?php foreach($data as $key => $row) : ?>
<?php if(!$key || !($key % 5000)) print $insert; ?>
<?= "('" . str_replace("|||", "','", str_replace("'", "''", implode("|||", $row))) . ((!(($key + 1) % 5000) || ($key == count($data) - 1)) ? "');\n" : "'),\n"); ?>
<?php endforeach; ?>