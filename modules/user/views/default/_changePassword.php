<?php

use yii\widgets\ActiveForm;


?>
<?php $form = ActiveForm::begin([
    'action' => '/user/default/change-password',
    'method' => 'POST',
    'id' => 'change-password',
    'options' => [
        'class' => 'row justify-content-center'
    ]
]); ?>



<br>
<br>
<div class="row">
    <div class="col-sm-12 ">
        <p style="" align="center">Смена пароля</p>
        <br>

        <?= $form->field($changePasswordForm, 'password')->textInput(['placeholder' => 'Введите новый пароль', 'type' => 'password'])->label(false); ?>
        <?= $form->field($changePasswordForm, 'password_repeat')->textInput(['placeholder' => 'Повторить новый пароль', 'type' => 'password'])->label(false); ?>
        <button class="accent-button button" type="submit">Сохранить</button>
        <br>
        <br>
    </div>
    <?php ActiveForm::end(); ?>
</div>
