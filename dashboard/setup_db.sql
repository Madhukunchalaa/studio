
-- Create the database if it doesn't exist (Optional, usually you get one from your host)
-- CREATE DATABASE IF NOT EXISTS studiox_db;
-- USE studiox_db;

-- Table structure for table `leads`
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `service_interest` varchar(255) DEFAULT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `timeline` varchar(100) DEFAULT NULL,
  `message` text,
  `page_source` varchar(100) DEFAULT NULL,
  `status` enum('New','Contacted','Qualified','Lost') DEFAULT 'New',
  `remarks` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional: Insert a test lead
-- INSERT INTO `leads` (`name`, `email`, `phone`, `page_source`, `message`) VALUES ('Test User', 'test@example.com', '1234567890', 'Manual Test', 'This is a test lead.');
