CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(40) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('M','F') NOT NULL DEFAULT 'M',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_username` (`username`),
  KEY `idx_lastname` (`lastname`(4))
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `wallet` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `bonus_id` bigint(20) NOT NULL DEFAULT '0',
  `initial_value` double NOT NULL DEFAULT '0',
  `current_value` double NOT NULL DEFAULT '0',
  `wagered_value` double NOT NULL DEFAULT '0',
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('ACTIVE','DEPLETED') NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_bonus_id` (`user_id`,`bonus_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `bonus` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `trigger` enum('DEPOSIT','LOGIN') NOT NULL DEFAULT 'DEPOSIT',
  `reward_wallet_type` enum('BONUS','REALMONEY') NOT NULL DEFAULT 'BONUS',
  `value_of_reward` double NOT NULL DEFAULT '0',
  `value_of_reward_type` enum('PERCENT','EURO') NOT NULL DEFAULT 'PERCENT',
  `multiplier` int(11) NOT NULL DEFAULT '1',
  `status` enum('ACTIVE','WAGERED','DEPLETED') NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`),
  KEY `status_trigger` (`status`,`trigger`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;