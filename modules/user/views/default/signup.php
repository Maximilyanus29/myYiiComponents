<?php

use yii\widgets\ActiveForm;

?>

<h2>Регистрация</h2>
<button type="button" class="close page-modal__close"></button>

<div class="page-modal__content">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'action' => '/user/default/signup', 'id' => 'signup']]); ?>

    <div class="control-wrap">
        <?= $form->field($model, 'name')->textInput(['id' => 'name', 'class' => 'input'])->label('Ваше Имя', [
            'for' => 'name',
            'class' => 'label',
        ]) ?>
    </div>


    <div class="control-wrap">
        <?= $form->field($model, 'email')->textInput(['id' => 'login', 'class' => 'input'])->label('Ваш E-mail', [
            'for' => 'login',
            'class' => 'label',
        ]) ?>
    </div>

    <div class="control-wrap">
        <?= $form->field($model, 'password')->textInput(['id' => 'password', 'class' => 'input'])->label('Ваш пароль', [
            'for' => 'password',
            'class' => 'label',
        ]) ?>
    </div>

    <button type="submit" class="accent-button authorize__submit authorize_button signup_submit">Зарегестрироватся</button>
<!--    <button type="button" class="accent-button authorize__submit lk">Авторизация</button>-->
    <button type="button" class="accent-button authorize__submit authorize_button login_submit login" >Авторизация</button>

    <button type="button" class="accent-button authorize__submit reset-password">Восстановить пароль</button>
    <?php ActiveForm::end(); ?>
</div>
