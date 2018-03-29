<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 28.03.2018
 * Time: 22:32
 */
class SetOfStuff
{
    private $weight;
    private $stuff = [];
    private $temperature = [];

    public function __construct(array $temperature, array $stuff, float $weight)
    {
        $this->temperature = $temperature;
        $this->stuff = $stuff;
        $this->weight = $weight;
    }

    /**
     * @return array
     */
    public function getStuff(): array
    {
        return $this->stuff;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return array
     */
    public function getTemperature(): array
    {
        return $this->temperature;
    }
}