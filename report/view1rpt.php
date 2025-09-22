<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start();
error_reporting('E_ALL & ~E_NOTICE ~E_WARNING');
?>
<?php include_once "phprptinc/ewrcfg8.php" ?>
<?php include_once "phprptinc/ewmysql.php" ?>
<?php include_once "phprptinc/ewrfn8.php" ?>
<?php include_once "phprptinc/ewrusrfn8.php" ?>
<?php include_once "view1rptinfo.php" ?>
<?php

//
// Page class
//

$view1_rpt = NULL; // Initialize page object first

class crview1_rpt extends crview1 {

	// Page ID
	var $PageID = 'rpt';

	// Project ID
	var $ProjectID = "{C02CCFC4-7C8C-4DFD-B8F7-92DA7EF81CD7}";

	// Page object name
	var $PageObjName = 'view1_rpt';

	// Page name
	function PageName() {
		return ewr_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ewr_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Export URLs
	var $ExportPrintUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportPdfUrl;
	var $ReportTableClass;
	var $ReportTableStyle = "";

	// Custom export
	var $ExportPrintCustom = FALSE;
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EWR_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EWR_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EWR_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EWR_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ewr_AddMessage($_SESSION[EWR_SESSION_WARNING_MESSAGE], $v);
	}

		// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EWR_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EWR_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EWR_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EWR_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog ewDisplayTable\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") // Header exists, display
			echo $sHeader;
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") // Fotoer exists, display
			echo $sFooter;
	}

	// Validate page request
	function IsPageRequest() {
		if ($this->UseTokenInUrl) {
			if (ewr_IsHttpPost())
				return ($this->TableVar == @$_POST("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == @$_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $CheckToken = EWR_CHECK_TOKEN;
	var $CheckTokenFn = "ewr_CheckToken";
	var $CreateTokenFn = "ewr_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ewr_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EWR_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EWR_TOKEN_NAME]);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $ReportLanguage;

		// Language object
		$ReportLanguage = new crLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (view1)
		if (!isset($GLOBALS["view1"])) {
			$GLOBALS["view1"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["view1"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";

		// Page ID
		if (!defined("EWR_PAGE_ID"))
			define("EWR_PAGE_ID", 'rpt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EWR_TABLE_NAME"))
			define("EWR_TABLE_NAME", 'view1', TRUE);

		// Start timer
		$GLOBALS["gsTimer"] = new crTimer();

		// Open connection
		$conn = ewr_Connect();

		// Export options
		$this->ExportOptions = new crListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Search options
		$this->SearchOptions = new crListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Filter options
		$this->FilterOptions = new crListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fview1rpt";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $gsEmailContentType, $ReportLanguage, $Security;
		global $gsCustomExport;

		// Get export parameters
		if (@$_GET["export"] <> "")
			$this->Export = strtolower($_GET["export"]);
		elseif (@$_POST["export"] <> "")
			$this->Export = strtolower($_POST["export"]);
		$gsExport = $this->Export; // Get export parameter, used in header
		$gsExportFile = $this->TableVar; // Get export file, used in header
		$gsEmailContentType = @$_POST["contenttype"]; // Get email content type

		// Setup placeholder
		$this->ngaykt->PlaceHolder = $this->ngaykt->FldCaption();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $ReportLanguage->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Create Token
		$this->CreateToken();
	}

	// Set up export options
	function SetupExportOptions() {
		global $ReportLanguage;
		$exportid = session_id();

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("PrinterFriendly", TRUE)) . "\" href=\"" . $this->ExportPrintUrl . "\">" . $ReportLanguage->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToExcel", TRUE)) . "\" href=\"" . $this->ExportExcelUrl . "\">" . $ReportLanguage->Phrase("ExportToExcel") . "</a>";
		$item->Visible = FALSE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToWord", TRUE)) . "\" href=\"" . $this->ExportWordUrl . "\">" . $ReportLanguage->Phrase("ExportToWord") . "</a>";

		//$item->Visible = TRUE;
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"" . $this->ExportPdfUrl . "\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Uncomment codes below to show export to Pdf link
//		$item->Visible = TRUE;
		// Export to Email

		$item = &$this->ExportOptions->Add("email");
		$url = $this->PageUrl() . "export=email";
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToEmail", TRUE)) . "\" id=\"emf_view1\" href=\"javascript:void(0);\" onclick=\"ewr_EmailDialogShow({lnk:'emf_view1',hdr:ewLanguage.Phrase('ExportToEmail'),url:'$url',exportid:'$exportid',el:this});\">" . $ReportLanguage->Phrase("ExportToEmail") . "</a>";
		$item->Visible = FALSE;

		// Drop down button for export
		$this->ExportOptions->UseDropDownButton = FALSE;
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = $this->ExportOptions->UseDropDownButton;
		$this->ExportOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter panel button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = " active";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-caption=\"" . $ReportLanguage->Phrase("SearchBtn", TRUE) . "\" data-toggle=\"button\" data-form=\"fview1rpt\">" . $ReportLanguage->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Reset filter
		$item = &$this->SearchOptions->Add("resetfilter");
		$item->Body = "<button type=\"button\" class=\"btn btn-default\" title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ResetAllFilter", TRUE)) . "\" onclick=\"location='" . ewr_CurrentPage() . "?cmd=reset'\">" . $ReportLanguage->Phrase("ResetAllFilter") . "</button>";
		$item->Visible = TRUE;

		// Button group for reset filter
		$this->SearchOptions->UseButtonGroup = TRUE;

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fview1rpt\" href=\"#\">" . $ReportLanguage->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fview1rpt\" href=\"#\">" . $ReportLanguage->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton; // v8
		$this->FilterOptions->DropDownButtonPhrase = $ReportLanguage->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Set up options (extended)
		$this->SetupExportOptionsExt();

		// Hide options for export
		if ($this->Export <> "") {
			$this->ExportOptions->HideAllOptions();
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Set up table class
		if ($this->Export == "word" || $this->Export == "excel" || $this->Export == "pdf")
			$this->ReportTableClass = "ewTable";
		else
			$this->ReportTableClass = "table ewTable";
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn, $ReportLanguage, $EWR_EXPORT, $gsExportFile;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		if ($this->Export <> "" && array_key_exists($this->Export, $EWR_EXPORT)) {
			$sContent = ob_get_contents();

			// Remove all <div data-tagid="..." id="orig..." class="hide">...</div> (for customviewtag export, except "googlemaps")
			if (preg_match_all('/<div\s+data-tagid=[\'"]([\s\S]*?)[\'"]\s+id=[\'"]orig([\s\S]*?)[\'"]\s+class\s*=\s*[\'"]hide[\'"]>([\s\S]*?)<\/div\s*>/i', $sContent, $divmatches, PREG_SET_ORDER)) {
				foreach ($divmatches as $divmatch) {
					if ($divmatch[1] <> "googlemaps")
						$sContent = str_replace($divmatch[0], '', $sContent);
				}
			}
			$fn = $EWR_EXPORT[$this->Export];
			if ($this->Export == "email") { // Email
				ob_end_clean();
				echo $this->$fn($sContent);
				$conn->Close(); // Close connection
				exit();
			} else {
				$this->$fn($sContent);
			}
		}

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EWR_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Initialize common variables
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $FilterOptions; // Filter options

	// Paging variables
	var $RecIndex = 0; // Record index
	var $RecCount = 0; // Record count
	var $StartGrp = 0; // Start group
	var $StopGrp = 0; // Stop group
	var $TotalGrps = 0; // Total groups
	var $GrpCount = 0; // Group count
	var $GrpCounter = array(); // Group counter
	var $DisplayGrps = 3; // Groups per page
	var $GrpRange = 10;
	var $Sort = "";
	var $Filter = "";
	var $PageFirstGroupFilter = "";
	var $UserIDFilter = "";
	var $DrillDown = FALSE;
	var $DrillDownInPanel = FALSE;
	var $DrillDownList = "";

	// Clear field for ext filter
	var $ClearExtFilter = "";
	var $PopupName = "";
	var $PopupValue = "";
	var $FilterApplied;
	var $SearchCommand = FALSE;
	var $ShowHeader;
	var $GrpFldCount = 0;
	var $SubGrpFldCount = 0;
	var $DtlFldCount = 0;
	var $Cnt, $Col, $Val, $Smry, $Mn, $Mx, $GrandCnt, $GrandSmry, $GrandMn, $GrandMx;
	var $TotCount;
	var $GrandSummarySetup = FALSE;
	var $GrpIdx;

	//
	// Page main
	//
	function Page_Main() {
		global $rs;
		global $rsgrp;
		global $gsFormError;
		global $gbDrillDownInPanel;
		global $ReportBreadcrumb;

		// Aggregate variables
		// 1st dimension = no of groups (level 0 used for grand total)
		// 2nd dimension = no of fields

		$nDtls = 11;
		$nGrps = 1;
		$this->Val = &ewr_InitArray($nDtls, 0);
		$this->Cnt = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Smry = &ewr_Init2DArray($nGrps, $nDtls, 0);
		$this->Mn = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->Mx = &ewr_Init2DArray($nGrps, $nDtls, NULL);
		$this->GrandCnt = &ewr_InitArray($nDtls, 0);
		$this->GrandSmry = &ewr_InitArray($nDtls, 0);
		$this->GrandMn = &ewr_InitArray($nDtls, NULL);
		$this->GrandMx = &ewr_InitArray($nDtls, NULL);

		// Set up array if accumulation required: array(Accum, SkipNullOrZero)
		$this->Col = array(array(FALSE, FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE), array(FALSE,FALSE));

		// Set up groups per page dynamically
		$this->SetUpDisplayGrps();

		// Set up Breadcrumb
		if ($this->Export == "")
			$this->SetupBreadcrumb();

		// Check if search command
		$this->SearchCommand = (@$_GET["cmd"] == "search");

		// Load default filter values
		$this->LoadDefaultFilters();

		// Load custom filters
		$this->Page_FilterLoad();

		// Set up popup filter
		$this->SetupPopup();

		// Load group db values if necessary
		$this->LoadGroupDbValues();

		// Handle Ajax popup
		$this->ProcessAjaxPopup();

		// Extended filter
		$sExtendedFilter = "";

		// Restore filter list
		$this->RestoreFilterList();

		// Build extended filter
		$sExtendedFilter = $this->GetExtendedFilter();
		ewr_AddFilter($this->Filter, $sExtendedFilter);

		// Build popup filter
		$sPopupFilter = $this->GetPopupFilter();

		//ewr_SetDebugMsg("popup filter: " . $sPopupFilter);
		ewr_AddFilter($this->Filter, $sPopupFilter);

		// Check if filter applied
		$this->FilterApplied = $this->CheckFilter();

		// Call Page Selecting event
		$this->Page_Selecting($this->Filter);
		$this->SearchOptions->GetItem("resetfilter")->Visible = $this->FilterApplied;

		// Get sort
		$this->Sort = $this->GetSort();

		// Get total count
		$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(), $this->Filter, $this->Sort);
		$this->TotalGrps = $this->GetCnt($sSql);
		if ($this->DisplayGrps <= 0 || $this->DrillDown) // Display all groups
			$this->DisplayGrps = $this->TotalGrps;
		$this->StartGrp = 1;

		// Show header
		$this->ShowHeader = ($this->TotalGrps > 0);

		// Set up start position if not export all
		if ($this->ExportAll && $this->Export <> "")
		    $this->DisplayGrps = $this->TotalGrps;
		else
			$this->SetUpStartGroup(); 

		// Hide export options if export
		if ($this->Export <> "")
			$this->ExportOptions->HideAllOptions();

		// Hide search/filter options if export/drilldown
		if ($this->Export <> "" || $this->DrillDown) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}

		// Get current page records
		$rs = $this->GetRs($sSql, $this->StartGrp, $this->DisplayGrps);
		$this->SetupFieldCount();
	}

	// Accummulate summary
	function AccumulateSummary() {
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				if ($this->Col[$iy][0]) { // Accumulate required
					$valwrk = $this->Val[$iy];
					if (is_null($valwrk)) {
						if (!$this->Col[$iy][1])
							$this->Cnt[$ix][$iy]++;
					} else {
						$accum = (!$this->Col[$iy][1] || !is_numeric($valwrk) || $valwrk <> 0);
						if ($accum) {
							$this->Cnt[$ix][$iy]++;
							if (is_numeric($valwrk)) {
								$this->Smry[$ix][$iy] += $valwrk;
								if (is_null($this->Mn[$ix][$iy])) {
									$this->Mn[$ix][$iy] = $valwrk;
									$this->Mx[$ix][$iy] = $valwrk;
								} else {
									if ($this->Mn[$ix][$iy] > $valwrk) $this->Mn[$ix][$iy] = $valwrk;
									if ($this->Mx[$ix][$iy] < $valwrk) $this->Mx[$ix][$iy] = $valwrk;
								}
							}
						}
					}
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = 0; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0]++;
		}
	}

	// Reset level summary
	function ResetLevelSummary($lvl) {

		// Clear summary values
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$cnty = count($this->Smry[$ix]);
			for ($iy = 1; $iy < $cnty; $iy++) {
				$this->Cnt[$ix][$iy] = 0;
				if ($this->Col[$iy][0]) {
					$this->Smry[$ix][$iy] = 0;
					$this->Mn[$ix][$iy] = NULL;
					$this->Mx[$ix][$iy] = NULL;
				}
			}
		}
		$cntx = count($this->Smry);
		for ($ix = $lvl; $ix < $cntx; $ix++) {
			$this->Cnt[$ix][0] = 0;
		}

		// Reset record count
		$this->RecCount = 0;
	}

	// Accummulate grand summary
	function AccumulateGrandSummary() {
		$this->TotCount++;
		$cntgs = count($this->GrandSmry);
		for ($iy = 1; $iy < $cntgs; $iy++) {
			if ($this->Col[$iy][0]) {
				$valwrk = $this->Val[$iy];
				if (is_null($valwrk) || !is_numeric($valwrk)) {
					if (!$this->Col[$iy][1])
						$this->GrandCnt[$iy]++;
				} else {
					if (!$this->Col[$iy][1] || $valwrk <> 0) {
						$this->GrandCnt[$iy]++;
						$this->GrandSmry[$iy] += $valwrk;
						if (is_null($this->GrandMn[$iy])) {
							$this->GrandMn[$iy] = $valwrk;
							$this->GrandMx[$iy] = $valwrk;
						} else {
							if ($this->GrandMn[$iy] > $valwrk) $this->GrandMn[$iy] = $valwrk;
							if ($this->GrandMx[$iy] < $valwrk) $this->GrandMx[$iy] = $valwrk;
						}
					}
				}
			}
		}
	}

	// Get count
	function GetCnt($sql) {
		global $conn;
		$rscnt = $conn->Execute($sql);
		$cnt = ($rscnt) ? $rscnt->RecordCount() : 0;
		if ($rscnt) $rscnt->Close();
		return $cnt;
	}

	// Get recordset
	function GetRs($wrksql, $start, $grps) {
		global $conn;
		$conn->raiseErrorFn = $GLOBALS["EWR_ERROR_FN"];
		$rswrk = $conn->SelectLimit($wrksql, $grps, $start - 1);
		$conn->raiseErrorFn = '';
		return $rswrk;
	}

	// Get row values
	function GetRow($opt) {
		global $rs;
		if (!$rs)
			return;
		if ($opt == 1) { // Get first row

	//		$rs->MoveFirst(); // NOTE: no need to move position
				$this->FirstRowData = array();
				$this->FirstRowData['maql'] = ewr_Conv($rs->fields('maql'),200);
				$this->FirstRowData['hoso'] = ewr_Conv($rs->fields('hoso'),200);
				$this->FirstRowData['mavt'] = ewr_Conv($rs->fields('mavt'),200);
				$this->FirstRowData['somay'] = ewr_Conv($rs->fields('somay'),200);
				$this->FirstRowData['ngayth'] = ewr_Conv($rs->fields('ngayth'),135);
				$this->FirstRowData['ngaykt'] = ewr_Conv($rs->fields('ngaykt'),135);
				$this->FirstRowData['stt'] = ewr_Conv($rs->fields('stt'),3);
				$this->FirstRowData['ngayyc'] = ewr_Conv($rs->fields('ngayyc'),133);
				$this->FirstRowData['madv'] = ewr_Conv($rs->fields('madv'),200);
				$this->FirstRowData['phieu'] = ewr_Conv($rs->fields('phieu'),3);
				$this->FirstRowData['solg'] = ewr_Conv($rs->fields('solg'),3);
				$this->FirstRowData['ngyeucau'] = ewr_Conv($rs->fields('ngyeucau'),200);
				$this->FirstRowData['ngnhyeucau'] = ewr_Conv($rs->fields('ngnhyeucau'),200);
				$this->FirstRowData['tbdosc'] = ewr_Conv($rs->fields('tbdosc'),200);
				$this->FirstRowData['serialtbdosc'] = ewr_Conv($rs->fields('serialtbdosc'),200);
				$this->FirstRowData['tbdosc1'] = ewr_Conv($rs->fields('tbdosc1'),200);
				$this->FirstRowData['serialtbdosc1'] = ewr_Conv($rs->fields('serialtbdosc1'),200);
				$this->FirstRowData['tbdosc2'] = ewr_Conv($rs->fields('tbdosc2'),200);
				$this->FirstRowData['serialtbdosc2'] = ewr_Conv($rs->fields('serialtbdosc2'),200);
				$this->FirstRowData['serialtbdosc3'] = ewr_Conv($rs->fields('serialtbdosc3'),200);
				$this->FirstRowData['tbdosc3'] = ewr_Conv($rs->fields('tbdosc3'),200);
				$this->FirstRowData['tbdosc4'] = ewr_Conv($rs->fields('tbdosc4'),200);
				$this->FirstRowData['serialtbdosc4'] = ewr_Conv($rs->fields('serialtbdosc4'),200);
				$this->FirstRowData['nhomsc'] = ewr_Conv($rs->fields('nhomsc'),200);
				$this->FirstRowData['bg'] = ewr_Conv($rs->fields('bg'),200);
		} else { // Get next row
			$rs->MoveNext();
		}
		if (!$rs->EOF) {
			$this->maql->setDbValue($rs->fields('maql'));
			$this->hoso->setDbValue($rs->fields('hoso'));
			$this->mavt->setDbValue($rs->fields('mavt'));
			$this->somay->setDbValue($rs->fields('somay'));
			$this->cv->setDbValue($rs->fields('cv'));
			$this->ngayth->setDbValue($rs->fields('ngayth'));
			$this->ngaykt->setDbValue($rs->fields('ngaykt'));
			$this->ttktafter->setDbValue($rs->fields('ttktafter'));
			$this->ghichu->setDbValue($rs->fields('ghichu'));
			$this->stt->setDbValue($rs->fields('stt'));
			$this->ngayyc->setDbValue($rs->fields('ngayyc'));
			$this->madv->setDbValue($rs->fields('madv'));
			$this->phieu->setDbValue($rs->fields('phieu'));
			$this->solg->setDbValue($rs->fields('solg'));
			$this->ngyeucau->setDbValue($rs->fields('ngyeucau'));
			$this->ngnhyeucau->setDbValue($rs->fields('ngnhyeucau'));
			$this->ttktbefore->setDbValue($rs->fields('ttktbefore'));
			$this->honghoc->setDbValue($rs->fields('honghoc'));
			$this->khacphuc->setDbValue($rs->fields('khacphuc'));
			$this->tbdosc->setDbValue($rs->fields('tbdosc'));
			$this->serialtbdosc->setDbValue($rs->fields('serialtbdosc'));
			$this->tbdosc1->setDbValue($rs->fields('tbdosc1'));
			$this->serialtbdosc1->setDbValue($rs->fields('serialtbdosc1'));
			$this->tbdosc2->setDbValue($rs->fields('tbdosc2'));
			$this->serialtbdosc2->setDbValue($rs->fields('serialtbdosc2'));
			$this->serialtbdosc3->setDbValue($rs->fields('serialtbdosc3'));
			$this->tbdosc3->setDbValue($rs->fields('tbdosc3'));
			$this->tbdosc4->setDbValue($rs->fields('tbdosc4'));
			$this->serialtbdosc4->setDbValue($rs->fields('serialtbdosc4'));
			$this->nhomsc->setDbValue($rs->fields('nhomsc'));
			$this->bg->setDbValue($rs->fields('bg'));
			$this->Val[1] = $this->maql->CurrentValue;
			$this->Val[2] = $this->hoso->CurrentValue;
			$this->Val[3] = $this->mavt->CurrentValue;
			$this->Val[4] = $this->somay->CurrentValue;
			$this->Val[5] = $this->cv->CurrentValue;
			$this->Val[6] = $this->ngayth->CurrentValue;
			$this->Val[7] = $this->ngaykt->CurrentValue;
			$this->Val[8] = $this->nhomsc->CurrentValue;
			$this->Val[9] = $this->ttktafter->CurrentValue;
			$this->Val[10] = $this->ghichu->CurrentValue;
		} else {
			$this->maql->setDbValue("");
			$this->hoso->setDbValue("");
			$this->mavt->setDbValue("");
			$this->somay->setDbValue("");
			$this->cv->setDbValue("");
			$this->ngayth->setDbValue("");
			$this->ngaykt->setDbValue("");
			$this->ttktafter->setDbValue("");
			$this->ghichu->setDbValue("");
			$this->stt->setDbValue("");
			$this->ngayyc->setDbValue("");
			$this->madv->setDbValue("");
			$this->phieu->setDbValue("");
			$this->solg->setDbValue("");
			$this->ngyeucau->setDbValue("");
			$this->ngnhyeucau->setDbValue("");
			$this->ttktbefore->setDbValue("");
			$this->honghoc->setDbValue("");
			$this->khacphuc->setDbValue("");
			$this->tbdosc->setDbValue("");
			$this->serialtbdosc->setDbValue("");
			$this->tbdosc1->setDbValue("");
			$this->serialtbdosc1->setDbValue("");
			$this->tbdosc2->setDbValue("");
			$this->serialtbdosc2->setDbValue("");
			$this->serialtbdosc3->setDbValue("");
			$this->tbdosc3->setDbValue("");
			$this->tbdosc4->setDbValue("");
			$this->serialtbdosc4->setDbValue("");
			$this->nhomsc->setDbValue("");
			$this->bg->setDbValue("");
		}
	}

	//  Set up starting group
	function SetUpStartGroup() {

		// Exit if no groups
		if ($this->DisplayGrps == 0)
			return;

		// Check for a 'start' parameter
		if (@$_GET[EWR_TABLE_START_GROUP] != "") {
			$this->StartGrp = $_GET[EWR_TABLE_START_GROUP];
			$this->setStartGroup($this->StartGrp);
		} elseif (@$_GET["pageno"] != "") {
			$nPageNo = $_GET["pageno"];
			if (is_numeric($nPageNo)) {
				$this->StartGrp = ($nPageNo-1)*$this->DisplayGrps+1;
				if ($this->StartGrp <= 0) {
					$this->StartGrp = 1;
				} elseif ($this->StartGrp >= intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1) {
					$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps)*$this->DisplayGrps+1;
				}
				$this->setStartGroup($this->StartGrp);
			} else {
				$this->StartGrp = $this->getStartGroup();
			}
		} else {
			$this->StartGrp = $this->getStartGroup();
		}

		// Check if correct start group counter
		if (!is_numeric($this->StartGrp) || $this->StartGrp == "") { // Avoid invalid start group counter
			$this->StartGrp = 1; // Reset start group counter
			$this->setStartGroup($this->StartGrp);
		} elseif (intval($this->StartGrp) > intval($this->TotalGrps)) { // Avoid starting group > total groups
			$this->StartGrp = intval(($this->TotalGrps-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to last page first group
			$this->setStartGroup($this->StartGrp);
		} elseif (($this->StartGrp-1) % $this->DisplayGrps <> 0) {
			$this->StartGrp = intval(($this->StartGrp-1)/$this->DisplayGrps) * $this->DisplayGrps + 1; // Point to page boundary
			$this->setStartGroup($this->StartGrp);
		}
	}

	// Load group db values if necessary
	function LoadGroupDbValues() {
		global $conn;
	}

	// Process Ajax popup
	function ProcessAjaxPopup() {
		global $conn, $ReportLanguage;
		$fld = NULL;
		if (@$_GET["popup"] <> "") {
			$popupname = $_GET["popup"];

			// Check popup name
			// Output data as Json

			if (!is_null($fld)) {
				$jsdb = ewr_GetJsDb($fld, $fld->FldType);
				ob_end_clean();
				echo $jsdb;
				exit();
			}
		}
	}

	// Set up popup
	function SetupPopup() {
		global $conn, $ReportLanguage;
		if ($this->DrillDown)
			return;

		// Process post back form
		if (ewr_IsHttpPost()) {
			$sName = @$_POST["popup"]; // Get popup form name
			if ($sName <> "") {
				$cntValues = (is_array(@$_POST["sel_$sName"])) ? count($_POST["sel_$sName"]) : 0;
				if ($cntValues > 0) {
					$arValues = ewr_StripSlashes($_POST["sel_$sName"]);
					if (trim($arValues[0]) == "") // Select all
						$arValues = EWR_INIT_VALUE;
					$this->PopupName = $sName;
					if (ewr_IsAdvancedFilterValue($arValues) || $arValues == EWR_INIT_VALUE)
						$this->PopupValue = $arValues;
					if (!ewr_MatchedArray($arValues, $_SESSION["sel_$sName"])) {
						if ($this->HasSessionFilterValues($sName))
							$this->ClearExtFilter = $sName; // Clear extended filter for this field
					}
					$_SESSION["sel_$sName"] = $arValues;
					$_SESSION["rf_$sName"] = ewr_StripSlashes(@$_POST["rf_$sName"]);
					$_SESSION["rt_$sName"] = ewr_StripSlashes(@$_POST["rt_$sName"]);
					$this->ResetPager();
				}
			}

		// Get 'reset' command
		} elseif (@$_GET["cmd"] <> "") {
			$sCmd = $_GET["cmd"];
			if (strtolower($sCmd) == "reset") {
				$this->ResetPager();
			}
		}

		// Load selection criteria to array
	}

	// Reset pager
	function ResetPager() {

		// Reset start position (reset command)
		$this->StartGrp = 1;
		$this->setStartGroup($this->StartGrp);
	}

	// Set up number of groups displayed per page
	function SetUpDisplayGrps() {
		$sWrk = @$_GET[EWR_TABLE_GROUP_PER_PAGE];
		if ($sWrk <> "") {
			if (is_numeric($sWrk)) {
				$this->DisplayGrps = intval($sWrk);
			} else {
				if (strtoupper($sWrk) == "ALL") { // Display all groups
					$this->DisplayGrps = -1;
				} else {
					$this->DisplayGrps = 3; // Non-numeric, load default
				}
			}
			$this->setGroupPerPage($this->DisplayGrps); // Save to session

			// Reset start position (reset command)
			$this->StartGrp = 1;
			$this->setStartGroup($this->StartGrp);
		} else {
			if ($this->getGroupPerPage() <> "") {
				$this->DisplayGrps = $this->getGroupPerPage(); // Restore from session
			} else {
				$this->DisplayGrps = 3; // Load default
			}
		}
	}

	// Render row
	function RenderRow() {
		global $conn, $rs, $Security, $ReportLanguage;
		if ($this->RowTotalType == EWR_ROWTOTAL_GRAND && !$this->GrandSummarySetup) { // Grand total
			$bGotCount = FALSE;
			$bGotSummary = FALSE;

			// Get total count from sql directly
			$sSql = ewr_BuildReportSql($this->getSqlSelectCount(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
			$rstot = $conn->Execute($sSql);
			if ($rstot) {
				$this->TotCount = ($rstot->RecordCount()>1) ? $rstot->RecordCount() : $rstot->fields[0];
				$rstot->Close();
				$bGotCount = TRUE;
			} else {
				$this->TotCount = 0;
			}
		$bGotSummary = TRUE;

			// Accumulate grand summary from detail records
			if (!$bGotCount || !$bGotSummary) {
				$sSql = ewr_BuildReportSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(), $this->getSqlHaving(), "", $this->Filter, "");
				$rs = $conn->Execute($sSql);
				if ($rs) {
					$this->GetRow(1);
					while (!$rs->EOF) {
						$this->AccumulateGrandSummary();
						$this->GetRow(2);
					}
					$rs->Close();
				}
			}
			$this->GrandSummarySetup = TRUE; // No need to set up again
		}

		// Call Row_Rendering event
		$this->Row_Rendering();

		//
		// Render view codes
		//

		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row

			// maql
			$this->maql->HrefValue = "";

			// hoso
			$this->hoso->HrefValue = "";

			// mavt
			$this->mavt->HrefValue = "";

			// somay
			$this->somay->HrefValue = "";

			// cv
			$this->cv->HrefValue = "";

			// ngayth
			$this->ngayth->HrefValue = "";

			// ngaykt
			$this->ngaykt->HrefValue = "";

			// nhomsc
			$this->nhomsc->HrefValue = "";

			// ttktafter
			$this->ttktafter->HrefValue = "";

			// ghichu
			$this->ghichu->HrefValue = "";
		} else {

			// maql
			$this->maql->ViewValue = $this->maql->CurrentValue;
			$this->maql->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// hoso
			$this->hoso->ViewValue = $this->hoso->CurrentValue;
			$this->hoso->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// mavt
			$this->mavt->ViewValue = $this->mavt->CurrentValue;
			$this->mavt->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// somay
			$this->somay->ViewValue = $this->somay->CurrentValue;
			$this->somay->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// cv
			$this->cv->ViewValue = $this->cv->CurrentValue;
			$this->cv->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ngayth
			$this->ngayth->ViewValue = $this->ngayth->CurrentValue;
			$this->ngayth->ViewValue = ewr_FormatDateTime($this->ngayth->ViewValue, 7);
			$this->ngayth->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ngaykt
			$this->ngaykt->ViewValue = $this->ngaykt->CurrentValue;
			$this->ngaykt->ViewValue = ewr_FormatDateTime($this->ngaykt->ViewValue, 7);
			$this->ngaykt->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ngth
			//$this->ngth->ViewValue = $this->ngth->CurrentValue;
			//$this->ngth->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";
			// nhomsc
			$this->nhomsc->ViewValue = $this->nhomsc->CurrentValue;
			$this->nhomsc->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ttktafter
			$this->ttktafter->ViewValue = $this->ttktafter->CurrentValue;
			$this->ttktafter->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// ghichu
			$this->ghichu->ViewValue = $this->ghichu->CurrentValue;
			$this->ghichu->CellAttrs["class"] = ($this->RecCount % 2 <> 1) ? "ewTableAltRow" : "ewTableRow";

			// maql
			$this->maql->HrefValue = "";

			// hoso
			$this->hoso->HrefValue = "";

			// mavt
			$this->mavt->HrefValue = "";

			// somay
			$this->somay->HrefValue = "";

			// cv
			$this->cv->HrefValue = "";

			// ngayth
			$this->ngayth->HrefValue = "";

			// ngaykt
			$this->ngaykt->HrefValue = "";

			//nhomsc
			$this->nhomsc->HrefValue = "";

			// ttktafter
			$this->ttktafter->HrefValue = "";

			// ghichu
			$this->ghichu->HrefValue = "";
		}

		// Call Cell_Rendered event
		if ($this->RowType == EWR_ROWTYPE_TOTAL) { // Summary row
		} else {

			// maql
			$CurrentValue = $this->maql->CurrentValue;
			$ViewValue = &$this->maql->ViewValue;
			$ViewAttrs = &$this->maql->ViewAttrs;
			$CellAttrs = &$this->maql->CellAttrs;
			$HrefValue = &$this->maql->HrefValue;
			$LinkAttrs = &$this->maql->LinkAttrs;
			$this->Cell_Rendered($this->maql, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// hoso
			$CurrentValue = $this->hoso->CurrentValue;
			$ViewValue = &$this->hoso->ViewValue;
			$ViewAttrs = &$this->hoso->ViewAttrs;
			$CellAttrs = &$this->hoso->CellAttrs;
			$HrefValue = &$this->hoso->HrefValue;
			$LinkAttrs = &$this->hoso->LinkAttrs;
			$this->Cell_Rendered($this->hoso, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// mavt
			$CurrentValue = $this->mavt->CurrentValue;
			$ViewValue = &$this->mavt->ViewValue;
			$ViewAttrs = &$this->mavt->ViewAttrs;
			$CellAttrs = &$this->mavt->CellAttrs;
			$HrefValue = &$this->mavt->HrefValue;
			$LinkAttrs = &$this->mavt->LinkAttrs;
			$this->Cell_Rendered($this->mavt, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// somay
			$CurrentValue = $this->somay->CurrentValue;
			$ViewValue = &$this->somay->ViewValue;
			$ViewAttrs = &$this->somay->ViewAttrs;
			$CellAttrs = &$this->somay->CellAttrs;
			$HrefValue = &$this->somay->HrefValue;
			$LinkAttrs = &$this->somay->LinkAttrs;
			$this->Cell_Rendered($this->somay, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// cv
			$CurrentValue = $this->cv->CurrentValue;
			$ViewValue = &$this->cv->ViewValue;
			$ViewAttrs = &$this->cv->ViewAttrs;
			$CellAttrs = &$this->cv->CellAttrs;
			$HrefValue = &$this->cv->HrefValue;
			$LinkAttrs = &$this->cv->LinkAttrs;
			$this->Cell_Rendered($this->cv, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ngayth
			$CurrentValue = $this->ngayth->CurrentValue;
			$ViewValue = &$this->ngayth->ViewValue;
			$ViewAttrs = &$this->ngayth->ViewAttrs;
			$CellAttrs = &$this->ngayth->CellAttrs;
			$HrefValue = &$this->ngayth->HrefValue;
			$LinkAttrs = &$this->ngayth->LinkAttrs;
			$this->Cell_Rendered($this->ngayth, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ngaykt
			$CurrentValue = $this->ngaykt->CurrentValue;
			$ViewValue = &$this->ngaykt->ViewValue;
			$ViewAttrs = &$this->ngaykt->ViewAttrs;
			$CellAttrs = &$this->ngaykt->CellAttrs;
			$HrefValue = &$this->ngaykt->HrefValue;
			$LinkAttrs = &$this->ngaykt->LinkAttrs;
			$this->Cell_Rendered($this->ngaykt, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
/*
			// ngth
			$CurrentValue = $this->ngth->CurrentValue;
			$ViewValue = &$this->ngth->ViewValue;
			$ViewAttrs = &$this->ngth->ViewAttrs;
			$CellAttrs = &$this->ngth->CellAttrs;
			$HrefValue = &$this->ngth->HrefValue;
			$LinkAttrs = &$this->ngth->LinkAttrs;
                    	$this->Cell_Rendered($this->ngth, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
 */
			// nhomsc
			$CurrentValue = $this->nhomsc->CurrentValue;
			$ViewValue = &$this->nhomsc->ViewValue;
			$ViewAttrs = &$this->nhomsc->ViewAttrs;
			$CellAttrs = &$this->nhomsc->CellAttrs;
			$HrefValue = &$this->nhomsc->HrefValue;
			$LinkAttrs = &$this->nhomsc->LinkAttrs;
                    	$this->Cell_Rendered($this->nhomsc, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ttktafter
			$CurrentValue = $this->ttktafter->CurrentValue;
			$ViewValue = &$this->ttktafter->ViewValue;
			$ViewAttrs = &$this->ttktafter->ViewAttrs;
			$CellAttrs = &$this->ttktafter->CellAttrs;
			$HrefValue = &$this->ttktafter->HrefValue;
			$LinkAttrs = &$this->ttktafter->LinkAttrs;
			$this->Cell_Rendered($this->ttktafter, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);

			// ghichu
			$CurrentValue = $this->ghichu->CurrentValue;
			$ViewValue = &$this->ghichu->ViewValue;
			$ViewAttrs = &$this->ghichu->ViewAttrs;
			$CellAttrs = &$this->ghichu->CellAttrs;
			$HrefValue = &$this->ghichu->HrefValue;
			$LinkAttrs = &$this->ghichu->LinkAttrs;
			$this->Cell_Rendered($this->ghichu, $CurrentValue, $ViewValue, $ViewAttrs, $CellAttrs, $HrefValue, $LinkAttrs);
		}

		// Call Row_Rendered event
		$this->Row_Rendered();
		$this->SetupFieldCount();
	}

	// Setup field count
	function SetupFieldCount() {
		$this->GrpFldCount = 0;
		$this->SubGrpFldCount = 0;
		$this->DtlFldCount = 0;
		if ($this->maql->Visible) $this->DtlFldCount += 1;
		if ($this->hoso->Visible) $this->DtlFldCount += 1;
		if ($this->mavt->Visible) $this->DtlFldCount += 1;
		if ($this->somay->Visible) $this->DtlFldCount += 1;
		if ($this->cv->Visible) $this->DtlFldCount += 1;
		if ($this->ngayth->Visible) $this->DtlFldCount += 1;
		if ($this->ngaykt->Visible) $this->DtlFldCount += 1;
		if ($this->nhomsc->Visible) $this->DtlFldCount += 1;
		if ($this->ttktafter->Visible) $this->DtlFldCount += 1;
		if ($this->ghichu->Visible) $this->DtlFldCount += 1;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $ReportBreadcrumb;
		$ReportBreadcrumb = new crBreadcrumb();
		$url = substr(ewr_CurrentUrl(), strrpos(ewr_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$ReportBreadcrumb->Add("rpt", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	function SetupExportOptionsExt() {
		global $ReportLanguage;
		$item =& $this->ExportOptions->GetItem("pdf");
		$item->Visible = TRUE;
		$exportid = session_id();
		$url = $this->ExportPdfUrl;
		$item->Body = "<a title=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" data-caption=\"" . ewr_HtmlEncode($ReportLanguage->Phrase("ExportToPDF", TRUE)) . "\" href=\"javascript:void(0);\" onclick=\"ewr_ExportCharts(this, '" . $url . "', '" . $exportid . "');\">" . $ReportLanguage->Phrase("ExportToPDF") . "</a>";
	}

	// Return extended filter
	function GetExtendedFilter() {
		global $gsFormError;
		$sFilter = "";
		if ($this->DrillDown)
			return "";
		$bPostBack = ewr_IsHttpPost();
		$bRestoreSession = TRUE;
		$bSetupFilter = FALSE;

		// Reset extended filter if filter changed
		if ($bPostBack) {

		// Reset search command
		} elseif (@$_GET["cmd"] == "reset") {

			// Load default values
			$this->SetSessionFilterValues($this->ngaykt->SearchValue, $this->ngaykt->SearchOperator, $this->ngaykt->SearchCondition, $this->ngaykt->SearchValue2, $this->ngaykt->SearchOperator2, 'ngaykt'); // Field ngaykt

			//$bSetupFilter = TRUE; // No need to set up, just use default
		} else {
			$bRestoreSession = !$this->SearchCommand;

			// Field ngaykt
			if ($this->GetFilterValues($this->ngaykt)) {
				$bSetupFilter = TRUE;
			}
			if (!$this->ValidateForm()) {
				$this->setFailureMessage($gsFormError);
				return $sFilter;
			}
		}

		// Restore session
		if ($bRestoreSession) {
			$this->GetSessionFilterValues($this->ngaykt); // Field ngaykt
		}

		// Call page filter validated event
		$this->Page_FilterValidated();

		// Build SQL
		$this->BuildExtendedFilter($this->ngaykt, $sFilter, FALSE, TRUE); // Field ngaykt

		// Save parms to session
		$this->SetSessionFilterValues($this->ngaykt->SearchValue, $this->ngaykt->SearchOperator, $this->ngaykt->SearchCondition, $this->ngaykt->SearchValue2, $this->ngaykt->SearchOperator2, 'ngaykt'); // Field ngaykt

		// Setup filter
		if ($bSetupFilter) {
		}
		return $sFilter;
	}

	// Build dropdown filter
	function BuildDropDownFilter(&$fld, &$FilterClause, $FldOpr, $Default = FALSE, $SaveFilter = FALSE) {
		$FldVal = ($Default) ? $fld->DefaultDropDownValue : $fld->DropDownValue;
		$sSql = "";
		if (is_array($FldVal)) {
			foreach ($FldVal as $val) {
				$sWrk = $this->GetDropDownFilter($fld, $val, $FldOpr);

				// Call Page Filtering event
				if (substr($val, 0, 2) <> "@@") $this->Page_Filtering($fld, $sWrk, "dropdown", $FldOpr, $val);
				if ($sWrk <> "") {
					if ($sSql <> "")
						$sSql .= " OR " . $sWrk;
					else
						$sSql = $sWrk;
				}
			}
		} else {
			$sSql = $this->GetDropDownFilter($fld, $FldVal, $FldOpr);

			// Call Page Filtering event
			if (substr($FldVal, 0, 2) <> "@@") $this->Page_Filtering($fld, $sSql, "dropdown", $FldOpr, $FldVal);
		}
		if ($sSql <> "") {
			ewr_AddFilter($FilterClause, $sSql);
			if ($SaveFilter) $fld->CurrentFilter = $sSql;
		}
	}

	function GetDropDownFilter(&$fld, $FldVal, $FldOpr) {
		$FldName = $fld->FldName;
		$FldExpression = $fld->FldExpression;
		$FldDataType = $fld->FldDataType;
		$FldDelimiter = $fld->FldDelimiter;
		$FldVal = strval($FldVal);
		$sWrk = "";
		if ($FldVal == EWR_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NULL";
		} elseif ($FldVal == EWR_NOT_NULL_VALUE) {
			$sWrk = $FldExpression . " IS NOT NULL";
		} elseif ($FldVal == EWR_EMPTY_VALUE) {
			$sWrk = $FldExpression . " = ''";
		} elseif ($FldVal == EWR_ALL_VALUE) {
			$sWrk = "1 = 1";
		} else {
			if (substr($FldVal, 0, 2) == "@@") {
				$sWrk = $this->GetCustomFilter($fld, $FldVal);
			} elseif ($FldDelimiter <> "" && trim($FldVal) <> "") {
				$sWrk = ewr_GetMultiSearchSql($FldExpression, trim($FldVal));
			} else {
				if ($FldVal <> "" && $FldVal <> EWR_INIT_VALUE) {
					if ($FldDataType == EWR_DATATYPE_DATE && $FldOpr <> "") {
						$sWrk = ewr_DateFilterString($FldExpression, $FldOpr, $FldVal, $FldDataType);
					} else {
						$sWrk = ewr_FilterString("=", $FldVal, $FldDataType);
						if ($sWrk <> "") $sWrk = $FldExpression . $sWrk;
					}
				}
			}
		}
		return $sWrk;
	}

	// Get custom filter
	function GetCustomFilter(&$fld, $FldVal) {
		$sWrk = "";
		if (is_array($fld->AdvancedFilters)) {
			foreach ($fld->AdvancedFilters as $filter) {
				if ($filter->ID == $FldVal && $filter->Enabled) {
					$sFld = $fld->FldExpression;
					$sFn = $filter->FunctionName;
					$wrkid = (substr($filter->ID,0,2) == "@@") ? substr($filter->ID,2) : $filter->ID;
					if ($sFn <> "")
						$sWrk = $sFn($sFld);
					else
						$sWrk = "";
					$this->Page_Filtering($fld, $sWrk, "custom", $wrkid);
					break;
				}
			}
		}
		return $sWrk;
	}

	// Build extended filter
	function BuildExtendedFilter(&$fld, &$FilterClause, $Default = FALSE, $SaveFilter = FALSE) {
		$sWrk = ewr_GetExtendedFilter($fld, $Default);
		if (!$Default)
			$this->Page_Filtering($fld, $sWrk, "extended", $fld->SearchOperator, $fld->SearchValue, $fld->SearchCondition, $fld->SearchOperator2, $fld->SearchValue2);
		if ($sWrk <> "") {
			ewr_AddFilter($FilterClause, $sWrk);
			if ($SaveFilter) $fld->CurrentFilter = $sWrk;
		}
	}

	// Get drop down value from querystring
	function GetDropDownValue(&$sv, $parm) {
		if (ewr_IsHttpPost())
			return FALSE; // Skip post back
		if (isset($_GET["sv_$parm"])) {
			$sv = ewr_StripSlashes(@$_GET["sv_$parm"]);
			return TRUE;
		}
		return FALSE;
	}

	// Get filter values from querystring
	function GetFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		if (ewr_IsHttpPost())
			return; // Skip post back
		$got = FALSE;
		if (isset($_GET["sv_$parm"])) {
			$fld->SearchValue = ewr_StripSlashes(@$_GET["sv_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so_$parm"])) {
			$fld->SearchOperator = ewr_StripSlashes(@$_GET["so_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sc_$parm"])) {
			$fld->SearchCondition = ewr_StripSlashes(@$_GET["sc_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["sv2_$parm"])) {
			$fld->SearchValue2 = ewr_StripSlashes(@$_GET["sv2_$parm"]);
			$got = TRUE;
		}
		if (isset($_GET["so2_$parm"])) {
			$fld->SearchOperator2 = ewr_StripSlashes($_GET["so2_$parm"]);
			$got = TRUE;
		}
		return $got;
	}

	// Set default ext filter
	function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2) {
		$fld->DefaultSearchValue = $sv1; // Default ext filter value 1
		$fld->DefaultSearchValue2 = $sv2; // Default ext filter value 2 (if operator 2 is enabled)
		$fld->DefaultSearchOperator = $so1; // Default search operator 1
		$fld->DefaultSearchOperator2 = $so2; // Default search operator 2 (if operator 2 is enabled)
		$fld->DefaultSearchCondition = $sc; // Default search condition (if operator 2 is enabled)
	}

	// Apply default ext filter
	function ApplyDefaultExtFilter(&$fld) {
		$fld->SearchValue = $fld->DefaultSearchValue;
		$fld->SearchValue2 = $fld->DefaultSearchValue2;
		$fld->SearchOperator = $fld->DefaultSearchOperator;
		$fld->SearchOperator2 = $fld->DefaultSearchOperator2;
		$fld->SearchCondition = $fld->DefaultSearchCondition;
	}

	// Check if Text Filter applied
	function TextFilterApplied(&$fld) {
		return (strval($fld->SearchValue) <> strval($fld->DefaultSearchValue) ||
			strval($fld->SearchValue2) <> strval($fld->DefaultSearchValue2) ||
			(strval($fld->SearchValue) <> "" &&
				strval($fld->SearchOperator) <> strval($fld->DefaultSearchOperator)) ||
			(strval($fld->SearchValue2) <> "" &&
				strval($fld->SearchOperator2) <> strval($fld->DefaultSearchOperator2)) ||
			strval($fld->SearchCondition) <> strval($fld->DefaultSearchCondition));
	}

	// Check if Non-Text Filter applied
	function NonTextFilterApplied(&$fld) {
		if (is_array($fld->DropDownValue)) {
			if (is_array($fld->DefaultDropDownValue)) {
				if (count($fld->DefaultDropDownValue) <> count($fld->DropDownValue))
					return TRUE;
				else
					return (count(array_diff($fld->DefaultDropDownValue, $fld->DropDownValue)) <> 0);
			} else {
				return TRUE;
			}
		} else {
			if (is_array($fld->DefaultDropDownValue))
				return TRUE;
			else
				$v1 = strval($fld->DefaultDropDownValue);
			if ($v1 == EWR_INIT_VALUE)
				$v1 = "";
			$v2 = strval($fld->DropDownValue);
			if ($v2 == EWR_INIT_VALUE || $v2 == EWR_ALL_VALUE)
				$v2 = "";
			return ($v1 <> $v2);
		}
	}

	// Get dropdown value from session
	function GetSessionDropDownValue(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->DropDownValue, 'sv_view1_' . $parm);
	}

	// Get filter values from session
	function GetSessionFilterValues(&$fld) {
		$parm = substr($fld->FldVar, 2);
		$this->GetSessionValue($fld->SearchValue, 'sv_view1_' . $parm);
		$this->GetSessionValue($fld->SearchOperator, 'so_view1_' . $parm);
		$this->GetSessionValue($fld->SearchCondition, 'sc_view1_' . $parm);
		$this->GetSessionValue($fld->SearchValue2, 'sv2_view1_' . $parm);
		$this->GetSessionValue($fld->SearchOperator2, 'so2_view1_' . $parm);
	}

	// Get value from session
	function GetSessionValue(&$sv, $sn) {
		if (array_key_exists($sn, $_SESSION))
			$sv = $_SESSION[$sn];
	}

	// Set dropdown value to session
	function SetSessionDropDownValue($sv, $parm) {
		$_SESSION['sv_view1_' . $parm] = $sv;
	}

	// Set filter values to session
	function SetSessionFilterValues($sv1, $so1, $sc, $sv2, $so2, $parm) {
		$_SESSION['sv_view1_' . $parm] = $sv1;
		$_SESSION['so_view1_' . $parm] = $so1;
		$_SESSION['sc_view1_' . $parm] = $sc;
		$_SESSION['sv2_view1_' . $parm] = $sv2;
		$_SESSION['so2_view1_' . $parm] = $so2;
	}

	// Check if has Session filter values
	function HasSessionFilterValues($parm) {
		return ((@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv_' . $parm] <> "" && @$_SESSION['sv_' . $parm] <> EWR_INIT_VALUE) ||
			(@$_SESSION['sv2_' . $parm] <> "" && @$_SESSION['sv2_' . $parm] <> EWR_INIT_VALUE));
	}

	// Dropdown filter exist
	function DropDownFilterExist(&$fld, $FldOpr) {
		$sWrk = "";
		$this->BuildDropDownFilter($fld, $sWrk, $FldOpr);
		return ($sWrk <> "");
	}

	// Extended filter exist
	function ExtendedFilterExist(&$fld) {
		$sExtWrk = "";
		$this->BuildExtendedFilter($fld, $sExtWrk);
		return ($sExtWrk <> "");
	}

	// Validate form
	function ValidateForm() {
		global $ReportLanguage, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EWR_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!ewr_CheckEuroDate($this->ngaykt->SearchValue)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->ngaykt->FldErrMsg();
		}
		if (!ewr_CheckEuroDate($this->ngaykt->SearchValue2)) {
			if ($gsFormError <> "") $gsFormError .= "<br>";
			$gsFormError .= $this->ngaykt->FldErrMsg();
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			$gsFormError .= ($gsFormError <> "") ? "<p>&nbsp;</p>" : "";
			$gsFormError .= $sFormCustomError;
		}
		return $ValidateForm;
	}

	// Clear selection stored in session
	function ClearSessionSelection($parm) {
		$_SESSION["sel_view1_$parm"] = "";
		$_SESSION["rf_view1_$parm"] = "";
		$_SESSION["rt_view1_$parm"] = "";
	}

	// Load selection from session
	function LoadSelectionFromSession($parm) {
		$fld = &$this->fields($parm);
		$fld->SelectionList = @$_SESSION["sel_view1_$parm"];
		$fld->RangeFrom = @$_SESSION["rf_view1_$parm"];
		$fld->RangeTo = @$_SESSION["rt_view1_$parm"];
	}

	// Load default value for filters
	function LoadDefaultFilters() {

		/**
		* Set up default values for non Text filters
		*/

		/**
		* Set up default values for extended filters
		* function SetDefaultExtFilter(&$fld, $so1, $sv1, $sc, $so2, $sv2)
		* Parameters:
		* $fld - Field object
		* $so1 - Default search operator 1
		* $sv1 - Default ext filter value 1
		* $sc - Default search condition (if operator 2 is enabled)
		* $so2 - Default search operator 2 (if operator 2 is enabled)
		* $sv2 - Default ext filter value 2 (if operator 2 is enabled)
		*/

		// Field ngaykt
		$this->SetDefaultExtFilter($this->ngaykt, "BETWEEN", NULL, 'AND', "=", NULL);
		if (!$this->SearchCommand) $this->ApplyDefaultExtFilter($this->ngaykt);

		/**
		* Set up default values for popup filters
		*/
	}

	// Check if filter applied
	function CheckFilter() {

		// Check ngaykt text filter
		if ($this->TextFilterApplied($this->ngaykt))
			return TRUE;
		return FALSE;
	}

	// Show list of filters
	function ShowFilterList() {
		global $ReportLanguage;

		// Initialize
		$sFilterList = "";

		// Field ngaykt
		$sExtWrk = "";
		$sWrk = "";
		$this->BuildExtendedFilter($this->ngaykt, $sExtWrk);
		$sFilter = "";
		if ($sExtWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sExtWrk</span>";
		elseif ($sWrk <> "")
			$sFilter .= "<span class=\"ewFilterValue\">$sWrk</span>";
		if ($sFilter <> "")
			$sFilterList .= "<div><span class=\"ewFilterCaption\">" . $this->ngaykt->FldCaption() . "</span>" . $sFilter . "</div>";
		$divstyle = "";
		$divdataclass = "";

		// Show Filters
		if ($sFilterList <> "") {
			$sMessage = "<div class=\"ewDisplayTable\"" . $divstyle . "><div id=\"ewrFilterList\" class=\"alert alert-info\"" . $divdataclass . "><div id=\"ewrCurrentFilters\">" . $ReportLanguage->Phrase("CurrentFilters") . "</div>" . $sFilterList . "</div></div>";
			$this->Message_Showing($sMessage, "");
			echo $sMessage;
		}
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";

		// Field ngaykt
		$sWrk = "";
		if ($this->ngaykt->SearchValue <> "" || $this->ngaykt->SearchValue2 <> "") {
			$sWrk = "\"sv_ngaykt\":\"" . ewr_JsEncode2($this->ngaykt->SearchValue) . "\"," .
			$sWrk = "\"so_ngaykt\":\"" . ewr_JsEncode2($this->ngaykt->SearchOperator) . "\"," .
			$sWrk = "\"sc_ngaykt\":\"" . ewr_JsEncode2($this->ngaykt->SearchCondition) . "\"," .
			$sWrk = "\"sv2_ngaykt\":\"" . ewr_JsEncode2($this->ngaykt->SearchValue2) . "\"," .
			$sWrk = "\"so2_ngaykt\":\"" . ewr_JsEncode2($this->ngaykt->SearchOperator2) . "\"";
		}
		if ($sWrk <> "") {
			if ($sFilterList <> "") $sFilterList .= ",";
			$sFilterList .= $sWrk;
		}

		// Return filter list in json
		if ($sFilterList <> "")
			return "{" . $sFilterList . "}";
		else
			return "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ewr_StripSlashes(@$_POST["filter"]), TRUE);

		// Field ngaykt
		if (array_key_exists("sv_ngaykt", $filter) || array_key_exists("so_ngaykt", $filter) ||
			array_key_exists("sc_ngaykt", $filter) ||
			array_key_exists("sv2_ngaykt", $filter) || array_key_exists("so2_ngaykt", $filter))
			$this->SetSessionFilterValues(@$filter["sv_ngaykt"], @$filter["so_ngaykt"], @$filter["sc_ngaykt"], @$filter["sv2_ngaykt"], @$filter["so2_ngaykt"], "ngaykt");
	}

	// Return popup filter
	function GetPopupFilter() {
		$sWrk = "";
		if ($this->DrillDown)
			return "";
		return $sWrk;
	}

	//-------------------------------------------------------------------------------
	// Function GetSort
	// - Return Sort parameters based on Sort Links clicked
	// - Variables setup: Session[EWR_TABLE_SESSION_ORDER_BY], Session["sort_Table_Field"]
	function GetSort() {
		if ($this->DrillDown)
			return "";

		// Check for a resetsort command
		if (strlen(@$_GET["cmd"]) > 0) {
			$sCmd = @$_GET["cmd"];
			if ($sCmd == "resetsort") {
				$this->setOrderBy("");
				$this->setStartGroup(1);
				$this->maql->setSort("");
				$this->hoso->setSort("");
				$this->mavt->setSort("");
				$this->somay->setSort("");
				$this->cv->setSort("");
				$this->ngayth->setSort("");
				$this->ngaykt->setSort("");
				$this->nhomsc->setSort("");
				$this->ttktafter->setSort("");
				$this->ghichu->setSort("");
			}

		// Check for an Order parameter
		} elseif (@$_GET["order"] <> "") {
			$this->CurrentOrder = ewr_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$sSortSql = $this->SortSql();
			$this->setOrderBy($sSortSql);
			$this->setStartGroup(1);
		}
		return $this->getOrderBy();
	}

	// Export to HTML
	function ExportHtml($html) {

		//global $gsExportFile;
		//header('Content-Type: text/html' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		//header('Content-Disposition: attachment; filename=' . $gsExportFile . '.html');
		//echo $html;

	} 

	// Export to WORD
	function ExportWord($html) {
		global $gsExportFile;
		
		header('Content-Type: application/vnd.ms-word' . (EWR_CHARSET <> '' ? ';charset=' . EWR_CHARSET : ''));
		header('Content-Disposition: attachment; filename=baocao.doc');
		echo $html;
	}

	// Export PDF
	function ExportPDF($html) {
		global $gsExportFile;
		include_once "dompdf061/dompdf_config.inc.php";
		@ini_set("memory_limit", EWR_PDF_MEMORY_LIMIT);
		set_time_limit(EWR_PDF_TIME_LIMIT);
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		ob_end_clean();
		$dompdf->set_paper("a4", "portrait");
		$dompdf->render();
		ewr_DeleteTmpImages($html);
		$dompdf->stream("baocao.pdf", array("Attachment" => 1)); // 0 to open in browser, 1 to download

//		exit();
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ewr_Header(FALSE) ?>
<?php

// Create page object
if (!isset($view1_rpt)) $view1_rpt = new crview1_rpt();
if (isset($Page)) $OldPage = $Page;
$Page = &$view1_rpt;

// Page init
$Page->Page_Init();

// Page main
$Page->Page_Main();

// Global Page Rendering event (in ewrusrfn*.php)
Page_Rendering();

// Page Rendering event
$Page->Page_Render();
?>
<?php include_once "phprptinc/header.php" ?>
<?php if ($Page->Export == "" || $Page->Export == "print" || $Page->Export == "email" && @$gsEmailContentType == "url") { ?>
<script type="text/javascript">

// Create page object
var view1_rpt = new ewr_Page("view1_rpt");

// Page properties
view1_rpt.PageID = "rpt"; // Page ID
var EWR_PAGE_ID = view1_rpt.PageID;

// Extend page with Chart_Rendering function
view1_rpt.Chart_Rendering = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }

// Extend page with Chart_Rendered function
view1_rpt.Chart_Rendered = 
 function(chart, chartid) { // DO NOT CHANGE THIS LINE!

 	//alert(chartid);
 }
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Form object
var fview1rpt = new ewr_Form("fview1rpt");

// Validate method
fview1rpt.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	var elm = fobj.sv_ngaykt;
	if (elm && !ewr_CheckEuroDate(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->ngaykt->FldErrMsg()) ?>"))
			return false;
	}
	var elm = fobj.sv2_ngaykt;
	if (elm && !ewr_CheckEuroDate(elm.value)) {
		if (!this.OnError(elm, "<?php echo ewr_JsEncode2($Page->ngaykt->FldErrMsg()) ?>"))
			return false;
	}

	// Call Form Custom Validate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}

// Form_CustomValidate method
fview1rpt.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }
<?php if (EWR_CLIENT_VALIDATE) { ?>
fview1rpt.ValidateRequired = true; // Uses JavaScript validation
<?php } else { ?>
fview1rpt.ValidateRequired = false; // No JavaScript validation
<?php } ?>

// Use Ajax
</script>
<?php } ?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<!-- container (begin) -->
<div id="ewContainer" class="ewContainer">
<!-- top container (begin) -->
<div id="ewTop" class="ewTop">
<a id="top"></a>
<?php } ?>
<!-- top slot -->
<div class="ewToolbar">
<?php if ($Page->Export == "" && (!$Page->DrillDown || !$Page->DrillDownInPanel)) { ?>
<?php if ($ReportBreadcrumb) $ReportBreadcrumb->Render(); ?>
<?php } ?>
<?php
if (!$Page->DrillDownInPanel) {
	$Page->ExportOptions->Render("body");
	$Page->SearchOptions->Render("body");
	$Page->FilterOptions->Render("body");
}
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<?php echo $ReportLanguage->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php $Page->ShowPageHeader(); ?>
<?php $Page->ShowMessage(); ?>
<?php if ($Page->Export == "") { ?>
</div>
<!-- top container (end) -->
	<!-- left container (begin) -->
	<div id="ewLeft" class="ewLeft">
<?php } ?>
	<!-- Left slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- left container (end) -->
	<!-- center container - report (begin) -->
	<div id="ewCenter" class="ewCenter">
<?php } ?>
	<!-- center slot -->
<!-- summary report starts -->
<?php if ($Page->Export <> "pdf") { ?>
<div id="report_summary">
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<!-- Search form (begin) -->
<form name="fview1rpt" id="fview1rpt" class="form-inline ewForm ewExtFilterForm" action="<?php echo ewr_CurrentPage() ?>">
<?php $SearchPanelClass = ($Page->Filter <> "") ? " in" : " in"; ?>
<div id="fview1rpt_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<div id="r_1" class="ewRow">
<div id="c_ngaykt" class="ewCell form-group">
	<label for="sv_ngaykt" class="ewSearchCaption ewLabel"></label>
	<span class="ewSearchOperator"><?php echo $ReportLanguage->Phrase("BETWEEN"); ?><input type="hidden" name="so_ngaykt" id="so_ngaykt" value="BETWEEN"></span>
	<span class="control-group ewSearchField">
<?php ewr_PrependClass($Page->ngaykt->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" id="sv_ngaykt" name="sv_ngaykt" placeholder="" value="<?php echo ewr_HtmlEncode($Page->ngaykt->SearchValue) ?>" data-calendar="true" data-format="%d/%m/%Y"<?php echo $Page->ngaykt->EditAttributes() ?>>
</span>
	<span class="ewSearchCond btw1_ngaykt"><?php echo $ReportLanguage->Phrase("AND") ?></span>
	<span class="ewSearchField btw1_ngaykt">
<?php ewr_PrependClass($Page->ngaykt->EditAttrs["class"], "form-control"); // PR8 ?>
<input type="text" id="sv2_ngaykt" name="sv2_ngaykt" placeholder="" value="<?php echo ewr_HtmlEncode($Page->ngaykt->SearchValue2) ?>" data-calendar="true" data-format="%d/%m/%Y"<?php echo $Page->ngaykt->EditAttributes() ?>>
</span>
</div>
</div>
<div class="ewRow"><input type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-primary" value="<?php echo $ReportLanguage->Phrase("Search") ?>">
<input type="reset" name="btnreset" id="btnreset" class="btn hide" value="<?php echo $ReportLanguage->Phrase("Reset") ?>"></div>
</div>
</form>
<script type="text/javascript">
fview1rpt.Init();
fview1rpt.FilterList = <?php echo $Page->GetFilterList() ?>;
</script>
<!-- Search form (end) -->
<?php } ?>
<?php if ($Page->ShowCurrentFilter) { ?>
<?php $Page->ShowFilterList() ?>
<?php } ?>
<?php } ?>
<?php

// Set the last group to display if not export all
if ($Page->ExportAll && $Page->Export <> "") {
	$Page->StopGrp = $Page->TotalGrps;
} else {
	$Page->StopGrp = $Page->StartGrp + $Page->DisplayGrps - 1;
}

// Stop group <= total number of groups
if (intval($Page->StopGrp) > intval($Page->TotalGrps))
	$Page->StopGrp = $Page->TotalGrps;
$Page->RecCount = 0;
$Page->RecIndex = 0;

// Get first row
if ($Page->TotalGrps > 0) {
	$Page->GetRow(1);
	$Page->GrpCount = 1;
}
$Page->GrpIdx = ewr_InitArray(2, -1);
$Page->GrpIdx[0] = -1;
$Page->GrpIdx[1] = $Page->StopGrp - $Page->StartGrp + 1;
while ($rs && !$rs->EOF && $Page->GrpCount <= $Page->DisplayGrps || $Page->ShowHeader) {

	// Show dummy header for custom template
	// Show header

	if ($Page->ShowHeader) {
	

?>
<?php
$a=0;
$b=0;
?>
<?php if ($Page->Export == "pdf") { ?>
<table width="100%">
<tr>
<td style="width:30%;">
<font size="-1"><b>XN Địa vật lý GK <br/>
Xưởng SC&CCM ĐVL </b></font>
</td>
<td style="width:70%;">
<font size="+0"><b> LIỆT KÊ CÔNG TÁC BẢO DƯỠNG SỬA CHỮA </b></font><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Từ <?php  echo $Page->ngaykt->SearchValue  ?> đến   <?php  echo $Page->ngaykt->SearchValue2  ?></strong><br/>   

</td>
</tr>
</table>
<?php } ?>
<?php if ($Page->Export == "") { ?>
<table width="100%">
<tr>
<td style="width:30%;">
<font size="+0"><b>XN Địa vật lý GK <br/>
Xưởng SC&CCM ĐVL </b></font>
</td>
<td style="width:70%;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="+1"><b> LIỆT KÊ CÔNG TÁC BẢO DƯỠNG SỬA CHỮA </b></font><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><font size="+0">Từ <?php  echo $Page->ngaykt->SearchValue  ?> đến   <?php  echo $Page->ngaykt->SearchValue2  ?></b></font><br/>  

</td>
</tr>
</table>
<?php } ?>
<?php if ($Page->Export == "word") { ?>
<table width="100%">
<tr>
<td style="width:30%;">
<font size="+0"><b>XN Địa vật lý GK <br/>
Xưởng SC&CCM ĐVL </b></font>
</td>
<td style="width:70%;">
<font size="+2"><b> LIỆT KÊ CÔNG TÁC BẢO DƯỠNG SỬA CHỮA </b></font><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Từ <?php  echo $Page->ngaykt->SearchValue  ?> đến   <?php  echo $Page->ngaykt->SearchValue2  ?></strong>   

</td>
</tr>
</table>
</br>
</br>

<?php } ?>
<?php if($Page->Export == "" ) { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<?php }if($Page->Export == "word" ||$Page->Export == "pdf" ) { ?>



<?php } ?>

<table  border=1 style="border-collapse:collapse" class="<?php echo $Page->ReportTableClass ?>">
<thead>
	<!-- Table header -->
	<tr class="ewTableHeader">
<?php if ($Page->maql->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="maql"><div class="view1_maql"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->maql->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="maql">
<?php if ($Page->SortUrl($Page->maql) == "") { ?>
		<div class="ewTableHeaderBtn view1_maql">
			<span class="ewTableHeaderCaption"><?php echo $Page->maql->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_maql" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->maql) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->maql->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->maql->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->maql->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->hoso->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="hoso" ><div class="view1_hoso"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->hoso->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="hoso">
<?php if ($Page->SortUrl($Page->hoso) == "") { ?>
		<div class="ewTableHeaderBtn view1_hoso">
			<span class="ewTableHeaderCaption"><?php echo $Page->hoso->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_hoso" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->hoso) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->hoso->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->hoso->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->hoso->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->mavt->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="mavt"><div class="view1_mavt"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->mavt->FldCaption() ?></b></center></div></td>
<?php } else { ?>
	<td data-field="mavt">
<?php if ($Page->SortUrl($Page->mavt) == "") { ?>
		<div class="ewTableHeaderBtn view1_mavt">
			<span class="ewTableHeaderCaption"><?php echo $Page->mavt->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_mavt" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->mavt) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->mavt->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->mavt->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->mavt->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->somay->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="somay"><div class="view1_somay"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->somay->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="somay">
<?php if ($Page->SortUrl($Page->somay) == "") { ?>
		<div class="ewTableHeaderBtn view1_somay">
			<span class="ewTableHeaderCaption"><?php echo $Page->somay->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_somay" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->somay) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->somay->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->somay->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->somay->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->cv->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="cv"><div class="view1_cv"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->cv->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="cv">
<?php if ($Page->SortUrl($Page->cv) == "") { ?>
		<div class="ewTableHeaderBtn view1_cv">
			<span class="ewTableHeaderCaption"><?php echo $Page->cv->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_cv" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->cv) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->cv->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->cv->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->cv->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ngayth->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ngayth"><div class="view1_ngayth"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->ngayth->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="ngayth">
<?php if ($Page->SortUrl($Page->ngayth) == "") { ?>
		<div class="ewTableHeaderBtn view1_ngayth">
			<span class="ewTableHeaderCaption"><?php echo $Page->ngayth->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_ngayth" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ngayth) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ngayth->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ngayth->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ngayth->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ngaykt->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ngaykt"><div class="view1_ngaykt"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->ngaykt->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="ngaykt">
<?php if ($Page->SortUrl($Page->ngaykt) == "") { ?>
		<div class="ewTableHeaderBtn view1_ngaykt">
			<span class="ewTableHeaderCaption"><?php echo $Page->ngaykt->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_ngaykt" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ngaykt) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ngaykt->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ngaykt->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ngaykt->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->nhomsc->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="nhomsc"><div class="view1_nhomsc"><span class="ewTableHeaderCaption"><center><b><?php echo"Nhóm SC" ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="nhomsc">
<?php if ($Page->SortUrl($Page->nhomsc) == "") { ?>
		<div class="ewTableHeaderBtn view1_nhomsc">
			<span class="ewTableHeaderCaption"><?php  echo"Nhóm SC" ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_nhomsc" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->nhomsc) ?>',0);">
			<span class="ewTableHeaderCaption"><?php  echo"Nhóm SC" ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->nhomsc->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->nhomsc->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ttktafter->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ttktafter"><div class="view1_ttktafter"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->ttktafter->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="ttktafter">
<?php if ($Page->SortUrl($Page->ttktafter) == "") { ?>
		<div class="ewTableHeaderBtn view1_ttktafter">
			<span class="ewTableHeaderCaption"><?php echo $Page->ttktafter->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_ttktafter" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ttktafter) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ttktafter->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ttktafter->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ttktafter->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
<?php if ($Page->ghichu->Visible) { ?>
<?php if ($Page->Export <> "" || $Page->DrillDown) { ?>
	<td data-field="ghichu"><div class="view1_ghichu"><span class="ewTableHeaderCaption"><center><b><?php echo $Page->ghichu->FldCaption() ?></b></center></span></div></td>
<?php } else { ?>
	<td data-field="ghichu">
<?php if ($Page->SortUrl($Page->ghichu) == "") { ?>
		<div class="ewTableHeaderBtn view1_ghichu">
			<span class="ewTableHeaderCaption"><?php echo $Page->ghichu->FldCaption() ?></span>
		</div>
<?php } else { ?>
		<div class="ewTableHeaderBtn ewPointer view1_ghichu" onclick="ewr_Sort(event,'<?php echo $Page->SortUrl($Page->ghichu) ?>',0);">
			<span class="ewTableHeaderCaption"><?php echo $Page->ghichu->FldCaption() ?></span>
			<span class="ewTableHeaderSort"><?php if ($Page->ghichu->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Page->ghichu->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span>
		</div>
<?php } ?>
	</td>
<?php } ?>
<?php } ?>
	</tr>
</thead>
<tbody>
<?php
		if ($Page->TotalGrps == 0) break; // Show header only
		$Page->ShowHeader = FALSE;
	}
	$Page->RecCount++;
	$Page->RecIndex++;

		// Render detail row
		$Page->ResetAttrs();
		$Page->RowType = EWR_ROWTYPE_DETAIL;
		$Page->RenderRow();
?>

<?php 
		$mavt= $Page->mavt->ListViewValue();
		$result1 = mysql_query("SELECT tenvt FROM thietbi_iso WHERE mavt='$mavt' ") ;
				while($row = mysql_fetch_array($result1))
				{
					$tenvt =$row['tenvt'];
				}
				
 ?>

<?php if ($Page->Export =="word") { ?>
	<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->maql->Visible) { ?>
		<td data-field="maql"<?php echo $Page->maql->CellAttributes() ?>width="150" style="padding-left:8px;">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_maql"<?php echo $Page->maql->ViewAttributes() ?>><?php echo $Page->maql->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->hoso->Visible) { ?>
		<td data-field="hoso"<?php echo $Page->hoso->CellAttributes() ?>width="100">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_hoso"<?php echo $Page->hoso->ViewAttributes() ?>><center><?php echo $Page->hoso->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->mavt->Visible) { ?>
		<td data-field="mavt"<?php echo $Page->mavt->CellAttributes() ?>width="230" style="padding-left:8px;">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_mavt"<?php echo $Page->mavt->ViewAttributes() ?>><?php echo $Page->mavt->ListViewValue()?><?php echo"-$tenvt"; ?></span></td>
<?php } ?>
<?php if ($Page->somay->Visible) { ?>
		<td data-field="somay"<?php echo $Page->somay->CellAttributes() ?>width="80">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_somay"<?php echo $Page->somay->ViewAttributes() ?>><center><?php echo $Page->somay->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->cv->Visible) { ?>
		<td data-field="cv"<?php echo $Page->cv->CellAttributes() ?>width="80">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_cv"<?php echo $Page->cv->ViewAttributes() ?>><center><?php echo $Page->cv->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngayth->Visible) { ?>
		<td data-field="ngayth"<?php echo $Page->ngayth->CellAttributes() ?>width="100">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngayth"<?php echo $Page->ngayth->ViewAttributes() ?>><center><?php echo $Page->ngayth->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngaykt->Visible) { ?>
		<td data-field="ngaykt"<?php echo $Page->ngaykt->CellAttributes() ?>width="100">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngaykt"<?php echo $Page->ngaykt->ViewAttributes() ?>><center><?php echo $Page->ngaykt->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->nhomsc->Visible) { ?>
		<td data-field="nhomsc"<?php echo $Page->nhomsc->CellAttributes() ?>width="100">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_nhomsc"<?php echo $Page->nhomsc->ViewAttributes() ?>><center><?php echo $Page->nhomsc->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ttktafter->Visible) { ?>
		<td data-field="ttktafter"<?php echo $Page->ttktafter->CellAttributes() ?>width="100">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ttktafter"<?php echo $Page->ttktafter->ViewAttributes() ?>><center><?php echo $Page->ttktafter->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ghichu->Visible) { ?>
		<td data-field="ghichu"<?php echo $Page->ghichu->CellAttributes() ?>width="100" style="padding-left:8px;">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ghichu"<?php echo $Page->ghichu->ViewAttributes() ?>><?php echo $Page->ghichu->ListViewValue() ?></span></td>
<?php } ?>
<?php } elseif ($Page->Export =="pdf") { ?>
<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->maql->Visible) { ?>
		<td data-field="maql"<?php echo $Page->maql->CellAttributes() ?>width="80px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_maql"<?php echo $Page->maql->ViewAttributes() ?>><?php echo $Page->maql->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->hoso->Visible) { ?>
		<td data-field="hoso"<?php echo $Page->hoso->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_hoso"<?php echo $Page->hoso->ViewAttributes() ?>><center><?php echo $Page->hoso->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->mavt->Visible) { ?>
		<td data-field="mavt"<?php echo $Page->mavt->CellAttributes()?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_mavt"<?php echo $Page->mavt->ViewAttributes() ?>><?php echo $Page->mavt->ListViewValue()?><?php echo"-$tenvt"; ?></span></td>
<?php } ?>
<?php if ($Page->somay->Visible) { ?>
		<td data-field="somay"<?php echo $Page->somay->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_somay"<?php echo $Page->somay->ViewAttributes() ?>><center><?php echo $Page->somay->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->cv->Visible) { ?>
		<td data-field="cv"<?php echo $Page->cv->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_cv"<?php echo $Page->cv->ViewAttributes() ?>><center><?php echo $Page->cv->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngayth->Visible) { ?>
		<td data-field="ngayth"<?php echo $Page->ngayth->CellAttributes() ?> width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngayth"<?php echo $Page->ngayth->ViewAttributes() ?>><center><?php echo $Page->ngayth->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngaykt->Visible) { ?>
		<td data-field="ngaykt"<?php echo $Page->ngaykt->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngaykt"<?php echo $Page->ngaykt->ViewAttributes() ?>><center><?php echo $Page->ngaykt->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->nhomsc->Visible) { ?>
		<td data-field="nhomsc"<?php echo $Page->nhomsc->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_nhomsc"<?php echo $Page->nhomsc->ViewAttributes() ?>><center><?php echo $Page->nhomsc->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ttktafter->Visible) { ?>
		<td data-field="ttktafter"<?php echo $Page->ttktafter->CellAttributes() ?>width="50px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ttktafter"<?php echo $Page->ttktafter->ViewAttributes() ?>><center><?php echo $Page->ttktafter->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ghichu->Visible) { ?>
		<td data-field="ghichu"<?php echo $Page->ghichu->CellAttributes() ?>width="120px">
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ghichu"<?php echo $Page->ghichu->ViewAttributes() ?>><?php echo $Page->ghichu->ListViewValue() ?></span></td>
<?php } ?>
<?php } else{ ?>
<tr<?php echo $Page->RowAttributes(); ?>>
<?php if ($Page->maql->Visible) { ?>
		<td data-field="maql"<?php echo $Page->maql->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_maql"<?php echo $Page->maql->ViewAttributes() ?>><?php echo $Page->maql->ListViewValue() ?></span></td>
<?php } ?>
<?php if ($Page->hoso->Visible) { ?>
		<td data-field="hoso"<?php echo $Page->hoso->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_hoso"<?php echo $Page->hoso->ViewAttributes() ?>><center><?php echo $Page->hoso->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->mavt->Visible) { ?>
		<td data-field="mavt"<?php echo $Page->mavt->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_mavt"<?php echo $Page->mavt->ViewAttributes() ?>><?php echo $Page->mavt->ListViewValue() ?><?php echo"-$tenvt"; ?></span></td>
<?php } ?>
<?php if ($Page->somay->Visible) { ?>
		<td data-field="somay"<?php echo $Page->somay->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_somay"<?php echo $Page->somay->ViewAttributes() ?>><center><?php echo $Page->somay->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->cv->Visible) { ?>
		<td data-field="cv"<?php echo $Page->cv->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_cv"<?php echo $Page->cv->ViewAttributes() ?>><center><?php echo $Page->cv->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngayth->Visible) { ?>
		<td data-field="ngayth"<?php echo $Page->ngayth->CellAttributes() ?> >
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngayth"<?php echo $Page->ngayth->ViewAttributes() ?>><center><?php echo $Page->ngayth->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ngaykt->Visible) { ?>
		<td data-field="ngaykt"<?php echo $Page->ngaykt->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ngaykt"<?php echo $Page->ngaykt->ViewAttributes() ?>><center><?php echo $Page->ngaykt->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->nhomsc->Visible) { ?>
		<td data-field="nhomsc"<?php echo $Page->nhomsc->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_nhomsc"<?php echo $Page->nhomsc->ViewAttributes() ?>><center><?php echo $Page->nhomsc->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ttktafter->Visible) { ?>
		<td data-field="ttktafter"<?php echo $Page->ttktafter->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ttktafter"<?php echo $Page->ttktafter->ViewAttributes() ?>><center><?php echo $Page->ttktafter->ListViewValue() ?></center></span></td>
<?php } ?>
<?php if ($Page->ghichu->Visible) { ?>
		<td data-field="ghichu"<?php echo $Page->ghichu->CellAttributes() ?>>
<span data-class="tpx<?php echo $Page->RecCount ?>_<?php echo $Page->RecCount ?>_view1_ghichu"<?php echo $Page->ghichu->ViewAttributes() ?>><?php echo $Page->ghichu->ListViewValue() ?></span></td>
<?php } ?>
<?php } ?>	
<?php
$ngaystemp = $Page->ngayth->ListViewValue();
for ($i=0;$i<=strlen($ngaystemp);$i++) {
	$p = stripos($ngaystemp,"/") ;

if ($i== 0) {
	$ngays = trim (substr($ngaystemp,0,$p)) ;
} 	
if ($i== 1) {
	$thangs = trim (substr($ngaystemp,0,$p)) ;
} 	
if ($i== 2) {
	$nams = trim ($ngaystemp) ;
} 	
$p++ ;
$ngaystemp = substr($ngaystemp,$p);
}

$ngaystemp1 = $Page->ngaykt->ListViewValue();
for ($i=0;$i<=strlen($ngaystemp1);$i++) {
	$p = stripos($ngaystemp1,"/") ;

if ($i== 0) {
	$ngayt = trim (substr($ngaystemp1,0,$p)) ;
} 	
if ($i== 1) {
	$thangt = trim (substr($ngaystemp1,0,$p)) ;
} 	
if ($i== 2) {
	$namt = trim ($ngaystemp1) ;
} 	
$p++ ;
$ngaystemp1 = substr($ngaystemp1,$p);
}


$date1 ="$nams-$thangs-$ngays";
$date2 = "$namt-$thangt-$ngayt";
$diff = abs(strtotime($date2) - strtotime($date1));


$years = floor($diff/(365*60*60*24));
$months = floor(($diff-$years*365*60*60*24)/(30*60*60*24));
$days = floor(($diff-$years*365*60*60*24-$months*30*60*60*24)/(60*60*24));

if($days>="15") { $a++;
}else{ $b++; }

$c=round($b/($a+$b)*100,2);

?>
	</tr>
<?php

		// Accumulate page summary
		$Page->AccumulateSummary();

		// Get next record
		$Page->GetRow(2);
	$Page->GrpCount++;
} // End while
?>

<?php if($Page->Export == "") { ?>
<tr<?php echo $Page->RowAttributes(); ?>>
<td colspan="10">
Số máy trên 15 ngày làm việc : <?php echo $a; ?> máy
</td> 
</tr>

<tr<?php echo $Page->RowAttributes(); ?>>
<td colspan="10">
Số máy dưới 15 ngày làm việc : <?php echo $b; ?> máy
</td> 
</tr>
<tr<?php echo $Page->RowAttributes(); ?>>
<td colspan="10">
% máy thực hiện dưới 15 ngày làm việc : <?php echo $c; ?>%
</td> 
</tr>

<?php } ?>

<?php if ($Page->TotalGrps > 0) { ?>
</tbody>
<tfoot>
	</tfoot>
	</table>
	
<?php if ($Page->Export == "pdf") { ?>

<p style="margin-right:100px;">Số máy trên 15 ngày làm việc : <?php echo $a; ?> máy </p>
<p style="margin-right:100px;">Số máy dưới 15 ngày làm việc : <?php echo $b; ?> máy </p>
<p style="margin-right:100px;">% máy thực hiện dưới 15 ngày làm việc : <?php echo $c; ?>%</p>
<?php } ?>
<?php if ($Page->Export == "word") { ?>

<p style="margin-right:100px;">Số máy trên 15 ngày làm việc : <?php echo $a; ?> máy </p>
<p style="margin-right:100px;">Số máy dưới 15 ngày làm việc : <?php echo $b; ?> máy </p>
<p style="margin-right:100px;">% máy thực hiện dưới 15 ngày làm việc : <?php echo $c; ?>%</p>
<?php } ?>


	
<?php } elseif (!$Page->ShowHeader) { // No header displayed ?>
<?php if ($Page->Export <> "pdf") { ?>
<div class="ewGrid"<?php echo $Page->ReportTableStyle ?>>
<?php } ?>
<!-- Report grid (begin) -->
<?php if ($Page->Export <> "pdf") { ?>
<div class="<?php if (ewr_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php } ?>
<?php } ?>

<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->Export == "" && !($Page->DrillDown && $Page->TotalGrps > 0)) { ?>
<div class="ewGridLowerPanel">
<?php include "view1rptpager.php" ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<?php if ($Page->Export <> "pdf") { ?>
</div>
<?php } ?>
<!-- Summary Report Ends -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- center container - report (end) -->
	<!-- right container (begin) -->
	<div id="ewRight" class="ewRight">
<?php } ?>
	<!-- Right slot -->
<?php if ($Page->Export == "") { ?>
	</div>
	<!-- right container (end) -->
<div class="clearfix"></div>
<!-- bottom container (begin) -->
<div id="ewBottom" class="ewBottom">
<?php } ?>
	<!-- Bottom slot -->
<?php if ($Page->Export == "") { ?>
	</div>
<!-- Bottom Container (End) -->
</div>
<!-- Table Container (End) -->
<?php } ?>
<?php $Page->ShowPageFooter(); ?>
<?php if (EWR_DEBUG_ENABLED) echo ewr_DebugMsg(); ?>
<?php

// Close recordsets
if ($rsgrp) $rsgrp->Close();
if ($rs) $rs->Close();
?>
<?php if ($Page->Export == "" && !$Page->DrillDown) { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "phprptinc/footer.php" ?>
<?php
$Page->Page_Terminate();
if (isset($OldPage)) $Page = $OldPage;
?>
