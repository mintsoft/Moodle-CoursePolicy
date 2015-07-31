<?php
/* coursepolicy.php by Nathaniel J. Bird (April 2007) for AliveTek, Inc. www.alivetek.com. 
// This is based on the policy.php 
// If an html file named coursepolicy#.php (# symbol replaced by an actual course id), then the page will be displayed 
//    with a requirement to click "Yes" before being able to continue with the course.
// Modifications to lib\moodlelib.php and lang\en_utf8\moodle.php are required. 
// A new VARCHAR field named coursepoliciesagreed is required in the mdl_user table also. 
*/
	
require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');

// Incoming variables
$id = optional_param('id', 0, PARAM_INT);
$agree = optional_param('agree', 0, PARAM_BOOL);
$courseagreed = optional_param('courseagreed', 0, PARAM_INT);

define('MESSAGE_WINDOW', true);  // This prevents the message window coming up

if (!isloggedin()) {
    require_login();
}

if ($agree and confirm_sesskey() and is_numeric($courseagreed)) {    // User has agreed
    $USER->coursepoliciesagreed .= ','.$courseagreed;
    if (!isguestuser()) {              // Don't remember guests
        if (!$DB->set_field('user', 'coursepoliciesagreed', $USER->coursepoliciesagreed, array('id' =>  $USER->id))) {
            error('Could not save your agreement');
        }
    }
    if (!empty($SESSION->wantsurl)) {
        $wantsurl = $SESSION->wantsurl;
        unset($SESSION->wantsurl);
        redirect($wantsurl);
    } else {
        redirect($CFG->wwwroot.'/');
    }
    exit;
}

$strpolicyagreement="Course Policy Agreement";

print_header($strpolicyagreement, $SITE->fullname, $strpolicyagreement);

if (empty($CFG->slasharguments)) {
    $coursepolicypage = $CFG->wwwroot.'/file.php?file=/1/coursepolicy'.$id.'.html';
} else {
    $coursepolicypage = $CFG->wwwroot.'/file.php/1/coursepolicy'.$id.'.html';
}

//RobHACK 2.0
$urlContent = file_get_contents($CFG->dataroot.'/1/coursepolicy'.$id.'.html');
$urlContent = trim($urlContent);
echo '<div class="noticebox" align="center">';
echo '<div style="width: 70%;" >'.$urlContent.'</div>';
echo '</div>';
//end robhack 2.0

$linkyes    = 'coursepolicy.php'; // Send back here to store agreement in the database
$optionsyes = array('agree'=>1, 'sesskey'=>sesskey(), 'courseagreed'=>$id);
$linkno     = $CFG->wwwroot; // Send user to home page if they don't agree
$optionsno  = array('sesskey'=>sesskey());

$strpolicyagree='You must agree to this policy to continue using this site.  Do you agree?';

echo $OUTPUT->confirm($strpolicyagree, new moodle_url($linkyes, $optionsyes), new moodle_url($linkno, $optionsno));

print_footer();