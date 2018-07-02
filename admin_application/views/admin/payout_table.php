<?php if($payouts) :?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Rank</th>
            <th>Prize Amount</th>
            <th>Prize Name</th>
            <th>Taxable Amount</th>
            <th>Weight</th>            
        </tr>
    </thead>
    <?php foreach($payouts as $payout) : ?>
    <tr>
        <td><?= $payout->Rank?></td>
        <td><?= $payout->PrizeAmount?></td>
        <td><?= $payout->PrizeName?></td>
        <td><?= $payout->TaxableAmount?></td>
        <td><?= $payout->Weight?></td>        
    </tr>
    <?php endforeach;?>
</table>
<?php endif; ?>