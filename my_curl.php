<?php
// запрос на получение цен по тарифам, с общим таймаутом в 30 секунд. Тарифы должны получатся динамически в оформлении заказа. Что бы пользователь не ждал всю страницу а подождал только тариф. Сделать несколько попыток а именно 3шт. с перерывом в 1-2 секунды.


// авторизация 3 попыткаи с интервалом 1-2 секунды.

// Запрос на оформление заказа(надо проверить, возможно мы уже получили ответ, что заказ создаётся, тогда не надо создавать новый заказ, а надо получить трек номер по уже созданному заказу.), 3 попытки с интервалом в 1-2 секунды. и общим таймаутом 30 сек. Получили статус, что заявка создана

// Если заявка создана, пытаемся получить трек номер заказа 3 попыткаи с интервалом 1-2 секунды. 


// Так же с печатью пдф и накладных. Создаём заявку с интервалом в 1-2с 3 попытки.

//  По сути нужно сделать надстройку над curl для сдэка, где по умолчанию 3 попытки с интервалом 1 сек и таймаутом 30с. Сделать так, что бы можно было изменить кол-во попыток интервал и таймаут


class HttpClient{

    private $headers = [];

    public function __construct(array $headers = []){
        $this->headers = $headers;
    }

    public function request($url, $method = "GET", $data = [], $headers = [], $try_count=3, $interval=1, $timeout=30){
        for($i= 0;$i<$try_count;$i++){
            error_log("try $try_count");

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_REQUEST_TARGET, $url);

            foreach($headers as $k => $v){
                curl_setopt($ch, CURLOPT_HEADER, "$k: $v");
            }

            //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            //curl_setopt($ch,CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $output = curl_exec($ch);

            if(curl_errno($ch)){
                error_log("".curl_error($ch));
            }else{
                if($output){
                    error_log("got response");
                    return $output;
                }else{
                    sleep($interval);
                }
            }
        }
    }
}

//уведомления
interface NotifyService{
    //notify()?
}

class TelegramService implements NotifyService{

}

class WhatsappService implements NotifyService{

}

class DevinoteleService implements NotifyService{

}

//доставка
interface ShippingService{
    //getPrice()
    //delivery()?
}

class CdekSerivce implements ShippingService{

}

interface PaymentService{
    function getLink();
}

class YookassaService implements PaymentService{
    function getLink(){}
}


class DolyamiService implements PaymentService{
    function getLink(){}
}


$client = new HttpClient;

$res = $client->request("www.google.com");

echo $res;die;

