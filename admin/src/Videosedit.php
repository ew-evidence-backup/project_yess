<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg10.php" ?>
<?php include_once "ewmysql10.php" ?>
<?php include_once "phpfn10.php" ?>
<?php include_once "Videosinfo.php" ?>
<?php include_once "userfn10.php" ?>
<?php

//
// Page class
//

$Videos_edit = NULL; // Initialize page object first

class cVideos_edit extends cVideos {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Videos';

	// Page object name
	var $PageObjName = 'Videos_edit';

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

		// Table object (Videos)
		if (!isset($GLOBALS["Videos"]) || get_class($GLOBALS["Videos"]) == "cVideos") {
			$GLOBALS["Videos"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["Videos"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'Videos', TRUE);

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
		$this->VideoID->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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
		if (@$_GET["VideoID"] <> "") {
			$this->VideoID->setQueryStringValue($_GET["VideoID"]);
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
		if ($this->VideoID->CurrentValue == "")
			$this->Page_Terminate("Videoslist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("Videoslist.php"); // No matching record, return to list
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
		if (!$this->VideoID->FldIsDetailKey)
			$this->VideoID->setFormValue($objForm->GetValue("x_VideoID"));
		if (!$this->Path->FldIsDetailKey) {
			$this->Path->setFormValue($objForm->GetValue("x_Path"));
		}
		if (!$this->DateModified->FldIsDetailKey) {
			$this->DateModified->setFormValue($objForm->GetValue("x_DateModified"));
			$this->DateModified->CurrentValue = ew_UnFormatDateTime($this->DateModified->CurrentValue, 5);
		}
		if (!$this->Date->FldIsDetailKey) {
			$this->Date->setFormValue($objForm->GetValue("x_Date"));
			$this->Date->CurrentValue = ew_UnFormatDateTime($this->Date->CurrentValue, 5);
		}
		if (!$this->VideoCategory->FldIsDetailKey) {
			$this->VideoCategory->setFormValue($objForm->GetValue("x_VideoCategory"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->VideoID->CurrentValue = $this->VideoID->FormValue;
		$this->Path->CurrentValue = $this->Path->FormValue;
		$this->DateModified->CurrentValue = $this->DateModified->FormValue;
		$this->DateModified->CurrentValue = ew_UnFormatDateTime($this->DateModified->CurrentValue, 5);
		$this->Date->CurrentValue = $this->Date->FormValue;
		$this->Date->CurrentValue = ew_UnFormatDateTime($this->Date->CurrentValue, 5);
		$this->VideoCategory->CurrentValue = $this->VideoCategory->FormValue;
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
		$this->VideoID->setDbValue($rs->fields('VideoID'));
		$this->Path->setDbValue($rs->fields('Path'));
		$this->DateModified->setDbValue($rs->fields('DateModified'));
		$this->Date->setDbValue($rs->fields('Date'));
		$this->VideoCategory->setDbValue($rs->fields('VideoCategory'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->VideoID->DbValue = $row['VideoID'];
		$this->Path->DbValue = $row['Path'];
		$this->DateModified->DbValue = $row['DateModified'];
		$this->Date->DbValue = $row['Date'];
		$this->VideoCategory->DbValue = $row['VideoCategory'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $conn, $Security, $Language;
		global $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// VideoID
		// Path
		// DateModified
		// Date
		// VideoCategory

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

			// VideoID
			$this->VideoID->ViewValue = $this->VideoID->CurrentValue;
			$this->VideoID->ViewCustomAttributes = "";

			// Path
			$this->Path->ViewValue = $this->Path->CurrentValue;
			$this->Path->ViewCustomAttributes = "";

			// DateModified
			$this->DateModified->ViewValue = $this->DateModified->CurrentValue;
			$this->DateModified->ViewValue = ew_FormatDateTime($this->DateModified->ViewValue, 5);
			$this->DateModified->ViewCustomAttributes = "";

			// Date
			$this->Date->ViewValue = $this->Date->CurrentValue;
			$this->Date->ViewValue = ew_FormatDateTime($this->Date->ViewValue, 5);
			$this->Date->ViewCustomAttributes = "";

			// VideoCategory
			if (strval($this->VideoCategory->CurrentValue) <> "") {
				$sFilterWrk = "`VideoCategory`" . ew_SearchString("=", $this->VideoCategory->CurrentValue, EW_DATATYPE_NUMBER);
			$sSqlWrk = "SELECT `VideoCategory`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `VideoCategories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->VideoCategory, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
				$rswrk = $conn->Execute($sSqlWrk);
				if ($rswrk && !$rswrk->EOF) { // Lookup values found
					$this->VideoCategory->ViewValue = $rswrk->fields('DispFld');
					$rswrk->Close();
				} else {
					$this->VideoCategory->ViewValue = $this->VideoCategory->CurrentValue;
				}
			} else {
				$this->VideoCategory->ViewValue = NULL;
			}
			$this->VideoCategory->ViewCustomAttributes = "";

			// VideoID
			$this->VideoID->LinkCustomAttributes = "";
			$this->VideoID->HrefValue = "";
			$this->VideoID->TooltipValue = "";

			// Path
			$this->Path->LinkCustomAttributes = "";
			$this->Path->HrefValue = "";
			$this->Path->TooltipValue = "";

			// DateModified
			$this->DateModified->LinkCustomAttributes = "";
			$this->DateModified->HrefValue = "";
			$this->DateModified->TooltipValue = "";

			// Date
			$this->Date->LinkCustomAttributes = "";
			$this->Date->HrefValue = "";
			$this->Date->TooltipValue = "";

			// VideoCategory
			$this->VideoCategory->LinkCustomAttributes = "";
			$this->VideoCategory->HrefValue = "";
			$this->VideoCategory->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// VideoID
			$this->VideoID->EditCustomAttributes = "";
			$this->VideoID->EditValue = $this->VideoID->CurrentValue;
			$this->VideoID->ViewCustomAttributes = "";

			// Path
			$this->Path->EditCustomAttributes = "";
			$this->Path->EditValue = $this->Path->CurrentValue;
			$this->Path->PlaceHolder = ew_RemoveHtml($this->Path->FldCaption());

			// DateModified
			$this->DateModified->EditCustomAttributes = "";
			$this->DateModified->EditValue = $this->DateModified->CurrentValue;
			$this->DateModified->EditValue = ew_FormatDateTime($this->DateModified->EditValue, 5);
			$this->DateModified->ViewCustomAttributes = "";

			// Date
			$this->Date->EditCustomAttributes = "";
			$this->Date->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->Date->CurrentValue, 5));
			$this->Date->PlaceHolder = ew_RemoveHtml($this->Date->FldCaption());

			// VideoCategory
			$this->VideoCategory->EditCustomAttributes = "";
			$sFilterWrk = "";
			$sSqlWrk = "SELECT `VideoCategory`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `VideoCategories`";
			$sWhereWrk = "";
			if ($sFilterWrk <> "") {
				ew_AddFilter($sWhereWrk, $sFilterWrk);
			}

			// Call Lookup selecting
			$this->Lookup_Selecting($this->VideoCategory, $sWhereWrk);
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = $conn->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->VideoCategory->EditValue = $arwrk;

			// Edit refer script
			// VideoID

			$this->VideoID->HrefValue = "";

			// Path
			$this->Path->HrefValue = "";

			// DateModified
			$this->DateModified->HrefValue = "";

			// Date
			$this->Date->HrefValue = "";

			// VideoCategory
			$this->VideoCategory->HrefValue = "";
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

			// Path
			$this->Path->SetDbValueDef($rsnew, $this->Path->CurrentValue, NULL, $this->Path->ReadOnly);

			// Date
			$this->Date->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->Date->CurrentValue, 5), NULL, $this->Date->ReadOnly);

			// VideoCategory
			$this->VideoCategory->SetDbValueDef($rsnew, $this->VideoCategory->CurrentValue, NULL, $this->VideoCategory->ReadOnly);

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
		$Breadcrumb->Add("list", $this->TableVar, "Videoslist.php", $this->TableVar, TRUE);
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
if (!isset($Videos_edit)) $Videos_edit = new cVideos_edit();

// Page init
$Videos_edit->Page_Init();

// Page main
$Videos_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Videos_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Videos_edit = new ew_Page("Videos_edit");
Videos_edit.PageID = "edit"; // Page ID
var EW_PAGE_ID = Videos_edit.PageID; // For backward compatibility

// Form object
var fVideosedit = new ew_Form("fVideosedit");

// Validate form
fVideosedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2($Videos->Date->FldErrMsg()) ?>");

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
fVideosedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fVideosedit.ValidateRequired = true;
<?php } else { ?>
fVideosedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fVideosedit.Lists["x_VideoCategory"] = {"LinkField":"x_VideoCategory","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php $Breadcrumb->Render(); ?>
<?php $Videos_edit->ShowPageHeader(); ?>
<?php
$Videos_edit->ShowMessage();
?>
<form name="fVideosedit" id="fVideosedit" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Videos">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<table class="ewGrid"><tr><td>
<table id="tbl_Videosedit" class="table table-bordered table-striped">
<?php if ($Videos->VideoID->Visible) { // VideoID ?>
	<tr id="r_VideoID">
		<td><span id="elh_Videos_VideoID"><?php echo $Videos->VideoID->FldCaption() ?></span></td>
		<td<?php echo $Videos->VideoID->CellAttributes() ?>>
<span id="el_Videos_VideoID" class="control-group">
<span<?php echo $Videos->VideoID->ViewAttributes() ?>>
<?php echo $Videos->VideoID->EditValue ?></span>
</span>
<input type="hidden" data-field="x_VideoID" name="x_VideoID" id="x_VideoID" value="<?php echo ew_HtmlEncode($Videos->VideoID->CurrentValue) ?>">
<?php echo $Videos->VideoID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Videos->Path->Visible) { // Path ?>
	<tr id="r_Path">
		<td><span id="elh_Videos_Path"><?php echo $Videos->Path->FldCaption() ?></span></td>
		<td<?php echo $Videos->Path->CellAttributes() ?>>
<span id="el_Videos_Path" class="control-group">
<textarea data-field="x_Path" name="x_Path" id="x_Path" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($Videos->Path->PlaceHolder) ?>"<?php echo $Videos->Path->EditAttributes() ?>><?php echo $Videos->Path->EditValue ?></textarea>
</span>
<?php echo $Videos->Path->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Videos->DateModified->Visible) { // DateModified ?>
	<tr id="r_DateModified">
		<td><span id="elh_Videos_DateModified"><?php echo $Videos->DateModified->FldCaption() ?></span></td>
		<td<?php echo $Videos->DateModified->CellAttributes() ?>>
<span id="el_Videos_DateModified" class="control-group">
<span<?php echo $Videos->DateModified->ViewAttributes() ?>>
<?php echo $Videos->DateModified->EditValue ?></span>
</span>
<input type="hidden" data-field="x_DateModified" name="x_DateModified" id="x_DateModified" value="<?php echo ew_HtmlEncode($Videos->DateModified->CurrentValue) ?>">
<?php echo $Videos->DateModified->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Videos->Date->Visible) { // Date ?>
	<tr id="r_Date">
		<td><span id="elh_Videos_Date"><?php echo $Videos->Date->FldCaption() ?></span></td>
		<td<?php echo $Videos->Date->CellAttributes() ?>>
<span id="el_Videos_Date" class="control-group">
<input type="text" data-field="x_Date" name="x_Date" id="x_Date" placeholder="<?php echo ew_HtmlEncode($Videos->Date->PlaceHolder) ?>" value="<?php echo $Videos->Date->EditValue ?>"<?php echo $Videos->Date->EditAttributes() ?>>
<?php if (!$Videos->Date->ReadOnly && !$Videos->Date->Disabled && @$Videos->Date->EditAttrs["readonly"] == "" && @$Videos->Date->EditAttrs["disabled"] == "") { ?>
<button id="cal_x_Date" name="cal_x_Date" class="btn" type="button"><img src="phpimages/calendar.png" alt="<?php echo $Language->Phrase("PickDate") ?>" title="<?php echo $Language->Phrase("PickDate") ?>" style="border: 0;"></button><script type="text/javascript">
ew_CreateCalendar("fVideosedit", "x_Date", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php echo $Videos->Date->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($Videos->VideoCategory->Visible) { // VideoCategory ?>
	<tr id="r_VideoCategory">
		<td><span id="elh_Videos_VideoCategory"><?php echo $Videos->VideoCategory->FldCaption() ?></span></td>
		<td<?php echo $Videos->VideoCategory->CellAttributes() ?>>
<span id="el_Videos_VideoCategory" class="control-group">
<select data-field="x_VideoCategory" id="x_VideoCategory" name="x_VideoCategory"<?php echo $Videos->VideoCategory->EditAttributes() ?>>
<?php
if (is_array($Videos->VideoCategory->EditValue)) {
	$arwrk = $Videos->VideoCategory->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($Videos->VideoCategory->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " selected=\"selected\"" : "";
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
fVideosedit.Lists["x_VideoCategory"].Options = <?php echo (is_array($Videos->VideoCategory->EditValue)) ? ew_ArrayToJson($Videos->VideoCategory->EditValue, 1) : "[]" ?>;
</script>
</span>
<?php echo $Videos->VideoCategory->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</td></tr></table>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("EditBtn") ?></button>
</form>
<script type="text/javascript">
fVideosedit.Init();
<?php if (EW_MOBILE_REFLOW && ew_IsMobile()) { ?>
ew_Reflow();
<?php } ?>
</script>
<?php
$Videos_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Videos_edit->Page_Terminate();
?>
