<script src="/js/tableTools.js"></script>
<link href="/css/tableTools.css" rel="stylesheet">
<div class="well">
    <label>Select Number of Records: </label>
    <select id="num_recs">
        <?php foreach($counts as $count) : ?>
        <option value="<?= $count; ?>" <?php if($count == $num_recs) echo "selected=''"; ?>><?= $count; ?></option>
        <?php endforeach; ?>
    </select>
    <br/><br/>
    <label>Select Order:</label><br/>
    <input type="radio" class="order-by" name="order_by" value="game_total" <?php if($order_by == "game_total") : ?>checked=""<?php endif; ?>/> Game Total<br/>
    <input type="radio" class="order-by" name="order_by" value="slot_total" <?php if($order_by == "slot_total") : ?>checked=""<?php endif; ?>/> Slot Total<br/>
    <input type="radio" class="order-by" name="order_by" value="sport_total" <?php if($order_by == "sport_total") : ?>checked=""<?php endif; ?>/> Sport Total<br/>
    <input type="radio" class="order-by" name="order_by" value="scratcher_total" <?php if($order_by == "scratcher_total") : ?>checked=""<?php endif; ?>/> Scratcher Total<br/>
    <input type="radio" class="order-by" name="order_by" value="amount" <?php if($order_by == "amount") : ?>checked=""<?php endif; ?>/> Winnings
</div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>ID</th>
            <th>Screen Name</th>
            <th>Gender</th>
            <th>Total Games</th>            
            <th>Total Slots</th>    
            <th>Total Scratcher</th>
            <th>Total Sports</th>
            <th>Last Login</th>
            <th>Amount Won</th>            
            <th>Avg. TOS</th>
            <th>Days</th>
        </tr>
    </thead>
    <tbody>
            <?php foreach($players as $key => $player) : ?>
        <tr>
            <td><?= $key + 1; ?></td>
            <td><a href="/admin/edit_player/<?= $player->playerId; ?>"><?= $player->screenName; ?></a></td>
            <td><?= $player->gender; ?></td>
            <td><?= $player->total_count; ?></td>
            <td><?= $player->slot_count; ?></td>
            <td><?= $player->scratcher_count; ?></td>
            <td><?= $player->sport_count; ?></td>
            <td><?= $player->lastLogin; ?></td>
            <td>$<?= $player->amount; ?></td>            
            <td><?= $player->tos; ?></td>
            <td><?= $player->days; ?></td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>

<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({
                    pageLength: <?= $num_recs; ?>, 
                    order: [[ 0, "asc" ]],
                    dom: 'T<"clear">lfrtip',
                    tableTools: {
                        sSwfPath: "/swf/copy_csv_xls_pdf.swf"
                }});
                
                $("#num_recs").change(function(){
                    var order_by = $('input[name=order_by]:checked').val();
                    location.href = "/admin_reports/top_ten/" + $(this).val() + "/" + order_by;
                });
                
                $(".order-by").click(function(){
                    var count = $("#num_recs").val();
                    var order_by = $('input[name=order_by]:checked').val();
                    location.href = "/admin_reports/top_ten/" + count + "/" + order_by;
                })
        });
</script>