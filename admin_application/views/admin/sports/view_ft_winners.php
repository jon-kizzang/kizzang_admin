<div class="modal-header">Results</div>
<div class="modal-body">
<div class="panel panel-primary">    
    <div class="panel-heading">Top 100</div>
    <div class="panel-body">
        <table id="show_winners" class="table table-striped">
            <thead>
                <th>Name</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Date</th>
            </thead>
            <tbody>
                <?php foreach($lines as $line) : ?>                
                <tr>                    
                    <td><?= $line->name ?></td>
                    <td><?= $line->wins ?></td>
                    <td><?= $line->losses ?></td>
                    <td><?= date("D F j, Y H:i:s", $line->dateTime) ?></td>
                </tr>                
                <?php endforeach; ?>
            </tbody>
        </table>        
    </div>
</div>
</div>
<div class="modal-footer"><button class="btn btn-default" data-dismiss="modal" type="button">Close</button></div>