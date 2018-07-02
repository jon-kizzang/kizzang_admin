<?= '"' . implode('","', $cols) . '"'; ?>            
<?php foreach($data as $row) : ?>
<?= '"' . str_replace('|||,|||', '","', str_replace('"', '\"', implode('|||,|||', $row))) . '"' . "\n"; ?>
<?php endforeach; ?>    