<?php

/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 29.03.2018
 * Time: 16:59
 */
//Класс принятия решений
class MakingDecisions
{
    //соединение с бд
    private $pdo;
    //набор доступных вещей
    private $stuff;
    //комплекты вещей
    private $setsOfStuff = [];
    //температура в Урумчи
    private $months = ['Січень' => -13, 'Лютий' => -11, 'Березень' => -1, 'Квітень' => 11, 'Травень' => 18,
        'Червень' => 23, 'Липень' => 25, 'Серпень' => 24, 'Вересень' => 18, 'Жовтень' => 9, 'Листопад' => -2,
        'Грудень' => -10];
    //кол-во месяцов
    private $countOfMonths;
    //стоимость перевозки 1 вещи
    private $freightCostPerOneThing = 10;
    //доп. стоимость покупки одной дополнительной вещи
    private $costOfOneAdditionalThing = 2;

    //таблица, отображающая, что необходимо докупить для каждого набора по каждому месяцу
    private $goodsTable;

    public function __construct()
    {
        $this->pdo = (new DB())->getPdo();
        //получаем набор вещей
        $this->getStuffFromDb();
        //кол-во месяцов
        $this->countOfMonths = count($this->months);
    }

    /**
     * Установка комплектов вещей
     * @param array $setsOfStuff
     */
    public function setSetsOfStuff(array $setsOfStuff)
    {
        $this->setsOfStuff = $setsOfStuff;
    }

    //получение набора вещей с бд
    private function getStuffFromDb():void
    {
        $sql = "SELECT * FROM stuff";
        $stmt = $this->pdo->query($sql);
        $this->stuff = $stmt->fetchAll(PDO::FETCH_CLASS, 'Thing');
        return;
    }

    //Заполняем таблицу вещей, которые не обходимо докупить
    public function fillGoodsTable(array $setsOfStuff)
    {
        /**
         * @param SetOfStuff $set
         */
        foreach ($setsOfStuff as $nameOfSet => $set) {
            $this->goodsTable[$nameOfSet] = [];
            //стоимость перевозки
            $freightCost = $this->freightCostPerOneThing * $set->getWeight();
            $this->goodsTable[$nameOfSet]['freightCost'] = $freightCost;
            //min and max температура ношения даного набора вещей
            $minTemperature = $set->getTemperature()['min'];
            $maxTemperature = $set->getTemperature()['max'];
            //Рассчитаем по каждому месяцу
            foreach ($this->months as $month => $temperature) {
                /*Если температура  ношения нашего набора входит в промежуток температуры данного месяца,
                то значит ничего нам докупать не надо*/
                if (($minTemperature <= $temperature) && ($temperature <= $maxTemperature)) {
                    $this->goodsTable[$nameOfSet]['months'][$month] = null;
                } else {
                    //Находим набор вещей, которые соответствуют температуре текущего месяца
                    $staffForCurrentTemperature = null;
                    foreach ($setsOfStuff as $setOfStuff) {
                        if (($setOfStuff->getTemperature()['min'] <= $temperature) && ($temperature <= $setOfStuff->getTemperature()['max'])) {
                            $staffForCurrentTemperature = $setOfStuff->getStuff();
                            break;
                        }
                    }
                    //Находим недостающие вещи для данной температуры данного месяца
                    $additionalStuff = array_diff_key($staffForCurrentTemperature, $set->getStuff());

                    //Рассчитаем стоимость недостающих элементов
                    $countOfAdditionalStuff = count($additionalStuff);
                    $sum = $countOfAdditionalStuff * $this->costOfOneAdditionalThing;
                    foreach ($additionalStuff as $item) {
                        $sum += $item->getCost();
                    }
                    $this->goodsTable[$nameOfSet]['months'][$month]['stuff'] = $additionalStuff;
                    $this->goodsTable[$nameOfSet]['months'][$month]['cost'] = $sum;
                }
            }
        }
    }

    public function getStuff()
    {
        return $this->stuff;
    }

    /**
     * @return array
     */
    public function getGoodsTable():array
    {
        return $this->goodsTable;
    }

    /**
     * @return array
     */
    public function getMonths(): array
    {
        return $this->months;
    }

    //Вычисляет наилучшую стратегию со всех возможных(лучший набор вещей)
    private function getBestStrategy(array $valuesOfStrategies):array
    {
        $bestStrategyName = '';
        $bestStrategyValue = PHP_INT_MIN;
        foreach ($valuesOfStrategies as $nameOfStrategy => $valueOfStrategy) {
            if ($bestStrategyValue < $valueOfStrategy) {
                $bestStrategyName = $nameOfStrategy;
                $bestStrategyValue = $valueOfStrategy;
            }
        }
        return ['strategyName' => $bestStrategyName, 'strategyValue' => $bestStrategyValue];
    }

    /*
     * Возвращает наилучшую стратегию при возвращении на протяжении 1 с 12-и месяцев, за условием, что вероятность
     * возвращения в каждом месяце одинакова
     */
    public function getStrategyWithEqualProbability():array
    {
        $probability = 1 / $this->countOfMonths;
        //Значения для каждой стратегии
        $valuesOfStrategies = [];
        foreach ($this->goodsTable as $numOfSet => $set) {
            $sum = 0;
            foreach ($set['months'] as $month) {
                if (!is_null($month)) {
                    $sum -= ($month['cost'] * $probability);
                }
            }
            $valuesOfStrategies[$numOfSet] = $sum;
        }
        //найдем лучшую стратегию(лучший набор вещей)
        $bestStrategy = $this->getBestStrategy($valuesOfStrategies);
        return [$valuesOfStrategies, $bestStrategy];
    }


    /*
     * Возвращает наилучшую стратегию при возвращении напротяжении одного сезона(зима-весна-лето-осень)
    */
    public function getStrategyInSeason(array $months):array
    {
        $probability = 1 / 3;
        $valuesOfStrategies = [];
        $availableMonths = $months;
        foreach ($this->goodsTable as $numOfSet => $set) {
            $sum = 0;
            foreach ($set['months'] as $monthName => $month) {
                if (!is_null($month) && in_array($monthName, $availableMonths)) {
                    $sum -= ($month['cost'] * $probability);
                }
            }
            $valuesOfStrategies[$numOfSet] = $sum;
        }
        //найдем лучшую стратегию(лучший набор вещей)
        $bestStrategy = $this->getBestStrategy($valuesOfStrategies);
        return [$valuesOfStrategies, $bestStrategy];
    }

    /*
     * Возвращает наилучшую стратегию при возвращении на протяжении 1 с 12-и месяцев, за условием, что вероятность
     * возвращения зимой в 3 раза выше чем в другие месяца
     */
    public function getStrategyWithGreaterWinterProbability():array
    {
        $winterProbability = 0.166;
        $probability = 0.055;
        $valuesOfStrategies = [];
        $winterMonths = ['Січень', 'Лютий', 'Грудень'];
        foreach ($this->goodsTable as $numOfSet => $set) {
            $sum = 0;
            foreach ($set['months'] as $monthName => $month) {
                if (!is_null($month) && in_array($monthName, $winterMonths)) {
                    $sum -= ($month['cost'] * $winterProbability);
                } elseif (!is_null($month)) {
                    $sum -= ($month['cost'] * $probability);
                }
            }
            $valuesOfStrategies[$numOfSet] = $sum;
        }
        //найдем лучшую стратегию(лучший набор вещей)
        $bestStrategy = $this->getBestStrategy($valuesOfStrategies);
        return [$valuesOfStrategies, $bestStrategy];
    }

    /*
     * Возвращает наилучшую стратегию при возвращении на протяжении одного з 12-ти месяцев при уcловии, что вероятность
     * возвращения зависит от количества дней в месяце(год считаем не высокосным)
    */
    public function getStrategyWithMonthlyProbability():array
    {
        $probabilityFor28Days = 0.077;
        $probabilityFor30Days = 0.082;
        $probabilityFor31Days = 0.085;

        $valuesOfStrategies = [];
        $monthsWith28Days = ['Лютий'];
        $monthsWith30Days = ['Квітень', 'Червень', 'Вересень', 'Листопад'];
        $monthsWith31Days = ['Січень', 'Березень', 'Травень', 'Липень', 'Серпень', 'Жовтень', 'Грудень'];
        foreach ($this->goodsTable as $numOfSet => $set) {
            $sum = 0;
            foreach ($set['months'] as $monthName => $month) {
                if (!is_null($month) && in_array($monthName, $monthsWith28Days)) {
                    $sum -= ($month['cost'] * $probabilityFor28Days);
                } elseif (!is_null($month) && in_array($monthName, $monthsWith30Days)) {
                    $sum -= ($month['cost'] * $probabilityFor30Days);
                } elseif (!is_null($month) && in_array($monthName, $monthsWith31Days)) {
                    $sum -= ($month['cost'] * $probabilityFor31Days);
                }
            }
            $valuesOfStrategies[$numOfSet] = $sum;
        }
        //найдем лучшую стратегию(лучший набор вещей)
        $bestStrategy = $this->getBestStrategy($valuesOfStrategies);
        return [$valuesOfStrategies, $bestStrategy];
    }

    /*
     * Возвращает наилучшую стратегию при возвращении на протяжении одного з 12-ти месяцев при уcловии, что вероятность
     * возвращения в каждом месяце одинакова, а начальная стоимость вещей с номерами № 2, 4, 9, 15, 16 уменьшится втрое
     */
    public function getStrategyWithReducedCost():array
    {

        $itemIds = [2, 4, 9, 15, 16];
        //Уменьшаем стоимость вещей с указаным id
        foreach ($this->stuff as &$item) {
            if (in_array($item->getId(), $itemIds)) {
                $item->setCost($item->getCost() / 3);
            }
        }
        //Заполним таблицу, в которой будет отображено, что необходимо докупить для каждого набора по каждому месяцу
        $this->fillGoodsTable($this->setsOfStuff);

        return $this->getStrategyWithEqualProbability();

    }
}