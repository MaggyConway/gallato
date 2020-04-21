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