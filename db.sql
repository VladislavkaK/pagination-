CREATE TABLE `posts` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`created` datetime NOT NULL,
`modified` datetime NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1=Active, 0=Inactive',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;