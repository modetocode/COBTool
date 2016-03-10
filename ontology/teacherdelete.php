<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$tip = $_GET["tip"];
if ($tip == 1) {
    //brishenje na klasi
    global $DB;
    $classestxt = $_GET["classes"];
    $classes = explode(" ", $classestxt);
    for ($i = 0; $i < count($classes); $i++) {
        $class = $DB->get_record('ontology_class', array('id' => $classes[$i]));
        $class->status = '5';
        $DB->update_record('ontology_class', $class);
    }
    for ($i = 0; $i < count($classes); $i++) {
        $expressions = $DB->get_records('ontology_class_expression', array('ontology_classid' => $classes[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_class_expression', $value);
        }
    }
    //da se izbrishat(da se stavi status 5) site expressioni koi se vo ontologija (status 2) ili se prifateni (status 3) i vo izrazot sodrzat edna od klasite
    $zabrishenje = $_GET['delexpressions'];
    if ($zabrishenje != '') {
        $brishi = explode(' ', $zabrishenje);
        for ($i = 0; $i < count($brishi); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $brishi[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    $zabrishenje2 = $_GET['delexpressions2'];
    if ($zabrishenje2 != '') {
        $brishi2 = explode(' ', $zabrishenje2);
        for ($i = 0; $i < count($brishi2); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $brishi2[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    $zabrishenje3 = $_GET['delexpressions3'];
    if ($zabrishenje3 != '') {
        $brishi3 = explode(' ', $zabrishenje3);
        for ($i = 0; $i < count($brishi3); $i++) {
            $expression = $DB->get_record('ontology_individual_expression', array('id' => $brishi3[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_individual_expression', $expression);
        }
    }
} else
if ($tip == 2) {
    global $DB;
    $propertiestxt = $_GET["properties"];
    $properties = explode(" ", $propertiestxt);
    for ($i = 0; $i < count($properties); $i++) {
        $property = $DB->get_record('ontology_property_individual', array('id' => $properties[$i]));
        $property->status = '5';
        $DB->update_record('ontology_property_individual', $property);
        $inverse = $DB->get_records('ontology_property_individual', array('inverse' => $properties[$i]));
        foreach ($inverse as $key => $value) {
            $value->inverse = '0';
            $DB->update_record('ontology_property_individual', $value);
        }
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $properties[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_expression', $value);
        }
    }
    //da se izbrishat(da se stavi status 5) site expressioni koi se vo ontologija (status 2) ili se prifateni (status 3) i vo izrazot sodrzat edna od klasite
    $zabrishenje = $_GET['delexpressions'];
    if ($zabrishenje != '') {
        $brishi = explode(' ', $zabrishenje);
        for ($i = 0; $i < count($brishi); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $brishi[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    $zabrishenje2 = $_GET['delexpressions2'];
    if ($zabrishenje2 != '') {
        $brishi2 = explode(' ', $zabrishenje2);
        for ($i = 0; $i < count($brishi2); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $brishi2[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    $zabrishenje3 = $_GET['delexpressions3'];
    if ($zabrishenje3 != '') {
        $brishi3 = explode(' ', $zabrishenje3);
        for ($i = 0; $i < count($brishi3); $i++) {
            $expression = $DB->get_record('ontology_individual_expression', array('id' => $brishi3[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_individual_expression', $expression);
        }
    }
} else
if ($tip == 3) {
    global $DB;
    $propertiestxt = $_GET["properties"];
    $properties = explode(" ", $propertiestxt);
    for ($i = 0; $i < count($properties); $i++) {
        $property = $DB->get_record('ontology_property_data', array('id' => $properties[$i]));
        $property->status = '5';
        $DB->update_record('ontology_property_data', $property);
    }
    for ($i = 0; $i < count($properties); $i++) {
        $expressions = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $properties[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_property_expression', $value);
        }
    }
    //da se izbrishat(da se stavi status 5) site expressioni koi se vo ontologija (status 2) ili se prifateni (status 3) i vo izrazot sodrzat edna od klasite
    $zabrishenje = $_GET['delexpressions'];
    if ($zabrishenje != '') {
        $brishi = explode(' ', $zabrishenje);
        for ($i = 0; $i < count($brishi); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $brishi[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    $zabrishenje2 = $_GET['delexpressions2'];
    if ($zabrishenje2 != '') {
        $brishi2 = explode(' ', $zabrishenje2);
        for ($i = 0; $i < count($brishi2); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $brishi2[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    $zabrishenje3 = $_GET['delexpressions3'];
    if ($zabrishenje3 != '') {
        $brishi3 = explode(' ', $zabrishenje3);
        for ($i = 0; $i < count($brishi3); $i++) {
            $expression = $DB->get_record('ontology_individual_expression', array('id' => $brishi3[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_individual_expression', $expression);
        }
    }
} else
if ($tip == 4) {
    global $DB;
    $individualstxt = $_GET["individuals"];
    $individuals = explode(" ", $individualstxt);
    for ($i = 0; $i < count($individuals); $i++) {
        $individual = $DB->get_record('ontology_individual', array('id' => $individuals[$i]));
        $individual->status = '5';
        $DB->update_record('ontology_individual', $individual);
    }
    for ($i = 0; $i < count($individuals); $i++) {
        $expressions = $DB->get_records('ontology_individual_expression', array('ontology_individualid' => $individuals[$i], 'status' => '3'));
        foreach ($expressions as $key => $value) {
            $value->status = '5';
            $DB->update_record('ontology_individual_expression', $value);
        }
    }
    //da se izbrishat(da se stavi status 5) site expressioni koi se vo ontologija (status 2) ili se prifateni (status 3) i vo izrazot sodrzat edna od klasite
    $zabrishenje = $_GET['delexpressions'];
    if ($zabrishenje != '') {
        $brishi = explode(' ', $zabrishenje);
        for ($i = 0; $i < count($brishi); $i++) {
            $expression = $DB->get_record('ontology_class_expression', array('id' => $brishi[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_class_expression', $expression);
        }
    }
    $zabrishenje2 = $_GET['delexpressions2'];
    if ($zabrishenje2 != '') {
        $brishi2 = explode(' ', $zabrishenje2);
        for ($i = 0; $i < count($brishi2); $i++) {
            $expression = $DB->get_record('ontology_property_expression', array('id' => $brishi2[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_property_expression', $expression);
        }
    }
    $zabrishenje3 = $_GET['delexpressions3'];
    if ($zabrishenje3 != '') {
        $brishi3 = explode(' ', $zabrishenje3);
        for ($i = 0; $i < count($brishi3); $i++) {
            $expression = $DB->get_record('ontology_individual_expression', array('id' => $brishi3[$i]));
            $expression->status = '5';
            $DB->update_record('ontology_individual_expression', $expression);
        }
    }
}