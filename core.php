<?php

function doExec($passedin) {
	global $db_table;
	
	// Run statistics on every question we wanted data on
	$q_stats = array();
	
	foreach($passedin['interested'] as $question) {
		$find = mysql_real_escape_string($question);
		$group = mysql_real_escape_string($passedin['filter']);
		$restrict_field = mysql_real_escape_string($passedin['restriction']);
		$restrict_value = mysql_real_escape_string($passedin['restriction_comparative']);
		
		// Apply grouping
		if($group != 'nofilter') {
			$sql = "SELECT `$find`, `$group` FROM `$db_table`";
		} else {
			$sql = "SELECT `$find` FROM `$db_table`";
		}
		
		$filter_desc = '';
		
		// Apply filter
		switch($passedin['restriction_type']) {
			case 'eq':
				$sql .= " WHERE `$restrict_field` = '$restrict_value'";
				$filter_desc = "Where $restrict_field is equal to $restrict_value.";
				break;
			case 'neq':
				$sql .= " WHERE `$restrict_field` != '$restrict_value'";
				$filter_desc = "Where $restrict_field is not equal to $restrict_value.";
				break;
			case 'lt':
				$sql .= " WHERE `$restrict_field` < '$restrict_value'";
				$filter_desc = "Where $restrict_field is less than $restrict_value.";
				break;
			case 'gt':
				$sql .= " WHERE `$restrict_field` > '$restrict_value'";
				$filter_desc = "Where $restrict_field is greater than $restrict_value.";
				break;
			case 'lte':
				$sql .= " WHERE `$restrict_field` <= '$restrict_value'";
				$filter_desc = "Where $restrict_field is less than or equal to $restrict_value.";
				break;
			case 'gte':
				$sql .= " WHERE `$restrict_field` >= '$restrict_value'";
				$filter_desc = "Where $restrict_field is greater than or equal to $restrict_value.";
				break;
			case 'like':
				$sql .= " WHERE `$restrict_field` LIKE '%$restrict_value%'";
				$filter_desc = "Where $restrict_field contains $restrict_value.";
				break;
		}
		
		// Describe ignore filters
		if(array_key_exists('ignoredws', $passedin) && $passedin['ignoredws'] == 1) {
			$filter_desc .= ' Ignoring \'Don\'t wish to say\'.';
		}
		
		if(array_key_exists('ignorena', $passedin) && $passedin['ignorena'] == 1) {
			$filter_desc .= ' Ignoring \'N/A\'.';
		}
		
		if($passedin['restriction_type'] != 'ignore') {
			if($restrict_value == '') {
				return('Error: Please supply a value to search with if you use a Where filter');
			}
		}
		
		$res = mysql_query($sql);
		
		if ($res == FALSE) {
			return('Error: '.mysql_error()."\n");
		}
		
		$find_global_totals = array();
	
		// Get totals
		$sql = "SELECT COUNT(*) AS `NumResponses`, `$find` AS `Category` FROM `$db_table` GROUP BY `$find`";
		
		$res2 = mysql_query($sql);
		if ($res2 == FALSE) {
			return('Error: '.mysql_error()."\n");
		}
		
		while($row2 = mysql_fetch_object($res2)) {
			$find_global_totals[$row2->Category] = $row2->NumResponses;
		}
		
		/*
		 * Some explanation:
		 *
		 * Filter values are what we compare the data to
		 * Field values are the corresponding values for that field in the response
		 *
		 * For example, if our filter is gneder, and field is eye colour
		 * filter values are Male/Female/(Don't want to say)
		 * field values are blue/green/brown/hazel/...
		 */
		
		$find_options = array();
		$group_options = array();
		$data = array();
		$data_count = array();
		$data_ungrouped = array();
		
		// For every response
		while($row = mysql_fetch_assoc($res)) {
			// Do the desired columns still exist
			if(!array_key_exists($find, $row)) {
				return("This is really bad, desired col $find is no longer valid. Tell Philip.\n");
			}
		
			if($group != 'nofilter') {
				if(!array_key_exists($group, $row)) {
					return("This is really bad, grouping col $group is no longer valid. Tell Philip.\n");
				}
			}
		
			// Obtain the desired column and grouping columns
			$find_val = $row[$find];
			
			if($group != 'nofilter') {
				$group_val = $row[$group];
			} else {
				$group_val = 'All responses';
			}
			
			// Strip out 'Don't wish to say' and 'N/A' if requested
			if(array_key_exists('ignoredws', $passedin) && $passedin['ignoredws'] == 1 && strtolower($group_val) == 'don\'t wish to say') {
				continue;
			}
			
			if(array_key_exists('ignorena', $passedin) && $passedin['ignorena'] == 1 && strtolower($group_val) == 'n/a') {
				continue;
			}
		
			// If we havent tracked any data for this grouping value, start tracking
			if(!array_key_exists($group_val, $data)) {
				$data[$group_val] = array();
				$data_count[$group_val] = 0;
			}
		
			// Strip out 'Don't wish to say' and 'N/A' if requested, again
			if(array_key_exists('ignoredws', $passedin) && $passedin['ignoredws'] == 1 && strtolower($find_val) == 'don\'t wish to say') {
				continue;
			}
			
			if(array_key_exists('ignorena', $passedin) && $passedin['ignorena'] == 1 && strtolower($find_val) == 'n/a') {
				continue;
			}
		
			// Record that we have a new item in this group
			$data_count[$group_val]++;
		
			// If we have a response value which we havent tracked before in this group, start tracking
			// i.e. if we havent yet recorded any men with blue eyes
			if(!array_key_exists($find_val, $data[$group_val])) {
				$data[$group_val][$find_val] = 0;
			}
			
			if(!array_key_exists($find_val, $data_ungrouped)) {
				$data_ungrouped[$find_val] = 0;
			}
		
			// Record that this response has occured in this group
			$data[$group_val][$find_val]++;
			$data_ungrouped[$find_val]++;
		
			// Add the recorded group and response to the list of all groups and responses
			$find_options[] = $find_val;
			$group_options[] = $group_val;
		}
		
		// Only use recorded options once
		$find_options = array_unique($find_options);
		$group_options = array_unique($group_options);
		
		// Alphabetical sort
		asort($find_options);
		asort($group_options);
		
		// Find the totals in each group
		$final_group_options = array();
		
		foreach($group_options as $group) {
			$final_group_options[$group] = array_sum($data[$group]);
		}
		
		// So
		// question_options = All the answers that are possible for this question
		// group_options = All groups for which responses exist, and the total number of responses in each group (for all possible question options)
		// grouped_question_responses = The number of people who chose each option, organised by group
		// total_question_respones = The number of people who chose each option, full stop.
		$q_stats[$question] = array('question_options' => $find_options, 'group_options' => $final_group_options, 'grouped_question_responses' => $data, 'total_question_responses' => $data_ungrouped);
	}
		
	// Record what the query was
	$find = implode(', ', $passedin['interested']);
	if($group != 'nofilter') {
		$query = $find.': grouped by '.$group;
	} else {
		$query = $find.': all responses';
	}
	
	$total_groups = array();
	$has_responses = 0;
	
	// Check to see if we have results, and list every group option found
	foreach($q_stats as $q_stat) {
		if(array_sum($q_stat['total_question_responses']) > 0 && $has_responses == 0) {
			$has_responses = 1;
		}
		
		// Lazy way of recording that a group was found
		foreach($q_stat['group_options'] as $group => $reponses) {
			$total_groups[$group] = $group;
		}
	}
	
	// Log the item in the query history
	if(!array_key_exists('dontlog', $passedin) || $passedin['dontlog'] != '1') {
		$store = serialize($passedin);
		
		$sql = "INSERT INTO `".$db_table."_history` VALUES(NULL, UNIX_TIMESTAMP(), '".mysql_real_escape_string($query)."', '".mysql_real_escape_string($filter_desc)."', '".mysql_real_escape_string($store)."')";
		
		$res2 = mysql_query($sql);
		if ($res2 == FALSE) {
			return('Error: '.mysql_error()."\n");
		}
	}
	
	// Return data to output format
	// query = Human name of query executed
	// filter = Human name of what filters were applied
	// questions = Statistics on each question requested (see $q_stats)
	// groups = Every group that was returned
	// has_respoonses = Are there responses
	return array('query' => $query, 'filter' => $filter_desc, 'questions' => $q_stats, 'groups' => $total_groups, 'has_responses' => $has_responses);
}