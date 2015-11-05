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

$Videos_delete = NULL; // Initialize page object first

class cVideos_delete extends cVideos {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{A1E1A318-A966-4120-A8D8-F8227648CCB2}";

	// Table name
	var $TableName = 'Videos';

	// Page object name
	var $PageObjName = 'Videos_delete';

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
			define("EW_PAGE_ID", 'delete', TRUE);

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
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("Videoslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in Videos class, Videosinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
	}

// No functions
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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $conn, $Language, $Security;
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = 'ew_ErrorFn';
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$conn->BeginTrans();

		// Clone old rows
		$rsold = ($rs) ? $rs->GetRows() : array();
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['VideoID'];
				$conn->raiseErrorFn = 'ew_ErrorFn';
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
		} else {
			$conn->RollbackTrans(); // Rollback changes
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$Breadcrumb->Add("list", $this->TableVar, "Videoslist.php", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, ew_CurrentUrl());
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
if (!isset($Videos_delete)) $Videos_delete = new cVideos_delete();

// Page init
$Videos_delete->Page_Init();

// Page main
$Videos_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$Videos_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Page object
var Videos_delete = new ew_Page("Videos_delete");
Videos_delete.PageID = "delete"; // Page ID
var EW_PAGE_ID = Videos_delete.PageID; // For backward compatibility

// Form object
var fVideosdelete = new ew_Form("fVideosdelete");

// Form_CustomValidate event
fVideosdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fVideosdelete.ValidateRequired = true;
<?php } else { ?>
fVideosdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fVideosdelete.Lists["x_VideoCategory"] = {"LinkField":"x_VideoCategory","Ajax":null,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":[],"FilterFields":[],"Options":[]};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($Videos_delete->Recordset = $Videos_delete->LoadRecordset())
	$Videos_deleteTotalRecs = $Videos_delete->Recordset->RecordCount(); // Get record count
if ($Videos_deleteTotalRecs <= 0) { // No record found, exit
	if ($Videos_delete->Recordset)
		$Videos_delete->Recordset->Close();
	$Videos_delete->Page_Terminate("Videoslist.php"); // Return to list
}
?>
<?php $Breadcrumb->Render(); ?>
<?php $Videos_delete->ShowPageHeader(); ?>
<?php
$Videos_delete->ShowMessage();
?>
<form name="fVideosdelete" id="fVideosdelete" class="ewForm form-inline" action="<?php echo ew_CurrentPage() ?>" method="post">
<input type="hidden" name="t" value="Videos">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($Videos_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<table class="ewGrid"><tr><td class="ewGridContent">
<div class="ewGridMiddlePanel">
<table id="tbl_Videosdelete" class="ewTable ewTableSeparate">
<?php echo $Videos->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($Videos->VideoID->Visible) { // VideoID ?>
		<td><span id="elh_Videos_VideoID" class="Videos_VideoID"><?php echo $Videos->VideoID->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Videos->DateModified->Visible) { // DateModified ?>
		<td><span id="elh_Videos_DateModified" class="Videos_DateModified"><?php echo $Videos->DateModified->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Videos->Date->Visible) { // Date ?>
		<td><span id="elh_Videos_Date" class="Videos_Date"><?php echo $Videos->Date->FldCaption() ?></span></td>
<?php } ?>
<?php if ($Videos->VideoCategory->Visible) { // VideoCategory ?>
		<td><span id="elh_Videos_VideoCategory" class="Videos_VideoCategory"><?php echo $Videos->VideoCategory->FldCaption() ?></span></td>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$Videos_delete->RecCnt = 0;
$i = 0;
while (!$Videos_delete->Recordset->EOF) {
	$Videos_delete->RecCnt++;
	$Videos_delete->RowCnt++;

	// Set row properties
	$Videos->ResetAttrs();
	$Videos->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$Videos_delete->LoadRowValues($Videos_delete->Recordset);

	// Render row
	$Videos_delete->RenderRow();
?>
	<tr<?php echo $Videos->RowAttributes() ?>>
<?php if ($Videos->VideoID->Visible) { // VideoID ?>
		<td<?php echo $Videos->VideoID->CellAttributes() ?>>
<span id="el<?php echo $Videos_delete->RowCnt ?>_Videos_VideoID" class="control-group Videos_VideoID">
<span<?php echo $Videos->VideoID->ViewAttributes() ?>>
<?php echo $Videos->VideoID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Videos->DateModified->Visible) { // DateModified ?>
		<td<?php echo $Videos->DateModified->CellAttributes() ?>>
<span id="el<?php echo $Videos_delete->RowCnt ?>_Videos_DateModified" class="control-group Videos_DateModified">
<span<?php echo $Videos->DateModified->ViewAttributes() ?>>
<?php echo $Videos->DateModified->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Videos->Date->Visible) { // Date ?>
		<td<?php echo $Videos->Date->CellAttributes() ?>>
<span id="el<?php echo $Videos_delete->RowCnt ?>_Videos_Date" class="control-group Videos_Date">
<span<?php echo $Videos->Date->ViewAttributes() ?>>
<?php echo $Videos->Date->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Videos->VideoCategory->Visible) { // VideoCategory ?>
		<td<?php echo $Videos->VideoCategory->CellAttributes() ?>>
<span id="el<?php echo $Videos_delete->RowCnt ?>_Videos_VideoCategory" class="control-group Videos_VideoCategory">
<span<?php echo $Videos->VideoCategory->ViewAttributes() ?>>
<?php echo $Videos->VideoCategory->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$Videos_delete->Recordset->MoveNext();
}
$Videos_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</td></tr></table>
<div class="btn-group ewButtonGroup">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fVideosdelete.Init();
</script>
<?php
$Videos_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$Videos_delete->Page_Terminate();
?>
