<table class="table table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Diff</th>
                    <th>Date</th>
                    <th>Over/Under</th>
                    <th>Spread</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($events as $event) : ?>
                <tr id="new_event_<?= $event->event_id; ?>">
                    <td><?= $event->category; ?></td>
                    <td><?= $event->team1; ?> (<?= $event->pr1; ?>)</td>
                    <td><?= $event->team2; ?> (<?= $event->pr2; ?>)</td>
                    <td><?= $event->diff; ?></td>
                    <td><?= $event->date; ?></td>
                    <td><input type="text" style="width: 50px;" class="form-control" id="ou_<?= $event->event_id; ?>"/></td>
                    <td><input type="text" style="width: 50px;" class="form-control" id="spread_<?= $event->event_id; ?>"/></td>
                    <td><button class="btn btn-default" onClick="add_parlay_event(<?= $event->event_id; ?>);">Add</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>