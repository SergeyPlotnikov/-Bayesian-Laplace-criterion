<?php
/**
 * Created by PhpStorm.
 * User: Serhii
 * Date: 28.03.2018
 * Time: 21:41
 */
declare(strict_types = 1);
include_once 'vendor/autoload.php';

$decision = new MakingDecisions();

//загружаем наборы одежды в виде массива
$sets = include('loadSetOfStuff.php');

//кол-во наборов одежды
$countOfSets = count($sets);
//Получаем массив наборов в виде объектов
$setsOfStuff = [];
$index = 1;
foreach ($sets as $set) {
    $stuffObjectsArray = [];
    for ($i = 0; $i < count($set['stuff']); $i++) {
        for ($j = 0; $j < count($decision->getStuff()); $j++) {
            if ($set['stuff'][$i] == $decision->getStuff()[$j]->getThing()) {
                $stuffObjectsArray[$set['stuff'][$i]] = $decision->getStuff()[$j];
            }
        }
    }
    $setsOfStuff["set{$index}"] = new SetOfStuff($set['temperature'], $stuffObjectsArray, $set['weight']);
    $index++;
};

$decision->setSetsOfStuff($setsOfStuff);
$decision->fillGoodsTable($setsOfStuff);

//Построим таблицу необходимых товаров по каждому месяцу для каждого товара
$tableOfGoods = $decision->getGoodsTable();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">Набір</th>
                    <th scope="col">Вартість</th>
                    <?php
                    foreach ($decision->getMonths() as $month => $temperature):?>
                        <th scope="col"><?= $month ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tableOfGoods as $nameOfSet => $set): ?>
                    <tr>
                        <td><?= $nameOfSet; ?></td>
                        <td><?= $set['freightCost'] ?></td>
                        <?php foreach ($set['months'] as $n => $month): ?>
                            <td>
                                <?php
                                if (is_null($month)) :
                                    echo '---';
                                else :
                                    ?>
                                    <table>
                                        <?php foreach ($month['stuff'] as $name => $item): ?>
                                            <tr>
                                                <td><?= $name ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td><i>Вартість</i>: <b><?= $month['cost'] ?></b></td>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
                повернення
                в кожен з місяців однакова</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом
             *   одного з 12-ти місяців за умови, що ймовірність повернення в кожен з місяців однакова
            */
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyWithEqualProbability();
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного сезону за наданих наборів ймовірностей
                повернення у кожен з місяців. ЗИМА</h5>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного сезону за наданих наборів ймовірностей
             * повернення у кожен з місяців. ЗИМА
             */
            $availableMonths = ['Січень', 'Лютий', 'Грудень'];
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyInSeason($availableMonths);
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного сезону за наданих наборів ймовірностей
                повернення у кожен з місяців. ВЕСНА</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного сезону за наданих наборів ймовірностей
             * повернення у кожен з місяців. Весна
             */
            $availableMonths = ['Березень', 'Квітень', 'Травень'];
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyInSeason($availableMonths);
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного сезону за наданих наборів ймовірностей
                повернення у кожен з місяців. ЛІТО</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного сезону за наданих наборів ймовірностей
             * повернення у кожен з місяців. Літо
             */
            $availableMonths = ['Червень', 'Липень', 'Серпень'];
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyInSeason($availableMonths);
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного сезону за наданих наборів ймовірностей
                повернення у кожен з місяців. ОСІНЬ</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного сезону за наданих наборів ймовірностей
             * повернення у кожен з місяців. Осінь
             */
            $availableMonths = ['Вересень', 'Жовтень', 'Листопад'];
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyInSeason($availableMonths);
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
                повернення взимку втричі більша за інші місяці.</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
             * повернення взимку втричі більша за інші місяці.
             */
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyWithGreaterWinterProbability();
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
                повернення залежить від кількості днів у місяці .</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
             * повернення залежить від кількості днів у місяці (рік вважати не високосним).
             */
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyWithMonthlyProbability();
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h5>Найкраща стратегія при поверненні протягом одного з 12-ти місяців за умови, що ймовірність
                повернення в кожен з місяців однакова, а початкова вартість речей з номерами
                № 2, 4, 9, 15, 16 зменшилася втричі.</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php
            /*
             * Визначити найкращу стратегію при поверненні протягом одного з 12-ти місяців за умови, що ймовірність повернення
             * в кожен з місяців однакова, а початкова вартість речей з номерами № 2, 4, 9, 15, 16 зменшилася втричі.
            */
            list($setOfStrategies, $bestStrategy) = $decision->getStrategyWithReducedCost();
            ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Набір</th>
                    <th>Вартість</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($setOfStrategies as $name => $cost): ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success">Найкраща стратегія: <b><?= $bestStrategy['strategyName'] ?></b></div>
        </div>
    </div>
</div>
</body>
</html>

