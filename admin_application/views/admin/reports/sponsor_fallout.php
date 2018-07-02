<style>
#mask {
            opacity: .3;
            background-color: #000;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            position: fixed;
            z-index: 1000;
    }
    
    #mask_message {
            z-index: 1001;
            height: 100px;
            width: 200px;
            background-color: #FFF;
            position: fixed;
            display: none;
            padding: 20px;
            text-align: center;
    }
    </style>
    
<div class="well">
    <label>Sponsors: </label>
    <select id="sel_campaign">
        <option value="">Select Sponsor</option>
        <?php foreach($sponsors as $sponsor) : ?>
        <option value="<?= $sponsor->name; ?>" <?php if($sponsor->name == $sponsorId) echo 'Selected=""'; ?>><?= $sponsor->name; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div id="message"></div>
<table id="show_games" class="table table-striped">
    <thead>
        <tr>            
            <th>Date</th>
            <th>Medium</th>
            <th>Impressions</th>
            <th>Conversion</th>
            <th>Conversion %</th>       
        </tr>
    </thead>
    <tbody>
            <?php foreach($recs as $rec) : ?>
        <tr>            
            <td><?php if($rec->agg_date) echo $rec->agg_date; else echo "<b>Total</b>";?></td>
            <td><?php if($rec->device_type) echo $rec->device_type; else echo "<b>Total</b>" ?></td>
            <td><?= number_format($rec->impression_count, 0); ?></td>
            <td><?= number_format($rec->conversion_count, 0); ?></td>
            <td><?= number_format(($rec->conversion_count / $rec->impression_count) * 100, 2) ?> %</td>
        </tr>
            <?php endforeach; ?>
    </tbody>
</table>
<div id="mask"></div>
<div id="mask_message">Getting Information</div>
<script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
                $('#show_games').dataTable({pageLength: 50, order: [[ 0, "asc" ]]});
                
                $("#sel_campaign").change(function(){
                    $("#mask").show();
                    $("#mask_message").html("Getting Information... Please wait.");
                    $("#mask_message").css({left: window.innerWidth / 2 - 100, top: window.innerHeight / 2 -50}).show();
                    window.location = "/admin_reports/sponsor_fallout/" + $(this).val();
                });
        } );
</script>