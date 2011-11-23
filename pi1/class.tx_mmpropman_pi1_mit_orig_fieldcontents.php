<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Your Name (your@email.com)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'PropertyManager' for the 'mm_propman' extension.
 *
 * @author	Your Name <your@email.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

class tx_mmpropman_pi1 extends tslib_pibase {
	var $prefixId 		= "tx_mmpropman_pi1";		// Same as class name
	var $scriptRelPath 	= "pi1/class.tx_mmpropman_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey 		= "mm_propman";	// The extension key.
	var $tablename		= "tx_mmpropman_data";
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		// Could be useful to uncomment this line
		//$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.
		
		// Here we make the main - Initialisation
		$this->init($conf);
	
		switch((string)$conf["CMD"])	{
			case "singleView":
				list($t) = explode(":",$this->cObj->currentRecord);
				$this->internal["currentTable"]=$t;
				$this->internal["currentRow"]=$this->cObj->data;
				return $this->pi_wrapInBaseClass($this->singleView($content,$conf));
			break;
			default:
				if (strstr($this->cObj->currentRecord,"tt_content"))	{
					$conf["pidList"] = $this->cObj->data["pages"];
					$conf["recursive"] = $this->cObj->data["recursive"];
				}
				return $this->pi_wrapInBaseClass($this->listView($content,$conf));
			break;
		}
	}
	
	/**
	 * Do the base configuration, Turn on/off caching and make ab Baseconfiguratin
	 * for the typo-links
	 * Info found on: http://typo3.hachmeister.org/Make_correct_Typolinks.412.0.html
	 * 
	 * Example for a link:
	 * 	$temp_conf = $this->typolink_conf;
	 *	$temp_conf["additionalParams"] .= "&tx_myextension_pi1[key]=value";
	 *	$temp_conf["useCacheHash"] = $this->allowCaching;
	 *	$temp_conf["no_cache"] = !$this->allowCaching;
	 *	$the_link = $this->local_cObj->typolink("Linktext", $temp_conf);
	 */
	function init($conf)
		{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		
		//debug($this->cObj->enableFields($this->tablename));
		//debug($GLOBALS);
		//debug($this->piVars);
		//debug($this->conf["basegroupname"]);
		//debug($GLOBALS["TSFE"]->fe_user->groupData['title'][2]);
		//debug($GLOBALS["TSFE"]->fe_user);
		
		// Preconfigure the typolink
	    $this->local_cObj = t3lib_div::makeInstance("tslib_cObj");
	    $this->local_cObj->setCurrentVal($GLOBALS["TSFE"]->id);
	    $this->typolink_conf = $this->conf["typolink."];
	    $this->typolink_conf["parameter."]["current"] = 1;
	    $this->typolink_conf["additionalParams"] = $this->cObj->stdWrap($this->typolink_conf["additionalParams"],$this->typolink_conf["additionalParams."]);
	    unset($this->typolink_conf["additionalParams."]);
	
	    // Configure caching
	    $this->allowCaching = $this->conf["allowCaching"] ? 1 : 0;
	    if (!$this->allowCaching) $GLOBALS["TSFE"]->set_no_cache();		
		
		// Usersettings
		$this->internal["isUserLoggedIn"] = false;
		if(isset($GLOBALS["TSFE"]->fe_user->groupData['title'][$this->conf["baseGroupID"]]) 
			&& isset($this->conf["baseGroupName"]) &&
			$GLOBALS["TSFE"]->fe_user->groupData['title'][$this->conf["baseGroupID"]] == $this->conf["baseGroupName"])
			{
			$this->internal["isUserLoggedIn"] = true;
			}
			
		// Fieldsettings
		$this->internal["hiddenFields"] = explode(',',isset($this->conf["hiddenFields"]) ? $this->conf["hiddenFields"] : 'pdffile');
		$this->internal["availableFields"] = array('name','age','pdffile');
		
		//debug($GLOBALS["TSFE"]->fe_user->groupData['title'][$this->conf["basegroupid"]]);
		//debug($this->internal);
		}
	
	/**
	 * [Put your description here]
	 */
	function listView($content,$conf)	{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values
		
		$lConf = $this->conf["listView."];	// Local settings for the listView function
	
		if ($this->piVars["showUid"])	{	// If a single element should be displayed:
			$this->internal["currentTable"] = "tx_mmpropman_data";
			$this->internal["currentRow"] = $this->pi_getRecord("tx_mmpropman_data",$this->piVars["showUid"]);
	
			$content = $this->singleView($content,$conf);
			return $content;
		} else {
			$items=array(
				"1"=> $this->pi_getLL("list_mode_1","Mode 1"),
				"2"=> $this->pi_getLL("list_mode_2","Mode 2"),
				"3"=> $this->pi_getLL("list_mode_3","Mode 3"),
			);
			if (!isset($this->piVars["pointer"]))	$this->piVars["pointer"]=0;
			if (!isset($this->piVars["mode"]))	$this->piVars["mode"]=1;
	
				// Initializing the query parameters:
			list($this->internal["orderBy"],$this->internal["descFlag"]) = explode(":",$this->piVars["sort"]);
			$this->internal["results_at_a_time"]=t3lib_div::intInRange($lConf["results_at_a_time"],0,1000,3);		// Number of results to show in a listing.
			$this->internal["maxPages"]=t3lib_div::intInRange($lConf["maxPages"],0,1000,2);;		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
			$this->internal["searchFieldList"]="objnr,title,teaser,description,location,yearofconstruction,livingarea,area,descposition,numberrooms,numberbedrooms,bath,toilet,kitchen,interiorequipment,furnished,stove,wellness,terbalkgar,basement,garage,heating,misc1,misc2,misc3";
			$this->internal["orderByList"]="uid,objnr,title,location,yearofconstruction,livingarea,area,numberrooms,numberbedrooms,bath,toilet,kitchen,furnished,stove,wellness,terbalkgar,basement,garage,heating";
	
				// Get number of records:
			$res = $this->pi_exec_query("tx_mmpropman_data",1);
			list($this->internal["res_count"]) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
	
				// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query("tx_mmpropman_data");
			$this->internal["currentTable"] = "tx_mmpropman_data";
	
				// Put the whole list together:
			$fullTable="";	// Clear var;
		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
	
				// Adds the mode selector.
			$fullTable.=$this->pi_list_modeSelector($items);
	
				// Adds the whole list table
			$fullTable.=$this->pi_list_makelist($res);
	
				// Adds the search box:
			$fullTable.=$this->pi_list_searchBox();
	
				// Adds the result browser:
			$fullTable.=$this->pi_list_browseresults();
	
				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	/**
	 * [Put your description here]
	 */
	function singleView($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		$markerArray['###TEST1###'] = "Super";
		$markerArray['###TEST2###'] = "TOLL";
		
		$template = $this->getTemplate('mike.tmpl');
		
		$templateFieldRow = $this->cObj->getSubpart($template,'###FIELD_ROW###');
		
		$myContent = '';
		$markerArray['###CLASSFIELNAME###'] = $this->pi_classParam("singleView-FieldName");
		$markerArray['###CLASSFIELDVALUE###'] = $this->pi_classParam("singleView-FieldValue");

		$markerArray['###FIELD###'] = "Feld1";
		$markerArray['###VALUE###'] = "Daten1";
		
		$myContent .= $this->cObj->substituteMarkerArray($templateFieldRow,$markerArray);

		$markerArray['###FIELD###'] = "Feld2";
		$markerArray['###VALUE###'] = "Daten2";
		$myContent .= $this->cObj->substituteMarkerArray($templateFieldRow,$markerArray);
		
		$template = $this->cObj->substituteSubpart($template,'###FIELD_ROW###',$myContent);
		debug($this->cObj->substituteMarkerArray($template,$markerArray));

			// This sets the title of the page for use in indexed search results:
		if ($this->internal["currentRow"]["title"])	$GLOBALS["TSFE"]->indexedDocTitle=$this->internal["currentRow"]["title"];
	
		$content='<div'.$this->pi_classParam("singleView").'>
			<H2>Record "'.$this->internal["currentRow"]["uid"].'" from table "'.$this->internal["currentTable"].'":</H2>
			<table>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("objnr").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("objnr").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("title").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("title").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("teaser").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("teaser").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("description").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("description").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("previewimage").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("previewimage").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("images").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("images").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("pdffiles").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("pdffiles").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("pricecategory").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("pricecategory").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("salestype").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("salestype").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("immotype").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("immotype").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("region").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("region").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("location").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("location").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("yearofconstruction").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("yearofconstruction").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("livingarea").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("livingarea").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("area").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("area").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("descposition").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("descposition").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("numberrooms").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("numberrooms").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("numberbedrooms").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("numberbedrooms").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("bath").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("bath").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("toilet").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("toilet").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("kitchen").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("kitchen").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("interiorequipment").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("interiorequipment").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("furnished").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("furnished").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("stove").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("stove").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("wellness").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("wellness").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("terbalkgar").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("terbalkgar").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("basement").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("basement").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("garage").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("garage").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("heating").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("heating").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("misc1").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("misc1").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("misc2").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("misc2").'</p></td>
				</tr>
				<tr>
					<td nowrap valign="top"'.$this->pi_classParam("singleView-HCell").'><p>'.$this->getFieldHeader("misc3").'</p></td>
					<td valign="top"><p>'.$this->getFieldContent("misc3").'</p></td>
				</tr>
				<tr>
					<td nowrap'.$this->pi_classParam("singleView-HCell").'><p>Last updated:</p></td>
					<td valign="top"><p>'.date("d-m-Y H:i",$this->internal["currentRow"]["tstamp"]).'</p></td>
				</tr>
				<tr>
					<td nowrap'.$this->pi_classParam("singleView-HCell").'><p>Created:</p></td>
					<td valign="top"><p>'.date("d-m-Y H:i",$this->internal["currentRow"]["crdate"]).'</p></td>
				</tr>
			</table>
		<p>'.$this->pi_list_linkSingle($this->pi_getLL("back","Back"),0).'</p></div>'.
		$this->pi_getEditPanel();
	
		return $content;
	}
	/**
	 * [Put your description here]
	 */
	function pi_list_row($c)	{
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";
	
		foreach($this->internal["currentRow"] as $key=>$value)
			{
			$markerArray['###' .  strtoupper($key) . '###']	= '<div'.$this->pi_classParam($key).'>' . 
				$this->getFieldContent($key) . '</div>';
			}
		$markerArray['###ROWCLASS###'] = ($c % 2 ? $this->pi_classParam("listrow-odd") : "");
		$markerArray['###EDITPANEL###'] = $editPanel;


		//---------------------------------
		$template = $this->getTemplate('list_view.tmpl');
		$templateFieldRow = $this->cObj->getSubpart($template,'###LIST_ROW###');
		
		return $this->cObj->substituteMarkerArray($templateFieldRow,$markerArray);
/*	
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";
	
		return '<tr'.($c%2 ? $this->pi_classParam("listrow-odd") : "").'>
				<td><p>'.$this->getFieldContent("uid").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("objnr").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("title").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("previewimage").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("images").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("pdffiles").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("pricecategory").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("salestype").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("immotype").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("region").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("location").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("yearofconstruction").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("livingarea").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("area").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("numberrooms").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("numberbedrooms").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("bath").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("toilet").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("kitchen").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("furnished").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("stove").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("wellness").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("terbalkgar").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("basement").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("garage").'</p></td>
				<td valign="top"><p>'.$this->getFieldContent("heating").'</p></td>
				'.$editPanel.'
			</tr>';
			*/
	}
	/**
	 * [Put your description here]
	 */
	function pi_list_header()	{
		$aFields = $GLOBALS['TYPO3_DB']->admin_get_fields($this->tablename);
		
		foreach($aFields as $key=>$value)
			{
			$markerArray['###HEADER_' .  strtoupper($key) . '###']	= '<div'.$this->pi_classParam('header_' . $key).'>' . 
				$this->getFieldHeader($key) . '</div>';			
			}
		$markerArray['###HEADERCLASS###'] = $this->pi_classParam("listheader");

		$template = $this->getTemplate('list_view.tmpl');
		$templateHeader = $this->cObj->getSubpart($template,'###LIST_HEADER###');
		
		return $this->cObj->substituteMarkerArray($templateHeader,$markerArray);
			
		/*	
		return '<tr'.$this->pi_classParam("listrow-header").'>
				<td><p>'.$this->getFieldHeader_sortLink("uid").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("objnr").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("title").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("previewimage").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("images").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("pdffiles").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("pricecategory").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("salestype").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("immotype").'</p></td>
				<td nowrap><p>'.$this->getFieldHeader("region").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("location").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("yearofconstruction").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("livingarea").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("area").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("numberrooms").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("numberbedrooms").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("bath").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("toilet").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("kitchen").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("furnished").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("stove").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("wellness").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("terbalkgar").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("basement").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("garage").'</p></td>
				<td><p>'.$this->getFieldHeader_sortLink("heating").'</p></td>
			</tr>';
			*/
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldContent($fN)	{
		switch($fN) {
			case "uid":
				return $this->pi_list_linkSingle($this->internal["currentRow"][$fN],$this->internal["currentRow"]["uid"],1);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
				break;
			case "pdffiles":
				$aPDFFiles = split(',',$this->internal["currentRow"][$fN]);
				if(!isset($aPDFFiles[0]) || strlen($aPDFFiles[0]) == 0) return '';

				return  $this->cObj->filelink($aPDFFiles[0],$this->conf['filelink.']);	
				//return $this->pi_list_linkSingle($aPDFFiles[0],$this->internal["currentRow"]["uid"],1);
				break;
			case "title": // This will wrap the title in a link.
				return $this->pi_list_linkSingle($this->internal["currentRow"][$fN],$this->internal["currentRow"]["uid"],1);
				break;
			case "description":
				return $this->pi_RTEcssText($this->internal["currentRow"][$fN]);
				break;
			case "previewimage":
				$img = $this->conf["preview."];
				$img["file"] = "uploads/tx_mmpropman/" . $this->internal["currentRow"][$fN];
//				$img["file"] = $this->internal["currentRow"][$fN];
				return $this->cObj->IMAGE($img);			
				break;
			default:
				return $this->internal["currentRow"][$fN];
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader($fN)	{
		switch($fN) {
			case "title":
				return $this->pi_getLL("listFieldHeader_title","<em>title</em>");
			break;
			default:
				return $this->pi_getLL("listFieldHeader_".$fN,"[".$fN."]");
			break;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function getFieldHeader_sortLink($fN)	{
		return $this->pi_linkTP_keepPIvars($this->getFieldHeader($fN),array("sort"=>$fN.":".($this->internal["descFlag"]?0:1)));
	}
	
	function getTemplate($templateName)
		{
		$langKey = strtoupper($GLOBALS['TSFE']->config['config']['language']);
		$template = $this->cObj->fileResource('EXT:' . $this->extKey . '/pi1/res/' . $templateName);		
		
		// Get language version of the help-template
		$template_lang = '';
		if ($langKey) {
			$template_lang = $this->cObj->getSubpart($template, "###TEMPLATE_" . $langKey . '###');
			}
			
		$template = $template_lang ? $template_lang : $this->cObj->getSubpart($template, '###TEMPLATE_DEFAULT###');
		
		// Markers and substitution:
		//$markerArray['###CODE###'] = $this->theCode?$this->theCode:'no CODE given!';
		return $template;
		}
	
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mm_propman/pi1/class.tx_mmpropman_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mm_propman/pi1/class.tx_mmpropman_pi1.php"]);
}

?>