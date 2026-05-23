-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 23, 2026 at 11:51 AM
-- Server version: 5.7.44
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quiz_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `answer` int(11) NOT NULL,
  `explanation` text,
  `difficulty` enum('easy','medium','hard') NOT NULL DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `answer`, `explanation`, `difficulty`) VALUES
(2, 'Which tag is used in HTML to create a hyperlink?', '<link>', '<a>', '<href>', '<url>', 2, 'The <a> tag (anchor tag) is used to create hyperlinks in HTML. The href attribute specifies the destination URL.', 'easy'),
(3, 'What does PHP stand for?', 'Pre Hypertext Processor', 'Hypertext Preprocessor', 'Hypertext Postprocessor', 'Post Hypertext Processor', 2, 'The <img> tag is used to embed images in HTML pages. The src attribute specifies the path to the image.', 'easy'),
(4, 'What does PHP stand for?', 'Personal Home Page', 'Preprocessor Hypertext', 'Private Hosting Page', 'Professional Home Page', 2, '<h1> is the largest heading element. Headings range from <h1> (most important) to <h6> (least important).', 'easy'),
(5, 'Which symbol is used for comments in PHP?', '//', '#', '/* */', 'All of the above', 4, 'CSS stands for Cascading Style Sheets. It is used to control the presentation and layout of web pages.', 'easy'),
(6, 'What is the correct way to start a session in PHP?', 'session_start()', 'start_session()', 'Session::start()', 'session_begin()', 1, 'The color property is used to set the text color. You can use color names, HEX, RGB, or HSL values.', 'easy'),
(7, 'What does HTML stand for?', 'Hyper Text Markup Language', 'Home Tool Markup Language', 'Hyperlinks and Text Markup Language', 'High Tech Modern Language', 1, 'In CSS, # is used to select an element by its id attribute. For example, #header selects the element with id=\"header\".', 'medium'),
(8, 'Which tag is used to create a hyperlink in HTML?', '<link>', '<a>', '<href>', '<url>', 2, 'Margin creates space AROUND an element (outside its border). Padding creates space INSIDE an element (between content and border).', 'medium'),
(9, 'Which HTML tag is used to insert an image?', '<img>', '<image>', '<src>', '<pic>', 1, 'JavaScript was created by Brendan Eich at Netscape in 1995. It was originally called Mocha, then LiveScript, and finally JavaScript.', 'medium'),
(10, 'What is the correct HTML element for the largest heading?', '<heading>', '<h6>', '<head>', '<h1>', 4, '// is used for single-line comments in JavaScript. /* */ is used for multi-line comments.', 'medium'),
(11, 'What does CSS stand for?', 'Creative Style Sheets', 'Computer Style Sheets', 'Cascading Style Sheets', 'Colorful Style Sheets', 3, 'console.log() is the correct method to output messages to the browser\'s console for debugging purposes.', 'medium'),
(12, 'Which CSS property is used to change the text color?', 'text-color', 'color', 'font-color', 'text-style', 2, '=== is the strict equality operator. It checks both value AND type without type conversion. == checks only value with type conversion.', 'medium'),
(13, 'How do you select an element with id \"header\" in CSS?', '.header', '#header', 'header', '*header', 2, 'PHP originally stood for Personal Home Page, but now it stands for PHP: Hypertext Preprocessor (recursive acronym).', 'medium'),
(14, 'Which CSS property controls the spacing between elements?', 'spacing', 'margin', 'padding', 'gap', 2, 'session_start() creates a new session or resumes an existing one. It must be called before any output is sent to the browser.', 'medium'),
(15, 'Which company developed JavaScript?', 'Microsoft', 'Sun Microsystems', 'Netscape', 'Google', 3, 'In PHP, all variables start with the $ symbol followed by the variable name. Example: $name = \"John\";', 'hard'),
(16, 'What is the correct way to write a comment in JavaScript?', '// This is a comment', '<!-- This is a comment -->', '# This is a comment', '/* This is a comment */', 1, 'mysqli_connect() is used to open a new connection to a MySQL server. It returns a connection object.', 'hard'),
(17, 'Which function is used to print something to the console in JavaScript?', 'print()', 'console.print()', 'log.console()', 'console.log()', 4, 'SQL stands for Structured Query Language. It is used to communicate with and manipulate databases.', 'medium'),
(18, 'What does \"===\" operator do in JavaScript?', 'Assignment', 'Comparison of values', 'Comparison of values and type', 'Not equal', 3, 'The SELECT statement is used to retrieve data from a database. Example: SELECT * FROM table_name;', 'medium'),
(19, 'What does PHP stand for?', 'Personal Home Page', 'Preprocessor Hypertext', 'Private Hosting Page', 'Professional Home Page', 2, 'Git is a distributed version control system used to track changes in source code during software development.', 'easy'),
(20, 'How do you start a PHP session?', 'session_start()', 'start_session()', 'Session::start()', 'session_begin()', 1, 'HTML (Hyper Text Markup Language) is a markup language, not a programming language. Programming languages like Python, Java, and C++ can execute logic and algorithms.', 'hard'),
(21, 'Which symbol is used to access a variable in PHP?', '$', '@', '#', '&', 1, NULL, 'medium'),
(22, 'What function is used to connect to MySQL database in PHP?', 'connect_mysql()', 'db_connect()', 'mysqli_connect()', 'mysql_connect()', 3, NULL, 'medium'),
(23, 'What does SQL stand for?', 'Structured Query Language', 'Simple Query Language', 'Style Query Language', 'Standard Query Language', 1, NULL, 'medium'),
(24, 'Which SQL statement is used to extract data from a database?', 'EXTRACT', 'GET', 'SELECT', 'OPEN', 3, NULL, 'medium'),
(25, 'What is Git used for?', 'Version Control', 'Code Compilation', 'Database Management', 'Web Hosting', 1, NULL, 'medium'),
(26, 'Which of the following is NOT a programming language?', 'Python', 'Java', 'HTML', 'C++', 3, NULL, 'medium'),
(27, 'What is a closure in JavaScript?', 'A function that has access to its own scope only', 'A function that has access to variables from its outer scope even after the outer function has returned', 'A way to close a browser window', 'A method to encrypt variables', 2, 'A closure is created when a function remembers its lexical scope even when the function is executed outside that scope. Example: function outer() { let x = 10; return function() { console.log(x); } }', 'hard'),
(28, 'What is the main difference between a session and a cookie in PHP?', 'Sessions store data on the server, cookies store data on the client', 'Cookies store data on the server, sessions store data on the client', 'Both store data only on the server', 'Both store data only on the client', 1, 'Sessions store user data on the server side (safer for sensitive data), while cookies store data on the client\'s browser (limited to 4KB). Sessions end when browser closes, cookies can persist.', 'hard'),
(29, 'Given the CSS: #header .nav > li a:hover, which has the highest specificity?', '#header (ID selector)', '.nav (Class selector)', 'li (Element selector)', 'a:hover (Pseudo-class selector)', 1, 'ID selectors have the highest specificity value (100 points). Classes have 10 points, elements have 1 point. #header as an ID selector wins.', 'hard'),
(30, 'Which SQL JOIN returns all records from the left table and matched records from the right table?', 'INNER JOIN', 'RIGHT JOIN', 'LEFT JOIN', 'FULL OUTER JOIN', 3, 'LEFT JOIN returns all records from the left table, and matched records from the right table. If no match, NULL values are returned for right table columns.', 'hard'),
(31, 'What is the output of: console.log(\"1\"); setTimeout(() => console.log(\"2\"), 0); console.log(\"3\");', '1, 2, 3', '1, 3, 2', '2, 1, 3', '3, 2, 1', 2, 'Even with 0ms delay, setTimeout is asynchronous. The callback goes to the task queue. Output: 1, 3, then 2 after the call stack is empty.', 'hard'),
(32, 'What is the output of: $x = 5; function test() { echo $x; } test();', '5', 'null', 'Error: Undefined variable $x', '0', 3, 'Variables defined outside a function are not accessible inside unless declared as global or passed as parameters. This produces an \"Undefined variable\" error.', 'hard'),
(33, 'Which column type is BEST suited for indexing in MySQL to improve query performance?', 'TEXT column with long strings', 'VARCHAR(255) with high cardinality', 'Column with many NULL values', 'Column with only 2 possible values (e.g., gender)', 2, 'Columns with high cardinality (many unique values) like VARCHAR are best for indexing. Low cardinality columns (only 2-3 values) give poor index performance.', 'hard');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `difficulty` varchar(20) DEFAULT 'all',
  `played_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `user_id`, `username`, `score`, `total_questions`, `percentage`, `difficulty`, `played_at`) VALUES
(1, 2, 'Ang5Lboyy', 13, 25, 52, 'all', '2026-05-23 10:36:23'),
(2, 3, 'Aram', 13, 25, 52, 'all', '2026-05-23 10:38:27'),
(3, 3, 'Aram', 16, 25, 64, 'all', '2026-05-23 10:42:56'),
(4, 4, 'Ashot', 4, 6, 67, 'easy', '2026-05-23 10:48:52'),
(5, 2, 'Ang5Lboyy', 6, 6, 100, 'easy', '2026-05-23 10:50:22'),
(6, 3, 'Aram', 4, 6, 67, 'easy', '2026-05-23 10:51:28'),
(7, 3, 'Aram', 9, 16, 56, 'medium', '2026-05-23 10:53:14'),
(8, 2, 'Ang5Lboyy', 9, 16, 56, 'medium', '2026-05-23 11:05:21'),
(9, 4, 'Ashot', 9, 16, 56, 'medium', '2026-05-23 11:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fifty_fifty_available` int(11) NOT NULL DEFAULT '1',
  `audience_available` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`, `fifty_fifty_available`, `audience_available`) VALUES
(2, 'Ang5Lboyy', 'angelbarseghyan12@gmail.com', '504353Angel', 1, '2026-05-23 09:12:59', 0, 1),
(3, 'Aram', 'aramaram@gmail.com', 'aram777', 0, '2026-05-23 10:13:21', 0, 0),
(4, 'Ashot', 'ashotashot@gmail.com', 'ashot777', 0, '2026-05-23 10:47:52', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `score` (`score`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
