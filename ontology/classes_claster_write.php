<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

global $DB;

$ime = $_GET['ime'];
$opis = $_GET['opis'];
$superklasaID = $_GET['superklasaID'];
$mid = $_GET['mid'];
$uid = $_GET['uid'];
$izraziIDa = $_GET['izraziIDa'];
$site_ida = $_GET['site_ida'];

for ($i = 0; $i < strlen($ime); $i++) {
    if ($ime[$i] == ' ') {
        $ime[$i] = '_';
    }
}


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
$class->name = $ime;
$class->description = $opis;
$class->superclass = $superklasaID;
$class->userid = $uid;
$class->course_modulesid = $mid;
$class->status = '2';
$class->points = '0';
$classid = $DB->insert_record('ontology_class', $class);

foreach (explode(" ", $site_ida) as $cid) {
    $raws = $DB->get_records('ontology_class', array('superclass' => $cid));
    foreach ($raws as $raw) {
        $raw->superclass = $classid;
        $DB->update_record('ontology_class', $raw);
    }
}

//echo 'class id '.$classid;
foreach (explode(",", $izraziIDa) as $iid) {
    if ($iid == '')
        continue;
    $raw = $DB->get_record('ontology_class_expression', array('id' => $iid));
    $raw->userid = $uid;
    $raw->ontology_classid = $classid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_class_expression', $raw);
}

foreach (explode(" ", $site_ida) as $cid) {
    $raw = $DB->get_record('ontology_class', array('id' => $cid));
    $raw->status = '5';
    $DB->update_record('ontology_class', $raw);
    $raws = $DB->get_records('ontology_class_expression', array('ontology_classid' => $cid, 'status' => 3));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_classid=$classid;
        $DB->update_record('ontology_class_expression', $tmp);
    }
    smeni_klasa($cid, $classid);
}

echo 'Класата е зачувана.'
?>    