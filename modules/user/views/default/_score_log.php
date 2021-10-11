<?php

use yii\widgets\ActiveForm;


?>

<div class="col-sm-12">


    <div class="tab-content">
        <!-- Список заказов -->
        <div class="tab-pane fade active show" id="one" role="tabpanel">

            <div class="accordion">
                <div class="card-header card-header--name">
                    <span class="card-header__col">Действие</span>
                    <span class="card-header__col">Дата</span>
                    <span class="card-header__col">Сумма баллов</span>

                </div>

                <?php foreach ($scores as $log): ?>


                    <div class="">

                        <div class="card-header">
                            <span class="card-header__col"><?= $log->action ?></span>
                            <span class="card-header__col"><?= date('d.m.Y', $log->created_at) ?></span>


                            <span class="card-header__col">
                            <span><?= $log->quantity ?></span>
                        </span>


                        </div>


                    </div>


                <?php endforeach; ?>



            </div>

            <br>
        </div>
    </div>


</div>