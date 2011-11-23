<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_mmpropman_data"] = Array (
	"ctrl" => $TCA["tx_mmpropman_data"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,calendarweek,objnr,title,teaser,description,previewimage,images,pdffiles,pricecategory,salestype,immotype,region,location,yearofconstruction,livingarea,area,descposition,numberrooms,numberbedrooms,bath,toilet,kitchen,interiorequipment,furnished,stove,wellness,terbalkgar,basement,garage,heating,misc1,misc2,misc3"
	),
	"feInterface" => $TCA["tx_mmpropman_data"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_mmpropman_data',
				'foreign_table_where' => 'AND tx_mmpropman_data.pid=###CURRENT_PID### AND tx_mmpropman_data.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"calendarweek" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.calendarweek",		
			"config" => Array (
				"type" => "input",
				"size" => "3",
				"max" => "2",
				"eval" => "int",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => 53,
					"lower" => 1
				)
			)
		),
		"objnr" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.objnr",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"eval" => "required",
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.title",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",	
				"eval" => "required",
			)
		),
		"teaser" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.teaser",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",
				"rows" => "8",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"previewimage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.previewimage",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",	
				"max_size" => 1000,	
				"uploadfolder" => "uploads/tx_mmpropman",
				"show_thumbs" => 1,	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"images" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.images",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",	
				"max_size" => 1000,	
				"uploadfolder" => "uploads/tx_mmpropman",
				"show_thumbs" => 1,	
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 20,
			)
		),
		"pdffiles" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pdffiles",		
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "",	
				"disallowed" => "php,php3",	
				"max_size" => 1000,	
				"uploadfolder" => "uploads/tx_mmpropman",
				"size" => 5,	
				"minitems" => 0,
				"maxitems" => 5,
			)
		),
		"pricecategory" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.0", "0"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.1", "1"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.2", "2"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.3", "3"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.4", "4"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.5", "5"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.6", "6"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.pricecategory.I.7", "7"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"salestype" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.salestype",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.salestype.I.0", "0"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.salestype.I.1", "1"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"immotype" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype.I.0", "0"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype.I.1", "1"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype.I.2", "2"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype.I.3", "3"),
					Array("LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.immotype.I.4", "4"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"region" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.region",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_mmpropman_region",	
				"foreign_table_where" => "ORDER BY tx_mmpropman_region.uid",	
				"size" => 8,	
				"minitems" => 0,
				"maxitems" => 10,	
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new record",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_mmpropman_region",
							"pid" => "###CURRENT_PID###",
							"setValue" => "prepend"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
						"type" => "popup",
						"title" => "Edit",
						"script" => "wizard_edit.php",
						"popup_onlyOpenIfSelected" => 1,
						"icon" => "edit2.gif",
						"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"location" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.location",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"yearofconstruction" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.yearofconstruction",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
				"max" => "4",
			)
		),
		"livingarea" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.livingarea",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"max" => "10",
			)
		),
		"area" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.area",		
			"config" => Array (
				"type" => "input",	
				"size" => "10",	
				"max" => "10",
			)
		),
		"descposition" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.descposition",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
		"numberrooms" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.numberrooms",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"numberbedrooms" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.numberbedrooms",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"bath" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.bath",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"toilet" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.toilet",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"kitchen" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.kitchen",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"interiorequipment" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.interiorequipment",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
		"furnished" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.furnished",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"stove" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.stove",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"wellness" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.wellness",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"terbalkgar" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.terbalkgar",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"basement" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.basement",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"garage" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.garage",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"heating" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.heating",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",
			)
		),
		"misc1" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.misc1",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
		"misc2" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.misc2",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
		"misc3" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.misc3",		
			"config" => Array (
				"type" => "text",
				"cols" => "40",	
				"rows" => "3",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1,calendarweek, objnr;;;;2-2-2, title;;;;3-3-3, teaser, description;;;
		richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]::rte_transform[mode=ts_css|imgpath=uploads/tx_mmpropman/rte/], 
		//richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[flag=rte_enabled|mode=ts|imgpath=uploads/tx_mmpropman/rte/],
		previewimage;;;;4-4-4,images, pdffiles, pricecategory;;;;5-5-5, salestype, immotype, region, location, yearofconstruction, livingarea, area, descposition, numberrooms, numberbedrooms, bath, toilet, kitchen, interiorequipment, furnished, stove, wellness, terbalkgar, basement, garage, heating, misc1, misc2, misc3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group"),
		"2" => Array("showitem" => "title, teaser")
	)
);



$TCA["tx_mmpropman_region"] = Array (
	"ctrl" => $TCA["tx_mmpropman_region"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,region"
	),
	"feInterface" => $TCA["tx_mmpropman_region"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_mmpropman_region',
				'foreign_table_where' => 'AND tx_mmpropman_region.pid=###CURRENT_PID### AND tx_mmpropman_region.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"region" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_region.region",		
			"config" => Array (
				"type" => "input",	
				"size" => "40",	
				"eval" => "required",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, region")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>