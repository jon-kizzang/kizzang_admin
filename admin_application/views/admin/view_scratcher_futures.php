
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Date</th>
            <?php foreach($games as $game) : ?>
            <th><?= $game->Name; ?></th>
            <?php endforeach; ?>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($ret as $date => $sns) : ?>
        <?php $total = 0;?>
        <tr>            
            <td><?= $date; ?></td>
            <?php foreach($sns as $sn) : ?>
            <?php if($sn) : ?>
            <td>
                <?php foreach($sn as $rec) : ?>
                <?php echo '$' . $rec->PrizeAmount . "<br/>"; $total += $rec->PrizeAmount; ?>
                <?php endforeach; ?>
            </td>
            <?php else : ?>
            <td>NONE</td>
            <?php endif; ?>
            <?php endforeach; ?>
            <td>$<?= number_format($total, 2); ?></td>            
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>