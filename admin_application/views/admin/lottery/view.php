
<div class="well">    
    <label>Start / End Dates:</label>
    <form action="/lottery/view" method="POST">
        <input type="text" name="startDate" id="StartDate" value="<?= $startDate; ?>" style="margin-left: 20px;"/>
        <input type="text" name="endDate" id="EndDate" value="<?= $endDate; ?>" style="margin-left: 20px;"/>
        <button class="btn btn-primary" id="btn_dates" style="margin-left: 20px;">Search Dates</button>
    </form>    
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>       
            <th>Id</th>
            <th>Total Balls</th>
            <th># of #s to Win</th>
            <th>Type</th>
            <th>Start Date</th>
            <th>End Date</th>             
            <th>Edit</th>             
        </tr>
    </thead>
    <tbody>
            <?php foreach($configs as $config) : ?>
        <tr>       
            <td><?= $config->id?></td>
            <td><?= $config->numTotalBalls; ?></td>
            <td><?= $config->numAnswerBalls; ?></td>
            <td><?= $config->numCards . " " . $config->cardLimit; ?></td>
            <td><?= $config->startDate; ?></td>
            <td><?= $config->endDate; ?></td>
            <td><a href="/lottery/add/<?= $config->id?>" class="btn btn-primary">Edit</a></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 1, "asc" ]]});
                
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