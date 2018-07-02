<div class="modal-header">Tables in <?= $schema; ?></div>
<div class="modal-body">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Table Name</th>
                <th>Rows</th>
                <th>Size</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($rows as $row) : ?>
            <tr>
                <td><?= $row->TABLE_NAME ? $row->TABLE_NAME : "<strong>TOTAL</strong>"; ?></td>
                <td><?= $row->table_rows; ?></td>
                <td><?= $row->size; ?> MB</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal-footer"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary">Close</button></div>