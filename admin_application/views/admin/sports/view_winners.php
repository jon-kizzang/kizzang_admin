<div class="modal-header">Results</div>
<div class="modal-body">
<div class="panel panel-primary">    
    <div class="panel-heading">Winners</div>
    <div class="panel-body" style="max-height: 400px; overflow: auto;">
        <?php if($winners) : ?>
        <table id="show_winners" class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Score</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Breakdown</th>
            </thead>
            <tbody>
                <?php foreach($winners as $winner) : ?>
                <tr>
                    <td><?= $winner->name; ?> (<?= $winner->id; ?>)</td>
                    <td><?= $winner->wins; ?></td>
                    <td>$ <?= number_format(str_replace('$', '', str_replace(",", "", $config->cardWin)) / count($winners), 2); ?></td>
                    <td><?php if($winner->isQuickpick == 1) echo "Manual Pick"; elseif($winner->isQuickpick == 2) echo "Quickpick"; else echo "Unknown"; ?></td>
                    <td>QPs: <?= $winner->qps; ?><br/>Manual: <?= $winner->nonqps; ?><br/>Total: <?= $winner->total; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <h2 style="text-align: center;">No Winners</h2>
        <?php endif; ?>
    </div>
</div>
<?php if(count($winners)) : ?>
<?php foreach($winners as $winner) : ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Winning Card for <?= $winner->name; ?> Card #: <?= $winner->pc_id; ?></div>
    <div class="panel-body">
        <table class="table table-bordered table-condensed">
            <?php foreach($winner->card as $row) : ?>
            <tr <?php if(isset($row['status'])):?>class="danger"><td><b><?= $row['status']; ?></b><?php else : ?>class="success"><td><?php endif;?> <?=$row['answer']; ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<div class="panel panel-primary">    
    <div class="panel-heading">Losers</div>
    <div class="panel-body">
        <?php if($losers) : ?>
        <table id="show_losers" class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Score</th>
                <th>Type</th>
            </thead>
            <tbody>
                <?php foreach($losers as $loser) : ?>
                <tr>
                    <td><?= $loser->name; ?> (<?= $loser->playerId; ?>)</td>
                    <td><?= $loser->wins; ?></td>
                    <td><?php if($loser->isQuickpick == 1) echo "Manual Pick"; elseif($loser->isQuickpick == 2) echo "Quickpick"; else echo "Unknown"; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else : ?>
        <h2>No Winners</h2>
        <?php endif; ?>
    </div>
</div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button></div>