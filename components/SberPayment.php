<?php

use yii\httpclient\Client;


namespace frontend\components;


use yii\httpclient\Client;

class SberPayment
{

    const USER_NAME = "T362201701422-api";
    const USER_PASS = "okirf0408RU";

    const ACTION_SUCCESS ="/cart/change-status-order";
    const ACTION_FAIL =   "/cart/fail";


    public static function createPayment($cost , $order_id)
    {


        $client = new Client();

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://securepayments.sberbank.ru/payment/rest/register.do')
//            ->setUrl('https://3dsec.sberbank.ru/payment/rest/register.do')
            ->setData([
                'userName'=> self::USER_NAME,
                'password'=> self::USER_PASS,
                'orderNumber'=> $order_id,
                'amount'=> $cost*100,
                'returnUrl'=> $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_HOST'] . '/' . self::ACTION_SUCCESS,
                'failUrl'=> $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_HOST'] . '/' . self::ACTION_FAIL,
            ])->send();
        

        if ($response->isOk) {
            return [
                'orderId' => $response->data['orderId'],
                'formUrl' => $response->data['formUrl']
            ];
        }else{
            return[
                'error'=> "status is not OK"
            ];
        }
    }



    /**
     * Проверяет, оплачен ли заказ
     * @param $paymentId int
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public static function isPayed($paymentId)
    {
        $client = new HClient();

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('https://web.rbsuat.com/ab/rest/getOrderStatus.do')
            ->setData([
                'userName' => self::$login,
                'password' => self::$password,
                'orderId' => $paymentId,
            ])->send();

        if ($response && isset($response->data['OrderStatus'])) {
            $status = $response->data['OrderStatus'];
            if ($status === self::PAYED) {
                return true;
            }
        }

        return false;
    }


    /*
    $client = new HClient();

    $response = $client->createRequest()
    ->setMethod('POST')
    ->setUrl('https://web.rbsuat.com/ab/rest/register.do')
    ->setData([
    'userName' => self::$login,
    'password' => self::$password,
    'orderNumber' => $orderId,
    'amount' => $price * 100,
    'returnUrl' => 'http://new.evkka.ru/order/success?id=' . $orderId,
    'failUrl' => 'http://new.evkka.ru/order/fail',
    ])->send();

    return [
    'orderId' => $response->data['orderId'],
    'formUrl' => $response->data['formUrl']
    ];*/



}