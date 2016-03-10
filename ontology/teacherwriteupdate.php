<?php

//zapishuvanje na podatoci za:
// TIP == 1     azuriranja na postoecka klasa
// TIP == 2     azuriranja na objektno svojstvo
// TIP == 3     azuriranja na podatocno svojstvo
// TIP == 4     azuriranja na individua

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$tip = $_GET["tip"];
if ($tip == 1) {
    //azuriranja na postoecka klasa
    $accepted = $_GET["accepted"];
    $refused = $_GET["refused"];
    $newSuperClass = $_GET["superclassExp"];
    $exp1 = array();
    $exp2 = array();
    if ($accepted != "")
        $exp1 = explode(' ', $accepted);
    if ($refused != "")
        $exp2 = explode(' ', $refused);
    global $DB;

    if ($newSuperClass != -1) {
        $expression = $DB->get_record('ontology_class_expression', array('id' => $exp1[0]));
        $class = $DB->get_record('ontology_class', array('id' => $expression->ontology_classid));
        $class->superclass = $newSuperClass;
        $DB->update_record('ontology_class', $class);
    }

    for ($i = 0; $i < count($exp1); $i++) {
        $expression = $DB->get_record('ontology_class_expression', array('id' => $exp1[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_class_expression', $expression);
    }
    for ($i = 0; $i < count($exp2); $i++) {
        $expression = $DB->get_record('ontology_class_expression', array('id' => $exp2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_class_expression', $expression);
    }
} else
if ($tip == 2) {
    //azuriranja na postoecko objektno svojstvo
    $accepted = $_GET["accepted"];
    $refused = $_GET["refused"];
    $accepted2 = $_GET["accepted2"];
    $refused2 = $_GET["refused2"];
    $accepted3 = $_GET["accepted3"];
    $refused3 = $_GET["refused3"];
    $exp1 = array();
    $exp2 = array();
    $exp3 = array();
    $exp4 = array();
    $exp5 = array();
    $exp6 = array();
    if ($accepted != "")
        $exp1 = explode(' ', $accepted);
    if ($refused != "")
        $exp2 = explode(' ', $refused);
    if ($accepted2 != "")
        $exp3 = explode(' ', $accepted2);
    if ($refused2 != "")
        $exp4 = explode(' ', $refused2);
    if ($accepted3 != "")
        $exp5 = explode(' ', $accepted3);
    if ($refused3 != "")
        $exp6 = explode(' ', $refused3);
    global $DB;
    for ($i = 0; $i < count($exp1); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $exp1[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($exp2); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $exp2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($exp3); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $exp3[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($exp4); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $exp4[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($exp5); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $exp5[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
    for ($i = 0; $i < count($exp6); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $exp6[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
} else
if ($tip == 3) {
    //azuriranja na postoecko podatocno svojstvo
    $accepted = $_GET["accepted"];
    $refused = $_GET["refused"];
    $accepted2 = $_GET["accepted2"];
    $refused2 = $_GET["refused2"];
    $accepted3 = $_GET["accepted3"];
    $refused3 = $_GET["refused3"];
    $exp1 = array();
    $exp2 = array();
    $exp3 = array();
    $exp4 = array();
    $exp5 = array();
    $exp6 = array();
    if ($accepted != "")
        $exp1 = explode(' ', $accepted);
    if ($refused != "")
        $exp2 = explode(' ', $refused);
    if ($accepted2 != "")
        $exp3 = explode(' ', $accepted2);
    if ($refused2 != "")
        $exp4 = explode(' ', $refused2);
    if ($accepted3 != "")
        $exp5 = explode(' ', $accepted3);
    if ($refused3 != "")
        $exp6 = explode(' ', $refused3);
    global $DB;
    for ($i = 0; $i < count($exp1); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $exp1[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($exp2); $i++) {
        $expression = $DB->get_record('ontology_property_expression', array('id' => $exp2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_expression', $expression);
    }
    for ($i = 0; $i < count($exp3); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $exp3[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($exp4); $i++) {
        $expression = $DB->get_record('ontology_property_equivalent', array('id' => $exp4[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_equivalent', $expression);
    }
    for ($i = 0; $i < count($exp5); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $exp5[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
    for ($i = 0; $i < count($exp6); $i++) {
        $expression = $DB->get_record('ontology_property_disjoint', array('id' => $exp6[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_property_disjoint', $expression);
    }
} else
if ($tip == 4) {
    //azuriranja na postoecka instanca
    $accepted = $_GET["accepted"];
    $refused = $_GET["refused"];
    $accepted2 = $_GET["accepted2"];
    $refused2 = $_GET["refused2"];
    $accepted3 = $_GET["accepted3"];
    $refused3 = $_GET["refused3"];
    $exp1 = array();
    $exp2 = array();
    $exp3 = array();
    $exp4 = array();
    $exp5 = array();
    $exp6 = array();
    if ($accepted != "")
        $exp1 = explode(' ', $accepted);
    if ($refused != "")
        $exp2 = explode(' ', $refused);
    if ($accepted2 != "")
        $exp3 = explode(' ', $accepted2);
    if ($refused2 != "")
        $exp4 = explode(' ', $refused2);
    if ($accepted3 != "")
        $exp5 = explode(' ', $accepted3);
    if ($refused3 != "")
        $exp6 = explode(' ', $refused3);
    global $DB;
    for ($i = 0; $i < count($exp1); $i++) {
        $expression = $DB->get_record('ontology_individual_expression', array('id' => $exp1[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_individual_expression', $expression);
    }
    for ($i = 0; $i < count($exp2); $i++) {
        $expression = $DB->get_record('ontology_individual_expression', array('id' => $exp2[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_expression', $expression);
    }
    for ($i = 0; $i < count($exp3); $i++) {
        $expression = $DB->get_record('ontology_individual_property_individual', array('id' => $exp3[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_individual_property_individual', $expression);
    }
    for ($i = 0; $i < count($exp4); $i++) {
        $expression = $DB->get_record('ontology_individual_property_individual', array('id' => $exp4[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_property_individual', $expression);
    }
    for ($i = 0; $i < count($exp5); $i++) {
        $expression = $DB->get_record('ontology_individual_property_data', array('id' => $exp5[$i]));
        $expression->status = '2';
        $DB->update_record('ontology_individual_property_data', $expression);
    }
    for ($i = 0; $i < count($exp6); $i++) {
        $expression = $DB->get_record('ontology_individual_property_data', array('id' => $exp6[$i]));
        $expression->status = '5';
        $DB->update_record('ontology_individual_property_data', $expression);
    }
}