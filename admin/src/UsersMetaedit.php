<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "UsersMetainfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$UsersMeta_edit = NULL; // Initialize page object first

class cUsersMeta_edit extends cUsersMeta {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'UsersMeta';

	// Page object name
	var $PageObjName = 'UsersMeta_edit';

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

		// Table object (UsersMeta)
		if (!isset($GLOBALS["UsersMeta"]) || get_class($GLOBALS["UsersMeta"]) == "cUsersMeta") {
			$GLOBALS["UsersMeta"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["UsersMeta"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'UsersMeta', TRUE);

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
		$this->MetaID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["MetaID"] <> "") {
			$this->MetaID->setQueryStringValue($_GET["MetaID"]);
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
		if ($this->MetaID->CurrentValue == "")
			$this->Page_Terminate("UsersMetalist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("UsersMetalist.php"); // No matching record, return to list
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
		if (!$this->MetaID->FldIsDetailKey)
			$this->MetaID->setFormValue($objForm->GetValue("x_MetaID"));
		if (!$this->ID->FldIsDetailKey) {
			$this->ID->setFormValue($objForm->GetValue("x_ID"));
		}
		if (!$this->EducationalStatus->FldIsDetailKey) {
			$this->EducationalStatus->setFormValue($objForm->GetValue("x_EducationalStatus"));
		}
		if (!$this->Created->FldIsDetailKey) {
			$this->Created->setFormValue($objForm->GetValue("x_Created"));
			$this->Created->CurrentValue = ew_UnFormatDateTime($this->Created->CurrentValue, 5);
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->MetaID->CurrentValue = $this->MetaID->FormValue;
		$this->ID->CurrentValue = $this->ID->FormValue;
		$this->EducationalStatus->CurrentValue = $this->EducationalStatus->FormValue;
		$this->Created->CurrentValue = $this->Created->FormValue;
		$this->Created->CurrentValue = ew_UnFormatDateTime($this->Created->CurrentValue, 5);
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
		$this->MetaID->setDbValue($rs->fields('MetaID'));
		$this->ID->setDbValue($rs->fields('ID'));
		$this->EducationalStatus->setDbValue($rs->fields('EducationalStatus'));
		$this->Created->setDbValue($rs->fields('Created'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->MetaID->DbValue = $row['MetaID'];
		$this->ID->DbValue = $row['ID'];
		$this->EducationalStatus->DbValue = $row['EducationalStatus'];
		$this->Created->DbValue = $row['Created'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// MetaID
		// ID
		// EducationalStatus
		// Created

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// MetaID
			$this->MetaID->ViewValue = $this->MetaID->CurrentValue;
			$this->MetaID->ViewCustomAttributes = "";

			// ID
			if (strval($this->ID->CurrentValue) <> "") {
				$sFilterWrk = "`ID`" . ew_SearchString("=", $this->ID->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `ID`, `Email` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `Users`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->ID->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->ID->ViewValue = $this->ID->CurrentValue;
				}
			} else {
				$this->ID->ViewValue = NULL;
			}
			$this->ID->ViewCustomAttributes = "";

			// EducationalStatus
			$this->EducationalStatus->ViewValue = $this->EducationalStatus->CurrentValue;
			$this->EducationalStatus->ViewCustomAttributes = "";

			// Created
			$this->Created->ViewValue = $this->Created->CurrentValue;
			$this->Created->ViewValue = ew_FormatDateTime($this->Created->ViewValue, 5);
			$this->Created->ViewCustomAttributes = "";

			// MetaID
			$this->MetaID->LinkCustomAttributes = "";
			$this->MetaID->HrefValue = "";
			$this->MetaID->TooltipValue = "";

			// ID
			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";
			$this->ID->TooltipValue = "";

			// EducationalStatus
			$this->EducationalStatus->LinkCustomAttributes = "";
			$this->EducationalStatus->HrefValue = "";
			$this->EducationalStatus->TooltipValue = "";

			// Created
			$this->Created->LinkCustomAttributes = "";
			$this->Created->HrefValue = "";
			$this->Created->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// MetaID
			$this->MetaID->EditCustomAttributes = "";
			$this->MetaID->EditValue = $this->MetaID->CurrentValue;
			$this->MetaID->ViewCustomAttributes = "";

			// ID
			$this->ID->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `ID`, `Email` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `Users`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->ID, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->ID->EditValue = $arwrk;

			// EducationalStatus
			$this->EducationalStatus->EditCustomAttributes = "";
			$this->EducationalStatus->EditValue = ew_HtmlEncode($this->EducationalStatus->CurrentValue);
			$this->EducationalStatus->PlaceHolder = ew_RemoveHtml($this->EducationalStatus->FldCaption());

			// Created
			$this->Created->EditCustomAttributes = "";
			$this->Created->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Created->CurrentValue, 5));
			$this->Created->PlaceHolder = ew_RemoveHtml($this->Created->FldCaption());

			// Edit refer script
			// MetaID

			$this->MetaID->HrefValue = "";

			// ID
			$this->ID->HrefValue = "";

			// EducationalStatus
			$this->EducationalStatus->HrefValue = "";

			// Created
			$this->Created->HrefValue = "";
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
		if (!$this->ID->FldIsDetailKey && !is_null($this->ID->FormValue) && $this->ID->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->ID->FldCaption());
		}
		if (!$this->EducationalStatus->FldIsDetailKey && !is_null($this->EducationalStatus->FormValue) && $this->EducationalStatus->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->EducationalStatus->FldCaption());
		}
		if (!ew_CheckInteger($this->EducationalStatus->FormValue)) {
			ew_AddMessage($gsFormError, $this->EducationalStatus->FldErrMsg());
		}
		if (!$this->Created->FldIsDetailKey && !is_null($this->Created->FormValue) && $this->Created->FormValue == "") {
			ew_AddMessage($gsFormError, $Language->Phrase("EnterRequiredField") . " - " . $this->Created->FldCaption());
		}
		if (!ew_CheckDate($this->Created->FormValue)) {
			ew_AddMessage($gsFormError, $this->Created->FldErrMsg());
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

			// ID
			$this->ID->SetDbValueDef($rsnew, $this->ID->CurrentValue, 0, $this->ID->ReadOnly);

			// EducationalStatus
			$this->EducationalStatus->SetDbValueDef($rsnew, $this->EducationalStatus->CurrentValue, 0, $this->EducationalStatus->ReadOnly);

			// Created
			$this->Created->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Created->CurrentValue, 5), ew_CurrentDate(), $this->Created->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, "UsersMetalist.php", $this->TableVar, TRUE);
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
if (!isset($UsersMeta_edit)) $UsersMeta_edit = new cUsersMeta_edit();

// Page init
$UsersMeta_edit->Page_Init();

// Page main
$UsersMeta_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$UsersMeta_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var UsersMeta_edit = new ew_Page("UsersMeta_edit");
UsersMeta_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = UsersMeta_edit.PageID; // For backward compatibility

// Form object
var fUsersMetaedit = new ew_Form("fUsersMetaedit");

// Validate form
fUsersMetaedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_ID");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($UsersMeta->ID->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_EducationalStatus");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($UsersMeta->EducationalStatus->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_EducationalStatus");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($UsersMeta->EducationalStatus->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Created");
			if (elm && !ew_HasValue(elm))
				return this.OnError(elm, ewLanguage.Phrase("EnterRequiredField") + " - <?php echo ew_JsEncode2($UsersMeta->Created->FldCaption()) ?>");
			elm = this.GetElements("x" + infix + "_Created");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($UsersMeta->Created->FldErrMsg()) ?>");

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
fUsersMetaedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fUsersMetaedit.ValidateRequired = true;
<?php } else { ?>
fUsersMetaedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fUsersMetaedit.Lists["x_ID"] = {"LinkField":"x_ID","Ajax":null,"AutoFill":false,"DisplayFields":["x__Email","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $UsersMeta_edit->ShowPageHeader(); ?>
<?php
$UsersMeta_edit->ShowMessage();
?>
<form name="fUsersMetaedit" id="fUsersMetaedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="UsersMeta">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_UsersMetaedit" class="table table-bordered table-striped">
<?php if ($UsersMeta->MetaID->Visible) { // MetaID ?>
	<tr id="r_MetaID">
		<td><span id="elh_UsersMeta_MetaID"><?php echo $UsersMeta->MetaID->FldCaption() ?></span></td>
		<td<?php echo $UsersMeta->MetaID->CellAttributes() ?>>
<span id="el_UsersMeta_MetaID" class="control-group">
<span<?php echo $UsersMeta->MetaID->ViewAttributes() ?>>
<?php echo $UsersMeta->MetaID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_MetaID" name="x_MetaID" id="x_MetaID" value="<?php echo ew_HtmlEncode($UsersMeta->MetaID->CurrentValue) ?>">
<?php echo $UsersMeta->MetaID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($UsersMeta->ID->Visible) { // ID ?>
	<tr id="r_ID">
		<td><span id="elh_UsersMeta_ID"><?php echo $UsersMeta->ID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $UsersMeta->ID->CellAttributes() ?>>
<span id="el_UsersMeta_ID" class="control-group">
<select data-field="x_ID" id="x_ID" name="x_ID"<?php echo $UsersMeta->ID->EditAttributes() ?>>
<?php
if (is_array($UsersMeta->ID->EditValue)) {
	$arwrk = $UsersMeta->ID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($UsersMeta->ID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
		if ($selwrk <> "") $emptywrk = FALSE;
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $arwrk[$rowcntwrk][1] ?>
</option>
<?php
	}
}
?>
</select>
<script type="text/javascript">
fUsersMetaedit.Lists["x_ID"].Options = <?php echo (is_array($UsersMeta->ID->EditValue)) ? ew_ArrayToJson($UsersMeta->ID->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $UsersMeta->ID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($UsersMeta->EducationalStatus->Visible) { // EducationalStatus ?>
	<tr id="r_EducationalStatus">
		<td><span id="elh_UsersMeta_EducationalStatus"><?php echo $UsersMeta->EducationalStatus->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $UsersMeta->EducationalStatus->CellAttributes() ?>>
<span id="el_UsersMeta_EducationalStatus" class="control-group">
<input type="text" data-field="x_EducationalStatus" name="x_EducationalStatus" id="x_EducationalStatus" size="30" placeholder="<?php echo ew_HtmlEncode($UsersMeta->EducationalStatus->PlaceHolder) ?>" value="<?php echo $UsersMeta->EducationalStatus->EditValue ?>"<?php echo $UsersMeta->EducationalStatus->EditAttributes() ?>>
</span>
<?php echo $UsersMeta->EducationalStatus->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($UsersMeta->Created->Visible) { // Created ?>
	<tr id="r_Created">
		<td><span id="elh_UsersMeta_Created"><?php echo $UsersMeta->Created->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $UsersMeta->Created->CellAttributes() ?>>
<span id="el_UsersMeta_Created" class="control-group">
<input type="text" data-field="x_Created" name="x_Created" id="x_Created" placeholder="<?php echo ew_HtmlEncode($UsersMeta->Created->PlaceHolder) ?>" value="<?php echo $UsersMeta->Created->EditValue ?>"<?php echo $UsersMeta->Created->EditAttributes() ?>>
</span>
<?php echo $UsersMeta->Created->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fUsersMetaedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$UsersMeta_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$UsersMeta_edit->Page_Terminate();
?>
