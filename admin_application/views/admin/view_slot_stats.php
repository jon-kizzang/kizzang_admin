<div class="modal-header">Tournament Stats</div>
<div class="modal-body">
    
<div class="panel panel-primary">    
    <div class="panel-heading">Top 10</div>
    <div class="panel-body">
        <table id="show_games" class="table table-striped">
        <thead>
            <tr>            
                <th>Place</th>
                <th>Name</th>
                <th>Spins Left</th>
                <th>Score</th>     
                <th>Game</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ranks as $index => $rank) : ?>
            <tr>            
                <td><?= $index + 1; ?></td>
                <td><?= $rank->player_name; ?></td>
                <td><?= $rank->SpinsLeft; ?></td>
                <td><?= number_format($rank->WinTotal, 0); ?></td>       
                <td><?= $rank->Name; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="panel-footer" style="text-align: right;"></div>
</div>
    
<div class="panel panel-primary" style="height: 300px; overflow: auto;">    
    <div class="panel-heading">Player Aggregate Scores</div>
    <div class="panel-body">
        <table id="show_games" class="table table-striped">
        <thead>
            <tr>            
                <th>Place</th>
                <th>Name</th>
                <th>Score</th>   
                <th># of Games</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($top_three as $index => $rank) : ?>
            <tr>            
                <td><?= $index + 1; ?></td>
                <td><?= $rank->player_name; ?></td>
                <td><?= number_format($rank->WinTotal, 0); ?></td>   
                <td><?= $rank->num_games; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>    
</div>

<div class="panel panel-primary">    
    <div class="panel-heading">Slots</div>
    <div class="panel-body">
        <table id="show_games" class="table table-striped">
        <thead>
            <tr>            
                <th>Name</th>
                <th>Min Win</th>
                <th>Max Win</th>
                <th># of Player</th>
                <th>Completed Games</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($games as $game) : ?>
            <tr>            
                <td><?= $game->Name; ?></td>
                <td><?= number_format($game->min_total, 0); ?></td>
                <td><?= number_format($game->max_total, 0); ?></td>
                <td><?= $game->num_players; ?></td>
                <td><?= $game->num_games; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>    
</div>
    
</div>
<div class="modal-footer"><button data-dismiss="modal" type="button" id="update_prize" class="btn btn-primary">Close</button></div>