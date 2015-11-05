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

$Countries_edit = NULL; // Initialize page object first

class cCountries_edit extends cCountries {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Countries';

	// Page object name
	var $PageObjName = 'Countries_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["CountryID"] <> "") {
			$this->CountryID->setQueryStringValue($_GET["CountryID"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->CountryID->CurrentValue == "")
			$this->Page_Terminate("Countrieslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("Countrieslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->CountryID->FldIsDetailKey)
			$this->CountryID->setFormValue($objForm->GetValue("x_CountryID"));
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
		$this->LoadRow();
		$this->CountryID->CurrentValue = $this->CountryID->FormValue;
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// CountryID
			$this->CountryID->EditCustomAttributes = "";
			$this->CountryID->EditValue = $this->CountryID->CurrentValue;
			$this->CountryID->ViewCustomAttributes = "";

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
			// CountryID

			$this->CountryID->HrefValue = "";

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

	// Update record based on key values
	function EditRow() {
		global $conn, $Security, $Language;
		$sFilter = $this->KeyFilter();
		if ($this->Code->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`Code` = '" . ew_AdjustSql($this->Code->CurrentValue) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = 'ew_ErrorFn';
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->Code->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->Code->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// Code
			$this->Code->SetDbValueDef($rsnew, $this->Code->CurrentValue, "", $this->Code->ReadOnly);

			// Name
			$this->Name->SetDbValueDef($rsnew, $this->Name->CurrentValue, "", $this->Name->ReadOnly);

			// FullName
			$this->FullName->SetDbValueDef($rsnew, $this->FullName->CurrentValue, "", $this->FullName->ReadOnly);

			// ISO3
			$this->ISO3->SetDbValueDef($rsnew, $this->ISO3->CurrentValue, "", $this->ISO3->ReadOnly);

			// Number
			$this->Number->SetDbValueDef($rsnew, $this->Number->CurrentValue, 0, $this->Number->ReadOnly);

			// ContinentCode
			$this->ContinentCode->SetDbValueDef($rsnew, $this->ContinentCode->CurrentValue, "", $this->ContinentCode->ReadOnly);

			// DisplayOrder
			$this->DisplayOrder->SetDbValueDef($rsnew, $this->DisplayOrder->CurrentValue, 0, $this->DisplayOrder->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = 'ew_ErrorFn';
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "Countrieslist.php", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, ew_CurrentUrl());
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
if (!isset($Countries_edit)) $Countries_edit = new cCountries_edit();

// Page init
$Countries_edit->Page_Init();

// Page main
$Countries_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Countries_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Countries_edit = new ew_Page("Countries_edit");
Countries_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = Countries_edit.PageID; // For backward compatibility

// Form object
var fCountriesedit = new ew_Form("fCountriesedit");

// Validate form
fCountriesedit.Validate = function() {
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
fCountriesedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fCountriesedit.ValidateRequired = true;
<?php } else { ?>
fCountriesedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $Countries_edit->ShowPageHeader(); ?>
<?php
$Countries_edit->ShowMessage();
?>
<form name="fCountriesedit" id="fCountriesedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Countries">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_Countriesedit" class="table table-bordered table-striped">
<?php if ($Countries->CountryID->Visible) { // CountryID ?>
	<tr id="r_CountryID">
		<td><span id="elh_Countries_CountryID"><?php echo $Countries->CountryID->FldCaption() ?></span></td>
		<td<?php echo $Countries->CountryID->CellAttributes() ?>>
<span id="el_Countries_CountryID" class="control-group">
<span<?php echo $Countries->CountryID->ViewAttributes() ?>>
<?php echo $Countries->CountryID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_CountryID" name="x_CountryID" id="x_CountryID" value="<?php echo ew_HtmlEncode($Countries->CountryID->CurrentValue) ?>">
<?php echo $Countries->CountryID->CustomMsg ?></td>
	</tr>
<?php } ?>
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
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fCountriesedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$Countries_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Countries_edit->Page_Terminate();
?>
