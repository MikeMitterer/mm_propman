#
# Table structure for table 'tt_content'
#
#CREATE TABLE tt_content (
#	tx_mmpropman_displaytype int(11) unsigned DEFAULT '0' NOT NULL
#);



#
# Table structure for table 'tx_mmpropman_data'
#
CREATE TABLE tx_mmpropman_data (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	calendarweek int(2) DEFAULT '0' NOT NULL,
	objnr tinytext NOT NULL,
	title tinytext NOT NULL,
	teaser text NOT NULL,
	description text NOT NULL,
	previewimage blob NOT NULL,
	images blob NOT NULL,
	pdffiles blob NOT NULL,
	pricecategory int(11) unsigned DEFAULT '0' NOT NULL,
	salestype int(11) unsigned DEFAULT '0' NOT NULL,
	immotype int(11) unsigned DEFAULT '0' NOT NULL,
	region blob NOT NULL,
	location tinytext NOT NULL,
	yearofconstruction tinytext NOT NULL,
	livingarea tinytext NOT NULL,
	area tinytext NOT NULL,
	descposition text NOT NULL,
	numberrooms tinytext NOT NULL,
	numberbedrooms tinytext NOT NULL,
	bath tinytext NOT NULL,
	toilet tinytext NOT NULL,
	kitchen tinytext NOT NULL,
	interiorequipment text NOT NULL,
	furnished tinytext NOT NULL,
	stove tinytext NOT NULL,
	wellness tinytext NOT NULL,
	terbalkgar tinytext NOT NULL,
	basement tinytext NOT NULL,
	garage tinytext NOT NULL,
	heating tinytext NOT NULL,
	misc1 text NOT NULL,
	misc2 text NOT NULL,
	misc3 text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_mmpropman_region'
#
CREATE TABLE tx_mmpropman_region (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	region tinytext NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);