create table if not exists vbch_bonus
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	BONUS decimal(18,4) not null default '0.0',
	USERID int(11) not null,
	ACTIVE_FROM datetime,
	ACTIVE_TO datetime,
	DESCRIPTION text,
	TYPES CHAR(50) not null,
	OPTIONS MEDIUMTEXT,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_1(USERID),
	INDEX ix_vbch_bonus_2(TYPES),
	INDEX ix_vbch_bonus_3(ACTIVE_FROM,ACTIVE_TO)
);
create table if not exists vbch_bonus_tmp
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	BONUS decimal(18,4) not null default '0.0',
	USERID int(11) not null,
	ACTIVE_FROM datetime,
	ACTIVE_TO datetime,
	DESCRIPTION text,
	TYPES CHAR(50) not null,
	OPTIONS MEDIUMTEXT,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_1(USERID),
	INDEX ix_vbch_bonus_2(TYPES),
	INDEX ix_vbch_bonus_3(ACTIVE_FROM,ACTIVE_TO)
);
create table if not exists vbch_bonus_account
(
	ID int not null auto_increment,
	USER_ID int not null,
	TIMESTAMP_X timestamp not null,
	CURRENT_BUDGET decimal(18,4) not null default '0.0',
	CURRENCY char(20) not null,
	NOTES text null,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_account_1(USER_ID)
);
create table if not exists vbch_bonus_profiles
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	SITE char(2) not null,
	NAME char(50) not null,
	BONUS char(5) not null default '0',
	TYPE char(25) not null,
	SCOREIN char(1) not null DEFAULT 'Y',
	ISADMIN char(1) not null DEFAULT 'Y',
	NOTIFICATION LONGTEXT null,
	FILTER LONGTEXT null,
	BONUSCONFIG LONGTEXT null,
	SETTINGS LONGTEXT null,
	COUNTS int(11) not null default '0',
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonusprof_1(SITE),
	INDEX ix_vbch_bonusprof_2(TYPE)
);

create table if not exists vbch_bonus_socialpush
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	SOCIAL char(25) null,
	USER_ID int not null,
	SOCIALTEXT LONGTEXT null,
	PRIMARY KEY (ID)
);
create table if not exists vbch_bonus_referal
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	REFFROM int(11) not null,
	REFERER char(50) not null,
	REFBONUS char(1) not null DEFAULT 'N',
	USERID int(11),
	COOKIE char(50) not null,
	PRIMARY KEY (ID)
);