create table `fight_request` (
  `id` bigint not null auto_increment,
  `fpl` bigint not null,
  `spl` bigint,
  primary key (`id`),
  constraint `fk_request_fpl` foreign key ( `fpl` ) references `user` ( `id` ),
  constraint `fk_request_spl` foreign key ( `spl` ) references `user` ( `id` )
)
engine = innodb,
default character set 'utf8',
default collate 'utf8_general_ci'
;