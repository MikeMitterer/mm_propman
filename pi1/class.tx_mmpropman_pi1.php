<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Your Name (mike.mitterer@bitcon.at)
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

require_once(t3lib_extMgm::extPath('mm_bccmsbase').'lib/class.mmlib_extfrontend.php');
//class tx_mmpropman_pi1 extends mmlib_extfrontend {

class tx_mmpropman_pi1 extends mmlib_extfrontend {
	var $prefixId			= 'tx_mmpropman_pi1';
	var $scriptRelPath		= "pi1/class.tx_mmpropman_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey 			= "mm_propman";	// The extension key.
	var $tablename			= "tx_mmpropman_data";
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		// Could be useful to uncomment this line
		//$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.

		// Here we make the main - Initialisation
		//$this->_initPidList();
		//$this->local_init($conf);

		// Init the mm_cmsbase-Framework
		$aInitData['tablename'] 		= $this->tablename;
		$aInitData['uploadfolder'] 	= 'tx_mmpropman';
		$aInitData['extensionkey'] 	= $this->extKey;
		
		// Optional
		$aInitData['flex2conf'] 		= $this->getFLEXConversionInfo();

		// Init the framework			
		$this->initFromArray($conf,$aInitData);
		
		// Local-Class INIT
		$this->_initUserSettings();

		// Fieldsettings
		$this->internal["hiddenFields"] = explode(',',isset($this->conf["hiddenFields"]) ? $this->conf["hiddenFields"] : 'pdffile');
		$this->internal["availableFields"] = array('name','age','pdffile');
		
		switch((string)$this->conf["CMD"])	{
			case "singleView":
				list($t) = explode(":",$this->cObj->currentRecord);
				$this->internal["currentTable"]=$t;
				return $this->pi_wrapInBaseClass($this->singleView($content));
			break;
			default:
				if (strstr($this->cObj->currentRecord,"tt_content"))	
					{
					$this->conf["pidList"] = $this->cObj->data["pages"];
					$this->conf["recursive"] = $this->cObj->data["recursive"];
					}
				return $this->pi_wrapInBaseClass($this->listView($content));
			break;
		}
	}
	
	/**
	 * Overwrites the function from mm_bccmsbase (+ Typo-Framework) to
	 * get the original T3 functionality
	 */
	function pi_list_makelist($res,$tableParams='')	{
		return tslib_pibase::pi_list_makelist($res,$tableParams);
		}	
	
	
	/**
	 * Shows you the available properties (houses...)
	 */
	function listView($content)	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values

		$lConf 							= $this->conf["listView."];	// Local settings for the listView function
		$lConfSearchForm 		= $this->conf["searchForm."];	// Local settings for the search function
		$strTableClassName	= ($lConf['tableClassName'] ? $lConf['tableClassName'] : 'table');		

		if ($this->piVars["showUid"])	{	// If a single element should be displayed:
			$this->internal["currentTable"] = $this->getTableName();
			$this->internal['currentRow'] 	= $this->initCurrentRow();
			
			$content = $this->singleView($content);
			return $content;
			}
		else 
			{
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
			$this->internal["orderByList"]="crdate,tstamp,objnr,title,teaser,description,location,yearofconstruction,livingarea,area,descposition,numberrooms,numberbedrooms,bath,toilet,kitchen,interiorequipment,furnished,stove,wellness,terbalkgar,basement,garage,heating,misc1,misc2,misc3";
			
			switch($this->piVars["mode"]) {
				case '3':
					$this->internal["orderBy"] = $lConf['mode_selector_order3'];
					$this->internal["descFlag"] = $lConf['mode_selector_order3_desc_flag'];
					break;
				case '2':
					$this->internal["orderBy"] = $lConf['mode_selector_order2'];
					$this->internal["descFlag"] = $lConf['mode_selector_order2_desc_flag'];
					break;
				default:
					$this->internal["orderBy"] = $lConf['mode_selector_order1'];
					$this->internal["descFlag"] = $lConf['mode_selector_order1_desc_flag'];
				}
			//debug($this->internal);	
			//debug($this->piVars);	
			
			// Wenn f�r eine Seite ein Filter gesetzt ist - dann hier einbauen
			$strWhereStatement = '';
			
			// Languagespecific!!
			$langSelectConf 		= $this->generateLangSpecificSelectConf();
			$strWhereStatement .= ($langSelectConf['where'] ? 'AND ' . $langSelectConf['where'] : '');
			
			//debug($this->conf['filter.']);
			if(is_array($this->conf['filter.']))
				{
				foreach($this->conf['filter.'] as $key=>$value)
					{ 
					if($key == 'calendarweek' && $value == 'true') $value = date("W");
					
					$strWhereStatement .= "AND $key = '$value'";
					//debug($strWhereStatement) ;
					}
				}
			if(is_array($this->piVars["search"]))
				{
				foreach($this->piVars["search"] as $key=>$value)
					{
					if($value == -1) continue;
					//$strWhereStatement .= "AND $key LIKE '%$value%'";
					//$strWhereStatement .= "AND '$value' in $key";
					$strWhereStatement .= "AND $key REGEXP '$value\[,]?'";
					}
				}

				// Get number of records:
			// debug($strWhereStatement);
			$res = $this->pi_exec_query("tx_mmpropman_data",1,$strWhereStatement);
			list($this->internal["res_count"]) = ($res ? $GLOBALS['TYPO3_DB']->sql_fetch_row($res) : 0);
			
			// If there are no items in the table or if the search fails - goto a specific page
			// debug("ResCount: " . $this->internal["res_count"]);
			if($this->internal["res_count"] == 0)
				{
				//$goToPage = tslib_pibase::pi_linkTP_keepPIvars_url(array(),0,0,$lConf['noItemsFoundPage']);
				$getVars = t3lib_div::_GET();
				$goToPage = tslib_pibase::pi_getPageLink( $lConf['noItemsFoundPage'] ? $lConf['noItemsFoundPage'] : $this->getPageTableData('pid'),
					'',$getVars['L']);
				header('Location: '.t3lib_div::locationHeaderUrl($goToPage));
				exit;
				}
			
				// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query("tx_mmpropman_data",0,$strWhereStatement);
			$this->internal["currentTable"] = "tx_mmpropman_data";
	
				// Put the whole list together:
			$fullTable="";	// Clear var;
		#	$fullTable.=t3lib_div::view_array($this->piVars);	// DEBUG: Output the content of $this->piVars for debug purposes. REMEMBER to comment out the IP-lock in the debug() function in t3lib/config_default.php if nothing happens when you un-comment this line!
	
				// Adds the mode selector.
			if($lConf['showModeSelector'] == 1) $fullTable .= $this->pi_list_modeSelector($items);
	
				// Adds the search box:
			if($lConfSearchForm['search_on_top'] == 1) {
				$fullTable .= $this->generateSearchBox(); }
				
				// Adds the whole list table
			if($lConf['hide_list_view'] == 0) {
				$fullTable .= $this->pi_list_makelist($res,'border="0" cellspacing="0" cellpadding="0"' . $this->pi_classParam($strTableClassName)); }
	
				// Adds the search box:
			if($lConfSearchForm['search_bottom'] == 1) {
				$fullTable .= $this->generateSearchBox(); }
				
				// Adds the result browser:
			if($lConf['showBrowserResults'] == 1) $fullTable .= $this->pi_list_browseresults();
	
				// Returns the content from the plugin.
			return $fullTable;
		}
	}
	
	/**
	 * [Put your description here]
	 */
	function singleView($content)	{
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$lConf 							= $this->conf['singleView.'];	// Local settings for the single function
		$secureConf 				= $this->conf['secureCheck.'];		
		$aGETVars 					= t3lib_div::_GET();	// Commandline
		$aPOSTVars 					= t3lib_div::_POST(); 	// Form
		$objUserAuth 				= t3lib_div::makeInstance('tslib_feUserAuth');
		$confRequestForm		= $this->conf['requestForm.'];	// Local settings for the single function
		
		// Show the login-form only if login ist enabled
		if($secureConf['enableLogin'] == 1) {
			if($secureConf['useStrictLogin'] == 1)
				{
				$objUserAuth->user = '';
				$objUserAuth->sendNoCacheHeaders = 1;
				$objUserAuth->logoff();
				$objUserAuth->gc();
				$objUserAuth->start();
				}
			else if($this->_initUserSettings() == false) $objUserAuth->start();
			
			// false - wenn kein User angemeldet ist...	
			if($this->_initUserSettings() == false)
				{
				$confForm = $this->conf["loginForm."]["login."];
				//$confForm['type'] = 'index.php?' . str_replace('&amp;','#',$GLOBALS['QUERY_STRING']);
	
				// die Aufrufende URL in die Form mitnehemn (merken des Types und der Objekt-ID)
				$confForm['type'] = $GLOBALS['REQUEST_URI'];
				
				// Zus�tzliche Felder im Formular
				$confForm['dataArray.']['90.']['type'] = 'showuid=hidden';
				$confForm['dataArray.']['90.']['value'] = $aGETVars['tx_mmpropman_pi1']['showUid'];
				$confForm['dataArray.']['95.']['type'] = 'chash=hidden';
				$confForm['dataArray.']['95.']['value'] = $aGETVars['cHash'];
				$confForm['hiddenFields.']['pid.']['value'] = $secureConf['feUserPage'] ? $secureConf['feUserPage'] : $aGETVars['id'];
				
				$strContent 	= $this->cObj->FORM($confForm);
				$strInfoLine 	= $this->pi_getLL("goto.registrationpage","Link zum ###LINK_REG_PAGE###Registierungsformular###LINK_REG_PAGE###");
				$strLinkText	= $this->cObj->getSubpart($strInfoLine,'###LINK_REG_PAGE###');
				$strLinkText 	= $this->pi_linkToPage($strLinkText,$secureConf['registrationPage']);
				$strInfoLine 	= $this->cObj->substituteSubpart($strInfoLine,'###LINK_REG_PAGE###',$strLinkText);
				
				$strContent .= $strInfoLine;
				
				return $strContent;
				}
			}
						
		$this->_sendInfoMail();
		
		// This sets the title of the page for use in indexed search results:
		if ($this->internal['currentRow']["title"])	$GLOBALS["TSFE"]->indexedDocTitle = $this->internal['currentRow']["title"];
	
		$strContent = '';
		$template 			= $this->getTemplate('single_view.tmpl');
		$templateListCol 	= $this->cObj->getSubpart($template,'###LIST_COL###');
		$templateMarker 	= $this->cObj->getSubpart($template,'###MARKERLINE###');
		//$templateDateInfo = $this->cObj->getSubpart($template,'###DATEINFO###');
		
		$markerArray['###SYS_UID###'] 			= $this->internal['currentRow']["uid"];
		$markerArray['###SYS_CURRENTTABLE###'] 	= $this->internal["currentTable"];
		$markerArray['###SYS_LASTUPDATE###'] 	= date("d-m-Y H:i",$this->internal['currentRow']["tstamp"]);
		$markerArray['###SYS_CREATION###'] 		= date("d-m-Y H:i",$this->internal['currentRow']["crdate"]);

		// Old!!!
		// $markerArray['###SYS_BACKLINK###'] 		= $this->pi_list_linkSingle($this->pi_getLL("back","Back"),0);
		$altBackPageID 												= $this->conf["singleView."]['backlinkToAltPage'] ? $this->conf["singleView."]['backlinkToAltPage'] : '0';
		$markerArray['###SYS_BACKLINK###'] 		= $this->pi_list_linkSingle($this->pi_getLL("back","Back"),0,1,array(),false,$altBackPageID);	
		
		$markerArray['###SYS_EDITPANEL###'] 	= $this->pi_getEditPanel();
		$markerArray['###SYS_ALLFIELDS###']		= '';

		$markerArray['###TABLECLASS###']		= $this->pi_classParam('singleView');
		$markerArray['###IMAGES###'] 			= '<div' . $this->pi_classParam('images') . '>' .
													$this->getFieldContent('images') . '</div>';
		$markerMarker['###MARKERCLASS###']		= $this->pi_classParam('singleView-marker');
		

		
		// Reihenfolge der Felder festlegen
		$aFieldsToDisplay = strlen($lConf['displayOrder']) > 0 ? explode(',',$lConf['displayOrder']) : array_keys($this->internal['currentRow']);
		
		// Diese Felder werden ausgeschlossen wenn sie leer oder auf 0 sind
		$aHideIfEmpty = strlen($lConf['hideIfEmpty']) > 0 ? explode(',',$lConf['hideIfEmpty']) : array();
		foreach($aHideIfEmpty as $evalue) $aTemp[] = trim($evalue);
		$aHideIfEmpty = $aTemp;
		
		$aFieldsToDisplay = array_keys($this->internal['currentRow']);
		$aFieldsToDisplay = split(',',$lConf['displayOrder']);
		
		$nCounter = 0;	
		$strColContent = '';
		foreach($aFieldsToDisplay as $key)
			{
			$key = trim($key);
				
			// Wenn im KEY (also im Feldnamen eine [ vorkommt dann ist das eine Leerzeile	
			if(preg_match('#^\[marker(.*)\]$#',$key,$aMatches)) {
				$markerMarker['###MARKERTEXT###'] = '&nbsp;';
				if(isset($aMatches[1]) && trim(strlen($aMatches[1])>0)) 
					{
					$strMarkerLable = trim($aMatches[1]);
					$markerMarker['###MARKERTEXT###'] = $this->pi_getLL($strMarkerLable,$strMarkerLable);
					}
				$strColContent .= $this->cObj->substituteMarkerArray($templateMarker,$markerMarker);
				continue;
				}
			
			$strFieldHeader = $this->getFieldHeader($key);

			// Wenn am Anfang und am Ende des Feldnamens ein [ bzw. ] steht dann ist das normalerweise der interne Name (internes Feld)
			if(preg_match('#^\[.*\]$#',$strFieldHeader)) continue;
			
			// Wenn leer und wenn der Status des Feldes auf ausblenden wenn leer
			if(($this->internal['currentRow'][$key] === '' || 
				$this->internal['currentRow'][$key] === 0) && 
				in_array($key,$aHideIfEmpty,true))
				{ 
				//debug("$key ->" . $this->getSingleViewFieldContent($key) . '#' . $this->internal['currentRow'][$key] . '#');
				continue; 
				}

			$markerArray['###SYS_ALLFIELDS###'] .= $key . ', ';
			// Die beiden Felder werden auf den selben Wert gezogen da damit 
			// entweder eine Tabelle erstellt werden kann die immer die Selben Zeilen verwendet
			// sowie eine Tabelle die ein individuelles Layout hat
			$markerArrayCol['###LABLE###']	= '<div'.$this->pi_classParam('lable ' . 'lable_' . $key).'>' . 
				$strFieldHeader . '</div>';

			$markerArrayCol['###LABLE_' . strtoupper($key) . '###'] = $markerArrayCol['###TITLE###'];
				
			// Und hier kommen die Feldwerte
			$markerArrayCol['###FIELD###']	= '<div'.$this->pi_classParam('field ' . 'field_' . $key).'>' . 
				$this->getSingleViewFieldContent($key) . '</div>';

			$markerArrayCol['###FIELD_' .  strtoupper($key) . '###']	= $markerArrayCol['###FIELD###'];
			$markerArrayCol['###' .  strtoupper($key) . '###']				= $markerArrayCol['###FIELD###'];
			
			$markerArrayCol['###COLCLASS###'] = ($nCounter % 2 ? $this->pi_classParam("listcol-odd") : "");
			
			$strColContent .= $this->cObj->substituteMarkerArray($templateListCol,$markerArrayCol);
			$nCounter++;
			}
		
		//debug($markerArrayCol);
		
		if($lConf['showFieldNames']	== 0) $markerArray['###SYS_ALLFIELDS###'] = '';
		
		//L�schen des Markerblocks - sonst wird dieser am Ende noch 1x angezeigt
		$template = $this->cObj->substituteSubpart($template,'###MARKERLINE###','');
		
		$template = $this->cObj->substituteSubpart($template,'###LIST_COL###',$strColContent);

		$template = $this->cObj->substituteMarkerArray($template,$markerArray);
		
		$strContent = $this->cObj->substituteMarkerArray($template,$markerArrayCol);
		
		
		if($confRequestForm['enable'] == 1) {
			$strContent .= $this->getRequestForm();
			}
		
		
		return $strContent;
	}
	/**
	 * [Put your description here]
	 */
	function pi_list_row($c)	{
		$strTemplateName	= ($this->conf["templateFile"] ? $this->conf["templateFile"] : 'list_view.tmpl');		
		$editPanel = $this->pi_getEditPanel();
		if ($editPanel)	$editPanel="<TD>".$editPanel."</TD>";
	
		foreach($this->internal['currentRow'] as $key=>$value)
			{
			$markerArray['###' .  strtoupper($key) . '_CLASS###']	= $this->pi_classParam($key);
			
			$markerArray['###' .  strtoupper($key) . '###']	= '<span'.$this->pi_classParam($key).'>' . 
				$this->getFieldContent($key) . '</span>';
			}
		$markerArray['###ROWCLASS###'] 			= ($c % 2 ? $this->pi_classParam("listrow-odd") : $this->pi_classParam("listrow-even"));
		$markerArray['###ROWCLASS2###'] 		= ($c % 2 ? $this->pi_classParam("listrow2-odd") : $this->pi_classParam("listrow2-even"));

		$markerArray['###SUBTABLE1CLASS###'] 	= $this->pi_classParam("subtable1");
		$markerArray['###SUBTABLE2CLASS###'] 	= $this->pi_classParam("subtable2");
		$markerArray['###SUBTABLE3CLASS###'] 	= $this->pi_classParam("subtable3");
		
		$markerArray['###FOOTERCLASS###']		= $this->pi_classParam('listView-footer');
		
		$markerArray['###EDITPANEL###'] 	= $editPanel;


		//---------------------------------
		$template = $this->getTemplate($strTemplateName);
		$templateFieldRow = $this->cObj->getSubpart($template,'###LIST_ROW###');
		
		return $this->cObj->substituteMarkerArray($templateFieldRow,$markerArray);
	}
	
	/**
	 * [Put your description here]
	 */
	function pi_list_header()	{
		$lConf 				= $this->conf["listView."];
		$aFields 			= $GLOBALS['TYPO3_DB']->admin_get_fields($this->tablename);
		$strTemplateName	= ($this->conf["templateFile"] ? $this->conf["templateFile"] : 'list_view.tmpl');		

		// Header soll nicht angezeigt werden
		if($lConf['showHeader'] == 0) return '';
			
		foreach($aFields as $key=>$value)
			{
			$markerArray['###HEADER_' .  strtoupper($key) . '###']	= '<div'.$this->pi_classParam('header_' . $key).'>' . 
				$this->getFieldHeader($key) . '</div>';			
			}
		$markerArray['###HEADERCLASS###'] = $this->pi_classParam("listheader");

		$template = $this->getTemplate($strTemplateName);
		$templateHeader = $this->cObj->getSubpart($template,'###LIST_HEADER###');
		
		$strOutput = $this->cObj->substituteMarkerArray($templateHeader,$markerArray);
		//debug($strOutput);
		
		return $strOutput;
		}
	
	/**
	 * Returns the contnet from the current table record
	 */
	function getFieldContent($fieldName)	{
		$altPageID = $this->conf["listView."]['linkToAltPage'] ? $this->conf["listView."]['linkToAltPage'] : '0';
		
		switch($fieldName) {
			case "title": // This will wrap the title in a link.
			case "uid":
				return $this->pi_list_linkSingle($this->internal['currentRow'][$fieldName],$this->internal['currentRow']["uid"],1,array(),false,$altPageID);	// The "1" means that the display of single items is CACHED! Set to zero to disable caching.
				break;
			case "pdffile":
			case "pdffiles":
				$aPDFFiles = split(',',$this->internal['currentRow'][$fieldName]);
				$strFileLink = '';
				
				if(!isset($aPDFFiles[0]) || strlen($aPDFFiles[0]) == 0) return '';
				$strFileLink = $this->cObj->filelink($aPDFFiles[0],$this->conf['filelink.']);	
				if($strFileLink == '')
					{
					$this->conf['filelink.']['path'] = $this->conf['filelink.']['path2'];
					$strFileLink = $this->cObj->filelink($aPDFFiles[0],$this->conf['filelink.']);
					}
				return $strFileLink; 
				//return $this->pi_list_linkSingle($aPDFFiles[0],$this->internal['currentRow']["uid"],1);
				break;
			case "salestype":
			case "immotype":
			case "pricecategory":
				return $this->getBL($fieldName,$this->internal['currentRow'][$fieldName]);
				break;
				
			case "description":
				return $this->pi_RTEcssText($this->internal['currentRow'][$fieldName]);
				break;
			case "previewimage":
				$img = $this->conf["listView."]["preview."];
				$img["file"] = "uploads/tx_mmpropman/" . $this->internal['currentRow'][$fieldName];
				$strImagURL = $this->cObj->IMAGE($img);

				return $this->pi_list_linkSingle($strImagURL,$this->internal['currentRow']["uid"],1,array(),false,$altPageID);	
				break;
			case "images":
				$aImages = split(',',$this->internal['currentRow'][$fieldName]);

				if(!isset($aImages[0]) || strlen($aImages[0]) == 0) return '';

				$strContent = '';
				$nCounter = 0;
				foreach($aImages as $image)
					{
					$img = $this->conf['singleView.']['images.'];
					$img["file"] = 'uploads/tx_mmpropman/' . $image;
					
					$strIMG = $this->cObj->IMAGE($img);
					
					$strContent .= ('<span' . $this->pi_classParam('image ' . $this->pi_getClassName('image-' . $nCounter)) . '>' . 
						$strIMG . '</span>');				
				
					$nCounter++;
					}
				//debug($strContent);
				return  $strContent;	
				break;
			case 'region': 
				$strRegions = '';
				// RegionsIDs (List 5,7)
				$conf['uidInList'] = $this->internal['currentRow'][$fieldName]; 
				// Da befinden sich die Daten
				$conf['pidInList'] = $this->pid_list;
				
				// Wurde durch uidInList und pidInList abgel�st
				// $conf['where'] = "uid='4' OR uid='5' OR uid='9'";
				$conf['selectFields'] = "region";
				
				$SQLStatement = $this->cObj->getQuery('tx_mmpropman_region',$conf);
				$result = $GLOBALS['TYPO3_DB']->sql_query($SQLStatement);
		        while(($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)))
                  { 
                  	$region[] = $record['region']; 
                  	}
				  
				if(is_array($region)) $strRegions = implode(', ',$region);
				
				return $strRegions;
				break;
			default:
				return $this->internal['currentRow'][$fieldName];
			break;
		}
	}

	function getSingleViewFieldContent($fieldName)	{
		switch($fieldName)
			{
			case "title": 
				return $this->internal['currentRow'][$fieldName];
				break;
			
			default:
				return $this->getFieldContent($fieldName);
				break;
			}
		}

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
		
	/**
	 * Daten der BackendLanguagedatei werden abgeholt
	 */
	function getBL($index,$subindex = -1)
		{
		$realIndex = 'LLL:EXT:mm_propman/locallang_db.php:tx_mmpropman_data.' . $index . ($subindex != -1 ? '.I.' . $subindex : '');
		return ($this->internal['BACKEND_LANG']->sL($realIndex));		
		}
	
	/**
	 * User wird identifiziert
	 */
	function _initUserSettings()
		{
		$this->conf["isUserLoggedIn"] = false;
		unset($this->conf['user']);
		
		//debug($GLOBALS["TSFE"]->fe_user->groupData['title']);
		
		$secureConf = $this->conf['secureCheck.'];
		if(isset($GLOBALS["TSFE"]->fe_user->groupData['title'][$secureConf['baseGroupID']]) 
			&& isset($secureConf["baseGroupName"]) &&
			$GLOBALS["TSFE"]->fe_user->groupData['title'][$secureConf['baseGroupID']] == $secureConf['baseGroupName'])
			{
			$this->conf['isUserLoggedIn'] = true;
			$this->conf['user'] = $GLOBALS["TSFE"]->fe_user->user;
			}
		//debug($GLOBALS["TSFE"]->fe_user->user);
		//debug($this->conf['isUserLoggedIn']);
		//debug($GLOBALS["TSFE"]->fe_user->groupData['title'][$secureConf['baseGroupID']]);
		//debug($secureConf['baseGroupName']);		
		return $this->conf["isUserLoggedIn"];
		}
		
	function _sendInfoMail()
		{
		$secureConf = $this->conf['secureCheck.'];
		if(!$secureConf['eMailAtLogon']) return;
		
		$to      	= $secureConf['eMailAtLogon'];
		$headers 	= 'From: ' . $secureConf['eMailAtLogon'] . "\r\n" .
		   			  'Reply-To: webmaster@example.com' . "\r\n" .
		   			  'X-Mailer: PHP/' . phpversion();
		$template 			= $this->getTemplate('infomail.tmpl');
		$templateSubject 	= $this->cObj->getSubpart($template,'###SUBJECT###');
		$templateText 		= $this->cObj->getSubpart($template,'###MESSAGE###');

		// Setzen all er Users-Vars f�r das Template
		foreach($GLOBALS["TSFE"]->fe_user->user as $key => $value)
			{
			$markerArray['###' . strtoupper($key) . '###'] = $value;
			//debug("$key -> $value<br>");
			}
		// Setzen all er Record-Vars f�r das Template		
		foreach($this->internal['currentRow'] as $key=>$value)
			{
			$markerArray['###' . strtoupper($key) . '###'] = $value;
			//debug("$key -> $value<br>");
			}
		
		$subject = $this->cObj->substituteMarkerArray($templateSubject,$markerArray);
		$message = $this->cObj->substituteMarkerArray($templateText,$markerArray);

		//debug($subject . $message);
		t3lib_div::plainMailEncoded($to,$subject,$message,$headers);
		}
	
	
	/**
	 * The array ties together the static-TS configuration and the Flex Form
	 * If data is set in both areas then FlexForm settings have the priority.
	 *
	 * @return	[array]	- Information about the TS2Flex connection
	 */	
	function getFLEXConversionInfo()	
		{
		return array('secureCheck.' => array (
									'enableLogin' 			=> 'sLOGIN:enable_login',
									'useStrictLogin' 		=> 'sLOGIN:use_strict_login',
									'feUserPage' 				=> 'sLOGIN:user_pages',
									'eMailAtLogon'			=> 'sLOGIN:notification_email',
									'baseGroupID'				=> 'sLOGIN:base_group_id',
									'baseGroupName'			=> 'sLOGIN:base_group_name',
									'registrationPage'	=> 'sLOGIN:registration_page',
									),	
								'requestForm.' 	=> array (
									'enable' 						=> 'sREQUESTFORM:enable_form',
									'form.'					=> array (
										'type' 						=> 'sREQUESTFORM:type_page',
										'recipient'				=> 'sREQUESTFORM:email_recipient',
										),
									),
								'searchForm.' 	=> array (
									'search_on_top' 						=> 'sSEARCH:search_on_top',
									'search_bottom' 						=> 'sSEARCH:search_bottom',
									'form.'					=> array (
											'type' 					=> 'sSEARCH:type_page',
										),
									),
								'listView.' => array (
									'preview.'					=> array (
											'file.' 					=> array (
												'maxW'					=> 'sLISTVIEW:max_w_preview',
											),
										),
									'linkToAltPage'			=> 'sLISTVIEW:link_to_alt_page',
									'showModeSelector'		=> 'sLISTVIEW:show_mode_selector',
									'showBrowserResults'	=> 'sLISTVIEW:show_browser_results',
									'showHeader'					=> 'sLISTVIEW:show_header',
									'hide_list_view'			=> 'sLISTVIEW:hide_list_view',
									'results_at_a_time'		=> 'sLISTVIEW:results_at_a_time',
									'noItemsFoundPage'		=> 'sLISTVIEW:no_items_found_page',
									),
								'singleView.' => array (
									'backlinkToAltPage'		=> 'sSINGLEVIEW:backlink_to_alt_page',
									'images.'					=> array (
											'file.' 					=> array (
												'maxW'					=> 'sSINGLEVIEW:max_w_singleview_image',
											),
											'imageLinkWrap.'	=> array (
												'width'					=> 'sSINGLEVIEW:max_w_zoom_image',
												'height'					=> 'sSINGLEVIEW:max_h_zoom_image',
											),
										),
									),
								);			
		}
		
	/**
	 * Generate a sarch-Box. This function implements something like 
	 * pi_list_searchBox.
	 * Returns a Search box, sending search words to piVars "sword" and setting the "no_cache" 
	 * parameter as well in the form. Submits the search request to the current REQUEST_URI	 
	 *
	 * @param		[string]	Attributes for the table tag which is wrapped around the table cells containing the search box
	 *
	 * @return	[string]	Parsed form data
	 */	
   function generateSearchBox($tableParams='')     {
			$lConf 				= $this->conf["searchForm."];	// Local settings for the search function
			$lConfForm		= $lConf['form.'];
			
			//debug($lConfForm);
			$lConfForm['dataArray.'] = $this->addArrayItemsFromTSDescription($lConfForm['dataArray.']);
			//debug($lConfForm);
 			$lConfForm['dataArray.'] = $this->makePreselectionInDataArray($lConfForm['dataArray.'],$this->piVars["search"]);		
			//debug($lConfForm);
			
			
			$lConfForm['dataArray.'] = $this->getLLLablesInForm($lConfForm['dataArray.']);
			//debug($lConfForm);
 				
			$content 	= $this->cObj->FORM($lConfForm);
			
			//debug($content);
			return $this->wrapInClass($content,'propman-searchbox');
			}	
			
	/**
	 * Parse through the form-fiels and look if there is a entry in the DB or in locallang_db or in locallang
	 *
	 *	TS-Configuration:
	 *		DB:<tablename>:<labelfield>:<valuefield>
	 *		BEL:<locallang_db entry> (BEL means BackEndLanguage)
	 *		FEL:<locallang entry> (FEL means FrontEndLanguage>
	 *
	 * @param		[array]		$dataArray: 		FormFiels
	 *
	 * @return	[array]		Array with language specific entries
	 */	
	function addArrayItemsFromTSDescription(&$dataArray) {
		
		$langUID 		= $GLOBALS["TSFE"]->config["config"]["sys_language_uid"];

		foreach($dataArray as $key => $value) {
			if(is_array($value)) {
				$dataArray[$key] = $this->addArrayItemsFromTSDescription($value);
				}
			else {
				// Get Data from Database
				if(strstr($value,'DB:')) {
					list($DB,$table,$lablefield,$valuefield) = split(':',$value);
					if(isset($DB) && isset($table) && isset($lablefield) && isset($valuefield))
						{
						$queryParts['SELECT'] 		= $lablefield . ',' . $valuefield; //'uid,region';
						$queryParts['FROM'] 		= $table;
						$queryParts['GROUPBY'] 		= $lablefield;
						$queryParts['ORDERBY'] 		= $lablefield;
						//$queryParts['WHERE']		= "$table.deleted=0 AND $table.hidden=0";
						
						if ($langUID) {
							$queryParts['WHERE']	= $table . '.sys_language_uid = ' . $langUID;
						}
						else {
							$queryParts['WHERE']		= $table . '.sys_language_uid IN (0,-1)';
						}
						
						$queryParts['WHERE']		.= $this->cObj->enableFields($table);
						
						//$queryParts['WHERE'] 			= "uid='" . $aGETVars[$this->prefixId]['showUid'] . "'";
						
						//debug($queryParts);
						//$SQLStatement = $this->cObj->getQuery('tx_mmpropman_region',$conf);
						//debug($SQLStatement);
						
						$arrayCounter = 10;
						$result = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
				    while($result && ($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) { 
				    	$dataArray[$key. '.'][$arrayCounter . '.']['label'] = $record[$lablefield];
				    	$dataArray[$key. '.'][$arrayCounter . '.']['value'] = $record[$valuefield];

							//debug($key . ',' . $arrayCounter,1);
							//debug($dataArray[$key . '.'][$arrayCounter . '.']['label'] . ' -> ' . $record[$valuefield] . ' (' . $valuefield . ')',1);
							
				    	$arrayCounter += 10;
				    	}
				    unset($dataArray[$key]);
						}
					}
				// Get Data from LocalLang - Files
				if(strstr($value,'BEL:') || strstr($value,'FEL:')) {
					list($LLL,$languageIndex) = split(':',$value);
					if(isset($LLL) && isset($languageIndex)) {
						$languageIndex = str_replace('tx_mmpropman_data.','',$languageIndex);
						$arrayCounter = 10;
						$maxIndexCounter = 100;
						for($indexCounter = 0;$indexCounter < $maxIndexCounter;$indexCounter++) {
							if($LLL == 'BEL') {
								$LLItem = $this->getBL($languageIndex,$indexCounter);
								}
							else {
								$localLangIndex = $languageIndex . $indexCounter; 
								$LLItem = $this->pi_getLL($localLangIndex,'');
								}
							if($LLItem) {
					    	$dataArray[$key. '.'][$arrayCounter . '.']['label'] = $LLItem;
					    	$dataArray[$key. '.'][$arrayCounter . '.']['value'] = $indexCounter;
					    	
					    	$arrayCounter += 10;
				    		}
				    	else if($indexCounter > 0) break; // if pi_getLL returns blank than it means that thera are no more values in locallang
							}
						unset($dataArray[$key]);
						}
					}
				}
			}
		return $dataArray;
		}
		
	/**
	 * Depending on the settings of $this->piVars["search"] this function make the preselection
	 *	of the form-fields
	 *
	 *
	 * @param		[array]		$dataArray: 		FormFields
	 * @param		[array]		$preselection:	Values from $this->piVars["search"]
	 *
	 * @return	[array]		Array with language specific entries
	 */	
	function makePreselectionInDataArray(&$dataArray,$preselection) {
		foreach($dataArray as $key => $value) {
			if(is_array($value)) {
				$dataArray[$key] = $this->makePreselectionInDataArray($value,$preselection);
				}
			else {
					if($key == 'type') {
						$realTypeVar = str_replace('tx_mmpropman_pi1[search]','',$value);
						$realTypeVar = preg_replace('#=\w*#','',$realTypeVar);
						$realTypeVar = trim(preg_replace('#[[\]]#','',$realTypeVar));
						
						if(isset($preselection[$realTypeVar]) && $preselection[$realTypeVar] != -1 && isset($dataArray['valueArray.'])) {
							foreach($dataArray['valueArray.'] as $key => $value) {
								foreach($dataArray['valueArray.'][$key] as $key2 => $value2) {
									if($key2 == 'value' && $value2 == $preselection[$realTypeVar]) {
										$dataArray['valueArray.'][$key]['selected'] = 1;
										break;
										}
									}
								}
							}
						}
					}
			}
		return $dataArray;
		}
		
	/**
	 * Generates a form at the end of the detailed view.
	 *
	 * @return	[string]	- Parsed form data
	 */	
	function getRequestForm() {
		$confForm = $this->conf['requestForm.']['form.'];
		

		// die Aufrufende URL in die Form mitnehemn (merken des Types und der Objekt-ID)
		$confForm['type'] = $confForm['type'] && $confForm['type'] != 0 ? $confForm['type'] : $GLOBALS['REQUEST_URI'];
		
		// Zus�tzliche Felder im Formular
		$confForm['dataArray.']['900.']['type'] = 'showuid=hidden';
		$confForm['dataArray.']['900.']['value'] = $aGETVars['tx_mmpropman_pi1']['showUid'];
		
		$confForm['dataArray.']['901.']['type'] = 'chash=hidden';
		$confForm['dataArray.']['901.']['value'] = $aGETVars['cHash'];

		$confForm['dataArray.']['902.']['type'] = 'objnr=hidden';
		$confForm['dataArray.']['902.']['value'] = $this->getPluginTableData('objnr');		
		
		$confForm['dataArray.']['903.']['type'] = 'title=hidden';
		$confForm['dataArray.']['903.']['value'] = $this->getPluginTableData('title');		
		
		$confForm['dataArray.'] = $this->getLLLablesInForm($confForm['dataArray.']);
		
		//$confForm['hiddenFields.']['pid.']['value'] = $secureConf['feUserPage'] ? $secureConf['feUserPage'] : $aGETVars['id'];
		
		$strInfoLine = '';
		$strContent 	= $this->cObj->FORM($confForm);
		/*
		$strInfoLine 	= $this->pi_getLL("goto.registrationpage","Link zum ###LINK_REG_PAGE###Registierungsformular###LINK_REG_PAGE###");
		$strLinkText	= $this->cObj->getSubpart($strInfoLine,'###LINK_REG_PAGE###');
		$strLinkText 	= $this->pi_linkToPage($strLinkText,$secureConf['registrationPage']);
		$strInfoLine 	= $this->cObj->substituteSubpart($strInfoLine,'###LINK_REG_PAGE###',$strLinkText);
		*/
		
		$strContent .= $strInfoLine;
		
		return $this->wrapInClass($strContent,'request_form');
		}		
	
	/**
	 * Parse through the form-fiels and look if there is a entry in locallang.php
	 *
	 * @param		[array]		$dataArray: 		FormFiels
	 *
	 * @return	[array]		Array with language specific entries
	 */	
	function getLLLablesInForm($dataArray) {
		foreach($dataArray as $key => $value) {
			if(is_array($value)) {
				$dataArray[$key] = $this->getLLLablesInForm($value);
				}
			else {
				if($key == 'value' || $key == 'label') {
					if(strstr($value,'BEL:'))
						{
						$value = str_replace('BEL:','',$value);
						$dataArray[$key] = $this->getBL($value);
						}
					else {
						$value = str_replace('FEL:','',$value);
						$dataArray[$key] = $this->pi_getLL($value,$value);
						}
					}
				}
			}
		return $dataArray;
		}
		
	/**
	 * Hook-function is called from mailformplus.
	 * Extends the marker-array from MFP.
	 *
	 * @return	[array]	- Array with MFP-Markers
	 */	
	function add_mailformplus_markers($markerArray) {
		$aGETVars 														= t3lib_div::_GET();	// Commandline
		$markerArray['###value_mike_test###'] = "hallo test";
		
		// Make the caller-cObj to the current cObj
		$this->cObj = $this->pObj->cObj;
		
		// Add the markers only for a specific UID
		if(isset($aGETVars[$this->prefixId]['showUid']))	{
			
			$queryParts['SELECT'] 	= '*'; //'uid,region';
			$queryParts['FROM'] 		= 'tx_mmpropman_data';
			//$queryParts['GROUPBY'] 	= 'region';
			//$queryParts['ORDERBY'] 	= 'region';
			$queryParts['WHERE'] 		= "uid='" . $aGETVars[$this->prefixId]['showUid'] . "'";
			$queryParts['WHERE']		.= $this->cObj->enableFields($queryParts['FROM']);
			
			//$SQLStatement = $this->cObj->getQuery('tx_mmpropman_region',$conf);
			//debug($SQLStatement);
			
			// Generates the mailformplus markers
			// Sample: ###value_tx_mmpropman_pi1_title### = This is a title
			$result = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
	    if(($record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result))) { 
	    	foreach($record as $key => $value) {
			    $markerArray['###value_' . $this->prefixId . '_' . $key.'###'] = $value;		
					$markerArray['###checked_' . $this->prefixId . '_' . $key.'_'.$value.'###'] = 'checked';
					$markerArray['###selected_' . $this->prefixId . '_' . $key.'_'.$value.'###'] = 'selected';
					
	    		}
	      }
			}
			
		return $markerArray;
		}
		
	function testUSERObj($content,$conf) {
		$content = "Servus die Wadln1";
		//debug($content);
		//debug($conf);
		return $content;
		}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mm_propman/pi1/class.tx_mmpropman_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mm_propman/pi1/class.tx_mmpropman_pi1.php"]);
}

?>
