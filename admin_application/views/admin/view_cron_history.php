<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Cron Job History</div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Schedule Date</th>
                    <th>Date Ran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($histories as $history) : ?>
                <tr>
                    <td><?=date("D M j, Y (h:i:s A)", strtotime($history->schedule_date)); ?></td>
                    <td><?=date("D M j, Y (h:i:s A)", strtotime($history->created)); ?></td>
                    <td><?= $history->status; ?></td>
                </tr>
                <?php endforeach; ?>                
            </tbody>
        </table>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="update_payout" data-dismiss="modal" class="btn btn-default">Close</button></div>
</div>