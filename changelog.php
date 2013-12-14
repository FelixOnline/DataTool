<?php require('db.php'); ?>
<?php require('core.php'); ?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title>Felix Surveys Data Tool - Changelog</title>

    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap-responsive.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="navbar navbar-fixed-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="#">Felix Surveys Data Tool - Changelog</a>
                </div>
            </div>
        </div>
		<div class="visible-desktop" style="padding-top: 60px;">
        </div>
		<div class="container">
	        <div class="btn-group">
		        <a href="#" class="btn" onclick="window.close(); return;">Close</a>
		    </div>
		    <br>
		    <p><b>Felix Surveys Data Tool</b> version 1.3, by Philip Kent. Portions from pChart, released under the terms of the GNU GPL v3.</p>
		    <p>This software comes with no warranty or guarantee, and is provided to you under the terms of the 3 clause BSD license.</p>
		    <h3>Changelog</h3>
			<b class="muted">VERSION 1.3.1 (26/02/2013)</b>
			<ul>
				<li>Internal tidyup, making the data arrays a bit more logical.</li>
				<li>When calculating 'Percentage of group responses	', fix a bug where the number we divide by may not necessarily be the number of responses in the group for that question.</li>
				<li>Remove incorrect count for total responses.</li>
				<li>Record whether 'Ignore' boxes were ticked in the Filter list.</li>
			</ul>
			<b class="muted">VERSION 1.3 (26/02/2013)</b>
			<ul>
				<li>Added the option of finding multiple questions by one grouping and set of filters.</li>
				<li>Cosmetic improvements.</li>
				<li><i>Query history before this date will be ignored as the format of stored queries has changed since version 1.2.</i></li>
			</ul>
			<b class="muted">VERSION 1.2</b>
			<ul>
				<li>Added query history, and made graphs appear in popup windows.</li>
			</ul>
			<b class="muted">VERSION 1.1</b>
			<ul>
				<li>Added checkboxes to ignore 'Do not wish to say' and 'N/A' responses and groupings.</li>
				<li>Added column for the number of a specific response in a group as a percentage of all of that response.</li>
			</ul>
			<b class="muted">VERSION 1.0</b>
			<p>Initial release.</p>
		</div>
    </div>
</body>
</html>
