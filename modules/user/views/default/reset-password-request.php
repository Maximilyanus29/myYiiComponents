<?php

use yii\widgets\ActiveForm;

?>

<h2>Восстановление пароля</h2>
<button type="button" class="close page-modal__close"></button>

<div class="page-modal__content">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'action' => '/user/default/reset-password-request', 'id' => 'reset-password']]); ?>



    <div class="control-wrap">
        <?= $form->field($model, 'email')->textInput(['id' => 'login', 'class' => 'input'])->label('Ваш E-mail', [
            'for' => 'login',
            'class' => 'label',
        ]) ?>
    </div>


    <button type="submit" class="accent-button authorize__submit reset-password_submit">Отправить ссылку на восстановление</button>
    <button type="button" class="accent-button authorize__submit authorize_button login_submit login" >Авторизация</button>

    <button type="button" class="accent-button authorize__submit signup">Регистрация</button>
    <?php ActiveForm::end(); ?>
</div>
