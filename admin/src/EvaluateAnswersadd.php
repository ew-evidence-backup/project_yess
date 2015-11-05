<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "EvaluateAnswersinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$EvaluateAnswers_add = NULL; // Initialize page object first

class cEvaluateAnswers_add extends cEvaluateAnswers {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'EvaluateAnswers';

	// Page object name
	var $PageObjName = 'EvaluateAnswers_add';

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

		// Table object (EvaluateAnswers)
		if (!isset($GLOBALS["EvaluateAnswers"]) || get_class($GLOBALS["EvaluateAnswers"]) == "cEvaluateAnswers") {
			$GLOBALS["EvaluateAnswers"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["EvaluateAnswers"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'EvaluateAnswers', TRUE);

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
			if (@$_GET["EvaluateAnswerID"] != "") {
				$this->EvaluateAnswerID->setQueryStringValue($_GET["EvaluateAnswerID"]);
				$this->setKey("EvaluateAnswerID", $this->EvaluateAnswerID->CurrentValue); // Set up key
			} else {
				$this->setKey("EvaluateAnswerID", ""); // Clear key
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
					$this->Page_Terminate("EvaluateAnswerslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "EvaluateAnswersview.php")
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
		$this->EvAnswer->CurrentValue = NULL;
		$this->EvAnswer->OldValue = $this->EvAnswer->CurrentValue;
		$this->lastModified->CurrentValue = NULL;
		$this->lastModified->OldValue = $this->lastModified->CurrentValue;
		$this->Date->CurrentValue = "0000-00-00 00:00:00";
		$this->ID->CurrentValue = NULL;
		$this->ID->OldValue = $this->ID->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->EvAnswer->FldIsDetailKey) {
			$this->EvAnswer->setFormValue($objForm->GetValue("x_EvAnswer"));
		}
		if (!$this->lastModified->FldIsDetailKey) {
			$this->lastModified->setFormValue($objForm->GetValue("x_lastModified"));
			$this->lastModified->CurrentValue = ew_UnFormatDateTime($this->lastModified->CurrentValue, 5);
		}
		if (!$this->Date->FldIsDetailKey) {
			$this->Date->setFormValue($objForm->GetValue("x_Date"));
			$this->Date->CurrentValue = ew_UnFormatDateTime($this->Date->CurrentValue, 5);
		}
		if (!$this->ID->FldIsDetailKey) {
			$this->ID->setFormValue($objForm->GetValue("x_ID"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->EvAnswer->CurrentValue = $this->EvAnswer->FormValue;
		$this->lastModified->CurrentValue = $this->lastModified->FormValue;
		$this->lastModified->CurrentValue = ew_UnFormatDateTime($this->lastModified->CurrentValue, 5);
		$this->Date->CurrentValue = $this->Date->FormValue;
		$this->Date->CurrentValue = ew_UnFormatDateTime($this->Date->CurrentValue, 5);
		$this->ID->CurrentValue = $this->ID->FormValue;
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
		$this->EvaluateAnswerID->setDbValue($rs->fields('EvaluateAnswerID'));
		$this->EvAnswer->setDbValue($rs->fields('EvAnswer'));
		$this->lastModified->setDbValue($rs->fields('lastModified'));
		$this->Date->setDbValue($rs->fields('Date'));
		$this->ID->setDbValue($rs->fields('ID'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->EvaluateAnswerID->DbValue = $row['EvaluateAnswerID'];
		$this->EvAnswer->DbValue = $row['EvAnswer'];
		$this->lastModified->DbValue = $row['lastModified'];
		$this->Date->DbValue = $row['Date'];
		$this->ID->DbValue = $row['ID'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("EvaluateAnswerID")) <> "")
			$this->EvaluateAnswerID->CurrentValue = $this->getKey("EvaluateAnswerID"); // EvaluateAnswerID
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
		// EvaluateAnswerID
		// EvAnswer
		// lastModified
		// Date
		// ID

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// EvaluateAnswerID
			$this->EvaluateAnswerID->ViewValue = $this->EvaluateAnswerID->CurrentValue;
			$this->EvaluateAnswerID->ViewCustomAttributes = "";

			// EvAnswer
			$this->EvAnswer->ViewValue = $this->EvAnswer->CurrentValue;
			$this->EvAnswer->ViewCustomAttributes = "";

			// lastModified
			$this->lastModified->ViewValue = $this->lastModified->CurrentValue;
			$this->lastModified->ViewValue = ew_FormatDateTime($this->lastModified->ViewValue, 5);
			$this->lastModified->ViewCustomAttributes = "";

			// Date
			$this->Date->ViewValue = $this->Date->CurrentValue;
			$this->Date->ViewValue = ew_FormatDateTime($this->Date->ViewValue, 5);
			$this->Date->ViewCustomAttributes = "";

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

			// EvAnswer
			$this->EvAnswer->LinkCustomAttributes = "";
			$this->EvAnswer->HrefValue = "";
			$this->EvAnswer->TooltipValue = "";

			// lastModified
			$this->lastModified->LinkCustomAttributes = "";
			$this->lastModified->HrefValue = "";
			$this->lastModified->TooltipValue = "";

			// Date
			$this->Date->LinkCustomAttributes = "";
			$this->Date->HrefValue = "";
			$this->Date->TooltipValue = "";

			// ID
			$this->ID->LinkCustomAttributes = "";
			$this->ID->HrefValue = "";
			$this->ID->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// EvAnswer
			$this->EvAnswer->EditCustomAttributes = "";
			$this->EvAnswer->EditValue = $this->EvAnswer->CurrentValue;
			$this->EvAnswer->PlaceHolder = ew_RemoveHtml($this->EvAnswer->FldCaption());

			// lastModified
			$this->lastModified->EditCustomAttributes = "";
			$this->lastModified->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->lastModified->CurrentValue, 5));
			$this->lastModified->PlaceHolder = ew_RemoveHtml($this->lastModified->FldCaption());

			// Date
			$this->Date->EditCustomAttributes = "";
			$this->Date->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Date->CurrentValue, 5));
			$this->Date->PlaceHolder = ew_RemoveHtml($this->Date->FldCaption());

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

			// Edit refer script
			// EvAnswer

			$this->EvAnswer->HrefValue = "";

			// lastModified
			$this->lastModified->HrefValue = "";

			// Date
			$this->Date->HrefValue = "";

			// ID
			$this->ID->HrefValue = "";
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
		if (!ew_CheckDate($this->lastModified->FormValue)) {
			ew_AddMessage($gsFormError, $this->lastModified->FldErrMsg());
		}
		if (!ew_CheckDate($this->Date->FormValue)) {
			ew_AddMessage($gsFormError, $this->Date->FldErrMsg());
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

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// EvAnswer
		$this->EvAnswer->SetDbValueDef($rsnew, $this->EvAnswer->CurrentValue, NULL, FALSE);

		// lastModified
		$this->lastModified->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->lastModified->CurrentValue, 5), NULL, FALSE);

		// Date
		$this->Date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Date->CurrentValue, 5), NULL, strval($this->Date->CurrentValue) == "");

		// ID
		$this->ID->SetDbValueDef($rsnew, $this->ID->CurrentValue, NULL, FALSE);

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
			$this->EvaluateAnswerID->setDbValue($conn->Insert_ID());
			$rsnew['EvaluateAnswerID'] = $this->EvaluateAnswerID->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "EvaluateAnswerslist.php", $this->TableVar, TRUE);
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
if (!isset($EvaluateAnswers_add)) $EvaluateAnswers_add = new cEvaluateAnswers_add();

// Page init
$EvaluateAnswers_add->Page_Init();

// Page main
$EvaluateAnswers_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$EvaluateAnswers_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var EvaluateAnswers_add = new ew_Page("EvaluateAnswers_add");
EvaluateAnswers_add.PageID = "add"; // Page ID
var EW_PAGE_ID = EvaluateAnswers_add.PageID; // For backward compatibility

// Form object
var fEvaluateAnswersadd = new ew_Form("fEvaluateAnswersadd");

// Validate form
fEvaluateAnswersadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_lastModified");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($EvaluateAnswers->lastModified->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Date");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($EvaluateAnswers->Date->FldErrMsg()) ?>");

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
fEvaluateAnswersadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fEvaluateAnswersadd.ValidateRequired = true;
<?php } else { ?>
fEvaluateAnswersadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fEvaluateAnswersadd.Lists["x_ID"] = {"LinkField":"x_ID","Ajax":null,"AutoFill":false,"DisplayFields":["x__Email","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $EvaluateAnswers_add->ShowPageHeader(); ?>
<?php
$EvaluateAnswers_add->ShowMessage();
?>
<form name="fEvaluateAnswersadd" id="fEvaluateAnswersadd" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="EvaluateAnswers">
<input type="hidden" name="a_add" id="a_add" value="A">
<table class="ewGrid"><tr><td>
<table id="tbl_EvaluateAnswersadd" class="table table-bordered table-striped">
<?php if ($EvaluateAnswers->EvAnswer->Visible) { // EvAnswer ?>
	<tr id="r_EvAnswer">
		<td><span id="elh_EvaluateAnswers_EvAnswer"><?php echo $EvaluateAnswers->EvAnswer->FldCaption() ?></span></td>
		<td<?php echo $EvaluateAnswers->EvAnswer->CellAttributes() ?>>
<span id="el_EvaluateAnswers_EvAnswer" class="control-group">
<textarea data-field="x_EvAnswer" name="x_EvAnswer" id="x_EvAnswer" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($EvaluateAnswers->EvAnswer->PlaceHolder) ?>"<?php echo $EvaluateAnswers->EvAnswer->EditAttributes() ?>><?php echo $EvaluateAnswers->EvAnswer->EditValue ?></textarea>
</span>
<?php echo $EvaluateAnswers->EvAnswer->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($EvaluateAnswers->lastModified->Visible) { // lastModified ?>
	<tr id="r_lastModified">
		<td><span id="elh_EvaluateAnswers_lastModified"><?php echo $EvaluateAnswers->lastModified->FldCaption() ?></span></td>
		<td<?php echo $EvaluateAnswers->lastModified->CellAttributes() ?>>
<span id="el_EvaluateAnswers_lastModified" class="control-group">
<input type="text" data-field="x_lastModified" name="x_lastModified" id="x_lastModified" placeholder="<?php echo ew_HtmlEncode($EvaluateAnswers->lastModified->PlaceHolder) ?>" value="<?php echo $EvaluateAnswers->lastModified->EditValue ?>"<?php echo $EvaluateAnswers->lastModified->EditAttributes() ?>>
</span>
<?php echo $EvaluateAnswers->lastModified->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($EvaluateAnswers->Date->Visible) { // Date ?>
	<tr id="r_Date">
		<td><span id="elh_EvaluateAnswers_Date"><?php echo $EvaluateAnswers->Date->FldCaption() ?></span></td>
		<td<?php echo $EvaluateAnswers->Date->CellAttributes() ?>>
<span id="el_EvaluateAnswers_Date" class="control-group">
<input type="text" data-field="x_Date" name="x_Date" id="x_Date" placeholder="<?php echo ew_HtmlEncode($EvaluateAnswers->Date->PlaceHolder) ?>" value="<?php echo $EvaluateAnswers->Date->EditValue ?>"<?php echo $EvaluateAnswers->Date->EditAttributes() ?>>
<?php if (!$EvaluateAnswers->Date->ReadOnly && !$EvaluateAnswers->Date->Disabled && @$EvaluateAnswers->Date->EditAttrs["readonly"] == "" && @$EvaluateAnswers->Date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_Date" name="cal_x_Date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fEvaluateAnswersadd", "x_Date", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $EvaluateAnswers->Date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($EvaluateAnswers->ID->Visible) { // ID ?>
	<tr id="r_ID">
		<td><span id="elh_EvaluateAnswers_ID"><?php echo $EvaluateAnswers->ID->FldCaption() ?></span></td>
		<td<?php echo $EvaluateAnswers->ID->CellAttributes() ?>>
<span id="el_EvaluateAnswers_ID" class="control-group">
<select data-field="x_ID" id="x_ID" name="x_ID"<?php echo $EvaluateAnswers->ID->EditAttributes() ?>>
<?php
if (is_array($EvaluateAnswers->ID->EditValue)) {
	$arwrk = $EvaluateAnswers->ID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($EvaluateAnswers->ID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fEvaluateAnswersadd.Lists["x_ID"].Options = <?php echo (is_array($EvaluateAnswers->ID->EditValue)) ? ew_ArrayToJson($EvaluateAnswers->ID->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $EvaluateAnswers->ID->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
</form>
<script type="text/javascript">
fEvaluateAnswersadd.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$EvaluateAnswers_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$EvaluateAnswers_add->Page_Terminate();
?>
