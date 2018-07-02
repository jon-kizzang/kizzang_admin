<div id="message"></div>
<div class="well">    
    <label>Start / End Dates:</label>
    <form action="/admin/bingo_games" method="POST">
        <input type="text" name="startDate" id="StartDate" value="<?= $startDate; ?>" style="margin-left: 20px;"/>
        <input type="text" name="endDate" id="EndDate" value="<?= $endDate; ?>" style="margin-left: 20px;"/>
        <button class="btn btn-primary" id="btn_dates" style="margin-left: 20px;">Search Dates</button>
    </form>    
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>ID</th>
            <th>StartTime</th>
            <th>EndTime</th>
            <th># of Balls</th>
            <th>Call Time (Seconds)</th>
            <th>Current Ball</th>
            <th>Status</th>
            <th># of Cards</th>
            <th>Edit</th>            
        </tr>
    </thead>
    <tbody>
            <?php foreach($games as $game) : ?>
        <tr>            
            <td><?= $game->id; ?></td>
            <td><?= date('D, F j g:i:s A', strtotime($game->startTime)); ?></td>
            <td><?= date('D, F j g:i:s A', strtotime($game->endTime)); ?></td>
            <td><?= $game->maxNumber; ?></td>
            <td><?= $game->callTime; ?></td>
            <td><?= $game->currentNum; ?></td>
            <td><?= $game->status; ?></td>
            <td><?= $game->cnt; ?></td>
            <td><a href="/admin/add_bingo_game/<?= $game->id?>" class="btn btn-primary">Edit</a>
                <?php if($game->cnt && strtotime($game->endTime) < strtotime("now")) : ?><a data-toggle="modal" data-target="#modal" href="/admin/view_bingo_cards/<?= $game->id?>" class="btn btn-success">Cards</a><?php endif; ?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50});
                
                $( "#StartDate" ).datepicker({
                    dateFormat: "yy-mm-dd",
                    setDate: "<?= $startDate; ?>", 
                    changeMonth: true,
                    numberOfMonths: 3,
                    maxDate: "<?= $endDate; ?>",
                    onClose: function( selectedDate ) {
                        $( "#EndDate" ).datepicker( "option", "minDate", selectedDate );
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
                    }
                });  
        } );
</script>