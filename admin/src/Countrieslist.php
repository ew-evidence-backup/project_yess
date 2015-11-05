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

$Countries_list = NULL; // Initialize page object first

class cCountries_list extends cCountries {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Countries';

	// Page object name
	var $PageObjName = 'Countries_list';

	// Grid form hidden field names
	var $FormName = 'fCountrieslist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

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

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "Countriesadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "Countriesdelete.php";
		$this->MultiUpdateUrl = "Countriesupdate.php";

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Countries', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";
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

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();
		$this->CountryID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Setup other options
		$this->SetupOtherOptions();

		// Set "checkbox" visible
		if (count($this->CustomActions) > 0)
			$this->ListOptions->Items["checkbox"]->Visible = TRUE;
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

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $OtherOptions = array(); // Other options
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process custom action first
			$this->ProcessCustomAction();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide export options
			if ($this->Export <> "" || $this->CurrentAction <> "")
				$this->ExportOptions->HideAllOptions();

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Restore search parms from Session if not searching / reset
			if ($this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall" && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->CountryID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->CountryID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Return basic search SQL
	function BasicSearchSQL($Keyword) {
		$sKeyword = ew_AdjustSql($Keyword);
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->Code, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->Name, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->FullName, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->ISO3, $Keyword);
		$this->BuildBasicSearchSQL($sWhere, $this->ContinentCode, $Keyword);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $Keyword) {
		if ($Keyword == EW_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NULL";
		} elseif ($Keyword == EW_NOT_NULL_VALUE) {
			$sWrk = $Fld->FldExpression . " IS NOT NULL";
		} else {
			$sFldExpression = ($Fld->FldVirtualExpression <> $Fld->FldExpression) ? $Fld->FldVirtualExpression : $Fld->FldBasicSearchExpression;
			$sWrk = $sFldExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING));
		}
		if ($Where <> "") $Where .= " OR ";
		$Where .= $sWrk;
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere() {
		global $Security;
		$sSearchStr = "";
		$sSearchKeyword = $this->BasicSearch->Keyword;
		$sSearchType = $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				while (strpos($sSearch, "  ") !== FALSE)
					$sSearch = str_replace("  ", " ", $sSearch);
				$arKeyword = explode(" ", trim($sSearch));
				foreach ($arKeyword as $sKeyword) {
					if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
					$sSearchStr .= "(" . $this->BasicSearchSQL($sKeyword) . ")";
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL($sSearch);
			}
			$this->Command = "search";
		}
		if ($this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->CountryID); // CountryID
			$this->UpdateSort($this->Code); // Code
			$this->UpdateSort($this->Name); // Name
			$this->UpdateSort($this->FullName); // FullName
			$this->UpdateSort($this->ISO3); // ISO3
			$this->UpdateSort($this->Number); // Number
			$this->UpdateSort($this->ContinentCode); // ContinentCode
			$this->UpdateSort($this->DisplayOrder); // DisplayOrder
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->SqlOrderBy() <> "") {
				$sOrderBy = $this->SqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->CountryID->setSort("");
				$this->Code->setSort("");
				$this->Name->setSort("");
				$this->FullName->setSort("");
				$this->ISO3->setSort("");
				$this->Number->setSort("");
				$this->ContinentCode->setSort("");
				$this->DisplayOrder->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = TRUE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "copy"
		$item = &$this->ListOptions->Add("copy");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->IsLoggedIn();
		$item->OnLeft = TRUE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = TRUE;
		$item->Header = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\"></label>";
		$item->MoveTo(0);
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseDropDownButton = FALSE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		$this->ListOptions->ButtonClass = "btn-small"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "copy"
		$oListOpt = &$this->ListOptions->Items["copy"];
		if ($Security->IsLoggedIn()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewCopy\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("CopyLink")) . "\" href=\"" . ew_HtmlEncode($this->CopyUrl) . "\">" . $Language->Phrase("CopyLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->IsLoggedIn())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<label class=\"checkbox\"><input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->CountryID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event, this);'></label>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->IsLoggedIn());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-small"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];
			foreach ($this->CustomActions as $action => $name) {

				// Add custom action
				$item = &$option->Add("custom_" . $action);
				$item->Body = "<a class=\"ewAction ewCustomAction\" href=\"\" onclick=\"ew_SubmitSelected(document.fCountrieslist, '" . ew_CurrentUrl() . "', null, '" . $action . "');return false;\">" . $name . "</a>";
			}

			// Hide grid edit, multi-delete and multi-update
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$item = &$option->GetItem("multidelete");
				if ($item) $item->Visible = FALSE;
				$item = &$option->GetItem("multiupdate");
				if ($item) $item->Visible = FALSE;
			}
	}

	// Process custom action
	function ProcessCustomAction() {
		global $conn, $Language, $Security;
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$rsuser = ($rs) ? $rs->GetRows() : array();
			if ($rs)
				$rs->Close();

			// Call row custom action event
			if (count($rsuser) > 0) {
				$conn->BeginTrans();
				foreach ($rsuser as $row) {
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $UserAction, $Language->Phrase("CustomActionCancelled")));
					}
				}
			}
		}
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
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

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {
		global $conn;

		// Call Recordset Selecting event
		$this->Recordset_Selecting($this->CurrentFilter);

		// Load List page SQL
		$sSql = $this->SelectSQL();
		if ($offset > -1 && $rowcnt > -1)
			$sSql .= " LIMIT $rowcnt OFFSET $offset";

		// Load recordset
		$rs = ew_LoadRecordset($sSql);

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
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

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("CountryID")) <> "")
			$this->CountryID->CurrentValue = $this->getKey("CountryID"); // CountryID
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$this->OldRecordset = ew_LoadRecordset($sSql);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

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
		$url = ew_CurrentUrl();
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, $this->TableVar, TRUE);
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

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Countries_list)) $Countries_list = new cCountries_list();

// Page init
$Countries_list->Page_Init();

// Page main
$Countries_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Countries_list->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Countries_list = new ew_Page("Countries_list");
Countries_list.PageID = "list"; // Page ID
var EW_PAGE_ID = Countries_list.PageID; // For backward compatibility

// Form object
var fCountrieslist = new ew_Form("fCountrieslist");
fCountrieslist.FormKeyCountName = '<?php echo $Countries_list->FormKeyCountName ?>';

// Form_CustomValidate event
fCountrieslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fCountrieslist.ValidateRequired = true;
<?php } else { ?>
fCountrieslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

var fCountrieslistsrch = new ew_Form("fCountrieslistsrch");
</script>
<style type="text/css">

/* main table preview row color */
.ewTablePreviewRow {
	background-color: #FFFFFF; /* preview row color */
}
.ewPreviewRowImage {
    min-width: 9px; /* for Chrome */
}
</style>
<div id="ewPreview" class="hide"><ul class="nav nav-tabs"></ul><div class="tab-content"><div class="tab-pane fade"></div></div></div>
<script type="text/javascript" src="phpjs/ewpreview.min.js"></script>
<script type="text/javascript">
var EW_PREVIEW_PLACEMENT = "right";
var EW_PREVIEW_SINGLE_ROW = false;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php if ($Countries_list->ExportOptions->Visible()) { ?>
<div class="ewListExportOptions"><?php $Countries_list->ExportOptions->Render("body") ?></div>
<?php } ?>
<?php
	$bSelectLimit = EW_SELECT_LIMIT;
	if ($bSelectLimit) {
		$Countries_list->TotalRecs = $Countries->SelectRecordCount();
	} else {
		if ($Countries_list->Recordset = $Countries_list->LoadRecordset())
			$Countries_list->TotalRecs = $Countries_list->Recordset->RecordCount();
	}
	$Countries_list->StartRec = 1;
	if ($Countries_list->DisplayRecs <= 0 || ($Countries->Export <> "" && $Countries->ExportAll)) // Display all records
		$Countries_list->DisplayRecs = $Countries_list->TotalRecs;
	if (!($Countries->Export <> "" && $Countries->ExportAll))
		$Countries_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$Countries_list->Recordset = $Countries_list->LoadRecordset($Countries_list->StartRec-1, $Countries_list->DisplayRecs);
$Countries_list->RenderOtherOptions();
?>
<?php if ($Security->IsLoggedIn()) { ?>
<?php if ($Countries->Export == "" && $Countries->CurrentAction == "") { ?>
<form name="fCountrieslistsrch" id="fCountrieslistsrch" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<div class="accordion ewDisplayTable ewSearchTable" id="fCountrieslistsrch_SearchGroup">
	<div class="accordion-group">
		<div class="accordion-heading">
<a class="accordion-toggle" data-toggle="collapse" data-parent="#fCountrieslistsrch_SearchGroup" href="#fCountrieslistsrch_SearchBody"><?php echo $Language->Phrase("Search") ?></a>
		</div>
		<div id="fCountrieslistsrch_SearchBody" class="accordion-body collapse in">
			<div class="accordion-inner">
<div id="fCountrieslistsrch_SearchPanel">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="Countries">
<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="btn-group ewButtonGroup">
	<div class="input-append">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="input-large" value="<?php echo ew_HtmlEncode($Countries_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
	<div class="btn-group ewButtonGroup">
	<a class="btn ewShowAll" href="<?php echo $Countries_list->PageUrl() ?>cmd=reset"><?php echo $Language->Phrase("ShowAll") ?></a>
	</div>
</div>
<div id="xsr_2" class="ewRow">
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="="<?php if ($Countries_list->BasicSearch->getType() == "=") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("ExactPhrase") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="AND"<?php if ($Countries_list->BasicSearch->getType() == "AND") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AllWord") ?></label>
	<label class="inline radio ewRadio" style="white-space: nowrap;"><input type="radio" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="OR"<?php if ($Countries_list->BasicSearch->getType() == "OR") { ?> checked="checked"<?php } ?>><?php echo $Language->Phrase("AnyWord") ?></label>
</div>
</div>
</div>
			</div>
		</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $Countries_list->ShowPageHeader(); ?>
<?php
$Countries_list->ShowMessage();
?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridUpperPanel">
<?php if ($Countries->CurrentAction <> "gridadd" && $Countries->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>">
<table class="ewPager">
<tr><td>
<?php if (!isset($Countries_list->Pager)) $Countries_list->Pager = new cPrevNextPager($Countries_list->StartRec, $Countries_list->DisplayRecs, $Countries_list->TotalRecs) ?>
<?php if ($Countries_list->Pager->RecordCount > 0) { ?>
<table class="ewStdTable"><tbody><tr><td>
	<?php echo $Language->Phrase("Page") ?>&nbsp;
<div class="input-prepend input-append">
<!--first page button-->
	<?php if ($Countries_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $Countries_list->PageUrl() ?>start=<?php echo $Countries_list->Pager->FirstButton->Start ?>"><i class="icon-step-backward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-backward"></i></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($Countries_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $Countries_list->PageUrl() ?>start=<?php echo $Countries_list->Pager->PrevButton->Start ?>"><i class="icon-prev"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-prev"></i></a>
	<?php } ?>
<!--current page number-->
	<input class="input-mini" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $Countries_list->Pager->CurrentPage ?>">
<!--next page button-->
	<?php if ($Countries_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $Countries_list->PageUrl() ?>start=<?php echo $Countries_list->Pager->NextButton->Start ?>"><i class="icon-play"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-play"></i></a>
	<?php } ?>
<!--last page button-->
	<?php if ($Countries_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-small" href="<?php echo $Countries_list->PageUrl() ?>start=<?php echo $Countries_list->Pager->LastButton->Start ?>"><i class="icon-step-forward"></i></a>
	<?php } else { ?>
	<a class="btn btn-small disabled"><i class="icon-step-forward"></i></a>
	<?php } ?>
</div>
	&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $Countries_list->Pager->PageCount ?>
</td>
<td>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $Countries_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $Countries_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $Countries_list->Pager->RecordCount ?>
</td>
</tr></tbody></table>
<?php } else { ?>
	<?php if ($Countries_list->SearchWhere == "0=101") { ?>
	<p><?php echo $Language->Phrase("EnterSearchCriteria") ?></p>
	<?php } else { ?>
	<p><?php echo $Language->Phrase("NoRecord") ?></p>
	<?php } ?>
<?php } ?>
</td>
</tr></table>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($Countries_list->OtherOptions as &$option)
		$option->Render("body");
?>
</div>
</div>
<form name="fCountrieslist" id="fCountrieslist" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Countries">
<div id="gmp_Countries" class="ewGridMiddlePanel">
<?php if ($Countries_list->TotalRecs > 0) { ?>
<table id="tbl_Countrieslist" class="ewTable ewTableSeparate">
<?php echo $Countries->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Render list options
$Countries_list->RenderListOptions();

// Render list options (header, left)
$Countries_list->ListOptions->Render("header", "left");
?>
<?php if ($Countries->CountryID->Visible) { // CountryID ?>
	<?php if ($Countries->SortUrl($Countries->CountryID) == "") { ?>
		<td><div id="elh_Countries_CountryID" class="Countries_CountryID"><div class="ewTableHeaderCaption"><?php echo $Countries->CountryID->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->CountryID) ?>',1);"><div id="elh_Countries_CountryID" class="Countries_CountryID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->CountryID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Countries->CountryID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->CountryID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->Code->Visible) { // Code ?>
	<?php if ($Countries->SortUrl($Countries->Code) == "") { ?>
		<td><div id="elh_Countries_Code" class="Countries_Code"><div class="ewTableHeaderCaption"><?php echo $Countries->Code->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->Code) ?>',1);"><div id="elh_Countries_Code" class="Countries_Code">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->Code->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Countries->Code->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->Code->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->Name->Visible) { // Name ?>
	<?php if ($Countries->SortUrl($Countries->Name) == "") { ?>
		<td><div id="elh_Countries_Name" class="Countries_Name"><div class="ewTableHeaderCaption"><?php echo $Countries->Name->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->Name) ?>',1);"><div id="elh_Countries_Name" class="Countries_Name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->Name->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Countries->Name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->Name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->FullName->Visible) { // FullName ?>
	<?php if ($Countries->SortUrl($Countries->FullName) == "") { ?>
		<td><div id="elh_Countries_FullName" class="Countries_FullName"><div class="ewTableHeaderCaption"><?php echo $Countries->FullName->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->FullName) ?>',1);"><div id="elh_Countries_FullName" class="Countries_FullName">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->FullName->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Countries->FullName->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->FullName->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
	<?php if ($Countries->SortUrl($Countries->ISO3) == "") { ?>
		<td><div id="elh_Countries_ISO3" class="Countries_ISO3"><div class="ewTableHeaderCaption"><?php echo $Countries->ISO3->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->ISO3) ?>',1);"><div id="elh_Countries_ISO3" class="Countries_ISO3">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->ISO3->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Countries->ISO3->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->ISO3->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->Number->Visible) { // Number ?>
	<?php if ($Countries->SortUrl($Countries->Number) == "") { ?>
		<td><div id="elh_Countries_Number" class="Countries_Number"><div class="ewTableHeaderCaption"><?php echo $Countries->Number->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->Number) ?>',1);"><div id="elh_Countries_Number" class="Countries_Number">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->Number->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Countries->Number->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->Number->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
	<?php if ($Countries->SortUrl($Countries->ContinentCode) == "") { ?>
		<td><div id="elh_Countries_ContinentCode" class="Countries_ContinentCode"><div class="ewTableHeaderCaption"><?php echo $Countries->ContinentCode->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->ContinentCode) ?>',1);"><div id="elh_Countries_ContinentCode" class="Countries_ContinentCode">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->ContinentCode->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($Countries->ContinentCode->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->ContinentCode->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
	<?php if ($Countries->SortUrl($Countries->DisplayOrder) == "") { ?>
		<td><div id="elh_Countries_DisplayOrder" class="Countries_DisplayOrder"><div class="ewTableHeaderCaption"><?php echo $Countries->DisplayOrder->FldCaption() ?></div></div></td>
	<?php } else { ?>
		<td><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $Countries->SortUrl($Countries->DisplayOrder) ?>',1);"><div id="elh_Countries_DisplayOrder" class="Countries_DisplayOrder">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $Countries->DisplayOrder->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($Countries->DisplayOrder->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($Countries->DisplayOrder->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></td>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$Countries_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($Countries->ExportAll && $Countries->Export <> "") {
	$Countries_list->StopRec = $Countries_list->TotalRecs;
} else {

	// Set the last record to display
	if ($Countries_list->TotalRecs > $Countries_list->StartRec + $Countries_list->DisplayRecs - 1)
		$Countries_list->StopRec = $Countries_list->StartRec + $Countries_list->DisplayRecs - 1;
	else
		$Countries_list->StopRec = $Countries_list->TotalRecs;
}
$Countries_list->RecCnt = $Countries_list->StartRec - 1;
if ($Countries_list->Recordset && !$Countries_list->Recordset->EOF) {
	$Countries_list->Recordset->MoveFirst();
	if (!$bSelectLimit && $Countries_list->StartRec > 1)
		$Countries_list->Recordset->Move($Countries_list->StartRec - 1);
} elseif (!$Countries->AllowAddDeleteRow && $Countries_list->StopRec == 0) {
	$Countries_list->StopRec = $Countries->GridAddRowCount;
}

// Initialize aggregate
$Countries->RowType = EW_ROWTYPE_AGGREGATEINIT;
$Countries->ResetAttrs();
$Countries_list->RenderRow();
while ($Countries_list->RecCnt < $Countries_list->StopRec) {
	$Countries_list->RecCnt++;
	if (intval($Countries_list->RecCnt) >= intval($Countries_list->StartRec)) {
		$Countries_list->RowCnt++;

		// Set up key count
		$Countries_list->KeyCount = $Countries_list->RowIndex;

		// Init row class and style
		$Countries->ResetAttrs();
		$Countries->CssClass = "";
		if ($Countries->CurrentAction == "gridadd") {
		} else {
			$Countries_list->LoadRowValues($Countries_list->Recordset); // Load row values
		}
		$Countries->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$Countries->RowAttrs = array_merge($Countries->RowAttrs, array('data-rowindex'=>$Countries_list->RowCnt, 'id'=>'r' . $Countries_list->RowCnt . '_Countries', 'data-rowtype'=>$Countries->RowType));

		// Render row
		$Countries_list->RenderRow();

		// Render list options
		$Countries_list->RenderListOptions();
?>
	<tr<?php echo $Countries->RowAttributes() ?>>
<?php

// Render list options (body, left)
$Countries_list->ListOptions->Render("body", "left", $Countries_list->RowCnt);
?>
	<?php if ($Countries->CountryID->Visible) { // CountryID ?>
		<td<?php echo $Countries->CountryID->CellAttributes() ?>>
<span<?php echo $Countries->CountryID->ViewAttributes() ?>>
<?php echo $Countries->CountryID->ListViewValue() ?></span>
<a id="<?php echo $Countries_list->PageObjName . "_row_" . $Countries_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($Countries->Code->Visible) { // Code ?>
		<td<?php echo $Countries->Code->CellAttributes() ?>>
<span<?php echo $Countries->Code->ViewAttributes() ?>>
<?php echo $Countries->Code->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->Name->Visible) { // Name ?>
		<td<?php echo $Countries->Name->CellAttributes() ?>>
<span<?php echo $Countries->Name->ViewAttributes() ?>>
<?php echo $Countries->Name->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->FullName->Visible) { // FullName ?>
		<td<?php echo $Countries->FullName->CellAttributes() ?>>
<span<?php echo $Countries->FullName->ViewAttributes() ?>>
<?php echo $Countries->FullName->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
		<td<?php echo $Countries->ISO3->CellAttributes() ?>>
<span<?php echo $Countries->ISO3->ViewAttributes() ?>>
<?php echo $Countries->ISO3->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->Number->Visible) { // Number ?>
		<td<?php echo $Countries->Number->CellAttributes() ?>>
<span<?php echo $Countries->Number->ViewAttributes() ?>>
<?php echo $Countries->Number->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
		<td<?php echo $Countries->ContinentCode->CellAttributes() ?>>
<span<?php echo $Countries->ContinentCode->ViewAttributes() ?>>
<?php echo $Countries->ContinentCode->ListViewValue() ?></span>
</td>
	<?php } ?>
	<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
		<td<?php echo $Countries->DisplayOrder->CellAttributes() ?>>
<span<?php echo $Countries->DisplayOrder->ViewAttributes() ?>>
<?php echo $Countries->DisplayOrder->ListViewValue() ?></span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$Countries_list->ListOptions->Render("body", "right", $Countries_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($Countries->CurrentAction <> "gridadd")
		$Countries_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($Countries->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($Countries_list->Recordset)
	$Countries_list->Recordset->Close();
?>
</td></tr></table>
<script type="text/javascript">
fCountrieslistsrch.Init();
fCountrieslist.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$Countries_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Countries_list->Page_Terminate();
?>
