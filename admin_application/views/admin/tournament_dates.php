<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Calculated Tournament Dates</div>
    <div class="panel-body">
        <div style="height: 200px; overflow: auto;">
            <h5>Below are the good <?= count($good); ?> date(s).</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($good as $row) : ?>
                    <tr>
                        <td><?= date("D M d, Y H:i A", strtotime($row['start_date'])); ?></td>
                        <td><?= date("D M d, Y H:i A", strtotime($row['end_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="height: 200px; overflow: auto;">
            <h5>Below are the bad <?= count($bad); ?> date(s) that overlap current tournaments.</h5>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bad as $row) : ?>
                    <tr>
                        <td><?= date("D M d, Y H:i A", strtotime($row['start_date'])); ?></td>
                        <td><?= date("D M d, Y H:i A", strtotime($row['end_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer" style="text-align: right;"><button type="button" id="add_dates" class="btn btn-primary">Add Dates</button></div>
</div>