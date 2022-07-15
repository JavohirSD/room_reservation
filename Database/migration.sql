CREATE TABLE `guest` (
  `id` int(11) NOT NULL primary key AUTO_INCREMENT,
  `full_name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`id`, `full_name`, `email`, `phone_number`, `created_at`) VALUES
(1, 'John Doe 01', NULL, NULL, 1657773702),
(2, 'John Doe 02', NULL, NULL, 1657773703),
(3, 'John Doe 03', NULL, NULL, 1657773704),
(4, 'John Doe 04', NULL, NULL, 1657773705),
(5, 'John Doe 05', NULL, NULL, 1657773706);

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(11) NOT NULL primary key AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `guest_id` int(11) NOT NULL,
  `arriving_date` int(11) NOT NULL,
  `leaving_date` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `comments` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`id`, `room_id`, `guest_id`, `arriving_date`, `leaving_date`, `created_at`, `comments`) VALUES
(1, 1, 2, 1657774385, 1657784385, 1657774385, 'test comment 1'),
(2, 3, 1, 1657803661, 1657890061, 1657774386, 'test comment 2'),
(3, 3, 3, 1658062861, 1658235661, 1657774386, 'test comment 3'),
(4, 3, 3, 1658494861, 1658581261, 1657774386, 'test comment 4');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL primary key AUTO_INCREMENT,
  `number` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `number`, `created_at`) VALUES
(1, '101', 1657737035),
(2, '102', 1657737036),
(3, '103', 1657737037),
(4, '104', 1657737038),
(5, '105', 1657737039);