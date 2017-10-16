--
-- Database: `bank_account`
--
CREATE DATABASE IF NOT EXISTS `bank_account` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bank_account`;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `number` int(20) NOT NULL,
  `user_name` varchar(300) NOT NULL,
  `balance` double NOT NULL DEFAULT '0',
  `overdraft` double NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Truncate table before insert `account`
--

TRUNCATE TABLE `account`;
--
-- Dumping data for table `account`
--

INSERT DELAYED IGNORE INTO `account` (`id`, `number`, `user_name`, `balance`, `overdraft`, `active`, `created`, `updated`) VALUES
(1, 100001, 'Test 1', 9952, 1000, 1, '2017-03-25 19:15:38', '2017-03-26 20:11:03'),
(2, 100002, 'Test 2', 0, 0, 1, '2017-03-25 21:18:34', '2017-03-26 20:11:03'),
(3, 100003, 'Test 3', 13222, 0, 0, '2017-03-25 21:54:58', '2017-03-26 20:11:03'),
(4, 100004, 'Test 4', 0, 200, 1, '2017-03-25 22:04:38', '2017-03-26 20:11:04'),
(5, 100005, 'Test 5', 25000, 1000, 0, '2017-03-25 22:04:38', '2017-03-26 20:11:04'),
(6, 100006, 'Test 6', 5000, 0, 1, '2017-03-25 22:04:38', '2017-03-26 20:11:04')
;

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `acc_number` (`number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
