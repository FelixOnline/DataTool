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

    <title>Felix Surveys: <?php echo $felix_survey_name; ?></title>

    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="bootstrap-responsive.min.css">

	<script language="javascript" type="text/javascript">
		window.name = "dt_form_<?php echo sha1($felix_survey_name); ?>";

		function open_recent() {
			newwindow=window.open('recentqueries.php','Query history','height=800,width=600');
			if (window.focus) {newwindow.focus()}
			return false;
		}
		
		function open_changelog() {
			newwindow=window.open('changelog.php','Changelog','height=800,width=600');
			if (window.focus) {newwindow.focus()}
			return false;
		}
	</script>
</head>
<body>
    <div class="container">
        <div class="navbar navbar-fixed-top navbar-inverse">
            <div class="navbar-header">
                <div class="container">
                    <a class="navbar-brand" href="index.php">Felix Surveys Data Tool</a>
                </div>
            </div>
        </div>
        <div class="visible-desktop" style="padding-top: 40px;">
	    </div>
        <header id="head">
            <h1><?php echo $felix_survey_name; ?></h1>
            <div class="alert alert-info">
		Please remember to close your browser after using this tool, as this is necessary to log out.
            </div>
        </header>
        <div class="well">
            <form class="form-inline" action="index.php" method="post" style="margin-bottom: 0;">
	            <div class="row" style="margin-left: -10px !important;">
		            <div class="col-md-4">
				<label for="interested">Find (select multiple using ctrl/cmd)</label><br>
		                <select id="interested" name="interested[]" multiple style="width: 100%; margin-top: 5px; margin-bottom: 5px; height: 150px" class="form-control">
		                    <?php foreach(getColNames() as $col): ?>
		                    <option <?php if(array_key_exists('interested', $_POST) && array_search($col, $_POST['interested']) !== FALSE): echo 'selected '; endif;?>value="<?php echo $col; ?>">
		                        <?php echo $col; ?>
		                    </option>
		                    <?php endforeach; ?>
		                </select>
		             </div>
		             <div class="col-md-8">
                		<label for="filter">grouped by</label>
		                <select name="filter" class="form-control" id="filter">
		                    <option <?php if(array_key_exists('interested', $_POST) && $_POST['interested'] == 'nofilter'): echo 'selected '; endif; ?>value="nofilter">
		                        No grouping (all responses)
		                    </option>
		                    <?php foreach(getColNames() as $col): ?>
		                    <option <?php if(array_key_exists('filter', $_POST) && $_POST['filter'] == $col): echo 'selected '; endif; ?>value="<?php echo $col; ?>">
		                        <?php echo $col; ?>
		                    </option>
		                    <?php endforeach; ?>
		                </select>&nbsp;
				<div class="btn-group pull-right">
		                <input type="submit" class="btn btn-primary" value="Search">
		                <input type="reset" class="btn btn-default" value="Clear">
		                </div>
				<br><br>
		                <label for="restriction">Where</label>
		                <select name="restriction" id="restriction" class="form-control">
		                    <?php foreach(getColNames() as $col): ?>
		                    <option <?php if(array_key_exists('restriction', $_POST) && $_POST['restriction'] == $col): echo 'selected '; endif; ?> value="<?php echo $col; ?>">
		                        <?php echo $col; ?>
		                    </option>
		                    <?php endforeach; ?>
		                </select>&nbsp;
		                <select name="restriction_type" class="form-control">
		                    <?php
		                    	$types = array('ignore' => 'Ignore this criterion', 'eq' => '=', 'neq' => '!=', 'lt' => '&lt;', 'gt' => '&gt;', 'lte' => '&lt;=', 'gte' => '&gt;=', 'like' => 'Contains');
		                    ?>
		                    <?php foreach($types as $key => $value): ?>
							<option <?php if(array_key_exists('restriction_type', $_POST) && $_POST['restriction_type'] == $key): echo 'selected '; endif; ?> value="<?php echo $key; ?>">
			                    <?php echo $value; ?>
			                </option>
			                <?php endforeach; ?>
		                </select>&nbsp;
		                <input type="text" class="form-control" <?php if(array_key_exists('filter', $_POST)): echo 'value="'.$_POST['restriction_comparative'].'" '; endif; ?> name="restriction_comparative" style="width: 
auto !important">
		                <br><br>
		                <label class="checkbox-inline">
		                    <input type="checkbox" <?php if(array_key_exists('ignorena', $_POST) && $_POST['ignorena'] == 1): ?>checked <?php endif; ?>value="1" id="ignorena" name="ignorena">
		                    &nbsp;Ignore N/A responses
		                </label>&nbsp;&nbsp;&nbsp;
		                <label class="checkbox-inline">
		                    <input type="checkbox" <?php if(array_key_exists('ignoredws', $_POST) && $_POST['ignoredws'] == 1): ?>checked <?php endif; ?>value="1" id="ignoredws" name="ignoredws">
		                    &nbsp;Ignore 'Don't wish to say'
		                </label>
		                <a href="#" onclick="return open_recent();" class="btn btn-info pull-right">Choose a previous query</a>
		             </div>
		</div>
            </form>
        </div>
        <?php
            if(array_key_exists('interested', $_POST)) {
	            if(count($_POST['interested']) != 1): ?>
	            	<div class="alert alert-info">
		            	Graphs are not available if more than one question is looked up.
		            </div>
		        <?php endif;
                // Form submitted, find data
                $data = doExec($_POST);
                if(!is_array($data)) {
                    // If its not an array, an error occured
                    ?><div class="alert alert-danger"><?php echo($data); ?></div><?php
                } else {
                    ?>
                        <form action="getcsv.php" class="pull-right" method="post">
                            <?php
                            foreach($_POST as $key => $value) {
								if(is_array($value)) {
									foreach($value as $value2) {
										echo '<input type="hidden" name="'.$key.'[]" value="'.$value2.'">';
									}
								} else {
									echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
								}
							}
                            echo '<input type="hidden" name="dontlog" value="1">';
                            ?>
                            <input type="submit" value="Export as CSV" class="btn btn-default">
                        </form>
						<?php if(count($_POST['interested']) == 1): ?>
						<?php $rt = sha1(time().rand()); ?>
                        <form action="getbarchart.php" class="pull-right" method="post" style="margin-right: 5px;" target="POPUPW_<?php echo $rt; ?>" onsubmit="POPUPW_<?php echo $rt; ?> = window.open('about:blank','POPUPW_<?php echo $rt; ?>', 'width=750,height=500');">
                            <?php
                            foreach($_POST as $key => $value) {
								if(is_array($value)) {
									foreach($value as $value2) {
										echo '<input type="hidden" name="'.$key.'[]" value="'.$value2.'">';
									}
								} else {
									echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
								}
							}
                            echo '<input type="hidden" name="dontlog" value="1">';
                            ?>
                            <input type="submit" value="Generate bar chart" class="btn btn-default">
                        </form>
                    	<?php endif; ?>
                    <?php
                    echo '<h2>'.strtoupper($data['query']).'</h2>';
                    echo '<p>'.$data['filter'].'</p>';
                    
                    // How many responses do we have in total?                  
                    if($data['has_responses'] == 0) {
	                    echo '<p class="lead">NO RESPONSES FOUND</p>';
                    } else {                    
	                    foreach($data['groups'] as $group_option) {
	                        ?>
	                        <?php if(count($_POST['interested']) == 1): ?>
	                        <?php $rt = sha1(time().rand()); ?>
	                        <form action="getpiechart.php" class="pull-right" method="post" target="POPUPW_<?php echo $rt; ?>" onsubmit="POPUPW_<?php echo $rt; ?> = window.open('about:blank','POPUPW_<?php echo $rt; ?>', 'width=750,height=500');">
	                            <?php
	                            foreach($_POST as $key => $value) {
									if(is_array($value)) {
										foreach($value as $value2) {
											echo '<input type="hidden" name="'.$key.'[]" value="'.$value2.'">';
										}
		                            } else {
	                                	echo '<input type="hidden" name="'.$key.'" value="'.$value.'">';
	                                }
	                            }
	                            ?>
	                            <input type="hidden" name="dontlog" value="1">
	                            <input type="hidden" name="requested_group" value="<?php echo $group_option; ?>">
	                            <input type="submit" value="Generate pie chart" class="btn btn-default">
	                        </form>
	                    	<?php endif; ?>
	                        <?php
	                        echo '<p class="lead">'.strtoupper($_POST['filter']).': '.strtoupper($group_option).'</p>';
	                        ?>
	                        <table class="table table-bordered table-hover table-striped" style="width: 70%;">
	                            <tr>
	                                <th>Question</th><th>Response</th><th>Responses in group</th><th>Percentage of group responses</th><th>Percentage of all responses</th>
	                            </tr>
	                        <?php
	                        foreach($data['questions'] as $question => $statistics) {
		                        foreach($statistics['question_options'] as $field_opt) {
		                    	    if(!array_key_exists($field_opt, $statistics['grouped_question_responses'][$group_option])) {
		                    	        $statistics['grouped_question_responses'][$group_option][$field_opt] = 0;
		                    	    }
		                    		
		                    		// Question name, Answer given to question
		                    		// Number of responses to question in this group
		                    		// Number of responses with that answer in group / number of responses in group
		                    		// Number of responses with that answer in group / number of responses with that answer
		                    	    echo '<tr><td>'.$question.'</td><td>'.$field_opt."</td><td>
			                    	".$statistics['grouped_question_responses'][$group_option][$field_opt]."</td><td>
				                    ".round(($statistics['grouped_question_responses'][$group_option][$field_opt]/$statistics['group_options'][$group_option])*100,2)."</td><td>
					                ".round(($statistics['grouped_question_responses'][$group_option][$field_opt]/$statistics['total_question_responses'][$field_opt])*100,2)."</td></tr>";
		                    	}
								echo "<tr style=\"font-weight: bold; color: white;\"><td style=\"background: black;\"></td><td style=\"background: black;\"></td><td style=\"background: black;\">".array_sum($statistics['grouped_question_responses'][$group_option])."</td><td style=\"background: black;\">100</td><td style=\"background: black;\"></td></tr>";
		                    }

	                    	// Also report all responses within this filter
	                    	if(count($_POST['interested']) == 1):

	                    	endif;
	                    	echo '</table>';
	                    }
	                }
                }
            } else {
        ?>
        <div class="jumbotron">
            <h1>Hi, there!</h1>
            <p>What would you like to find out? Run a query to display some statistics.</p>
        </div>
        <?php
            }
        ?>
        <footer style="border-top: 1px solid #e5e5e5; color: #777; padding: 30px 0; margin-top: 70px;">
			<p>&copy; Felix Imperial <a href="#head">Top of page</a> - <a href="#" onClick="return open_changelog();">Version 1.4</a></p>
        </footer>
    </div>
</body>
</html>
