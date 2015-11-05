/**
 * Scrolling Table for PHPMaker
 * (C)2013 e.World Technology Ltd.
 */
var EW_SCROLLABLE_TABLE_CLASS_SCROLLABLE = "ewScrollableTable";
var EW_SCROLLABLE_TABLE_CLASS_HEADER = "ewScrollableTableHeader";
var EW_SCROLLABLE_TABLE_CLASS_BODY = "ewScrollableTableBody";

// Scrolling table
function ew_ScrollableTable(elContainer, width, height) {
	var j = jQuery, $elContainer = $("#" + elContainer), tbd = $elContainer.find("table.ewTable")[0];
	if ($.ua.ie && $.ua.ie < 8 || // Note: requires IE >= 8
		!width && !height || !tbd || !tbd.rows ||
		!$elContainer[0] || !$elContainer.hasClass("ewGridMiddlePanel") ||
		!($elContainer.is("div") && $elContainer.hasClass("ewGridMiddlePanel"))) 
		return;

	// height => Y scrolling => split the header
	var yscroll = height, xscroll = width, sbwidth = 17, 
		elContainer = $elContainer.addClass(EW_SCROLLABLE_TABLE_CLASS_SCROLLABLE)[0], $tbd = $(tbd).detach(); // remove the form/table	

	// now the grid has the lower/upper panel only, ensure same width as the upper/lower panel
	var sp = $elContainer.parent().prev(".ewGridUpperPanel")[0] || $elContainer.parent().next(".ewGridLowerPanel")[0];
	if (width && sp && sp.offsetWidth > 0 && parseInt(width) < sp.offsetWidth)
		width = sp.offsetWidth + "px";

	// insert DIV to all cells for getting/setting widths
	$(tbd.rows).each(function() {
		$(this.cells).each(function() {
			$(this).wrapInner("<div></div>");
		});
	});	

	// adjust the width/height and add back the form to get the widths
	$elContainer.width(width || "").height(height || "")
		.css("overflowX", (width) ? "auto" : "hidden").css("overflowY", (height) ? "auto" : "hidden").append(tbd); // render
	var xscrolling = width && elContainer.scrollWidth > elContainer.clientWidth;
	var yscrolling = height && elContainer.scrollHeight > elContainer.clientHeight;

	// get the widths
	var awidth = $(tbd.rows[0].cells).map(function() {
		return this.firstChild.offsetWidth;
	}).get();

	// reset the container styles
	$elContainer.width("").height("").css({ overflowX: "hidden", overflowY: "hidden" });

	// create container for header TABLE
	var $elHdContainer, tr, thd, $thd;
	if (yscroll) {
		$elHdContainer = $("<div></div>").width(width || "").addClass(EW_SCROLLABLE_TABLE_CLASS_HEADER).appendTo($elContainer);
		var $pthd = $("<table></table>").attr({ border: 0, cellSpacing: 0, cellPadding: 0, width: "100%" });
		tr = $pthd[0].insertRow(-1);
		$(tr).addClass("ewTableHeader");		
		$thd = $("<table></table>").attr({ border: 0, cellSpacing: 0, cellPadding: 0 })
			.addClass("ewTable ewTableSeparate").appendTo($(tr.insertCell(-1)).css({ padding: "0px", border: "0px" }));
		thd = $thd[0];
		$elHdContainer.append($pthd);
	}

	// create container for body TABLE
	var $elBdContainer = $("<div></div>").width(width || "").height(height || "")
		.css("overflowX", (width) ? "auto" : "hidden").css("overflowY", (height) ? "auto" : "hidden")
		.addClass(EW_SCROLLABLE_TABLE_CLASS_BODY)
		.scroll(function(e) { if ($elHdContainer) $elHdContainer.scrollLeft(this.scrollLeft); });
	var elBdContainer = $elBdContainer[0];
	$elContainer.append(elBdContainer);

	// move the form to the body container
	$elBdContainer.append(tbd);

	// move the table header
	if (yscroll) {
		$(tbd.tHead).clone().appendTo(thd);
		$(tbd.tHead).hide();
	}

	// sync the widths
	if (awidth && tbd.rows && tbd.rows[0] && tbd.rows[0].cells) {
		var stmt = "";
		for (var i = 0, ccnt = tbd.rows[0].cells.length; i < ccnt; i++) {
			if (thd && thd.tHead && thd.tHead.rows && thd.tHead.rows[0])
				stmt += "thd.tHead.rows[0].cells[" + i + "].firstChild.style.width='" + awidth[i] + "px';";
			for (var j = 0, rcnt = tbd.rows.length; j < rcnt; j++)
				stmt += "tbd.rows[" + j + "].cells[" + i + "].firstChild.style.width='" + awidth[i] + "px';";				
		}
		if (stmt != "") eval(stmt);
	}	

	// check if scrolling
	yscrolling = elBdContainer.scrollHeight > elBdContainer.clientHeight;
	xscrolling = elBdContainer.scrollWidth > elBdContainer.clientWidth;
	if (yscroll && yscrolling && tr)
		$(tr.insertCell(-1)).addClass("ewScrollableTableOverhang").html("<div style='width: " + sbwidth + "px'></div>");

	// setup the table
	ew_SetupTable(-1, tbd);	

	// check last row
	if (yscroll && tbd.rows)
		$(tbd.rows[tbd.rows.length-1]).toggleClass(EW_TABLE_LAST_ROW_CLASSNAME, elBdContainer.offsetHeight <= tbd.offsetHeight);
}
