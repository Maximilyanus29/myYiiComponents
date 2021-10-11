<?php

namespace frontend\components\cart;

use common\models\Good;

class CartItem
{

    /**
     * @var integer $id
     */
    private $id;
    /**
     * @var integer $quantity
     */
    private $quantity;
    /**
     * @var integer|float $price
     */
    private $price;
    /**
     * @var integer|float $old_price
     */
    private $old_price;
    /**
     * @var  $param
     */
    private $param;

    public function __construct($id, $quantity, $param)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->param = $param;

        $good = Good::find()->where(['good.id' => $id])->joinWith('goodParams')->limit(1)->one();

        $this->setPrices(
            $good->getPrice($param),
            $good->getOldPrice($param)
        );

    }


    public function setPrices($price, $old_price): void
    {
        $this->price = $price;
        $this->old_price = $old_price;
    }

    /**
     * Returns the id of the item
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the price of the item
     * @return integer|float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Returns the price of the item
     * @return integer|float
     */
    public function getOldPrice()
    {
        return $this->old_price;
    }

    /**
     * Returns the price of the item
     * @return integer|null
     */
    public function getParam(): ?int
    {
        return $this->param;
    }

    /**
     * Returns the cost of the item
     * @param bool $withDiscount
     * @return integer|float
     */
    public function getCost($withDiscount = false)
    {
        if ($withDiscount === true){
            return ceil($this->getPrice() * $this->quantity);
        }

        return ceil($this->getOldPrice() * $this->quantity);
    }

    /**
     * Returns the quantity of the item
     * @return integer
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity of the item
     * @param integer $quantity
     * @return void
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }
}
