<div class="panel panel-primary" style="margin-bottom: 0px;">    
    <div class="panel-heading">Daily Activity</div>
    <div class="panel-body" style="text-align: center; height: 100%; overflow: auto;">    
        <table class="table table-striped table-bordered table-responsive">
            <tr><th>Time</th><th>Game Type</th><th>Serial Number</th><th>Info</th></tr>
        <?php foreach($data as $row) : ?>
        <?php if($row->game_type == 'Slots') : ?>
            <tr><td><?= date('g:i:s a', strtotime($row->started)); ?></td><td><?= $row->game_type; ?></td><td><?= sprintf("KS%05d", floor($row->foreign_id / 1000000)); ?></td><td>Score: <?= $row->extra; ?></td></tr>
        <?php elseif($row->game_type == 'Scratchers') : ?>
            <tr><td><?= date('g:i:s a', strtotime($row->started)); ?></td><td><?= $row->game_type; ?></td><td><?= $row->extra;  ?></td><td>Card # <?= $row->foreign_id; ?></td></tr>
        <?php elseif($row->game_type == 'Parlay') : ?>
            <tr><td><?= date('g:i:s a', strtotime($row->started)); ?></td><td><?= $row->game_type; ?></td><td>N/A</td><td># of Correct Answers: <?= $row->extra; ?></td></tr>
        <?php elseif($row->game_type == 'Sweepstakes') : ?>
            <tr><td><?= date('g:i:s a', strtotime($row->started)); ?></td><td><?= $row->game_type; ?></td><td><?= sprintf("KW%05d", $row->foreign_id); ?></td><td># of Tickets: <?= $row->extra; ?></td></tr>
        <?php endif ?>
        <?php endforeach; ?>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary close">Close</button></div>
</div>
