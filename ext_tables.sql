#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	subpages int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'sys_category'
#
CREATE TABLE sys_category (
	sub_categories int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	golb_sorting varchar(255) DEFAULT NULL,
	golb_sorting_direction varchar(255) DEFAULT NULL,
	golb_limit int(4) DEFAULT NULL,
	golb_offset int(4) DEFAULT NULL,
	golb_related varchar(255)
);