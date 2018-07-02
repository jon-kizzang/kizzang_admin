<div class="well-lg">
    <form id="frmDates" method="POST" class="form-inline">
        <label>Start Date:</label><input type="text" id="StartDate" style="width: 200px;" class="form-control" name="startDate" value="<?= $startDate; ?>"/>
        <label style="margin-left: 20px;">End Date:</label><input type="text" id="EndDate" style="width: 200px;" class="form-control" name="endDate" value="<?= $endDate; ?>"/>
        <button style="margin-left: 20px;" class="btn btn-success">Submit</button>
    </form>
</div>
<?php foreach($slots as $key => $slotTournaments) : ?>
<?php foreach($slotTournaments as $slot) : ?>
<h3>Slot Stats <?= $key; ?> (<?= date("d/m/Y", strtotime($slot->startDate)); ?> - <?= date("d/m/Y", strtotime($slot->endDate)); ?>)</h3>
<table class="table table-bordered table-striped">
     <thead>
            <tr>            
                <th>Name</th>
                <th>Min Win</th>
                <th>Max Win</th>
                <th>Unique Players</th>
                <th>Completed Games</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($slot->games as $game) : ?>
            <tr>            
                <td><?php if($game->ID) : ?> <?=$game->Name; ?> <?php else : ?> <strong>TOTAL</strong><?php endif; ?></td>
                <td><?= number_format($game->min_total, 0); ?></td>
                <td><?= number_format($game->max_total, 0); ?></td>
                <td><?= $game->num_players; ?></td>
                <td><?= $game->num_games; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
</table>
<?php endforeach; ?>
<?php endforeach;?>
<script>
$(document).ready(function() {
    $( "#StartDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $startDate; ?>", 
            changeMonth: true,
            numberOfMonths: 3,
            maxDate: "<?= $endDate; ?>",
            onClose: function( selectedDate ) {
                $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
                getData();
            }
        });

        $( "#EndDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            setDate: "<?= $endDate; ?>",
            changeMonth: true,
            numberOfMonths: 3,
            minDate: "<?= $startDate; ?>",
            onClose: function( selectedDate ) {
                $( "#StartDate" ).datepicker( "option", "maxDate", selectedDate );
                getData();
            }
        });          
});
</script>