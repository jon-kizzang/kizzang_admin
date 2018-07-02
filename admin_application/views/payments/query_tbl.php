<table id="query_tbl" class="table table-condensed table-responsive">     
<tr>
        <?php foreach($cols as $col) : ?>
        <th><?= $col; ?></th>
        <?php endforeach; ?>
    </tr>    
        <?php foreach($data as $row) : ?>
    <tr>
        <?php foreach($row as $point) :?>
        <td><?= $point; ?></td>
        <?php endforeach; ?>
    </tr>
        <?php endforeach; ?>    
</table>