DROP DATABASE IF EXISTS tests;
CREATE DATABASE         tests;
USE                     tests;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`           int(11) NOT NULL AUTO_INCREMENT,
  `person_guid`  varchar( 40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username`     varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email`        varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `salt`         varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password`     varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `roles`        longtext     COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `account_name` varchar(80)  COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_1483A5E992FC23A8` (`username`),
  UNIQUE KEY `UNIQ_1483A5E9A0D96FBF` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1447 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO users VALUES
( 1,
  'C4AF1DBD-4945-4269-97A6-E2E203319D58',
  'ahundiak@testing.com','ahundiak@testing.com',
  'salt',
  'tfNORVo3b6P0EpBzApQpxP8/B2xM/LnCnL1AbtWGDV6bmDAAPY5cpWcdF/E+RcEUXixDZM9s6lZL8LPFTN3rYw==',
  'a:2:{i:0;s:9:\"ROLE_USER\";i:4;s:16:\"ROLE_SUPER_ADMIN\";}',
  'Art Hundiak'
),
( 2,
  '1F9BB8B8-0D8F-414D-9763-E4679E882D67',
  'bailey5000','bailey5000@testing.com',
  'salt',
  'tyo48VJsCv9YW3/hw2HrPgJ9RIdNcLBMps1v0ayOwVDgzM1jGhUFi2SdhSbS1evPqWd+5nF64VBzwZXDC8tDOg==',
  'a:0:{}',
  'Bill Bailey'
),
( 3,
  '7A43DF09-7D0F-4CA2-B991-305094B2340E',
  'ayso1sra@testing.com','ayso1sra@testing.com',
  'salt',
  'Fn+9aBGM9L04FO4YrDZMuKvgIn8ZC6dHBkpAnSQv5yGEiqs94S1uZZCokuqSjBbkwMs6gn0oxWbFnL2loEzgEw==',
  'a:2:{i:0;s:9:\"ROLE_USER\";i:2;s:16:\"ROLE_ADMIN\";}',
  'Rick Roberts'
);
