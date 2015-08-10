create table `user` (
  `id` bigint not null auto_increment,
  `login` varchar(255) not null,
  `email` varchar(255) not null,
  `password` varchar(255) not null,
  `activation` varchar(255),
  primary key (`id`),
  unique (`login`),
  unique (`email`)
)
engine = innodb,
default character set 'utf8',
default collate 'utf8_general_ci'
;
