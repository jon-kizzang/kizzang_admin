<table class="table table-responsive">
    <thead>
        <tr>
            <th>Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Tax Value</th>
            <th>Winner(s)</th>
            <th># Tickets</th>
            <th># of People</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $config->name; ?></td>
            <td><?= $config->startDate; ?></td>
            <td><?= $config->endDate; ?></td>
            <td><?= $config->taxValue; ?></td>
            <td><?= $config->winner; ?></td>
            <td><?= $config->cnt; ?></td>
            <td><?= $config->people_cnt; ?></td>
        </tr>
    </tbody>
</table>