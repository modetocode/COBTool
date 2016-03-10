<?php

//zapishuvanje na podatoci za:
// TIP == 1     nova klsa
// TIP == 2     novo objektno svojstvo
// TIP == 3     novo podatocno svojstvo
// TIP == 4     nova individua

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$tip = $_GET["tip"];
if ($tip == 1) {
    //klasi
    global $DB;
    $id = $_GET["id"];
    $classestxt = $_GET["classes"];
    $class = $DB->get_record('ontology_class', array('id' => $id));
    $class->status = '2';
    $DB->update_record('ontology_class', $class);
    $classes = explode(" ", $classestxt);
    for ($i = 0; $i < count($classes); $i++) {
        if ($classes[$i] != $id) {
            $class = $DB->get_record('ontology_class', array('id' => $classes[$i]));
            $class->status = '5';
            $DB->update_record('ontology_class', $class);
        }
    }
    $superktxt = $_GET["superk"];
    if ($superktxt != "") {
        $superk = explode(" ", $superktxt);
        for ($i = 0; $i < count($superk); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $superk[$i]));
            $expression->ontology_classid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    for ($i = 0; $i < count($classes); $i++) {
        $expressions = $DB->get_records('ontology_class_expression', array('ontology_classid' => $classes[$i], 'status' => '3', 'type' => '1'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_class_expression', $value);
        }
    }
    $ekviktxt = $_GET["ekvik"];
    if ($ekviktxt != "") {
        $ekvik = explode(" ", $ekviktxt);
        for ($i = 0; $i < count($ekvik); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $ekvik[$i]));
            $expression->ontology_classid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    for ($i = 0; $i < count($classes); $i++) {
        $expressions = $DB->get_records('ontology_class_expression', array('ontology_classid' => $classes[$i], 'status' => '3', 'type' => '2'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_class_expression', $value);
        }
    }
    $disjktxt = $_GET["disjk"];
    if ($disjktxt != "") {
        $disjk = explode(" ", $disjktxt);
        for ($i = 0; $i < count($disjk); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $disjk[$i]));
            $expression->ontology_classid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    for ($i = 0; $i < count($classes); $i++) {
        $expressions = $DB->get_records('ontology_class_expression', array('ontology_classid' => $classes[$i], 'status' => '3', 'type' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_class_expression', $value);
        }
    }
} else
if ($tip == 2) {
    global $DB;
    $id = $_GET["id"];
    $property = $DB->get_record('ontology_property_individual', array('id' => $id));
    $property->status = '2';
    $DB->update_record('ontology_property_individual', $property);
    $propertiestxt = $_GET["properties"];
    $properties = explode(" ", $propertiestxt);
    for ($i = 0; $i < count($properties); $i++) {
        if ($properties[$i] != $id) {
            $property = $DB->get_record('ontology_property_individual', array('id' => $properties[$i]));
            $property->status = '5';
            $DB->update_record('ontology_property_individual', $property);
        }
    }
    $domenitxt = $_GET["domeni"];
    if ($domenitxt != "") {
        $domeni = explode(" ", $domenitxt);
        for ($i = 0; $i < count($domeni); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $domeni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '1'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_expression', $value);
        }
    }
    $rangovitxt = $_GET["rangovi"];
    if ($rangovitxt != "") {
        $rangovi = explode(" ", $rangovitxt);
        for ($i = 0; $i < count($rangovi); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $rangovi[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '2'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_expression', $value);
        }
    }
    $ekvivalentnitxt = $_GET["ekvivalentni"];
    if ($ekvivalentnitxt != "") {
        $ekvivalentni = explode(" ", $ekvivalentnitxt);
        for ($i = 0; $i < count($ekvivalentni); $i++) {
            $expression = $DB->get_record('ontology_property_equivalent', array('id' => $ekvivalentni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_equivalent', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '1'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_equivalent', $value);
        }
    }
    $disjunktnitxt = $_GET["disjunktni"];
    if ($disjunktnitxt != "") {
        $disjunktni = explode(" ", $disjunktnitxt);
        for ($i = 0; $i < count($disjunktni); $i++) {
            $expression = $DB->get_record('ontology_property_disjoint', array('id' => $disjunktni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_disjoint', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '1'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_disjoint', $value);
        }
    }
} else
if ($tip == 3) {
    global $DB;
    $id = $_GET["id"];
    $property = $DB->get_record('ontology_property_data', array('id' => $id));
    $property->status = '2';
    $DB->update_record('ontology_property_data', $property);
    $propertiestxt = $_GET["properties"];
    $properties = explode(" ", $propertiestxt);
    for ($i = 0; $i < count($properties); $i++) {
        if ($properties[$i] != $id) {
            $property = $DB->get_record('ontology_property_data', array('id' => $properties[$i]));
            $property->status = '5';
            $DB->update_record('ontology_property_data', $property);
        }
    }
    $domenitxt = $_GET["domeni"];
    if ($domenitxt != "") {
        $domeni = explode(" ", $domenitxt);
        for ($i = 0; $i < count($domeni); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $domeni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_expression', $value);
        }
    }
    $ekvivalentnitxt = $_GET["ekvivalentni"];
    if ($ekvivalentnitxt != "") {
        $ekvivalentni = explode(" ", $ekvivalentnitxt);
        for ($i = 0; $i < count($ekvivalentni); $i++) {
            $expression = $DB->get_record('ontology_property_equivalent', array('id' => $ekvivalentni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_equivalent', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '2'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_equivalent', $value);
        }
    }
    $disjunktnitxt = $_GET["disjunktni"];
    if ($disjunktnitxt != "") {
        $disjunktni = explode(" ", $disjunktnitxt);
        for ($i = 0; $i < count($disjunktni); $i++) {
            $expression = $DB->get_record('ontology_property_disjoint', array('id' => $disjunktni[$i]));
            $expression->ontology_propertyid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_property_disjoint', $expression);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $properties[$i], 'status' => '3', 'type' => '2'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_disjoint', $value);
        }
    }
} else
if ($tip == 4) {
    global $DB;
    $id = $_GET["id"];
    $individual = $DB->get_record('ontology_individual', array('id' => $id));
    $individual->status = '2';
    $DB->update_record('ontology_individual', $individual);
    $individualstxt = $_GET["individuals"];
    $individuals = explode(" ", $individualstxt);
    for ($i = 0; $i < count($individuals); $i++) {
        if ($individuals[$i] != $id) {
            $individual = $DB->get_record('ontology_individual', array('id' => $individuals[$i]));
            $individual->status = '5';
            $DB->update_record('ontology_individual', $individual);
        }
    }
    $klasitxt = $_GET["klasi"];
    if ($klasitxt != "") {
        $klasi = explode(" ", $klasitxt);
        for ($i = 0; $i < count($klasi); $i++) {
            $expression = $DB->get_record('ontology_individual_expression', array('id' => $klasi[$i]));
            $expression->ontology_individualid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_individual_expression', $expression);
        }
    }
    for ($i = 0; $i < count($individuals); $i++) {
        $expressions = $DB->get_records('ontology_individual_expression', array('ontology_individualid' => $individuals[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_individual_expression', $value);
        }
    }
    $osvojstvatxt = $_GET["osvojstva"];
    if ($osvojstvatxt != "") {
        $osvojstva = explode(" ", $osvojstvatxt);
        for ($i = 0; $i < count($osvojstva); $i++) {
            $expression = $DB->get_record('ontology_individual_property_individual', array('id' => $osvojstva[$i]));
            $expression->ontology_individualid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_individual_property_individual', $expression);
        }
    }
    for ($i = 0; $i < count($individuals); $i++) {
        $expressions = $DB->get_records('ontology_individual_property_individual', array('ontology_individualid' => $individuals[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_individual_property_individual', $value);
        }
    }
    $dsvojstvatxt = $_GET["dsvojstva"];
    if ($dsvojstvatxt != "") {
        $dsvojstva = explode(" ", $dsvojstvatxt);
        for ($i = 0; $i < count($dsvojstva); $i++) {
            $expression = $DB->get_record('ontology_individual_property_data', array('id' => $dsvojstva[$i]));
            $expression->ontology_individualid = $id;
            $expression->status = '2';
            $DB->update_record('ontology_individual_property_data', $expression);
        }
    }
    for ($i = 0; $i < count($individuals); $i++) {
        $expressions = $DB->get_records('ontology_individual_property_data', array('ontology_individualid' => $individuals[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_individual_property_data', $value);
        }
    }
}