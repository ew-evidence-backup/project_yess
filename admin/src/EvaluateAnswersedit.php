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

$EvaluateAnswers_edit = NULL; // Initialize page object first

class cEvaluateAnswers_edit extends cEvaluateAnswers {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'EvaluateAnswers';

	// Page object name
	var $PageObjName = 'EvaluateAnswers_edit';

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
			define("EW_PAGE_ID", 'edit', TRUE);

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
		$this->EvaluateAnswerID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["EvaluateAnswerID"] <> "") {
			$this->EvaluateAnswerID->setQueryStringValue($_GET["EvaluateAnswerID"]);
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
		if ($this->EvaluateAnswerID->CurrentValue == "")
			$this->Page_Terminate("EvaluateAnswerslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("EvaluateAnswerslist.php"); // No matching record, return to list
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
		if (!$this->EvaluateAnswerID->FldIsDetailKey)
			$this->EvaluateAnswerID->setFormValue($objForm->GetValue("x_EvaluateAnswerID"));
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
		$this->LoadRow();
		$this->EvaluateAnswerID->CurrentValue = $this->EvaluateAnswerID->FormValue;
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

			// EvaluateAnswerID
			$this->EvaluateAnswerID->LinkCustomAttributes = "";
			$this->EvaluateAnswerID->HrefValue = "";
			$this->EvaluateAnswerID->TooltipValue = "";

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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// EvaluateAnswerID
			$this->EvaluateAnswerID->EditCustomAttributes = "";
			$this->EvaluateAnswerID->EditValue = $this->EvaluateAnswerID->CurrentValue;
			$this->EvaluateAnswerID->ViewCustomAttributes = "";

			// EvAnswer
			$this->EvAnswer->EditCustomAttributes = "";
			$this->EvAnswer->EditValue = $this->EvAnswer->CurrentValue;
			$this->EvAnswer->PlaceHolder = ew_RemoveHtml($this->EvAnswer->FldCaption());

			// lastModified
			$this->lastModified->EditCustomAttributes = "";
			$this->lastModified->EditValue = $this->lastModified->CurrentValue;
			$this->lastModified->EditValue = ew_FormatDateTime($this->lastModified->EditValue, 5);
			$this->lastModified->ViewCustomAttributes = "";

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
			// EvaluateAnswerID

			$this->EvaluateAnswerID->HrefValue = "";

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

			// EvAnswer
			$this->EvAnswer->SetDbValueDef($rsnew, $this->EvAnswer->CurrentValue, NULL, $this->EvAnswer->ReadOnly);

			// Date
			$this->Date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Date->CurrentValue, 5), NULL, $this->Date->ReadOnly);

			// ID
			$this->ID->SetDbValueDef($rsnew, $this->ID->CurrentValue, NULL, $this->ID->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, "EvaluateAnswerslist.php", $this->TableVar, TRUE);
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
if (!isset($EvaluateAnswers_edit)) $EvaluateAnswers_edit = new cEvaluateAnswers_edit();

// Page init
$EvaluateAnswers_edit->Page_Init();

// Page main
$EvaluateAnswers_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$EvaluateAnswers_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var EvaluateAnswers_edit = new ew_Page("EvaluateAnswers_edit");
EvaluateAnswers_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = EvaluateAnswers_edit.PageID; // For backward compatibility

// Form object
var fEvaluateAnswersedit = new ew_Form("fEvaluateAnswersedit");

// Validate form
fEvaluateAnswersedit.Validate = function() {
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
fEvaluateAnswersedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fEvaluateAnswersedit.ValidateRequired = true;
<?php } else { ?>
fEvaluateAnswersedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fEvaluateAnswersedit.Lists["x_ID"] = {"LinkField":"x_ID","Ajax":null,"AutoFill":false,"DisplayFields":["x__Email","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $EvaluateAnswers_edit->ShowPageHeader(); ?>
<?php
$EvaluateAnswers_edit->ShowMessage();
?>
<form name="fEvaluateAnswersedit" id="fEvaluateAnswersedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="EvaluateAnswers">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_EvaluateAnswersedit" class="table table-bordered table-striped">
<?php if ($EvaluateAnswers->EvaluateAnswerID->Visible) { // EvaluateAnswerID ?>
	<tr id="r_EvaluateAnswerID">
		<td><span id="elh_EvaluateAnswers_EvaluateAnswerID"><?php echo $EvaluateAnswers->EvaluateAnswerID->FldCaption() ?></span></td>
		<td<?php echo $EvaluateAnswers->EvaluateAnswerID->CellAttributes() ?>>
<span id="el_EvaluateAnswers_EvaluateAnswerID" class="control-group">
<span<?php echo $EvaluateAnswers->EvaluateAnswerID->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->EvaluateAnswerID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_EvaluateAnswerID" name="x_EvaluateAnswerID" id="x_EvaluateAnswerID" value="<?php echo ew_HtmlEncode($EvaluateAnswers->EvaluateAnswerID->CurrentValue) ?>">
<?php echo $EvaluateAnswers->EvaluateAnswerID->CustomMsg ?></td>
	</tr>
<?php } ?>
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
<span<?php echo $EvaluateAnswers->lastModified->ViewAttributes() ?>>
<?php echo $EvaluateAnswers->lastModified->EditValue ?></span>
</span>
<input type="hidden" data-field="x_lastModified" name="x_lastModified" id="x_lastModified" value="<?php echo ew_HtmlEncode($EvaluateAnswers->lastModified->CurrentValue) ?>">
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
ew_CreateCalendar("fEvaluateAnswersedit", "x_Date", "%Y/%m/%d");
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
fEvaluateAnswersedit.Lists["x_ID"].Options = <?php echo (is_array($EvaluateAnswers->ID->EditValue)) ? ew_ArrayToJson($EvaluateAnswers->ID->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $EvaluateAnswers->ID->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fEvaluateAnswersedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$EvaluateAnswers_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$EvaluateAnswers_edit->Page_Terminate();
?>
