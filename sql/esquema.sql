create database calendario default character set utf8 collate utf8_unicode_ci;
grant all on calendario.* to admin@localhost identified by 'admin';
flush privileges;

use calendario;

CREATE TABLE IF NOT EXISTS `profesor` (
  `login` varchar(30) unique primary key not null,
  `clave` varchar(40) not null,
  `administrador` tinyint(1) DEFAULT 0
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `aula` (
    `numero` int NOT NULL primary key,
    `tutor` varchar(30) unique not null,
         constraint foreign key(tutor) references profesor(login) on update cascade on delete cascade
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `clase` (
    `asignatura` varchar(30) NOT NULL,
        `dia` int NOT NULL,
        `inicio` int NOT NULL,
        `profesor` varchar(30) NOT NULL,
        `aula` int NOT NULL,
        `fin` int NOT NULL,
    `id` int NOT NULL primary key auto_increment,
         constraint foreign key(profesor) references profesor(login) on update cascade on delete cascade,
         constraint foreign key(aula) references aula(numero) on update cascade on delete cascade
) ENGINE=InnoDB;
