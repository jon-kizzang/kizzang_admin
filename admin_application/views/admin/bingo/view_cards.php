<style>
    .tbl_bingo {
        width: 300px;
        height: 300px;
        margin: 0 auto;
    }
    
    .tbl_bingo td{
        width: 60px;
        height: 60px;
        text-align: center;
        border: 1px solid #000;
    }
    
    .unmarked {
        background-color: #DDDDDD;
    }
    
    .marked {
        background-color: #00FF00;
    }
    
    .bingo-player {
        width: 300px;
        height: 40px;
        margin: 10px auto 0px;
        text-align: center;
        border: 1px solid #000;
        font-size: 20px;
        padding-top: 5px;
    }
    
    .ctr-left {
        height: 60px;
        text-align: left;
        padding-left: 5px;
        float: left;
        width: 29px;
        padding-top: 20px;
        border-right: 1px solid #000;
    }
    
    .ctr-right {
        height: 60px;
        text-align: right;
        padding-right: 5px;
        float: right;
        width: 29px;
        padding-top: 20px;
    }
</style>

<div class="panel panel-primary">    
    <div class="panel-heading">Top 10 Bingo Cards</div>
    <div class="panel-body">
        <?php foreach($views as $view) : ?>
        <div class="bingo-player"><?= $view['player']->firstName . " " . $view['player']->lastName . " (" . $view['player']->playerId . ")"; ?></div>
        <table class="tbl_bingo">
            <?php foreach($view['card'] as $line) : ?>
            <?= $line . "\n"; ?>
            <?php endforeach; ?>
        </table>
        <?php endforeach; ?> 
    </div>    
</div>
