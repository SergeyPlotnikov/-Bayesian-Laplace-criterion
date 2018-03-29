<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 28.03.2018
 * Time: 22:13
 */
//Класс, отвечающий за вещь с набора
class Thing
{
    private $id;
    private $thing;
    private $weight;
    private $cost;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param float $cost
     */
    public function setCost(float $cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return mixed
     */
    public function getThing()
    {
        return $this->thing;
    }
}