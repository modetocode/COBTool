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
global $DB;
$properties = $DB->get_records('ontology_property_individual', array('status' => '3', 'course_modulesid' => $id));
$ontologyid = $DB->get_record('course_modules', array('id' => $id)); //id na ontologijata
$klastertxt = $_GET["klaster"];
$klaster = explode(" ", $klastertxt);
$podredeni_svojstva = array();
foreach ($properties as $key => $value) { //gi pominuvame site klasi koi treba da se prikazat
    $zaklasteriranje = false;
    for ($i = 0; $i < count($klaster); $i++) {
        if ($klaster[$i] == $key) {
            $zaklasteriranje = true;
            break;
        }
    }
    if (!$zaklasteriranje) {
        $goima = false; //pretpostavuvame deka klasata so isto ime i ista superklasa ne e stavena 
        for ($i = 0; $i < count($podredeni_svojstva); $i++) { //gi pominuvame site staveni klasi
            //    echo $podredeni_svojstva[$i]->name.'|'.$value->name;
            if (strtolower($podredeni_svojstva[$i]->name) == strtolower($value->name) && $podredeni_svojstva[$i]->superproperty == $value->superproperty) {
                $rank = $DB->get_record('ontology_student_rank', array('userid' => $value->userid, 'ontologyid' => $ontologyid->instance));
                //sme ja nasle klasata
                /*
                  $podredeni_svojstva[$i]->id[]=$value->id;
                  $podredeni_svojstva[$i]->userid[]=$value->userid;
                  $podredeni_svojstva[$i]->rankovi[]=$rankovi[$value->userid];
                  $podredeni_svojstva[$i]->rank=$podredeni_svojstva[$i]->rank+$rankovi[$value->userid];
                 */
                for ($j = 0; $j < count($podredeni_svojstva[$i]->id); $j++) {
                    if ($podredeni_svojstva[$i]->rankovi[$j] < $rank->rating) {
                        //novata klasa treba da se stavi na j-ta pozicija
                        $podredeni_svojstva[$i]->id = insertArrayIndex($podredeni_svojstva[$i]->id, $value->id, $j);
                        $podredeni_svojstva[$i]->userid = insertArrayIndex($podredeni_svojstva[$i]->userid, $value->userid, $j);
                        $podredeni_svojstva[$i]->rankovi = insertArrayIndex($podredeni_svojstva[$i]->rankovi, $rank->rating, $j);
                        $podredeni_svojstva[$i]->rank = $podredeni_svojstva[$i]->rank + $rank->rating;
                        $goima = true;
                        //  echo $podredeni_svojstva[$i]->rank;
                        break;
                    }
                }
                if (!$goima) { //ima najmal rejting od site pa go dolepuvame na kraj na nizata
                    $podredeni_svojstva[$i]->id[] = $value->id;
                    $podredeni_svojstva[$i]->userid[] = $value->userid;
                    $podredeni_svojstva[$i]->rankovi[] = $rank->rating;
                    $podredeni_svojstva[$i]->rank = $podredeni_svojstva[$i]->rank + $rank->rating;
                    // echo $podredeni_svojstva[$i]->rank;
                    $goima = true;
                }
                break;
            }
        }
        if (!$goima) { //klasata seuste ne e dodadena pa ja dodavame
            //$podredeni_svojstva[count($podredeni_svojstva)]=$value;
            $rank = $DB->get_record('ontology_student_rank', array('userid' => $value->userid, 'ontologyid' => $ontologyid->instance));
            $n = count($podredeni_svojstva);
            $podredeni_svojstva[$n]->name = $value->name;
            $podredeni_svojstva[$n]->superproperty = $value->superproperty;
            $podredeni_svojstva[$n]->id = array();
            $podredeni_svojstva[$n]->id[] = $value->id;
            $podredeni_svojstva[$n]->userid = array();
            $podredeni_svojstva[$n]->userid[] = $value->userid;
            $podredeni_svojstva[$n]->rank = $rank->rating;
            $podredeni_svojstva[$n]->rankovi = array();
            $podredeni_svojstva[$n]->rankovi[] = $rank->rating;
            //   echo 'ne Golemina: '.count($podredeni_svojstva).'<br/>';
        }
    }
}



//echo count($podredeni_svojstva);
//echo $podredeni_svojstva[1]->id[0];
//sortiranje po rank
if ($_GET["tip"] == 1) {
    $ranks = array();
    foreach ($podredeni_svojstva as $key => $value)
        $ranks[] = $value->rank;
    array_multisort($ranks, SORT_NUMERIC, SORT_DESC, $podredeni_svojstva);
} else {
    //sortiranje po ime
    $names = array();
    foreach ($podredeni_svojstva as $key => $value)
        $names[] = $value->name;
    array_multisort($names, SORT_STRING, SORT_ASC, $podredeni_svojstva);
    //sortDataSet($podredeni_svojstva, 'rank', SORT_DESC, SORT_NUMERIC); 
}
?><form name="svojstva" id="svojstva">

    <table>
        <tr><td valign="top" >
                <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 650px;">
                    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>
<?php echo get_string('new_properties_in_ontology', 'ontology'); ?>
                            </center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">  


                        <table>
<?php
if (count($podredeni_svojstva) == 0) {
    echo '<tr> <td colspan=3> ' . get_string('no_new_object_properties_for_ontology', 'ontology') . ' </td> </tr>';
}
for ($i = 0; $i < count($podredeni_svojstva); $i++) {
    ?>
                                <tr style="border:1px solid #7CAFFC; color: green; background: #E8E8E8;">
                                    <td colspan="2">
    <?php echo get_string('property_name', 'ontology') . ': <b> ' . get_name_of_oproperty($podredeni_svojstva[$i]->id[0]) . '</b> <b> ' . get_string('superproperty', 'ontology') . ':' . get_name_of_oproperty($podredeni_svojstva[$i]->superproperty) . ' </b> ' . get_string('rating', 'ontology') . ' ' . $podredeni_svojstva[$i]->rank; ?>  


                                    </td>
                                    <td>
    <?php
    echo get_string('students', 'ontology') . ":";
    for ($j = 0; $j < count($podredeni_svojstva[$i]->userid); $j++) {
        global $DB;
        $user = $DB->get_record('user', array('id' => $podredeni_svojstva[$i]->userid[$j]), '*', MUST_EXIST);
        //get_user_profile ($podredeni_svojstva[$i]->userid[$j]).
        echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
    }
    ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_description_characteristicks_and_inverse_function', 'ontology'); ?> </b> </td>
                                </tr>
    <?php
    $podredeni_opisi = array();
    //gi pominuvame site opisi i proveruvame dali imame isti
    //gi grupirame site isti opisi
    for ($j = 0; $j < count($podredeni_svojstva[$i]->userid); $j++) {
        $karak = array();
        $karak[] = get_string('functional', 'ontology');
        $karak[] = get_string('inverse_functional', 'ontology');
        $karak[] = get_string('transitive', 'ontology');
        $karak[] = get_string('symmetric', 'ontology');
        $karak[] = get_string('asymmetric', 'ontology');
        $karak[] = get_string('reflexive', 'ontology');
        $karak[] = get_string('irreflexive', 'ontology');
        $svojstvo = $DB->get_record('ontology_property_individual', array('id' => $podredeni_svojstva[$i]->id[$j]));
        $str = $svojstvo->attributes . '';
        $karakteristiki = "";
        for ($k = 0; $k < strlen($str); $k++) {
            if ($str[$k] == '1') {
                if ($karakteristiki == "")
                    $karakteristiki = $karak[$k];
                else
                    $karakteristiki = $karakteristiki . ', ' . $karak[$k];
            }
        }
        if ($karakteristiki == '')
            $karakteristiki = get_string('no_characteristics', 'ontology');
        if ($svojstvo->inverse == '0')
            $inverzna = get_string('no_inverse_property', 'ontology');
        else
            $inverzna = get_name_of_oproperty($svojstvo->inverse);
        $n = count($podredeni_opisi);
        $podredeni_opisi[$n]->opis = get_opis_of_oproperty($podredeni_svojstva[$i]->id[$j]) . '<br/> <b> &nbsp &nbsp &nbsp ' . get_string('characteristicks', 'ontology') . ': </b>' . $karakteristiki . '<br/> <b> &nbsp &nbsp &nbsp ' . get_string('inverse_property', 'ontology') . ': </b>' . $inverzna;
        $podredeni_opisi[$n]->userid = array();
        $podredeni_opisi[$n]->userid[] = $podredeni_svojstva[$i]->userid[$j];
        $podredeni_opisi[$n]->id = $podredeni_svojstva[$i]->id[$j];
    }
    for ($j = 0; $j < count($podredeni_opisi); $j++) {
        echo '<tr> <td width="20"> </td> <td> <input type="radio" id="opisk' . $i . '" name="opisk' . $i . '" value="' . $podredeni_opisi[$j]->id . '"';
        if ($j == 0)
            echo ' CHECKED';
        echo '> <b> ' . get_string('description', 'ontology') . ': </b>' . $podredeni_opisi[$j]->opis . '</input> </td> <td>';
        for ($k = 0; $k < count($podredeni_opisi[$j]->userid); $k++) {
            $user = $DB->get_record('user', array('id' => $podredeni_opisi[$j]->userid[$k]), '*', MUST_EXIST);
            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
            //echo get_user_profile ($podredeni_opisi[$j]->userid[$k]).' ';
        }
        echo '</td> </tr>';
    }
    ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_domain_expressions', 'ontology'); ?>: </b> </td>
                                </tr>         
                                <?php
                                global $DB;
                                $allexpressions = array();
                                for ($j = 0; $j < count($podredeni_svojstva[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'type' => '1', 'ontology_propertyid' => $podredeni_svojstva[$i]->id[$j]));
                                    foreach ($expressions as $key => $value) {
                                        $allexpressions[] = $value;
                                    }
                                }
                                $podredeni_izrazi = array();
                                //gi pominuvame site izrazi i proveruvame dali imame isti
                                //gi grupirame site isti opisi
                                for ($j = 0; $j < count($allexpressions); $j++) {
                                    $goima = false;
                                    for ($k = 0; $k < count($podredeni_izrazi); $k++) {
                                        if ($podredeni_izrazi[$k]->text == $allexpressions[$j]->expression_text) {
                                            $podredeni_izrazi[$k]->userid[] = $allexpressions[$j]->userid;
                                            $goima = true;
                                            break;
                                        }
                                    }
                                    if (!$goima) {
                                        $n = count($podredeni_izrazi);
                                        $podredeni_izrazi[$n]->text = $allexpressions[$j]->expression_text;
                                        $podredeni_izrazi[$n]->userid = array();
                                        $podredeni_izrazi[$n]->userid[] = $allexpressions[$j]->userid;
                                        $podredeni_izrazi[$n]->id = $allexpressions[$j]->id;
                                        $podredeni_izrazi[$n]->expression = $allexpressions[$j]->expression;
                                    }
                                }
                                if (count($allexpressions) == 0) {
                                    echo '<tr> <td width="20"> </td> <td> ' . get_string('there_are_no_domain_expressions', 'ontology') . ' </td> </tr>';
                                } else {
                                    for ($j = 0; $j < count($podredeni_izrazi); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="domeni' . $i . '" id="domeni' . $i . '" value="' . $podredeni_izrazi[$j]->id . '">  ';
                                        get_expression_in_color($podredeni_izrazi[$j]->expression);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_izrazi[$j]->userid); $k++) {
                                            // echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $podredeni_izrazi[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                        }
                                        echo '</td></tr>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_range_expressions', 'ontology'); ?>: </b> </td>
                                </tr>         
                                <?php
                                global $DB;
                                $allexpressions = array();
                                for ($j = 0; $j < count($podredeni_svojstva[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'type' => '2', 'ontology_propertyid' => $podredeni_svojstva[$i]->id[$j]));
                                    foreach ($expressions as $key => $value) {
                                        $allexpressions[] = $value;
                                    }
                                }
                                $podredeni_izrazi = array();
                                //gi pominuvame site izrazi i proveruvame dali imame isti
                                //gi grupirame site isti opisi
                                for ($j = 0; $j < count($allexpressions); $j++) {
                                    $goima = false;
                                    for ($k = 0; $k < count($podredeni_izrazi); $k++) {
                                        if ($podredeni_izrazi[$k]->text == $allexpressions[$j]->expression_text) {
                                            $podredeni_izrazi[$k]->userid[] = $allexpressions[$j]->userid;
                                            $goima = true;
                                            break;
                                        }
                                    }
                                    if (!$goima) {
                                        $n = count($podredeni_izrazi);
                                        $podredeni_izrazi[$n]->text = $allexpressions[$j]->expression_text;
                                        $podredeni_izrazi[$n]->userid = array();
                                        $podredeni_izrazi[$n]->userid[] = $allexpressions[$j]->userid;
                                        $podredeni_izrazi[$n]->id = $allexpressions[$j]->id;
                                        $podredeni_izrazi[$n]->expression = $allexpressions[$j]->expression;
                                    }
                                }
                                if (count($allexpressions) == 0) {
                                    echo '<tr> <td width="20"> </td> <td> ' . get_string('there_are_no_range_expressions', 'ontology') . ' </td> </tr>';
                                } else {
                                    for ($j = 0; $j < count($podredeni_izrazi); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="range' . $i . '" id="range' . $i . '" value="' . $podredeni_izrazi[$j]->id . '">  ';
                                        get_expression_in_color($podredeni_izrazi[$j]->expression);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_izrazi[$j]->userid); $k++) {
                                            // echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $podredeni_izrazi[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                        }
                                        echo '</td></tr>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_equivalent_properties', 'ontology'); ?>: </b> </td>
                                </tr>
                                <?php
                                global $DB;
                                $allequivalent = array();
                                for ($j = 0; $j < count($podredeni_svojstva[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_property_equivalent', array('status' => '3', 'type' => '1', 'ontology_propertyid' => $podredeni_svojstva[$i]->id[$j]));
                                    foreach ($expressions as $key => $value) {
                                        $allequivalent[] = $value;
                                    }
                                }
                                $podredeni_ekvivalentni = array();
                                for ($j = 0; $j < count($allequivalent); $j++) {
                                    $goima = false;
                                    for ($k = 0; $k < count($podredeni_ekvivalentni); $k++) {
                                        if ($podredeni_ekvivalentni[$k]->text == get_name_of_oproperty($allequivalent[$j]->ontology_propertyid2)) {
                                            $podredeni_ekvivalentni[$k]->userid[] = $allequivalent[$j]->userid;
                                            $goima = true;
                                            break;
                                        }
                                    }
                                    if (!$goima) {
                                        $n = count($podredeni_ekvivalentni);
                                        $podredeni_ekvivalentni[$n]->text = get_name_of_oproperty($allequivalent[$j]->ontology_propertyid2);
                                        $podredeni_ekvivalentni[$n]->userid = array();
                                        $podredeni_ekvivalentni[$n]->userid[] = $allequivalent[$j]->userid;
                                        $podredeni_ekvivalentni[$n]->id = $allequivalent[$j]->id;
                                        $podredeni_ekvivalentni[$n]->textid = $allequivalent[$j]->ontology_propertyid2;
                                    }
                                }
                                if (count($allequivalent) == 0) {
                                    echo '<tr> <td width="20"> </td> <td> ' . get_string('there_are_no_equivalent_properties', 'ontology') . ' </td> </tr>';
                                } {
                                    for ($j = 0; $j < count($podredeni_ekvivalentni); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="ekviv' . $i . '" id="ekviv' . $i . '" value="' . $podredeni_ekvivalentni[$j]->id . '">  ';
                                        echo get_name_of_oproperty($podredeni_ekvivalentni[$j]->textid);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_ekvivalentni[$j]->userid); $k++) {
                                            // echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $podredeni_ekvivalentni[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                        }
                                        echo '</td></tr>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_disjoint_properties', 'ontology'); ?>: </b> </td>
                                </tr>
                                <?php
                                global $DB;
                                $alldisjoint = array();
                                for ($j = 0; $j < count($podredeni_svojstva[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_property_disjoint', array('status' => '3', 'type' => '1', 'ontology_propertyid' => $podredeni_svojstva[$i]->id[$j]));
                                    foreach ($expressions as $key => $value) {
                                        $alldisjoint[] = $value;
                                    }
                                }
                                $podredeni_disjunktni = array();
                                for ($j = 0; $j < count($alldisjoint); $j++) {
                                    $goima = false;
                                    for ($k = 0; $k < count($podredeni_disjunktni); $k++) {
                                        if ($podredeni_disjunktni[$k]->text == get_name_of_oproperty($alldisjoint[$j]->ontology_propertyid2)) {
                                            $podredeni_disjunktni[$k]->userid[] = $alldisjoint[$j]->userid;
                                            $goima = true;
                                            break;
                                        }
                                    }
                                    if (!$goima) {
                                        $n = count($podredeni_disjunktni);
                                        $podredeni_disjunktni[$n]->text = get_name_of_oproperty($alldisjoint[$j]->ontology_propertyid2);
                                        $podredeni_disjunktni[$n]->userid = array();
                                        $podredeni_disjunktni[$n]->userid[] = $alldisjoint[$j]->userid;
                                        $podredeni_disjunktni[$n]->id = $alldisjoint[$j]->id;
                                        $podredeni_disjunktni[$n]->textid = $alldisjoint[$j]->ontology_propertyid2;
                                    }
                                }
                                if (count($alldisjoint) == 0) {
                                    echo '<tr> <td width="20"> </td> <td> ' . get_string('there_are_no_disjoint_properties', 'ontology') . ' </td> </tr>';
                                } {
                                    for ($j = 0; $j < count($podredeni_disjunktni); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="disjk' . $i . '" id="disjk' . $i . '" value="' . $podredeni_disjunktni[$j]->id . '">  ';
                                        echo get_name_of_oproperty($podredeni_disjunktni[$j]->textid);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_disjunktni[$j]->userid); $k++) {
                                            // echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $podredeni_disjunktni[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                        }
                                        echo '</td></tr>';
                                    }
                                }
                                //proverka za neregularnosti vo bazata na znaenje
                                $expressions = $DB->get_records_sql('SELECT * FROM mdl_ontology_class_expression WHERE (status=2 OR status=3) AND course_modulesid=?', array($id));
                                $prvpat = true;
                                $zabrishenje = "";
                                foreach ($expressions as $key => $value) {
                                    $izraz = explode(' ', $value->expression);
                                    $epominato = false;
                                    for ($j = 0; $j < count($izraz); $j++) {
                                        if (strlen($izraz[$j]) > 1) {
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'o') {//proverka dali e svojstvo
                                                for ($k = 0; $k < count($podredeni_svojstva[$i]->id); $k++) {
                                                    if ($podredeni_svojstva[$i]->id[$k] == substr($izraz[$j], 2)) {
                                                        if ($prvpat) {
                                                            echo '<tr> <td> </td> <td style="color: red"> <b> ' . get_string('caution_By_removing_the_property_the_following_expressions_will_also_be_removed', 'ontology') . ': </b> </td> </tr>';
                                                        }
                                                        echo '<tr> <td></td><td>';
                                                        get_expression_in_color($value->expression);
                                                        echo '- ' . get_string('expression_for_the_class', 'ontology') . ': ' . get_name_of_class($value->ontology_classid);
                                                        echo '</td> </tr>';
                                                        if ($zabrishenje == "") {
                                                            $zabrishenje = $key;
                                                        }
                                                        else
                                                            $zabrishenje = $zabrishenje . ' ' . $key;
                                                        $prvpat = false;
                                                        $epominato = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($epominato)
                                            break;
                                        //$podredeni_svojstva[$i]->id[$k]
                                    }
                                }

                                $expressions = $DB->get_records_sql('SELECT * FROM mdl_ontology_property_expression WHERE (status=2 OR status=3) AND course_modulesid=?', array($id));
                                $zabrishenje2 = "";
                                foreach ($expressions as $key => $value) {
                                    $izraz = explode(' ', $value->expression);
                                    $epominato = false;
                                    for ($j = 0; $j < count($izraz); $j++) {
                                        if (strlen($izraz[$j]) > 1) {
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'o') {//proverka dali e svojstvo
                                                for ($k = 0; $k < count($podredeni_svojstva[$i]->id); $k++) {
                                                    if ($podredeni_svojstva[$i]->id[$k] == substr($izraz[$j], 2)) {
                                                        if ($prvpat) {
                                                            echo '<tr> <td> </td> <td style="color: red"> <b> ' . get_string('caution_By_removing_the_property_the_following_expressions_will_also_be_removed', 'ontology') . ': </b> </td> </tr>';
                                                        }
                                                        echo '<tr> <td></td><td>';
                                                        get_expression_in_color($value->expression);
                                                        if ($value->type == 1)
                                                            echo '- ' . get_string('expression_for_the_property', 'ontology') . ': ' . get_name_of_oproperty($value->ontology_propertyid);
                                                        else
                                                            echo '- ' . get_string('expression_for_the_property', 'ontology') . ': ' . get_name_of_dproperty($value->ontology_propertyid);
                                                        echo '</td> </tr>';
                                                        if ($zabrishenje2 == "") {
                                                            $zabrishenje2 = $key;
                                                        }
                                                        else
                                                            $zabrishenje2 = $zabrishenje2 . ' ' . $key;
                                                        $epominato = true;
                                                        $prvpat = false;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($epominato)
                                            break;
                                    }
                                }

                                $expressions = $DB->get_records_sql('SELECT * FROM mdl_ontology_individual_expression WHERE (status=2 OR status=3) AND course_modulesid=?', array($id));
                                $zabrishenje3 = "";
                                foreach ($expressions as $key => $value) {
                                    $izraz = explode(' ', $value->expression);
                                    $epominato = false;
                                    for ($j = 0; $j < count($izraz); $j++) {
                                        if (strlen($izraz[$j]) > 1) {
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'o') {//proverka dali e svojstvo
                                                for ($k = 0; $k < count($podredeni_svojstva[$i]->id); $k++) {
                                                    if ($podredeni_svojstva[$i]->id[$k] == substr($izraz[$j], 2)) {
                                                        if ($prvpat) {
                                                            echo '<tr> <td> </td> <td style="color: red"> <b> ' . get_string('caution_By_removing_the_property_the_following_expressions_will_also_be_removed', 'ontology') . ': </b> </td> </tr>';
                                                        }
                                                        echo '<tr> <td></td><td>';
                                                        get_expression_in_color($value->expression);
                                                        echo '- ' . get_string('expression_for_the_individual', 'ontology') . ': ' . get_name_of_individual($value->ontology_individualid);
                                                        echo '</td> </tr>';
                                                        if ($zabrishenje3 == "") {
                                                            $zabrishenje3 = $key;
                                                        }
                                                        else
                                                            $zabrishenje3 = $zabrishenje3 . ' ' . $key;
                                                        $epominato = true;
                                                        $prvpat = false;
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                        if ($epominato)
                                            break;
                                    }
                                }

                                $idia = $podredeni_svojstva[$i]->id[0];
                                for ($j = 1; $j < count($podredeni_svojstva[$i]->id); $j++) {
                                    $idia = $idia . ' ' . $podredeni_svojstva[$i]->id[$j];
                                }
                                ?>
                                <tr>
                                    <td colspan="3">
                                <?php
                                echo '<input type="hidden" id="svojs' . $i . '" value="' . $idia . '" > </input>';
                                echo '<input type="hidden" id="brishi' . $i . '" value="' . $zabrishenje . '" > </input>';
                                echo '<input type="hidden" id="brishi2' . $i . '" value="' . $zabrishenje2 . '" > </input>';
                                echo '<input type="hidden" id="brishi3' . $i . '" value="' . $zabrishenje3 . '" > </input>';
                                echo '<div align="center" >';
                                $superpropertyaccepted = $DB->get_records('ontology_property_individual', array('id' => $podredeni_svojstva[$i]->superproperty, 'status' => '2'));
                                if (count($superpropertyaccepted) == 0)
                                    echo '<input type="button" id="add' . $i . '" value="' . get_string('add_to_ontology', 'ontology') . '" onclick="insertProperty(this);" style="width:500px;" disabled="true" /> <br />';
                                else
                                    echo '<input type="button" id="add' . $i . '" value="' . get_string('add_to_ontology', 'ontology') . '" onclick="insertProperty(this);" style="width:500px;"/><br />';
                                echo '<input type="button" id="klaster' . $i . '" value="' . get_string('add_property_to_merging_list', 'ontology') . '" onclick="klasterProperty(this);" style="width:500px;"/><br />';
                                echo '<input type="button" id="spoj' . $i . '" value="' . get_string('merge_it_with_another_already_accepted_property_from_ontology', 'ontology') . '" onclick="spojProperty(this);" style="width:500px;"/><br />';
                                echo '<input type="button" id="del' . $i . '" value="' . get_string('remove', 'ontology') . '" onclick="deleteProperty(this);" style="width:500px;"/></div>';
                                ?>
                                        <hr/>
                                    </td>
                                <?php
                            }
                            ?>
                        </table>
                    </div></div>
            </td>
            <td valign="top"> 
                <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest">
                    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center><?php echo get_string('list_of_properties_for_merging', 'ontology'); ?>:</center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">

                        <table>

                            <tr>
                                <td>
                            <?php
                            if ($klastertxt == "")
                                echo get_string('there_are_no_properties_present_in_the_list', 'ontology');
                            else {
                                $prethodna = "";
                                for ($i = 0; $i < count($klaster); $i++) {
                                    if (get_name_of_oproperty($klaster[$i]) != $prethodna)
                                        echo get_name_of_oproperty($klaster[$i]) . '<br/>';
                                    $prethodna = get_name_of_oproperty($klaster[$i]);
                                }
                                echo '<input type="button" value="' . get_string('edit_the_list', 'ontology') . '" onClick="KlasteriraniOSvojstva();"> </input>';
                            }
                            ?>
                                </td>
                            </tr>
                        </table>
                    </div></div>
            </td>
    </table>
    <table><tr><td valign="top">
                <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 650px;">
                    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center><?php echo get_string('update_existing_properties_in_ontology', 'ontology'); ?></center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">

                        <table align="center">
                            <?php
                            $ontology = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
                            $properties = $DB->get_records_sql('SELECT * FROM mdl_ontology_property_individual WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
                            $i = 0;
                            foreach ($properties as $key => $value) {
                                $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                $expressions2 = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '2'));
                                $equ_properties = $DB->get_records('ontology_property_equivalent', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                $dis_properties = $DB->get_records('ontology_property_disjoint', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                if (count($expressions) > 0 || count($expressions2) > 0 || count($equ_properties) > 0 || count($dis_properties) > 0) {
                                    echo '<tr style="border:1px solid #111; color: green; background: #E8E8E8;"> <td colspan="2"> ';
                                    echo get_string('property_name', 'ontology') . ': <b> ' . get_name_of_oproperty($key) . '</b> ' . get_string('superproperty', 'ontology') . ': <b>' . get_name_of_oproperty($value->superproperty) . '</b> </td> <td> </td> </tr>';

                                    $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_domain_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($expressions) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td>  </tr>';
                                    else {
                                        foreach ($expressions as $key2 => $value2) {
                                            echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="updatek' . $i . '" id="updatek' . $i . '" value="' . $key2 . '">  ';
                                            get_expression_in_color($value2->expression);
                                            echo '</input> </td> <td>';
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $value2->userid), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            echo '</td></tr>';
                                        }
                                    }

                                    $expressions = $DB->get_records('ontology_property_expression', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '2'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_range_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($expressions) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td>  </tr>';
                                    else {
                                        foreach ($expressions as $key2 => $value2) {
                                            echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="updatek' . $i . '" id="updatek' . $i . '" value="' . $key2 . '">  ';
                                            get_expression_in_color($value2->expression);
                                            echo '</input> </td> <td>';
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $value2->userid), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            echo '</td></tr>';
                                        }
                                    }

                                    $equ_properties = $DB->get_records('ontology_property_equivalent', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_equivalent_property_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($equ_properties) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td>  </tr>';
                                    else {
                                        foreach ($equ_properties as $key2 => $value2) {
                                            echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="updateq' . $i . '" id="updateq' . $i . '" value="' . $key2 . '">  ';
                                            echo get_name_of_oproperty($value2->ontology_propertyid2);
                                            echo '</input> </td> <td>';
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $value2->userid), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            echo '</td></tr>';
                                        }
                                    }

                                    $dis_properties = $DB->get_records('ontology_property_disjoint', array('status' => '3', 'ontology_propertyid' => $key, 'type' => '1'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_disjoint_property_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($dis_properties) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td>  </tr>';
                                    else {
                                        foreach ($dis_properties as $key2 => $value2) {
                                            echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="updated' . $i . '" id="updated' . $i . '" value="' . $key2 . '">  ';
                                            echo get_name_of_oproperty($value2->ontology_propertyid2);
                                            echo '</input> </td> <td>';
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $value2->userid), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            echo '</td></tr>';
                                        }
                                    }

                                    echo '<tr> <td colspan=3>';
                                    echo '<input type="button" id="updins' . $i . '" value="' . get_string('accept_the_changes', 'ontology') . '" onClick="updateProperty(this);" />';
                                    echo '<input type="button" id="upddel' . $i . '" value="' . get_string('reject_all_changes', 'ontology') . '" onClick="updateDeleteProperty(this);"/>';
                                    echo '<hr/> </td> </tr>';
                                    $i++;
                                }
                            }
                            if ($i == 0) {
                                echo '<tr> <td colspan=2> ' . get_string('existing_properties_not_updated', 'ontology') . ' </td> </tr>';
                            }
                            ?>
                        </table>
                    </div></div></td></tr></table>
    <table><tr><td><input type="button" value="<?php echo get_string('back', 'ontology') ?>" onclick="nazad();"/></td></tr></table>
</form>
<style>
    .latest { overflow:visible; position:static; }
</style>
<?php 
   // echo $OUTPUT->footer();