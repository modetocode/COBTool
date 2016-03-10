<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$note = $_GET["note"];
$id = $_GET['id'];
$grditem = $DB->get_record('grade_items', array('itemmodule' => 'ontology', 'iteminstance' => $id));
$userid = $_GET['userid'];
$grdgrade = $DB->get_records('grade_grades', array('itemid' => $grditem->id, 'userid' => $userid));
if (count($grdgrade) == 0) {
    $grade->itemid = $grditem->id;
    $grade->userid = $userid;
    $grade->rawgrade = '0';
    $grade->rawgrademax = '100.00000';
    $grade->rawgrademin = '0.00000';
    $grade->finalgrade = '0';
    $grade->hidden = '0';
    $grade->locked = '0';
    $grade->locktime = '0';
    $grade->exported = '0';
    $grade->overridden = '0';
    $grade->excluded = '0';
    $grade->feedback = $note;
    $grade->feedbackformat = '0';
    $grade->informationformat = '0';
    $grade->timecreated = time();
    $grade->timemodified = time();
    $DB->insert_record('grade_grades', $grade);
} else {
    foreach ($grdgrade as $key => $value) {
        $value->feedback = $note;
        $DB->update_record('grade_grades', $value);
        break;
    }
}
?>