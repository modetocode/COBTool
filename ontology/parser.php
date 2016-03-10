<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace newmodule with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module

if ($id) {
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $ontology = $DB->get_record('ontology', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $ontology->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ontology', $ontology->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);

$a[] = "q";
$style[] = "#000000";
$a[] = "(";
$style[] = "#000000";
$a[] = ")";
$style[] = "#000000";
$a[] = "{";
$style[] = "#000000";
$a[] = "}";
$style[] = "#000000";
$a[] = "[";
$style[] = "#000000";
$a[] = "]";
$style[] = "#000000";
$a[] = "and";
$style[] = "#9F0EA9";
$a[] = "or";
$style[] = "#9F0EA9";
$a[] = "not";
$style[] = "#9F0EA9";
$a[] = "min";
$style[] = "#122B6B";
$a[] = "max";
$style[] = "#122B6B";
$a[] = "exactly";
$style[] = "#122B6B";
$a[] = "value";
$style[] = "#122B6B";
$a[] = "some";
$style[] = "#122B6B";
$a[] = "only";
$style[] = "#122B6B";
$a[] = "<";
$style[] = "#122B6B";
$a[] = ">";
$style[] = "#122B6B";
$a[] = "<=";
$style[] = "#122B6B";
$a[] = ">=";
$style[] = "#122B6B";
$a[] = "lenght";
$style[] = "#122B6B";
$a[] = "maxlenght";
$style[] = "#122B6B";
$a[] = "minlenght";
$style[] = "#122B6B";
$a[] = "totalDigits";
$style[] = "#122B6B";
$a[] = "klasa";
$style[] = "#FF9D00";
$a[] = "tip";
$style[] = "#0E2B79";
$a[] = "broj";
$style[] = "#D51010";
$a[] = "individua";
$style[] = "#FF0404";
$a[] = "psvojstvo";
$style[] = "#19B411";
$a[] = "osvojstvo";
$style[] = "#006FFF";
$a[] = "$";

$v[] = "E";
$v[] = "E'";
$v[] = "I";
$v[] = "P";
$v[] = "O";
$v[] = "E1";
$v[] = "T";
$v[] = "T1";



$m = array(array('/', '-', '-', '/', '/', '/', '-', '-'), //q
    array('( E ) E\'', '-', '-', '/', '/', '( E ) E\'', '-', '-'), //(
    array('/', '-', '-', '/', '/', '/', '-', '-'), //)
    array('{ I } E\'', '-', '-', '/', '/', '{ I } E\'', '-', '-'), //{
    array('/', '-', '-', '/', '/', '/', '-', '-'), //}
    array('/', '-', '-', '/', '/', '/', '[ T1 ]', '-'), //[
    array('/', '-', '-', '/', '/', '/', '-', '-'), //]
    array('/', 'and E E\'', '-', '/', '/', '/', '-', '-'), //and
    array('/', 'or E E\'', '-', '/', '/', '/', '-', '-'), //or
    array('not E1 E\'', '-', '-', '/', '/', '/', '-', '-'), //not
    array('/', '-', '-', 'min broj', 'min broj', '/', '-', '-'), //min
    array('/', '-', '-', 'max broj', 'max broj', '/', '-', '-'), //max
    array('/', '-', '-', 'exactly broj', 'exactly broj', '/', '-', '-'), //exactly
    array('/', '-', '-', 'value broj tip T', 'value individua', '/', '-', '-'), //value
    array('/', '-', '-', 'some tip T', 'some E1', '/', '-', '-'), //some
    array('/', '-', '-', 'only tip T', 'only E1', '/', '-', '-'), //only
    array('/', '-', '-', '/', '/', '/', '-', '< broj T1'), //<
    array('/', '-', '-', '/', '/', '/', '-', '> broj T1'), //>
    array('/', '-', '-', '/', '/', '/', '-', '<= broj T1'), //<=
    array('/', '-', '-', '/', '/', '/', '-', '>= broj T1'), //>=
    array('/', '-', '-', '/', '/', '/', '-', 'lenght broj T1'), //lenght
    array('/', '-', '-', '/', '/', '/', '-', 'maxlenght broj T1'), //maxlenght
    array('/', '-', '-', '/', '/', '/', '-', 'minlenght broj T1'), //minlenght
    array('/', '-', '-', '/', '/', '/', '-', 'totalDigits broj T1'), //totalDigits
    array('klasa E\'', '-', '-', '/', '/', 'klasa E\'', '-', '-'), //klasa
    array('/', '-', '-', '/', '/', '/', '-', '-'), //tip
    array('/', '-', '-', '/', '/', '/', '-', '-'), //broj
    array('/', '-', 'individua I', '/', '/', '/', '-', '-'), //individua
    array('psvojstvo P E\'', '-', '-', '/', '/', '/', '-', '-'), //psvojstvo
    array('osvojstvo O E\'', '-', '-', '/', '/', '/', '-', '-'), //osvojstvo
    array('/', '/', '/', '/', '/', '/', '/', '/'), //$
);

$stektxt = $_GET["stek"]; //stekot za push-down avtomatot
$inputtxt = $_GET["input"]; //tekstot sto treba da se parsira (ist kako tekst samo sto namesto iminja sodrzi ID
$moduleid = $_GET["id"]; //id na modulot
$userid = $_GET["userid"]; //korisnik
$javascript = $_GET["jfunction"]; //ime na javaskript funkciite i na listbox
$tip = $_GET["tip"]; //potrebno za zapishuvanje vo baza TIP 1 - klasa TIP 2 - objektno svojstvo TIP 3 - podatocno svojstvo TIP 4 - individua
$stek = explode(" ", $stektxt);
$input = explode(" ", $inputtxt);
if ($stektxt == "") {//korisnikot e na pocetok na pisuvanjeto na tvrdenjeto
    $stektxt = "E"; //go polnime stekot so prviot Neterminal
    $stek = "E";
    $index = 0; //se naogjame vo pocetnata sostojba
    $index2 = 0; //indeks na posledniot neterminal e 0
    $posledenvnes = "";
} else {//korisnikot pocnal so pisuvanje na izrazot
    $posledenvnes = $input[count($input) - 1];
    if ($posledenvnes[0] == '^') {
        //korisnikot vnel klasa, tip, broj, individua, podatocno svojstvo ili objektno svojstvo
        if ($posledenvnes[1] == 'k')
            $index = 24;
        else
        if ($posledenvnes[1] == 't')
            $index = 25;
        else
        if ($posledenvnes[1] == 'b')
            $index = 26;
        else
        if ($posledenvnes[1] == 'i')
            $index = 27;
        else
        if ($posledenvnes[1] == 'p')
            $index = 28;
        else
        if ($posledenvnes[1] == 'o')
            $index = 29;
    }
    else {
        //korisnikot vnel nekoj zbor koj go ima vo jazikot
        for ($i = 1; $i < 24; $i++) {
            if ($a[$i] == $posledenvnes)
                $index = $i;
        }
    }
    //go dobivame indeksot za redicata vo koja se naogja narednoto pravilo
    //sega go proveruvame stekot
    //echo 'Indeksot na posledniot karakter e: '.$index.'<br>';
    while ($index != 0) {//se dodeka ne stasame do pocetnata sostojba
        $posledenstek = $stek[count($stek) - 1]; //go zemame posledniot element staven na stekot
        array_pop($stek);
        //proveruvame dali e terminal
        if ($a[$index] == $posledenstek) {
            $index = 0;
            //echo 'Dojdovme do terminalot: '.$posledenstek.'<br>';
        } else {
            //vo stekot imame neterminal
            for ($i = 0; $i < count($v); $i++)
                if ($v[$i] == $posledenstek) {
                    $index2 = $i;
                    break;
                }
            //echo 'Indeksot na posledniot neterminal e: '.$index2.'<br>';
            if ($m[$index][$index2] != '-') {//nemame lambda premin
                $temp = explode(' ', $m[$index][$index2]);
                for ($i = (count($temp) - 1); $i >= 0; $i--) {
                    $stek[] = $temp[$i];
                }
                //echo 'Primenuvame pravilo za '.$posledenstek.'<br/>';
                //echo 'Stekot e: <br/>';
                // for ($i=0;$i<count($stek);$i++)
                // echo $stek[$i].' ';
                // echo '<br/>';
            } else {//imame lambda premin
                //echo 'Primenuvame lambda premin pravilo za '.$posledenstek.'<br/>';
            }
        }
    }
}
//otkako se vrativme vo pocetnata sostojba sega potrebno e da gi predvidime idnite vnesovi za korisnikot
global $DB;
//najprvin proveruvame sto imame na vrvot na stekot
$posledenstek = $stek[count($stek) - 1]; //go zemame posledniot element staven na stekot
$index2 = -1;
for ($i = 0; $i < count($v); $i++)
    if ($v[$i] == $posledenstek) {
        $index2 = $i;
        break;
    }
if ($index2 == -1 || $posledenvnes == '$') { //sme dosle do broj, individua ili tip
    if ($posledenvnes == '$') {
        if ($input[count($input) - 1] == '$')
            $size = count($input) - 1;
        else
            $size = count($input);
        $expressiontekst = "";
        for ($i = 0; $i < $size; $i++) {
            $posledenvnes = $input[$i];
            if ($posledenvnes[0] == '^') {
                if ($posledenvnes[1] == 'k') {
                    $class = $DB->get_record('ontology_class', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                    $txt = $class->name;
                } else
                if ($posledenvnes[1] == 't')
                    $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
                else
                if ($posledenvnes[1] == 'b')
                    $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
                else
                if ($posledenvnes[1] == 'i') {
                    $individual = $DB->get_record('ontology_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                    $txt = $individual->name;
                } else
                if ($posledenvnes[1] == 'p') {
                    $psvojstvo = $DB->get_record('ontology_property_data', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                    $txt = $psvojstvo->name;
                } else
                if ($posledenvnes[1] == 'o') {
                    $osvojstvo = $DB->get_record('ontology_property_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                    $txt = $osvojstvo->name;
                }
            } else {
                for ($j = 1; $j < 24; $j++)
                    if ($a[$j] == $posledenvnes)
                        $txt = $a[$j];
            }
            if ($expressiontekst == "")
                $expressiontekst = $txt;
            else
                $expressiontekst = $expressiontekst . ' ' . $txt;
        }
        if ($tip == 1) {
            //vnesuvanje na expressioni za klasi
            $expression->ontology_classid = $_GET["classid"];
            $expression->expression = $inputtxt;
            if (substr($javascript, -1) == '1')
                $expression->type = '1';
            else
            if (substr($javascript, -1) == '2')
                $expression->type = '2';
            else
                $expression->type = '3';
            if (is_Teacher())
                $expression->status = '2';
            else
                $expression->status = '1';
            $expression->points = '0';
            $expression->userid = $userid;
            $expression->course_modulesid = $moduleid;
            $expression->expression_text = $expressiontekst;
            $DB->insert_record('ontology_class_expression', $expression);
        }
        else
        if ($tip == 2) { //vnesuvanje expressioni za objektni svojstva
            $expression->ontology_propertyid = $_GET["classid"];
            $expression->expression = $inputtxt;
            $expression->expression_text = $expressiontekst;
            if (is_Teacher())
                $expression->status = '2';
            else
                $expression->status = '1';
            $expression->points = '0';
            $expression->userid = $userid;
            $expression->course_modulesid = $moduleid;
            if (substr($javascript, -1) == '1')
                $expression->type = '1';
            else
                $expression->type = '2';
            $DB->insert_record('ontology_property_expression', $expression);
        }
        else
        if ($tip == 3) { //vnesuvanje expressioni za podatocni svojstva
            $expression->ontology_propertyid = $_GET["classid"];
            $expression->expression = $inputtxt;
            $expression->expression_text = $expressiontekst;
            if (is_Teacher())
                $expression->status = '2';
            else
                $expression->status = '1';
            $expression->points = '0';
            $expression->userid = $userid;
            $expression->course_modulesid = $moduleid;
            $expression->type = '3';
            $DB->insert_record('ontology_property_expression', $expression);
        }
        else { //vnesuvanje expressioni za individui
            $expression->ontology_individualid = $_GET["classid"];
            $expression->expression = $inputtxt;
            $expression->expression_text = $expressiontekst;
            if (is_Teacher())
                $expression->status = '2';
            else
                $expression->status = '1';
            $expression->points = '0';
            $expression->userid = $userid;
            $expression->course_modulesid = $moduleid;
            $DB->insert_record('ontology_individual_expression', $expression);
        }
    }
    else {
        if ($posledenstek == 'broj') {
            //echo 'Korisnikot treba da vnese broj';
            echo get_string('Input_number', 'ontology') . ':<input type="text" id="broj" name="broj"> <input type="button" id="vnesbroj" name="vnesbroj" value="OK" onClick="' . $javascript . '2();"';
        } else {

            echo '<SELECT name="' . $javascript . '" size="10" onchange="' . $javascript . '1(this);">';
            if ($posledenstek == 'individua') {
                $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
                $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
                $modules = $DB->get_records('course_modules', array('module' => $ontologyid->id, 'instance' => $ontologyinstanceid->instance));
                foreach ($modules as $key1 => $value1) {
                    $individuals = $DB->get_records('ontology_individual', array('status' => '2', 'course_modulesid' => $key1));
                    foreach ($individuals as $key => $value) {
                        echo "<option style=\"color: #FF0404\" value=\"^i" . $key . "\">" . $value->name . " </option>";
                    }
                    $individuals = $DB->get_records('ontology_individual', array('status' => '1', 'userid' => $userid, 'course_modulesid' => $key1));
                    foreach ($individuals as $key => $value) {
                        echo "<option style=\"color: #FF0404\" value=\"^i" . $key . "\">" . $value->name . " </option>";
                    }
                }
            } else {
                echo "<option style=\"color: #0E2B79\" value=\"^tboolean\"> boolean </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tbyte\"> byte </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tdateTime\"> dateTime </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tdecimal\"> decimal </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tdouble\"> double </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tfloat\"> float </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tinteger\"> integer </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tlong\"> long </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^treal\"> real </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tshort\"> short </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tstring\"> string </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tunsignedByte\"> unsignedByte </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tunsignedInt\"> unsignedInt </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tunsignedLong\"> unsignedLong </option>";
                echo "<option style=\"color: #0E2B79\" value=\"^tunsignedShort\"> unsignedShort </option>";
            }
            echo '</SELECT>';
        }
    }
} else {
    echo '<SELECT name="' . $javascript . '" size="10" onchange="' . $javascript . '1(this);">';

    //prvin zborovite od azbukata (,),{,},[,]
    for ($i = 1; $i < 7; $i++) {
        if ($m[$i][$index2] != '/' && $m[$i][$index2] != '-') {
            echo "<option style=\"color: #000000\" value=\"" . $a[$i] . "\">" . $a[$i] . " </option>";
        }
    }
    //potoa and,or,not
    for ($i = 7; $i < 10; $i++) {
        if ($m[$i][$index2] != '/' && $m[$i][$index2] != '-') {
            echo "<option style=\"color: #9F0EA9\" value=\"" . $a[$i] . "\">" . $a[$i] . " </option>";
        }
    }
    //potoa min,max,exactly,value,some,only
    for ($i = 10; $i < 16; $i++) {
        if ($m[$i][$index2] != '/' && $m[$i][$index2] != '-') {
            echo "<option style=\"color: #122B6B\" value=\"" . $a[$i] . "\">" . $a[$i] . " </option>";
        }
    }
    //potoa <,>,<=,>=,lenght,maxlenght,minlenght,totalDigits
    for ($i = 16; $i < 24; $i++) {
        if ($m[$i][$index2] != '/' && $m[$i][$index2] != '-') {
            echo "<option style=\"color: #9F0EA9\" value=\"" . $a[$i] . "\">" . $a[$i] . " </option>";
        }
    }
    //sega proveruvame za klasite
    if ($m[24][$index2] != '/' && $m[24][$index2] != '-') {
        $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
        $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
        $modules = $DB->get_records('course_modules', array('module' => $ontologyid->id, 'instance' => $ontologyinstanceid->instance));
        foreach ($modules as $key1 => $value1) {
            $classes = $DB->get_records('ontology_class', array('status' => '2', 'course_modulesid' => $key1));
            foreach ($classes as $key => $value) {
                echo "<option style=\"color: #FF9D00\" value=\"^k" . $key . "\">" . $value->name . " </option>";
            }
            $classes = $DB->get_records('ontology_class', array('status' => '1', 'userid' => $userid, 'course_modulesid' => $key1));
            foreach ($classes as $key => $value) {
                echo "<option style=\"color: #FF9D00\" value=\"^k" . $key . "\">" . $value->name . " </option>";
            }
        }
    }
    //sega proveruvame za tipovi
    if ($m[25][$index2] != '/' && $m[25][$index2] != '-') {
        echo "<option style=\"color: #0E2B79\" value=\"^tboolean\"> boolean </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tbyte\"> byte </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tdateTime\"> dateTime </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tdecimal\"> decimal </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tdouble\"> double </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tfloat\"> float </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tinteger\"> integer </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tlong\"> long </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^treal\"> real </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tshort\"> short </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tstring\"> string </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tunsignedByte\"> unsignedByte </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tunsignedInt\"> unsignedInt </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tunsignedLong\"> unsignedLong </option>";
        echo "<option style=\"color: #0E2B79\" value=\"^tunsignedShort\"> unsignedShort </option>";
    }
    //sega proveruvame za individui
    if ($m[27][$index2] != '/' && $m[27][$index2] != '-') {
        $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
        $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
        $modules = $DB->get_records('course_modules', array('module' => $ontologyid->id, 'instance' => $ontologyinstanceid->instance));
        foreach ($modules as $key1 => $value1) {
            $individuals = $DB->get_records('ontology_individual', array('status' => '2', 'course_modulesid' => $key1));
            foreach ($individuals as $key => $value) {
                echo "<option style=\"color: #FF0404\" value=\"^i" . $key . "\">" . $value->name . " </option>";
            }
            $individuals = $DB->get_records('ontology_individual', array('status' => '1', 'userid' => $userid, 'course_modulesid' => $key1));
            foreach ($individuals as $key => $value) {
                echo "<option style=\"color: #FF0404\" value=\"^i" . $key . "\">" . $value->name . " </option>";
            }
        }
    }
    //sega proveruvame za podatocni svojstva
    if ($m[28][$index2] != '/' && $m[28][$index2] != '-') {
        $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
        $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
        $modules = $DB->get_records('course_modules', array('module' => $ontologyid->id, 'instance' => $ontologyinstanceid->instance));
        foreach ($modules as $key1 => $value1) {
            $psvojstva = $DB->get_records('ontology_property_data', array('status' => '2', 'course_modulesid' => $key1));
            foreach ($psvojstva as $key => $value) {
                if ($value->name != "Основно")
                    echo "<option style=\"color: #19B411\" value=\"^p" . $key . "\">" . $value->name . " </option>";
            }
            $psvojstva = $DB->get_records('ontology_property_data', array('status' => '1', 'userid' => $userid, 'course_modulesid' => $key1));
            foreach ($psvojstva as $key => $value) {
                if ($value->name != "Основно")
                    echo "<option style=\"color: #19B411\" value=\"^p" . $key . "\">" . $value->name . " </option>";
            }
        }
    }
    //sega proveruvame za objektni svojstva
    if ($m[29][$index2] != '/' && $m[29][$index2] != '-') {
        $ontologyinstanceid = $DB->get_record('course_modules', array('id' => $moduleid)); //id na instancata na ontologijata
        $ontologyid = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
        $modules = $DB->get_records('course_modules', array('module' => $ontologyid->id, 'instance' => $ontologyinstanceid->instance));
        foreach ($modules as $key1 => $value1) {
            $osvojstva = $DB->get_records('ontology_property_individual', array('status' => '2', 'course_modulesid' => $key1));
            foreach ($osvojstva as $key => $value) {
                if ($value->name != "Основно")
                    echo "<option style=\"color: #006FFF\" value=\"^o" . $key . "\">" . $value->name . " </option>";
            }
            $osvojstva = $DB->get_records('ontology_property_individual', array('status' => '1', 'userid' => $userid, 'course_modulesid' => $key1));
            foreach ($osvojstva as $key => $value) {
                if ($value->name != "Основно")
                    echo "<option style=\"color: #006FFF\" value=\"^o" . $key . "\">" . $value->name . " </option>";
            }
        }
    }

    //specijalen slucaj koga treba kaj podatocnite svojstva osven tipot da se vnesat i >=, <=, >, < itn
    if ($posledenstek == "T") {
        echo "<option style=\"color: #9F0EA9\" value=\"and\"> and </option>";
        echo "<option style=\"color: #9F0EA9\" value=\"or\"> or </option>";
    }

    //proverka dali treba da se stavi EOF, ) } ili ]
    $isOppened = false; //pretpostavuvame deka nema otvorena zagrada ) ili } ili ]
    $i = count($stek) - 1;
    for ($i = (count($stek) - 1); $i >= 0; $i--) {
        if (!($stek[$i] == $v[1] || $stek[$i] == $v[2] || $stek[$i] == $v[6] || $stek[$i] == $v[7])) {
            if ($stek[$i] == ')') {
                echo "<option style=\"color: #000000\" value=\")\"> ) </option>";
            } else
            if ($stek[$i] == '}') {
                echo "<option style=\"color: #000000\" value=\"}\"> } </option>";
            } else
            if ($stek[$i] == ']') {
                echo "<option style=\"color: #000000\" value=\"]\"> ] </option>";
            }
            break;
        }
        if ($i == 0) {
            echo "<option style=\"color: #000000\" value=\"$\"> Крај на пишувањето </option>";
        }
    }
}

//ostana uste da zapiseme vo labelata za stekot
$string = $stek[0];
for ($i = 1; $i < (count($stek)); $i++)
    $string = $string . ' ' . $stek[$i];

echo '</SELECT> <br/>';

// echo 'Stringot e:'.$string.'.';
?>

<?php

echo '<input type="text" id="stek' . $javascript . '" name="stek' . $javascript . '" value="' . $string . '" style="visibility: hidden;" /> <br/>';
if ($posledenvnes != '$' && $posledenvnes != "") {
    if ($input[count($input) - 1] == '$')
        $size = count($input) - 1;
    else
        $size = count($input);
    for ($i = 0; $i < $size; $i++) {
        $posledenvnes = $input[$i];
        if ($posledenvnes[0] == '^') {
            if ($posledenvnes[1] == 'k') {
                $index = 24;
                $class = $DB->get_record('ontology_class', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $class->name;
            } else
            if ($posledenvnes[1] == 't') {
                $index = 25;
                $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
            } else
            if ($posledenvnes[1] == 'b') {
                $index = 26;
                $txt = substr($posledenvnes, 2 - strlen($posledenvnes));
            } else
            if ($posledenvnes[1] == 'i') {
                $index = 27;
                $individual = $DB->get_record('ontology_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $individual->name;
            } else
            if ($posledenvnes[1] == 'p') {
                $index = 28;
                $psvojstvo = $DB->get_record('ontology_property_data', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $psvojstvo->name;
            } else
            if ($posledenvnes[1] == 'o') {
                $index = 29;
                $osvojstvo = $DB->get_record('ontology_property_individual', array('id' => substr($posledenvnes, 2 - strlen($posledenvnes))));
                $txt = $osvojstvo->name;
            }
        } else {
            for ($j = 1; $j < 24; $j++) {
                if ($a[$j] == $posledenvnes) {
                    $index = $j;
                    $txt = $a[$index];
                }
            }
        }
        echo"<span style=\"color: " . $style[$index] . "\">" . $txt . " </span>";
    }
}
?>
