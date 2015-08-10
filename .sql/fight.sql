create table `fight` (
  `id` bigint not null auto_increment,
  `fpl` bigint not null,
  `spl` bigint not null,
  `dump` varchar(255) not null,
  `player` int not null default 0,
  `turn` int not null default 0,
  `lrok` int not null default 3,
  `srok` int not null default 3,
  `aturn` int not null default 0,
  `smove` int not null default 0,
  `win` int,
  `fatch` int not null default 0,
  `satch` int not null default 0,
  `active` int not null default 1,
  primary key (`id`),
  constraint `fk_fight_fpl` foreign key ( `fpl` ) references `user` ( `id` ),
  constraint `fk_fight_spl` foreign key ( `spl` ) references `user` ( `id` )
)
engine = innodb,
default character set 'utf8',
default collate 'utf8_general_ci'
;
