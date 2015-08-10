create table `fight_log` (
  `id` bigint not null auto_increment,
  `fight` bigint not null,
  `dump` varchar(255) not null,
  `player` int not null,
  `turn` int not null,
  `adata`int not null,
  primary key (`id`),
  constraint `fk_log_fight` foreign key ( `fight` ) references `fight` ( `id` )
)
engine = innodb,
default character set 'utf8',
default collate 'utf8_general_ci'
;