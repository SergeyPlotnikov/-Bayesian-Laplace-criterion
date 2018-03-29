-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 29 2018 г., 23:49
-- Версия сервера: 5.7.14
-- Версия PHP: 7.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `bayes-laplas`
--

-- --------------------------------------------------------

--
-- Структура таблицы `stuff`
--

CREATE TABLE `stuff` (
  `id` int(11) NOT NULL,
  `thing` varchar(255) NOT NULL,
  `weight` float UNSIGNED NOT NULL,
  `cost` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `stuff`
--

INSERT INTO `stuff` (`id`, `thing`, `weight`, `cost`) VALUES
(1, 'Блайзер', 0.5, 6),
(2, 'Бушлат', 4, 48),
(3, 'Ватні штани', 2, 24),
(4, 'В’єтнамки', 0.5, 6),
(5, 'Джинси', 1, 12),
(6, 'Кепка', 0.5, 6),
(7, 'Кросівки', 1, 12),
(8, 'Куртка', 2, 24),
(9, 'Пальто', 3, 36),
(10, 'Рукавички', 0.5, 6),
(11, 'Светр', 1, 12),
(12, 'Сорочка', 0.5, 6),
(13, 'Футболка', 0.5, 6),
(14, 'Черевики', 1.5, 18),
(15, 'Чоботи', 2, 24),
(16, 'Шапка', 1, 12),
(17, 'Шорти', 0.5, 6);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `stuff`
--
ALTER TABLE `stuff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `stuff`
--
ALTER TABLE `stuff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
