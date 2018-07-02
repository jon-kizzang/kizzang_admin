<div class="panel panel-primary">
    <div class="panel-heading">Slots</div>
    <div class="panel-body">
        <table id="show_slots" class="table table-striped">
            <thead>
                <tr>            
                    <th>Name</th>
                    <th>Terms of Service</th>
                    <th>Privacy Policy</th>
                    <th>Participation Rules</th>                               
                    <th>Edit</th>            
                </tr>
            </thead>
            <tbody>
               <?php foreach($slots as $slot) : ?>
                <tr>            
                    <td><?= $slot->Name; ?></td>
                    <td><?php if($slot->rules) : ?><a target="_blank" href="<?= $slot->rules->TermsOfService; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($slot->rules) : ?><a target="_blank" href="<?= $slot->rules->PrivacyPolicy; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($slot->rules) : ?><a target="_blank" href="<?= $slot->rules->ParticipationRules; ?>">View</a><?php else : ?>None<?php endif; ?></td>           
                    <td><a data-toggle="modal" style="margin-right: 15px;" href="/admin/add_rule/3/<?= $slot->ID?>" data-target="#modal" class="btn btn-primary">Edit</a></td>
                </tr>
               <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-primary">
    <div class="panel-heading">Scratch Cards</div>
    <div class="panel-body">
        <table id="show_scratch_cards" class="table table-striped">
            <thead>
                <tr>            
                    <th>Name</th>
                    <th>Terms of Service</th>
                    <th>Privacy Policy</th>
                    <th>Participation Rules</th>                               
                    <th>Edit</th>            
                </tr>
            </thead>
            <tbody>
               <?php foreach($scratch_cards as $scratch_card) : ?>
                <tr>            
                    <td><?= $scratch_card->Name; ?></td>
                    <td><?php if($scratch_card->rules) : ?><a target="_blank" href="<?= $scratch_card->rules->TermsOfService; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($scratch_card->rules) : ?><a target="_blank" href="<?= $scratch_card->rules->PrivacyPolicy; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($scratch_card->rules) : ?><a target="_blank" href="<?= $scratch_card->rules->ParticipationRules; ?>">View</a><?php else : ?>None<?php endif; ?></td>          
                    <td><a data-toggle="modal" style="margin-right: 15px;" href="/admin/add_rule/1/<?= $slot->ID?>" data-target="#modal" class="btn btn-primary">Edit</a></td>
                </tr>
               <?php endforeach; ?>
            </tbody>
        </table>
    </div>    
</div>

<div class="panel panel-primary" style="margin-bottom: 0px;">
    <div class="panel-heading">Sweepstakes</div>
    <div class="panel-body">
        <table id="show_sweepstakes" class="table table-striped">
            <thead>
                <tr>            
                    <th>Name</th>
                    <th>Terms of Service</th>
                    <th>Privacy Policy</th>
                    <th>Participation Rules</th>                               
                    <th>Edit</th>            
                </tr>
            </thead>
            <tbody>
               <?php foreach($sweepstakes as $sweepstake) : ?>
                <tr>            
                    <td><?= $sweepstake->Name; ?></td>
                    <td><?php if($sweepstake->rules) : ?><a target="_blank" href="<?= $sweepstake->rules->TermsOfService; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($sweepstake->rules) : ?><a target="_blank" href="<?= $sweepstake->rules->PrivacyPolicy; ?>">View</a><?php else : ?>None<?php endif; ?></td>
                    <td><?php if($sweepstake->rules) : ?><a target="_blank" href="<?= $sweepstake->rules->ParticipationRules; ?>">View</a><?php else : ?>None<?php endif; ?></td>          
                    <td><a data-toggle="modal" style="margin-right: 15px;" href="/admin/add_rule/2/<?= $slot->ID?>" data-target="#modal" class="btn btn-primary">Edit</a></td>
                </tr>
               <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>