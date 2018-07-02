<table class="table table-responsive">
    <thead>
        <tr>
            <th>PlayerID</th>
            <th>Login Type</th>
            <th>Login Source</th>
            <th>Mobile Type</th>
            <th>App ID</th>
            <th>Campaign</th>            
        </tr>
    </thead>
    <tbody>
        <?php foreach($accounts as $account) : ?>
        <tr>
            <td><?= $account->playerId; ?></td>
            <td><?= $account->loginType; ?></td>
            <td><?= $account->loginSource; ?></td>
            <td><?= $account->mobileType; ?></td>
            <td><?= $account->appId; ?></td>
            <td><?= $account->email_campaign_id; ?></td>            
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>