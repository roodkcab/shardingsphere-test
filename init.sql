create database test;
create table test (id int primary key auto_increment, val varchar(100) not null default '');
insert into test (val) values ('test1');
