-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Дек 11 2023 г., 09:01
-- Версия сервера: 8.0.34
-- Версия PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `book`
--

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `id` int NOT NULL,
  `title` varchar(300) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `author` text NOT NULL,
  `genre` varchar(300) DEFAULT NULL,
  `user_add` int NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `author`, `genre`, `user_add`, `create_date`) VALUES
(1, 'Обраниця чаклуна', 'Анотація до книги \"Обраниця чаклуна\"\r\n― А тепер, щоб навіки скріпити ваш союз, поцілуйте наречену! ― урочисто проголошує жрець.\r\nГаряче дихання нареченого обпікає щоку.\r\nІ в цей момент двері каплиці з моторошним скрипом відчиняються. Огидний скрегіт звук ножем проходить по і так напруженим нервам.\r\nУ білому, залитому яскравим денним світлом, отворі темніє величезна чоловіча постать.\r\n― Зупиніть обряд! Негайно! ― Розноситься по святій обителі.\r\n― Дозвольте поцікавитися, з якої причини? ― роздратовано вимовляє наречений, знущально вигнувши брову.\r\n― Леді Касія заручена. І ніяк не може вийти заміж за вас, ― гість вміло копіює знущальний тон й упевнено заявляє: ― Леді Касія моя наречена!', 'Олеся Лис', ',1,2,', 1, '2023-12-10 08:42:12'),
(6, 'Обурливо жадана, або Спокуса Його Величності', 'Король сам благословив свого Радника на шлюб з Елайзою, 18-річною наївною сиротою зі знатного роду. Але як же гірко він пошкодував, коли побачив наречену. Невже вона саме та, про кого йдеться у давньому пророцтві?\n\nЕлайза — це не Елайза, а я — Поліна. Я потрапила в її тіло, а заразом і у великі неприємності. Мене готують до шлюбу з одним із найвпливовіших людей королівства. Але я дізналася про нього такеееее... Не зносити мені голови... Але, до речі, мені не 18, мені 30, і я не та наївна овечка, яку бачать у мені оточуючі. Ми ще повоюємо…\n\n#безжурна героїня\n#сильний чоловік, який вміє по-справжньому кохати\n#маг, який бачить жінок наскрізь і безсоромно цим користується\n#сильні почуття\n#інтриги та таємниці', 'Ольга Обська', ',1,', 1, '2023-12-10 20:26:49'),
(7, 'Варвар у моєму ліжку', 'Лідія вже звикла, що спадок, який дістався їй від чоловіка, ласий шматок для багатьох. Надто для родичів чоловічої статі, яким муляє, що такими статками одноосібно володіє й розпоряджається жінка. Та досі молодій вдові вдавалося відстояти себе. Аж доки їй не прийшов лист від короля з запрошенням на аудієнцію.\nЯке ж було обурення красуні герцогині, коли монарх звелів їй знову вийти заміж. Та ще й за кого? За свого воєначальника, грубого солдафона, справжнісінького варвара, який заслужив монаршу милість, врятувавши життя королю.\nБути платою за чиюсь звитягу Лідії зовсім не до вподоби, та вибору немає. Доведеться миритися з новим чоловіком й відстоювати себе уже в законному шлюбі з цим варваром.\nТа чи такий він насправді, яким видається?', 'Ольга Островська', ',1,', 1, '2023-12-10 20:27:48');

-- --------------------------------------------------------

--
-- Структура таблицы `genre`
--

CREATE TABLE `genre` (
  `id` int NOT NULL,
  `name` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `genre`
--

INSERT INTO `genre` (`id`, `name`, `description`) VALUES
(1, 'Фентезі', NULL),
(2, 'Міське фентезі', NULL),
(3, 'Сучасна проза', NULL),
(4, 'Жіночий роман', NULL),
(5, 'Бойовик', NULL),
(6, 'Гумор', NULL),
(7, 'Трилер', NULL),
(8, 'Містика/Жахи', NULL),
(9, 'Дитяча література', NULL),
(10, 'Не художня література', NULL),
(11, 'Наукова фантастика', NULL),
(12, 'MY_GENRE', NULL),
(13, 'sfasf', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `chat_id` bigint NOT NULL,
  `username` varchar(300) DEFAULT NULL,
  `write_text` mediumtext COMMENT 'сюда пишем массив с которого сохраняем пост',
  `action` varchar(300) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `chat_id`, `username`, `write_text`, `action`, `create_date`) VALUES
(1, 1520278206, 'vor0tnik', NULL, NULL, '2023-12-09 19:02:45');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `genre`
--
ALTER TABLE `genre`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
