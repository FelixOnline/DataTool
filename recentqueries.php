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

    <title>Query history for <?php echo $felix_survey_name; ?></title>

    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap-responsive.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="navbar navbar-fixed-top navbar-inverse">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="#">Query history for <?php echo $felix_survey_name; ?></a>
                </div>
            </div>
        </div>
		<div class="container">
	        <div class="btn-group">
		        <a href="#" class="btn" onclick="window.close(); return;">Close</a>
		        <a href="#" class="btn" onclick="document.location.reload(); return;">Update query list</a>
		    </div>
		    <br>
	        <?php
	        	$sql = 'SELECT * FROM `'.$db_table.'_history` WHERE `date` >= 1361996400 ORDER BY `date` DESC';
	        	
				$res = mysql_query($sql);
				
				if ($res == FALSE) {
					return('<div class="alert alert-error">Error: '.mysql_error()."</div>");
				}
				
				while($row = mysql_fetch_object($res)) {
					echo '<b class="muted">'.strtoupper(date('l jS F Y H:i', $row->date)).'</b>';
					$form = unserialize($row->query);
					echo '<form action="index.php" method="POST" target="dt_form_'.sha1($felix_survey_name).'">';
					foreach($form as $key => $value) {
						if(is_array($value)) {
							foreach($value as $value2) {
								echo '<input type="hidden" name="'.$key.'[]" value="'.$value2.'">';
							}
						} else {
							echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
						}
					}
					echo '<input type="hidden" name="dontlog" value="1">';
					echo '<input type="submit" value="Rerun query" class="btn btn-primary pull-right">';
					echo '<p style="font-weight: bold; margin-bottom: 0; padding-bottom: 0;" class="lead">'.$row->title.'</p>';
					echo '<p style="margin-bottom: 0;">'.$row->filter.'</p>';
					echo '</form>';
				}
				
				if(mysql_num_rows($res) == 0) {
					echo '<div class="alert alert-info">No queries have been run.</div><i>Some queries may no longer be available if the query format has changed during an update, see the changelog for details.</i>';
				}
			?>
		</div>
    </div>
</body>
</html>
