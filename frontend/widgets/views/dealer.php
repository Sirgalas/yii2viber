<?php
/**
 * @var $dealer common\entities\user\User
 */

?>
<div class="col-md 10 col-md-offset-1 dealer_widget">
    <h4>Контакты Вашего дилера</h4>
    <p><?= ($dealer->first_name)?$dealer->first_name:$dealer->username ?> <?= $dealer->surname?$dealer->surname:''; ?> <?=$dealer->family?$dealer->family:''; ?></p>
    <p><?= $dealer->tel; ?></p>
    <p><?= $dealer->email ?></p>
    <p><?= $dealer->time_work; ?></p>
</div>
