<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "Countriesinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$Countries_view = NULL; // Initialize page object first

class cCountries_view extends cCountries {

	// Page ID
	var $PageID = 'view';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Countries';

	// Page object name
	var $PageObjName = 'Countries_view';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
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
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-error ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<table class=\"ewStdTable\"><tr><td><div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div></td></tr></table>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (Countries)
		if (!isset($GLOBALS["Countries"]) || get_class($GLOBALS["Countries"]) == "cCountries") {
			$GLOBALS["Countries"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Countries"];
		}
		$KeyUrl = "";
		if (@$_GET["CountryID"] <> "") {
			$this->RecKey["CountryID"] = $_GET["CountryID"];
			$KeyUrl .= "&amp;CountryID=" . urlencode($this->RecKey["CountryID"]);
		}
		$this->ExportPrintUrl = $this->PageUrl() . "export=print" . $KeyUrl;
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html" . $KeyUrl;
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel" . $KeyUrl;
		$this->ExportWordUrl = $this->PageUrl() . "export=word" . $KeyUrl;
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml" . $KeyUrl;
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv" . $KeyUrl;
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf" . $KeyUrl;

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'view', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Countries', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) {
			$Security->SaveLastUrl();
			$this->Page_Terminate("login.php");
		}
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action
		$this->CountryID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $conn;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();
		$this->Page_Redirecting($url);

		 // Close connection
		$conn->Close();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 1;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $RecCnt;
	var $RecKey = array();
	var $Recordset;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Load current record
		$bLoadCurrentRecord = FALSE;
		$sReturnUrl = "";
		$bMatchRecord = FALSE;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET["CountryID"] <> "") {
				$this->CountryID->setQueryStringValue($_GET["CountryID"]);
				$this->RecKey["CountryID"] = $this->CountryID->QueryStringValue;
			} else {
				$sReturnUrl = "Countrieslist.php"; // Return to list
			}

			// Get action
			$this->CurrentAction = "I"; // Display form
			switch ($this->CurrentAction) {
				case "I": // Get a record to display
					if (!$this->LoadRow()) { // Load record based on key
						if ($this->getSuccessMessage() == "" && $this->getFailureMessage() == "")
							$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
						$sReturnUrl = "Countrieslist.php"; // No matching record, return to list
					}
			}
		} else {
			$sReturnUrl = "Countrieslist.php"; // Not page request, return to list
		}
		if ($sReturnUrl <> "")
			$this->Page_Terminate($sReturnUrl);

		// Render row
		$this->RowType = EW_ROWTYPE_VIEW;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = &$options["action"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAction ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("ViewPageAddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->IsLoggedIn());

		// Edit
		$item = &$option->Add("edit");
		$item->Body = "<a class=\"ewAction ewEdit\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("ViewPageEditLink") . "</a>";
		$item->Visible = ($this->EditUrl <> "" && $Security->IsLoggedIn());

		// Copy
		$item = &$option->Add("copy");
		$item->Body = "<a class=\"ewAction ewCopy\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("ViewPageCopyLink") . "</a>";
		$item->Visible = ($this->CopyUrl <> "" && $Security->IsLoggedIn());

		// Delete
		$item = &$option->Add("delete");
		$item->Body = "<a class=\"ewAction ewDelete\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("ViewPageDeleteLink") . "</a>";
		$item->Visible = ($this->DeleteUrl <> "" && $Security->IsLoggedIn());

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load row based on key values
	function LoadRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		global $conn;
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->CountryID->setDbValue($rs->fields('CountryID'));
		$this->Code->setDbValue($rs->fields('Code'));
		$this->Name->setDbValue($rs->fields('Name'));
		$this->FullName->setDbValue($rs->fields('FullName'));
		$this->ISO3->setDbValue($rs->fields('ISO3'));
		$this->Number->setDbValue($rs->fields('Number'));
		$this->ContinentCode->setDbValue($rs->fields('ContinentCode'));
		$this->DisplayOrder->setDbValue($rs->fields('DisplayOrder'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->CountryID->DbValue = $row['CountryID'];
		$this->Code->DbValue = $row['Code'];
		$this->Name->DbValue = $row['Name'];
		$this->FullName->DbValue = $row['FullName'];
		$this->ISO3->DbValue = $row['ISO3'];
		$this->Number->DbValue = $row['Number'];
		$this->ContinentCode->DbValue = $row['ContinentCode'];
		$this->DisplayOrder->DbValue = $row['DisplayOrder'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->AddUrl = $this->GetAddUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();
		$this->ListUrl = $this->GetListUrl();
		$this->SetupOtherOptions();

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// CountryID
		// Code
		// Name
		// FullName
		// ISO3
		// Number
		// ContinentCode
		// DisplayOrder

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// CountryID
			$this->CountryID->ViewValue = $this->CountryID->CurrentValue;
			$this->CountryID->ViewCustomAttributes = "";

			// Code
			$this->Code->ViewValue = $this->Code->CurrentValue;
			$this->Code->ViewCustomAttributes = "";

			// Name
			$this->Name->ViewValue = $this->Name->CurrentValue;
			$this->Name->ViewCustomAttributes = "";

			// FullName
			$this->FullName->ViewValue = $this->FullName->CurrentValue;
			$this->FullName->ViewCustomAttributes = "";

			// ISO3
			$this->ISO3->ViewValue = $this->ISO3->CurrentValue;
			$this->ISO3->ViewCustomAttributes = "";

			// Number
			$this->Number->ViewValue = $this->Number->CurrentValue;
			$this->Number->ViewCustomAttributes = "";

			// ContinentCode
			$this->ContinentCode->ViewValue = $this->ContinentCode->CurrentValue;
			$this->ContinentCode->ViewCustomAttributes = "";

			// DisplayOrder
			$this->DisplayOrder->ViewValue = $this->DisplayOrder->CurrentValue;
			$this->DisplayOrder->ViewCustomAttributes = "";

			// CountryID
			$this->CountryID->LinkCustomAttributes = "";
			$this->CountryID->HrefValue = "";
			$this->CountryID->TooltipValue = "";

			// Code
			$this->Code->LinkCustomAttributes = "";
			$this->Code->HrefValue = "";
			$this->Code->TooltipValue = "";

			// Name
			$this->Name->LinkCustomAttributes = "";
			$this->Name->HrefValue = "";
			$this->Name->TooltipValue = "";

			// FullName
			$this->FullName->LinkCustomAttributes = "";
			$this->FullName->HrefValue = "";
			$this->FullName->TooltipValue = "";

			// ISO3
			$this->ISO3->LinkCustomAttributes = "";
			$this->ISO3->HrefValue = "";
			$this->ISO3->TooltipValue = "";

			// Number
			$this->Number->LinkCustomAttributes = "";
			$this->Number->HrefValue = "";
			$this->Number->TooltipValue = "";

			// ContinentCode
			$this->ContinentCode->LinkCustomAttributes = "";
			$this->ContinentCode->HrefValue = "";
			$this->ContinentCode->TooltipValue = "";

			// DisplayOrder
			$this->DisplayOrder->LinkCustomAttributes = "";
			$this->DisplayOrder->HrefValue = "";
			$this->DisplayOrder->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "Countrieslist.php", $this->TableVar, TRUE);
		$PageId = "view";
		$Breadcrumb->Add("view", $PageId, ew_CurrentUrl());
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Countries_view)) $Countries_view = new cCountries_view();

// Page init
$Countries_view->Page_Init();

// Page main
$Countries_view->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Countries_view->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Countries_view = new ew_Page("Countries_view");
Countries_view.PageID = "view"; // Page ID
var EW_PAGE_ID = Countries_view.PageID; // For backward compatibility

// Form object
var fCountriesview = new ew_Form("fCountriesview");

// Form_CustomValidate event
fCountriesview.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fCountriesview.ValidateRequired = true;
<?php } else { ?>
fCountriesview.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<div class="ewViewExportOptions">
<?php $Countries_view->ExportOptions->Render("body") ?>
<?php if (!$Countries_view->ExportOptions->UseDropDownButton) { ?>
</div>
<div class="ewViewOtherOptions">
<?php } ?>
<?php
	foreach ($Countries_view->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
<?php $Countries_view->ShowPageHeader(); ?>
<?php
$Countries_view->ShowMessage();
?>
<form name="fCountriesview" id="fCountriesview" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Countries">
<table class="ewGrid"><tr><td>
<table id="tbl_Countriesview" class="table table-bordered table-striped">
<?php if ($Countries->CountryID->Visible) { // CountryID ?>
	<tr id="r_CountryID">
		<td><span id="elh_Countries_CountryID"><?php echo $Countries->CountryID->FldCaption() ?></span></td>
		<td<?php echo $Countries->CountryID->CellAttributes() ?>>
<span id="el_Countries_CountryID" class="control-group">
<span<?php echo $Countries->CountryID->ViewAttributes() ?>>
<?php echo $Countries->CountryID->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->Code->Visible) { // Code ?>
	<tr id="r_Code">
		<td><span id="elh_Countries_Code"><?php echo $Countries->Code->FldCaption() ?></span></td>
		<td<?php echo $Countries->Code->CellAttributes() ?>>
<span id="el_Countries_Code" class="control-group">
<span<?php echo $Countries->Code->ViewAttributes() ?>>
<?php echo $Countries->Code->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->Name->Visible) { // Name ?>
	<tr id="r_Name">
		<td><span id="elh_Countries_Name"><?php echo $Countries->Name->FldCaption() ?></span></td>
		<td<?php echo $Countries->Name->CellAttributes() ?>>
<span id="el_Countries_Name" class="control-group">
<span<?php echo $Countries->Name->ViewAttributes() ?>>
<?php echo $Countries->Name->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->FullName->Visible) { // FullName ?>
	<tr id="r_FullName">
		<td><span id="elh_Countries_FullName"><?php echo $Countries->FullName->FldCaption() ?></span></td>
		<td<?php echo $Countries->FullName->CellAttributes() ?>>
<span id="el_Countries_FullName" class="control-group">
<span<?php echo $Countries->FullName->ViewAttributes() ?>>
<?php echo $Countries->FullName->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
	<tr id="r_ISO3">
		<td><span id="elh_Countries_ISO3"><?php echo $Countries->ISO3->FldCaption() ?></span></td>
		<td<?php echo $Countries->ISO3->CellAttributes() ?>>
<span id="el_Countries_ISO3" class="control-group">
<span<?php echo $Countries->ISO3->ViewAttributes() ?>>
<?php echo $Countries->ISO3->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->Number->Visible) { // Number ?>
	<tr id="r_Number">
		<td><span id="elh_Countries_Number"><?php echo $Countries->Number->FldCaption() ?></span></td>
		<td<?php echo $Countries->Number->CellAttributes() ?>>
<span id="el_Countries_Number" class="control-group">
<span<?php echo $Countries->Number->ViewAttributes() ?>>
<?php echo $Countries->Number->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
	<tr id="r_ContinentCode">
		<td><span id="elh_Countries_ContinentCode"><?php echo $Countries->ContinentCode->FldCaption() ?></span></td>
		<td<?php echo $Countries->ContinentCode->CellAttributes() ?>>
<span id="el_Countries_ContinentCode" class="control-group">
<span<?php echo $Countries->ContinentCode->ViewAttributes() ?>>
<?php echo $Countries->ContinentCode->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
	<tr id="r_DisplayOrder">
		<td><span id="elh_Countries_DisplayOrder"><?php echo $Countries->DisplayOrder->FldCaption() ?></span></td>
		<td<?php echo $Countries->DisplayOrder->CellAttributes() ?>>
<span id="el_Countries_DisplayOrder" class="control-group">
<span<?php echo $Countries->DisplayOrder->ViewAttributes() ?>>
<?php echo $Countries->DisplayOrder->ViewValue ?></span>
</span>
</td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
</form>
<script type="text/javascript">
fCountriesview.Init();
</script>
<?php
$Countries_view->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Countries_view->Page_Terminate();
?>
