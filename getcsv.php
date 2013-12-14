<?php

require('db.php');
require('core.php');

if(array_key_exists('interested', $_POST)) {
    $fp = fopen('php://output', 'w'); 
    
    // Form submitted, find data
    $data = doExec($_POST);
    if(!is_array($data)) {
        // If its not an array, an error occured
        die('An error occured while producing the CSV: '.$data);
    } else {
        header("Content-type: text/csv");  
        header("Cache-Control: no-store, no-cache");  
        header('Content-Disposition: attachment; filename="'.str_replace(' ', '_', str_replace(',', '', $data['query'])).'.csv"');

        fputcsv($fp, array($data['query'], '', '', '', ''));
        
        foreach($data['groups'] as $group_option => $num_in_group) {
            if($data['filter'] != '') {
                fputcsv($fp, array('', '', '', '', ''));
                fputcsv($fp, array('Filter applied: '.$data['filter'], '', '', '', ''));
            }
			fputcsv($fp, array('', '', '', '', ''));
            fputcsv($fp, array('Group:', $group_option, '', ''));
            fputcsv($fp, array('Question', 'Response', 'Responses in group', 'Percentage of group responses', 'Percentage of all responses'));

            foreach($data['questions'] as $question => $statistics) {
                foreach($statistics['question_options'] as $field_opt) {
                    if(!array_key_exists($field_opt, $statistics['grouped_question_responses'][$group_option])) {
                        $statistics['grouped_question_responses'][$group_option][$field_opt] = 0;
                    }
                    
                    // Question name, Answer given to question
                    // Number of responses to question in this group
                    // Number of responses with that answer in group / number of responses in group
                    // Number of responses with that answer in group / number of responses with that answer
                    fputcsv($fp,array($question,
                    $field_opt,
                    $statistics['grouped_question_responses'][$group_option][$field_opt],
                    round(($statistics['grouped_question_responses'][$group_option][$field_opt]/$statistics['group_options'][$group_option])*100,2),
                    round(($statistics['grouped_question_responses'][$group_option][$field_opt]/$statistics['total_question_responses'][$field_opt])*100,2)
                    ));
                }
            }
        }
    }
} else {
    die('Data was not passed through');
}