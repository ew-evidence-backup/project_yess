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

$Countries_add = NULL; // Initialize page object first

class cCountries_add extends cCountries {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Countries';

	// Page object name
	var $PageObjName = 'Countries_add';

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

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Countries', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect();
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

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["CountryID"] != "") {
				$this->CountryID->setQueryStringValue($_GET["CountryID"]);
				$this->setKey("CountryID", $this->CountryID->CurrentValue); // Set up key
			} else {
				$this->setKey("CountryID", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("Countrieslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "Countriesview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD;  // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->Code->CurrentValue = NULL;
		$this->Code->OldValue = $this->Code->CurrentValue;
		$this->Name->CurrentValue = NULL;
		$this->Name->OldValue = $this->Name->CurrentValue;
		$this->FullName->CurrentValue = NULL;
		$this->FullName->OldValue = $this->FullName->CurrentValue;
		$this->ISO3->CurrentValue = NULL;
		$this->ISO3->OldValue = $this->ISO3->CurrentValue;
		$this->Number->CurrentValue = NULL;
		$this->Number->OldValue = $this->Number->CurrentValue;
		$this->ContinentCode->CurrentValue = NULL;
		$this->ContinentCode->OldValue = $this->ContinentCode->CurrentValue;
		$this->DisplayOrder->CurrentValue = 900;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Code->FldIsDetailKey) {
			$this->Code->setFormValue($objForm->GetValue("x_Code"));
		}
		if (!$this->Name->FldIsDetailKey) {
			$this->Name->setFormValue($objForm->GetValue("x_Name"));
		}
		if (!$this->FullName->FldIsDetailKey) {
			$this->FullName->setFormValue($objForm->GetValue("x_FullName"));
		}
		if (!$this->ISO3->FldIsDetailKey) {
			$this->ISO3->setFormValue($objForm->GetValue("x_ISO3"));
		}
		if (!$this->Number->FldIsDetailKey) {
			$this->Number->setFormValue($objForm->GetValue("x_Number"));
		}
		if (!$this->ContinentCode->FldIsDetailKey) {
			$this->ContinentCode->setFormValue($objForm->GetValue("x_ContinentCode"));
		}
		if (!$this->DisplayOrder->FldIsDetailKey) {
			$this->DisplayOrder->setFormValue($objForm->GetValue("x_DisplayOrder"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->Code->CurrentValue = $this->Code->FormValue;
		$this->Name->CurrentValue = $this->Name->FormValue;
		$this->FullName->CurrentValue = $this->FullName->FormValue;
		$this->ISO3->CurrentValue = $this->ISO3->FormValue;
		$this->Number->CurrentValue = $this->Number->FormValue;
		$this->ContinentCode->CurrentValue = $this->ContinentCode->FormValue;
		$this->DisplayOrder->CurrentValue = $this->DisplayOrder->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Code
			$this->Code->EditCustomAttributes = "";
			$this->Code->EditValue = ew_HtmlEncode($this->Code->CurrentValue);
			$this->Code->PlaceHolder = ew_RemoveHtml($this->Code->FldCaption());

			// Name
			$this->Name->EditCustomAttributes = "";
			$this->Name->EditValue = ew_HtmlEncode($this->Name->CurrentValue);
			$this->Name->PlaceHolder = ew_RemoveHtml($this->Name->FldCaption());

			// FullName
			$this->FullName->EditCustomAttributes = "";
			$this->FullName->EditValue = ew_HtmlEncode($this->FullName->CurrentValue);
			$this->FullName->PlaceHolder = ew_RemoveHtml($this->FullName->FldCaption());

			// ISO3
			$this->ISO3->EditCustomAttributes = "";
			$this->ISO3->EditValue = ew_HtmlEncode($this->ISO3->CurrentValue);
			$this->ISO3->PlaceHolder = ew_RemoveHtml($this->ISO3->FldCaption());

			// Number
			$this->Number->EditCustomAttributes = "";
			$this->Number->EditValue = ew_HtmlEncode($this->Number->CurrentValue);
			$this->Number->PlaceHolder = ew_RemoveHtml($this->Number->FldCaption());

			// ContinentCode
			$this->ContinentCode->EditCustomAttributes = "";
			$this->ContinentCode->EditValue = ew_HtmlEncode($this->ContinentCode->CurrentValue);
			$this->ContinentCode->PlaceHolder = ew_RemoveHtml($this->ContinentCode->FldCaption());

			// DisplayOrder
			$this->DisplayOrder->EditCustomAttributes = "";
			$this->DisplayOrder->EditValue = ew_HtmlEncode($this->DisplayOrder->CurrentValue);
			$this->DisplayOrder->PlaceHolder = ew_RemoveHtml($this->DisplayOrder->FldCaption());

			// Edit refer script
			// Code

			$this->Code->HrefValue = "";

			// Name
			$this->Name->HrefValue = "";

			// FullName
			$this->FullName->HrefValue = "";

			// ISO3
			$this->ISO3->HrefValue = "";

			// Number
			$this->Number->HrefValue = "";

			// ContinentCode
			$this->ContinentCode->HrefValue = "";

			// DisplayOrder
			$this->DisplayOrder->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->Code->FldIsDetailKey && !is_null($this->Code->FormValue) && $this->Code->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Code->FldCaption());
		}
		if (!$this->Name->FldIsDetailKey && !is_null($this->Name->FormValue) && $this->Name->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Name->FldCaption());
		}
		if (!$this->FullName->FldIsDetailKey && !is_null($this->FullName->FormValue) && $this->FullName->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->FullName->FldCaption());
		}
		if (!$this->ISO3->FldIsDetailKey && !is_null($this->ISO3->FormValue) && $this->ISO3->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->ISO3->FldCaption());
		}
		if (!$this->Number->FldIsDetailKey && !is_null($this->Number->FormValue) && $this->Number->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Number->FldCaption());
		}
		if (!ew_CheckInteger($this->Number->FormValue)) {
			ew_AddMessage($gsFormError, $this->Number->FldErrMsg());
		}
		if (!$this->ContinentCode->FldIsDetailKey && !is_null($this->ContinentCode->FormValue) && $this->ContinentCode->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->ContinentCode->FldCaption());
		}
		if (!$this->DisplayOrder->FldIsDetailKey && !is_null($this->DisplayOrder->FormValue) && $this->DisplayOrder->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->DisplayOrder->FldCaption());
		}
		if (!ew_CheckInteger($this->DisplayOrder->FormValue)) {
			ew_AddMessage($gsFormError, $this->DisplayOrder->FldErrMsg());
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $conn, $Language, $Security;
		if ($this->Code->CurrentValue <> "") { // Check field with unique index
			$sFilter = "(Code = '" . ew_AdjustSql($this->Code->CurrentValue) . "')";
			$rsChk = $this->LoadRs($sFilter);
			if ($rsChk && !$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->Code->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->Code->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
		}

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// Code
		$this->Code->SetDbValueDef($rsnew, $this->Code->CurrentValue, "", FALSE);

		// Name
		$this->Name->SetDbValueDef($rsnew, $this->Name->CurrentValue, "", FALSE);

		// FullName
		$this->FullName->SetDbValueDef($rsnew, $this->FullName->CurrentValue, "", FALSE);

		// ISO3
		$this->ISO3->SetDbValueDef($rsnew, $this->ISO3->CurrentValue, "", FALSE);

		// Number
		$this->Number->SetDbValueDef($rsnew, $this->Number->CurrentValue, 0, FALSE);

		// ContinentCode
		$this->ContinentCode->SetDbValueDef($rsnew, $this->ContinentCode->CurrentValue, "", FALSE);

		// DisplayOrder
		$this->DisplayOrder->SetDbValueDef($rsnew, $this->DisplayOrder->CurrentValue, 0, strval($this->DisplayOrder->CurrentValue) == "");

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}

		// Get insert id if necessary
		if ($AddRow) {
			$this->CountryID->setDbValue($conn->Insert_ID());
			$rsnew['CountryID'] = $this->CountryID->DbValue;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "Countrieslist.php", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, ew_CurrentUrl());
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($Countries_add)) $Countries_add = new cCountries_add();

// Page init
$Countries_add->Page_Init();

// Page main
$Countries_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Countries_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Countries_add = new ew_Page("Countries_add");
Countries_add.PageID = "add"; // Page ID
var EW_PAGE_ID = Countries_add.PageID; // For backward compatibility

// Form object
var fCountriesadd = new ew_Form("fCountriesadd");

// Validate form
fCountriesadd.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	this.PostAutoSuggest();
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_Code");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->Code->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Name");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->Name->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_FullName");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->FullName->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_ISO3");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->ISO3->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Number");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->Number->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Number");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($Countries->Number->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_ContinentCode");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->ContinentCode->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_DisplayOrder");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($Countries->DisplayOrder->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_DisplayOrder");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($Countries->DisplayOrder->FldErrMsg()) ?>");

			// Set up row object
			ew_ElementsToRow(fobj);

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fCountriesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fCountriesadd.ValidateRequired = true;
<?php } else { ?>
fCountriesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $Countries_add->ShowPageHeader(); ?>
<?php
$Countries_add->ShowMessage();
?>
<form name="fCountriesadd" id="fCountriesadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Countries">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_Countriesadd" class="table table-bordered table-striped">
<?php if ($Countries->Code->Visible) { // Code ?>
	<tr id="r_Code">
		<td><span id="elh_Countries_Code"><?php echo $Countries->Code->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->Code->CellAttributes() ?>>
<span id="el_Countries_Code" class="control-group">
<input type="text" data-field="x_Code" name="x_Code" id="x_Code" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($Countries->Code->PlaceHolder) ?>" value="<?php echo $Countries->Code->EditValue ?>"<?php echo $Countries->Code->EditAttributes() ?>>
</span>
<?php echo $Countries->Code->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->Name->Visible) { // Name ?>
	<tr id="r_Name">
		<td><span id="elh_Countries_Name"><?php echo $Countries->Name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->Name->CellAttributes() ?>>
<span id="el_Countries_Name" class="control-group">
<input type="text" data-field="x_Name" name="x_Name" id="x_Name" size="30" maxlength="64" placeholder="<?php echo ew_HtmlEncode($Countries->Name->PlaceHolder) ?>" value="<?php echo $Countries->Name->EditValue ?>"<?php echo $Countries->Name->EditAttributes() ?>>
</span>
<?php echo $Countries->Name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->FullName->Visible) { // FullName ?>
	<tr id="r_FullName">
		<td><span id="elh_Countries_FullName"><?php echo $Countries->FullName->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->FullName->CellAttributes() ?>>
<span id="el_Countries_FullName" class="control-group">
<input type="text" data-field="x_FullName" name="x_FullName" id="x_FullName" size="30" maxlength="128" placeholder="<?php echo ew_HtmlEncode($Countries->FullName->PlaceHolder) ?>" value="<?php echo $Countries->FullName->EditValue ?>"<?php echo $Countries->FullName->EditAttributes() ?>>
</span>
<?php echo $Countries->FullName->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->ISO3->Visible) { // ISO3 ?>
	<tr id="r_ISO3">
		<td><span id="elh_Countries_ISO3"><?php echo $Countries->ISO3->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->ISO3->CellAttributes() ?>>
<span id="el_Countries_ISO3" class="control-group">
<input type="text" data-field="x_ISO3" name="x_ISO3" id="x_ISO3" size="30" maxlength="3" placeholder="<?php echo ew_HtmlEncode($Countries->ISO3->PlaceHolder) ?>" value="<?php echo $Countries->ISO3->EditValue ?>"<?php echo $Countries->ISO3->EditAttributes() ?>>
</span>
<?php echo $Countries->ISO3->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->Number->Visible) { // Number ?>
	<tr id="r_Number">
		<td><span id="elh_Countries_Number"><?php echo $Countries->Number->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->Number->CellAttributes() ?>>
<span id="el_Countries_Number" class="control-group">
<input type="text" data-field="x_Number" name="x_Number" id="x_Number" size="30" placeholder="<?php echo ew_HtmlEncode($Countries->Number->PlaceHolder) ?>" value="<?php echo $Countries->Number->EditValue ?>"<?php echo $Countries->Number->EditAttributes() ?>>
</span>
<?php echo $Countries->Number->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->ContinentCode->Visible) { // ContinentCode ?>
	<tr id="r_ContinentCode">
		<td><span id="elh_Countries_ContinentCode"><?php echo $Countries->ContinentCode->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->ContinentCode->CellAttributes() ?>>
<span id="el_Countries_ContinentCode" class="control-group">
<input type="text" data-field="x_ContinentCode" name="x_ContinentCode" id="x_ContinentCode" size="30" maxlength="2" placeholder="<?php echo ew_HtmlEncode($Countries->ContinentCode->PlaceHolder) ?>" value="<?php echo $Countries->ContinentCode->EditValue ?>"<?php echo $Countries->ContinentCode->EditAttributes() ?>>
</span>
<?php echo $Countries->ContinentCode->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Countries->DisplayOrder->Visible) { // DisplayOrder ?>
	<tr id="r_DisplayOrder">
		<td><span id="elh_Countries_DisplayOrder"><?php echo $Countries->DisplayOrder->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $Countries->DisplayOrder->CellAttributes() ?>>
<span id="el_Countries_DisplayOrder" class="control-group">
<input type="text" data-field="x_DisplayOrder" name="x_DisplayOrder" id="x_DisplayOrder" size="30" placeholder="<?php echo ew_HtmlEncode($Countries->DisplayOrder->PlaceHolder) ?>" value="<?php echo $Countries->DisplayOrder->EditValue ?>"<?php echo $Countries->DisplayOrder->EditAttributes() ?>>
</span>
<?php echo $Countries->DisplayOrder->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fCountriesadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$Countries_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Countries_add->Page_Terminate();
?>
