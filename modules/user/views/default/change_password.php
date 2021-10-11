<?php

use yii\widgets\ActiveForm;


?>
<?php $form = ActiveForm::begin([
    'action' => '/user/default/reset-password',
    'method' => 'POST',
//    'id' => 'change-password',
    'options' => [
        'class' => 'row justify-content-center'
    ]
]); ?>



<div class="change-password">
        <h1>Смена пароля</h1>
        <div class="control-wrap change-password__item">
            <?= $form->field($model, 'password')->textInput(['id' => 'password', 'class' => 'input'])->label('Ваш пароль', [
                'for' => 'password',
                'class' => 'label',
            ]) ?>
        </div>

        <div class="control-wrap change-password__item">
            <?= $form->field($model, 'password_repeat')->textInput(['id' => 'password_repeat', 'class' => 'input'])->label('Повторите пароль', [
                'for' => 'password_repeat',
                'class' => 'label',
            ]) ?>
        </div>
        <button type="submit" class="accent-button   login_submit">Сохранить</button>
    <?php ActiveForm::end(); ?>
</div>
