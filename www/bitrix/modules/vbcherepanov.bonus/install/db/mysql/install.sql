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
	UPDATE_1C char(1) not null DEFAULT 'N',
	UPDATE_DATE timestamp not null,
	SORT int(11) not null DEFAULT '500',
	PARTPAY int(11) not null DEFAULT '0',
	BONUSACCOUNTSID int(11) not null,
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
	UPDATE_1C char(1) not null DEFAULT 'N',
	UPDATE_DATE timestamp not null,
	SORT int(11) not null DEFAULT '500',
	PARTPAY int(11) not null DEFAULT '0',
	BONUSACCOUNTSID int(11) not null,
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
	BONUSACCOUNTSID int(11) not null,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_account_1(USER_ID)
);
create table if not exists vbch_bonus_profiles
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	ACTIVE_TO datetime,
	ACTIVE_FROM datetime,
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
create table if not exists vbch_moneyback
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	BONUS decimal(18,4) not null default '0.0',
	USERID int(11) not null,
	BACK_DATE timestamp not null,
	BACK_PERIOD timestamp not null,
	DESCRIPTION text,
	STATUS int(2) not null,
	USERREKV char(255),
	BONUSACCOUNTSID int(11),
	PRIMARY KEY (ID),
	INDEX ix_vbch_moneyback_1(USERID)
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
	ADDRECORDTYPE int(11) not null,
	PRIMARY KEY (ID)
);

create table if not exists vbch_bonus_linkmail
(
	ID int(11) not null auto_increment,
	BONUS decimal(18,4) not null default '0.0',
	USER_ID int(11) not null,
	HASH char(32) not null,
	PRIMARY KEY (ID)
);
create table if not exists vbch_bonusaccounts
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	NAME	char(50) not null,
	PAYSYSTEMID int(11) not null unique,
	SETTINGS MEDIUMTEXT,
	PRIMARY KEY (ID)
);
create table if not exists vbch_bonus_double
(
	ID int not null auto_increment,
	USER_ID int not null,
	TIMESTAMP_X timestamp not null,
	DEBIT decimal(18,4)  default '0.0',
	CREDIT decimal(18,4) default '0.0',
	BONUSACCOUNTSID int(11) not null,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_double_1(USER_ID)
);
create table if not exists vbch_bonus_affiliate
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	BONUS decimal(18,4) not null default '0.0',
	USERID int(11) not null,
	ACTIVE_FROM datetime,
	ACTIVE_TO datetime,
	NAME char(255),
	PROMOCODE char(50),
	DOMAINE char(255),
	URL MEDIUMTEXT,
	COMMISIA char(10),
    COMMISIAPROMO char(10),
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_aff_1(USERID),
	INDEX ix_vbch_bonus_aff_2(USERID),
	INDEX ix_vbch_bonus_aff_3(ACTIVE_FROM,ACTIVE_TO)
);

create table if not exists vbch_bonus_card
(
	ID int(11) not null auto_increment,
	TIMESTAMP_X timestamp not null,
	LID char(2) not null,
	ACTIVE char(1) not null DEFAULT 'Y',
	USERID int(11),
	DEFAULTBONUS decimal(18,4) not null default '0.0',
	BONUSACCOUNTS int(11),
	NUM char(50) not null,
	PRIMARY KEY (ID),
	INDEX ix_vbch_bonus_card_1(USERID),
	INDEX ix_vbch_bonus_card_2(NUM)
);

create table if not exists itr_bonuc_coupon
(
	ID int not null auto_increment,
	ACTIVE char(1) not null default 'Y',
	ACTIVE_FROM datetime null,
	ACTIVE_TO datetime null,
	COUPON varchar(32) not null,
	TYPE int not null default 0,
	MAX_USE int not null default 0,
	USE_COUNT int not null default 0,
	USER_ID int not null default 0,
	TIMESTAMP_X datetime null,
	MODIFIED_BY int(18) null,
	DATE_CREATE datetime null,
	CREATED_BY int(18) null,
	DESCRIPTION text null,
	BONUS decimal(18,4) not null default '0.0',
	BONUSLIVE char(250),
	BONUSACTIVE char(250),
	BONUSACCOUNTSID int(11) not null,
	primary key (ID),
	index IX_S_D_COUPON(COUPON)
);
create table if not exists itr_statistic_bonuc_coupon
(
	ID int not null auto_increment,
	COUPON varchar(32) not null,
	USER_ID int not null default 0,
	TIMESTAMP_X datetime null,
	CLIENT_IP	char(17) default '',
	CLIENT_BROWSER	char(255)  default '',
	CLIENT_REFERER char(255)  default '',
	CLIENT_UTM	char(255) default '',
	primary key (ID)
);