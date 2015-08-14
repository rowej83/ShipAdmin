<?php

/**
 * Created by PhpStorm.
 * User: jrowe
 * Date: 10/19/2014
 * Time: 6:34 PM
 */
class Item extends Eloquent
{
    protected $table = 'items';

    /**
     * @param $quantity
     * @return float
     */
    public function getSpaceCount($quantity)
    {
        if ($this->size == 'T') {
            return ($quantity / $this->case) / $this->cartonsperpallet;
        } else {
            return (($quantity / $this->case) / $this->cartonsperpallet) / 2;
        }
    }

    /**
     * @param $quantity
     * @param int $ppp
     * @return float
     */
    public function getSpaceCountPerBased($quantity,$ppp=35)
    {
        if ($this->size == 'T') {
            return $this->getCartonCount($quantity) /$ppp;
        } else {
            return ($this->getCartonCount($quantity)/$ppp) / 2;
        }
    }

    /**
     * @param $quantity
     * @return mixed
     */
    public function getWeight($quantity)
    {
        return $this->weight * ($quantity / $this->case);

    }

    /**
     * @param $quantity
     * @return float
     */
    public function getPalletCount($quantity)
    {
        return $this->getCartonCount($quantity) / $this->cartonsperpallet;
    }


//getpalletcountbased allows for user to input the amount of pallets per pallet ($ppp) .. usually 35ish
//$total in method below is total amount of cartons of the shipment result
    /**
     * @param $total
     * @param int $ppp
     * @return float
     */
    public function getPalletCountTotalBased($total, $ppp = 35)
    {
        return $total / $ppp;
    }


    //$quantity
    /**
     * @param $quantity
     * @param int $ppp
     * @return float
     */
    public function getPalletCountPerBased($quantity, $ppp = 35)
    {
        return $this->getCartonCount($quantity) / $ppp;
    }

    /**
     * @param $quantity
     * @return float
     */
    public function getCartonCount($quantity)
    {
        return $quantity / $this->case;
    }
}