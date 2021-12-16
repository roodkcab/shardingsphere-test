create database test1;
create table test1.test (id int primary key auto_increment, val varchar(100) not null default '');
insert into test1.test (val) values ('1');

create database test2;
create table test2.test (id int primary key auto_increment, val varchar(100) not null default '');
insert into test2.test (val) values ('2');
