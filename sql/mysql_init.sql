drop schema if exists `wechat_ss_database`;
create schema if not exists `wechat_ss_database`
	default character set utf8
	default collate utf8_bin;

use `wechat_ss_database`

drop table if exists `app`;
create table if not exists `app`(
	`id` bigint unsigned auto_increment not null,
	`app_id` char(18) character set utf8 collate utf8_bin not null,
	`app_secret` char(32) character set utf8 collate utf8_bin not null,
	`app_access_token` varchar(1000) character set utf8 collate utf8_bin null,
	`add_time` timestamp default current_timestamp,
	primary key(`id`)
)
engine InnoDB
default character set utf8
default collate utf8_bin;

insert into `app`(`app_id`, `app_secret`) values('wx9273e6456f7c1304', '1095dd011629d4875601a3b05272d141');