<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

global $DB;

$ime = $_GET['ime'];
$opis = $_GET['opis'];
$intanciodID = $_GET['intanciodID'];
$mid = $_GET['mid'];
$uid = $_GET['uid'];
$objektniID = $_GET['objektniID'];
$podatochniID = $_GET['podatochniID'];
$site_ida = $_GET['site_ida'];

for ($i = 0; $i < strlen($ime); $i++) {
    if ($ime[$i] == ' ') {
        $ime[$i] = '_';
    }
}

//echo $ime.' '.$opis.' '.$intanciodID.' '.$mid.' '.$uid.' '.$objektniID.' '.$podatochniID.' '.$site_ida;
//$MAX=$DB->get_records('ontology_class');
//$mmax=0;
//foreach($MAX as $tmp){
//    if($tmp->id>$mmax)
//    $mmax=$tmp->id;
//}
//$mmax+=1;
//echo $ime.'</br>';
//echo $opis.'</br>';
//echo $superklasaID.'</br>';
//echo $izraziIDa.'</br>';
//$class->id=$mmax;
$individual->name = $ime;
$individual->description = $opis;
$individual->userid = $uid;
$individual->course_modulesid = $mid;
$individual->status = '2';
$individual->points = '0';
$individualid = $DB->insert_record('ontology_individual', $individual);



//echo 'class id '.$classid;
foreach (explode(",", $intanciodID) as $iid) {
    if ($iid == '')
        continue;
    $raw = $DB->get_record('ontology_individual_expression', array('id' => $iid));
    $raw->userid = $uid;
    $raw->ontology_individualid = $individualid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_individual_expression', $raw);
}

foreach (explode(",", $objektniID) as $oid) {
    if ($oid == '')
        continue;
    $raw = $DB->get_record('ontology_individual_property_individual', array('id' => $oid));
    $raw->userid = $uid;
    $raw->ontology_individualid = $individualid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_individual_property_individual', $raw);
}

foreach (explode(",", $podatochniID) as $pid) {
    if ($pid == '')
        continue;
    $raw = $DB->get_record('ontology_individual_property_data', array('id' => $pid));
    $raw->userid = $uid;
    $raw->ontology_individualid = $individualid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_individual_property_data', $raw);
}

foreach (explode(" ", $site_ida) as $iid) {
    $raw = $DB->get_record('ontology_individual', array('id' => $iid));
    $raw->status = '5';
    $DB->update_record('ontology_individual', $raw);
    $raws = $DB->get_records('ontology_individual_expression', array('ontology_individualid' => $iid, 'status' => 3));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_individualid=$individualid;
        $DB->update_record('ontology_individual_expression', $tmp);
    }
    $raws = $DB->get_records('ontology_individual_property_individual', array('ontology_individualid' => $iid, 'status' => 3));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_individualid=$individualid;
        $DB->update_record('ontology_individual_property_individual', $tmp);
    }
    $raws = $DB->get_records('ontology_individual_property_individual', array('ontology_individualid2' => $iid, 'status' => 2));
    foreach ($raws as $tmp) {
        $tmp->ontology_individualid2 = $individualid;
        $DB->update_record('ontology_individual_property_individual', $tmp);
    }
    $raws = $DB->get_records('ontology_individual_property_data', array('ontology_individualid' => $iid, 'status' => 3));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_individualid=$individualid;
        $DB->update_record('ontology_individual_property_data', $tmp);
    }
    smeni_instanca($iid, $instanceid);
}

echo 'Инстанцата е зачувана.'
?>    