<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\User;


?>

    <?php $form = ActiveForm::begin([
        'action' => '/user/default/change-user-data',
        'method' => 'POST',
        'options' => [
            'class' => 'row justify-content-center'
        ]
    ]); ?>


    <div class="col-md-6">
        <?= $form->field( $user, 'name' )->textInput([ 'placeholder' => 'Имя'])->label('Фио') ?>

        <?= $form->field( $user, 'phone' )->textInput([ 'placeholder' => 'Телефон'])->label('Телефон') ?>

        <?= $form->field( $user, 'address' )->textInput(['placeholder' => 'Адрес'])->label('Адрес доставки')?>

        <?= $form->field( $user, 'email' )->textInput(['placeholder' => 'Адрес', 'disabled'=>true])->label('Email')?>

        <p>Для изменения email обратитесь к администратору.</p>
        <br>
        <button class="accent-button button" type="submit">Сохранить</button>
    </div>


<?php ActiveForm::end(); ?>




<script>
    // Маска для поля ввода телефона
    function maskPhone(selector, masked = '+7 (___) ___-__-__') {
        const elems = document.querySelectorAll(selector);

        function mask(event) {
            const keyCode = event.keyCode;
            const template = masked,
                def = template.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, "");
            let i = 0,
                newValue = template.replace(/[_\d]/g, a => (i < val.length ? val.charAt(i++) || def.charAt(i) : a));
            i = newValue.indexOf("_");
            if (i != -1) {
                newValue = newValue.slice(0, i);
            }
            let reg = template.substr(0, this.value.length).replace(/_+/g,
                a => "\\d{1," + a.length + "}").replace(/[+()]/g, "\\$&");
            reg = new RegExp("^" + reg + "$");
            if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) {
                this.value = newValue;
            }
            if (event.type == "blur" && this.value.length < 5) {
                this.value = "";
            }

        }

        for (const elem of elems) {
            elem.addEventListener("input", mask);
            elem.addEventListener("focus", mask);
            elem.addEventListener("blur", mask);
        }
    }
    maskPhone('#user-phone');
</script>

