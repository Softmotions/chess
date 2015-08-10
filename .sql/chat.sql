CREATE TABLE `chat` (
  `id` bigint not null auto_increment,
  `key` bigint not null,
  `time` bigint not null,
  `message` varchar(255) not null,
  `user` bigint not null,
  primary key (`id`),
  constraint `fk_chat_user` foreign key ( `user` ) references `user` ( `id` )
)
engine = innodb,
default character set 'utf8',
default collate 'utf8_general_ci'
;
