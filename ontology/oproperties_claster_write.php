<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

global $DB;

$ime = $_GET['ime'];
$opis = $_GET['opis'];
$nadsvojstvoID = $_GET['superpropertyID'];
$mid = $_GET['mid'];
$uid = $_GET['uid'];
$attributes = $_GET['attributes'];
$disjointID = $_GET['disjointID'];
$equivalentID = $_GET['equivalentID'];
$expressionID = $_GET['expressionID'];
$inverseID = $_GET['inverseID'];
$site_ida = $_GET['site_ida'];

for ($i = 0; $i < strlen($ime); $i++) {
    if ($ime[$i] == ' ') {
        $ime[$i] = '_';
    }
}

//echo 'ime:'.$ime.' opis:'.$opis.' nads:'.$nadsvojstvoID.' atrb:'.$attributes.' disj:'.$disjointID.' equ:'.$equivalentID.' expr:'.$expressionID.' inv:'.$inverseID.' siteid:'.$site_ida;
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
$property->name = $ime;
$property->description = $opis;
$property->superproperty = $nadsvojstvoID;
$property->userid = $uid;
$property->inverse = $inverseID;
$property->attributes = $attributes;
$property->course_modulesid = $mid;
$property->status = '2';
$property->points = '0';
$propertyid = $DB->insert_record('ontology_property_individual', $property);

//zamenuvanje na superklasa na site ostanati podklasi so novata klasa
foreach (explode(" ", $site_ida) as $pid) {
    $raws = $DB->get_records('ontology_property_individual', array('superproperty' => $pid));
    foreach ($raws as $raw) {
        $raw->superproperty = $propertyid;
        $DB->update_record('ontology_property_individual', $raw);
    }
}
//zamenuvanje na site inverzni, isto kako superklasite
foreach (explode(" ", $site_ida) as $pid) {
    $raws = $DB->get_records('ontology_property_individual', array('inverse' => $pid));
    foreach ($raws as $raw) {
        $raw->inverse = $propertyid;
        $DB->update_record('ontology_property_individual', $raw);
    }
}


//echo 'class id '.$classid;
foreach (explode(",", $disjointID) as $did) {
    if ($did == '')
        continue;
    $raw = $DB->get_record('ontology_property_disjoint', array('id' => $did));
    $raw->userid = $uid;
    $raw->ontology_propertyid = $propertyid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_property_disjoint', $raw);
}

//
foreach (explode(",", $equivalentID) as $eid) {
    if ($eid == '')
        continue;
    $raw = $DB->get_record('ontology_property_equivalent', array('id' => $eid));
    $raw->userid = $uid;
    $raw->ontology_propertyid = $propertyid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_property_equivalent', $raw);
}

foreach (explode(",", $expressionID) as $eid) {
    if ($eid == '')
        continue;
    $raw = $DB->get_record('ontology_property_expression', array('id' => $eid));
    $raw->userid = $uid;
    $raw->ontology_propertyid = $propertyid;
    $raw->status = '2';
    $raw->points = '0';
    $DB->insert_record('ontology_property_expression', $raw);
}


foreach (explode(" ", $site_ida) as $pid) {
    $raw = $DB->get_record('ontology_property_individual', array('id' => $pid));
    $raw->status = '5';
    $DB->update_record('ontology_property_individual', $raw);
    $raws = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $pid, 'type' => 1));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_propertyid=$propertyid;
        $DB->update_record('ontology_property_disjoint', $tmp);
    }
    $raws = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $pid, 'type' => 1));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
        //       $tmp->ontology_propertyid=$propertyid;
        $DB->update_record('ontology_property_equivalent', $tmp);
    }
    $raws = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $pid, 'type' => 1));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_propertyid=$propertyid;
        $DB->update_record('ontology_property_expression', $tmp);
    }
    $raws = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $pid, 'type' => 2));
    foreach ($raws as $tmp) {
        $tmp->status = '5';
//        $tmp->ontology_propertyid=$propertyid;
        $DB->update_record('ontology_property_expression', $tmp);
    }
    smeni_obj_svojstvo($pid, $propertyid);
}

echo 'Својството е зачувано.'
?>    