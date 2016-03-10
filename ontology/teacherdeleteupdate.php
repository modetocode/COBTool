<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$tip = $_GET["tip"];
if ($tip == 1) {
    //brishenje na klasi
    $expressionstxt = $_GET['expressions'];
    $expressions = array();
    if ($expressionstxt != "")
        $expressions = explode(' ', $expressionstxt);
    global $DB;
    for ($i = 0; $i < count($expressions); $i++) {
        $expression = $DB->get_record('ontology_class_expression', array('id' => $expressions[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_class_expression', $expression);
    }
} else
if ($tip == 2) {
    //brishenje na svojstva
    $expressionstxt = $_GET['expressions'];
    $expressionstxt2 = $_GET['expressions2'];
    $expressionstxt3 = $_GET['expressions3'];
    $expressions = array();
    $expressions2 = array();
    $expressions3 = array();
    if ($expressionstxt != "")
        $expressions = explode(' ', $expressionstxt);
    if ($expressionstxt2 != "")
        $expressions2 = explode(' ', $expressionstxt2);
    if ($expressionstxt3 != "")
        $expressions3 = explode(' ', $expressionstxt3);
    global $DB;
    for ($i = 0; $i < count($expressions); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $expressions[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($expressions2); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $expressions2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($expressions3); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $expressions3[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
} else
if ($tip == 3) {
    //brishenje na svojstva
    $expressionstxt = $_GET['expressions'];
    $expressionstxt2 = $_GET['expressions2'];
    $expressionstxt3 = $_GET['expressions3'];
    $expressions = array();
    $expressions2 = array();
    $expressions3 = array();
    if ($expressionstxt != "")
        $expressions = explode(' ', $expressionstxt);
    if ($expressionstxt2 != "")
        $expressions2 = explode(' ', $expressionstxt2);
    if ($expressionstxt3 != "")
        $expressions3 = explode(' ', $expressionstxt3);
    global $DB;
    for ($i = 0; $i < count($expressions); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $expressions[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($expressions2); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $expressions2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($expressions3); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $expressions3[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
} else
if ($tip == 4) {
    //brishenje na instanci
    $expressionstxt = $_GET['expressions'];
    $expressionstxt2 = $_GET['expressions2'];
    $expressionstxt3 = $_GET['expressions3'];
    $expressions = array();
    $expressions2 = array();
    $expressions3 = array();
    if ($expressionstxt != "")
        $expressions = explode(' ', $expressionstxt);
    if ($expressionstxt2 != "")
        $expressions2 = explode(' ', $expressionstxt2);
    if ($expressionstxt3 != "")
        $expressions3 = explode(' ', $expressionstxt3);
    global $DB;
    for ($i = 0; $i < count($expressions); $i++) {
        $expression = $DB->get_record('ontology_individual_expression', array('id' => $expressions[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_expression', $expression);
    }
    for ($i = 0; $i < count($expressions2); $i++) {
        $expression = $DB->get_record('ontology_individual_property_individual', array('id' => $expressions2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_property_individual', $expression);
    }
    for ($i = 0; $i < count($expressions3); $i++) {
        $expression = $DB->get_record('ontology_individual_property_data', array('id' => $expressions3[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_property_data', $expression);
    }
}