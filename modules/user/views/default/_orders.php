<?php
use common\models\Good;
use common\models\GoodPriceList;
use yii\helpers\Url;




?>

<div class="accordion">
    <div class="card-header card-header--name">
        <span class="card-header__col">Номер заказа</span>
        <span class="card-header__col">Статус</span>
        <span class="card-header__col">Способ <br> доставки</span>
        <span class="card-header__col">Адрес доставки</span>
        <span class="card-header__col">Сумма</span>
        <span class="card-header__col">Оплачено баллами</span>

        <span class="card-header__col">&nbsp;</span>
    </div>
    <?php foreach( $orders as $order ) : ?>

        <div class="">
            <div class="card-header" data-toggle="collapse" data-target="#collapse-<?= $order->id ?>" aria-expanded="false" aria-controls=".collapse-<?= $order->id ?>">
                <span class="card-header__col"><?=$order->id; ?></span>
                <span class="card-header__col"><?= $order->getOrderStatuses()[$order->order_status] ?></span>


                <span class="card-header__col"><?= $order->getDeliveryMethods()[$order->delivery_method] ?></span>
                <span class="card-header__col"><?= $order->delivery_method !== 0 ? $order->delivery_address : null ?></span>



                <span class="card-header__col">
                    <span class="rub"><?= $order->price_total ?></span>
                </span>


                <span class="card-header__col">
                    <span class="rub"><?= $order->payment_scores ?></span>
                </span>

                <span class="card-header__col">&#9660;</i></span>
                
                <span class="card-header__col repeat-order">
                    <a href="/cart/repeat-order?order_id=<?=$order->id; ?>">Повторить заказ</a>
                </span>


            </div>
            <div class="collapse" aria-labelledby=".collapse-<?= $order->id ?>" data-parent=".accordion" id="collapse-<?= $order->id ?>">
                <div class="card-body">
                    <?php
                    $iterator = 1;
                    foreach( $order->orderGoods as $orderGood ) : ?>
                            <ul style="list-style: none">
                                <li><?= $iterator; ?></li>

                                <?php if ( $orderGood->good ) : ?>
                                    <li class="li-link"><a href="<?= Url::to(['/good/'.$orderGood->good->slug]); ?>" target="_blank"><?= $orderGood->good->name;?></a></li>
                                 <?php endif; ?>

                                <li> 
                                    <span class="rub"><?= $orderGood->price ?></span>
                                </li>
                                <li><?= $orderGood->quantity; ?> шт</li>
                                <li> 
                                    <span class="rub"><?= $orderGood->price ?></span>

                                </li>
                            </ul>
                    <?php $iterator ++; endforeach; ?>
          

                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<br>
