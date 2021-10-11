<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'reg-form',
    'action' => '/user/default/signup',
]);

?>


<div class="form-group highlight-addon field-loginform-username required has-success">

    <?= $form->field($model, 'email')->textInput() ?>

    <div class="help-block invalid-feedback"></div>

</div>        <div class="form-group highlight-addon field-loginform-password required has-success">


    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

    <div class="help-block invalid-feedback"></div>

</div>
<div class="text-center">
    <?= Html::submitButton('Зарегестрироватся', ['class' => 'button button_mid']) ?>
</div>
<p>Нажимая кнопку “Войти” Вы даете свое согласие на <a href="/polytic" target="_blank">обработку персональных данных.</a></p>

<br>
<div class="text-center">
    <button type="button" class="g-btn g-btn--gray" data-target=".req-pasw">Восстановить пароль</button>
</div>
<br>


<?php ActiveForm::end(); ?>


