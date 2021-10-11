<?php

use frontend\modules\user\assets\MainAsset;

MainAsset::register($this);

$this->title = "Личный кабинет | Space Love";

?>

<style>

    .tab-content{
        position: relative;
        z-index: 2;
    }
</style>


<div class="content content--inner">
    <div class="inner-content">

        <div class="inner-col">
            <section class="content-section content-section--inner">
                <div class="container">
                    <h1 class="g-title mb">Личный кабинет</h1>
                    <nav class="lc-nav">
                        <div class="nav nav-tabs" role="tablist">
                            <a class="nav-item tab-lk nav-link <?= $currentTab == 'one' ? 'active' : '' ?>" data-toggle="tab" href="#one" role="tab" aria-controls="one" aria-selected="true">Список заказов</a>
                            <a class="nav-item tab-lk nav-link <?= $currentTab == 'two' ? 'active' : '' ?>" data-toggle="tab" href="#two" role="tab" aria-controls="two" aria-selected="false">Личные данные</a>
                            <a class="nav-item tab-lk nav-link <?= $currentTab == 'three' ? 'active' : '' ?>" data-toggle="tab" href="#three" role="tab" aria-controls="three" aria-selected="false">Смена пароля</a>
                            <a class="nav-item tab-lk nav-link <?= $currentTab == 'four' ? 'active' : '' ?>" data-toggle="tab" href="#four" role="tab" aria-controls="three" aria-selected="false">Love-бонусы (<?= Yii::$app->user->identity->score ?>)</a>
                        </div>
                    </nav>
                    <div class="tab-content">
                        <!-- Список заказов -->
                        <div class="tab-pane fade <?= $currentTab == 'one' ? 'show active' : '' ?>" id="one" role="tabpanel">
                            <?= $this->render('_orders', compact('orders')); ?>
                        </div>

                        <!-- Данные пользователя -->
                        <div class="tab-pane fade <?= $currentTab == 'two' ? 'show active' : '' ?>" id="two" role="tabpanel">
                            <?= $this->render('_userData', compact('user', 'user')); ?>
                        </div>

                        <!-- Изменение  пароля -->
                        <div class="tab-pane fade <?= $currentTab == 'three' ? 'show active' : '' ?>" id="three" role="tabpanel">
                            <?= $this->render('_changePassword', compact('changePasswordForm')); ?>
                        </div>

                        <!-- Логи баллов -->
                        <div class="tab-pane fade <?= $currentTab == 'three' ? 'show active' : '' ?>" id="four" role="tabpanel">
                            <?= $this->render('_score_log', compact('scores')); ?>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<br>