<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

	// add FlexForm field to tt_content
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY."_pi1"]='pi_flexform';

$tempColumns = Array (
	"tx_mmpropman_displaytype" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mm_propman/locallang_db.php:tt_content.tx_mmpropman_displaytype",		
		"config" => Array (
			"type" => "select",
			"items" => Array (
				Array("LLL:EXT:mm_propman/locallang_db.php:tt_content.tx_mmpropman_displaytype.I.0", "0"),
				Array("LLL:EXT:mm_propman/locallang_db.php:tt_content.tx_mmpropman_displaytype.I.1", "1"),
			),
			"size" => 2,	
			"maxitems" => 1,
		)
	),
);


t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);


t3lib_extMgm::allowTableOnStandardPages("tx_mmpropman_data");


t3lib_extMgm::addToInsertRecords("tx_mmpropman_data");

$TCA["tx_mmpropman_data"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data",		
		"label" => "title",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"languageField" => "sys_language_uid",	
		"transOrigPointerField" => "l18n_parent",	
		"transOrigDiffSourceField" => "l18n_diffsource",	
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_mmpropman_data.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, objnr, title, teaser, description, previewimage, images, pdffiles, pricecategory, salestype, immotype, region, location, yearofconstruction, livingarea, area, descposition, numberrooms, numberbedrooms, bath, toilet, kitchen, interiorequipment, furnished, stove, wellness, terbalkgar, basement, garage, heating, misc1, misc2, misc3",
	)
);


t3lib_extMgm::allowTableOnStandardPages("tx_mmpropman_region");

$TCA["tx_mmpropman_region"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_region",		
		"label" => "region",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"languageField" => "sys_language_uid",	
		"transOrigPointerField" => "l18n_parent",	
		"transOrigDiffSourceField" => "l18n_diffsource",	
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_mmpropman_region.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, region",
	)
);

t3lib_extMgm::addPiFlexFormValue($_EXTKEY."_pi1", 'FILE:EXT:mm_propman/flexform_ds_pi1.xml');

t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";
//$TCA["tt_content"]["types"]["list"]["subtypes_addlist"][$_EXTKEY."_pi1"]="tx_mmpropman_displaytype;;;;1-1-1";


t3lib_extMgm::addPlugin(Array("LLL:EXT:mm_propman/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","PropertyManager");
?>