<div class="panel panel-primary">
    <div class="panel-heading">Main DB</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Table Schema</th>
                    <th># of Tables</th>
                    <th>Rows</th>
                    <th>Data Size</th>
                    <th>Index Size</th>
                    <th>Total Size</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($admin as $row) : ?>
                <tr>
                    <td><a href="/admin_reports/db_tables/admin/<?= $row->table_schema; ?>" data-target="#big-modal" data-toggle="modal" ><?= $row->table_schema ? $row->table_schema : "<strong>TOTAL</strong>"; ?></a></td>
                    <td><?= $row->tables; ?></td>
                    <td><?= $row->rows; ?></td>
                    <td><?= $row->data; ?></td>
                    <td><?= $row->idx; ?></td>
                    <td><?= $row->total_size; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Slot DB</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Table Schema</th>
                    <th># of Tables</th>
                    <th>Rows</th>
                    <th>Data Size</th>
                    <th>Index Size</th>
                    <th>Total Size</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($slots as $row) : ?>
                <tr>
                    <td><a href="/admin_reports/db_tables/slots/<?= $row->table_schema; ?>" data-target="#big-modal" data-toggle="modal" ><?= $row->table_schema ? $row->table_schema : "<strong>TOTAL</strong>"; ?></a></td>
                    <td><?= $row->tables; ?></td>
                    <td><?= $row->rows; ?></td>
                    <td><?= $row->data; ?></td>
                    <td><?= $row->idx; ?></td>
                    <td><?= $row->total_size; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Scratchers DB</div>
    <div class="panel-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Table Schema</th>
                    <th># of Tables</th>
                    <th>Rows</th>
                    <th>Data Size</th>
                    <th>Index Size</th>
                    <th>Total Size</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($default as $row) : ?>
                <tr>
                    <td><a href="/admin_reports/db_tables/default/<?= $row->table_schema; ?>" data-target="#big-modal" data-toggle="modal" ><?= $row->table_schema ? $row->table_schema : "<strong>TOTAL</strong>"; ?></a></td>
                    <td><?= $row->tables; ?></td>
                    <td><?= $row->rows; ?></td>
                    <td><?= $row->data; ?></td>
                    <td><?= $row->idx; ?></td>
                    <td><?= $row->total_size; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>