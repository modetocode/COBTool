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
$classes = $DB->get_records('ontology_class', array('status' => '3', 'course_modulesid' => $id));
$ontologyid = $DB->get_record('course_modules', array('id' => $id)); //id na ontologijata
$klastertxt = $_GET["klaster"];
$klaster = explode(" ", $klastertxt);
$podredeni_klasi = array();
foreach ($classes as $key => $value) { //gi pominuvame site klasi koi treba da se prikazat
    $zaklasteriranje = false;
    for ($i = 0; $i < count($klaster); $i++) {
        if ($klaster[$i] == $key) {
            $zaklasteriranje = true;
            break;
        }
    }
    if (!$zaklasteriranje) {
        $goima = false; //pretpostavuvame deka klasata so isto ime i ista superklasa ne e stavena 
        for ($i = 0; $i < count($podredeni_klasi); $i++) { //gi pominuvame site staveni klasi
            //    echo $podredeni_klasi[$i]->name.'|'.$value->name;
            if (strtolower($podredeni_klasi[$i]->name) == strtolower($value->name) && $podredeni_klasi[$i]->superclass == $value->superclass) {
                $rank = $DB->get_record('ontology_student_rank', array('userid' => $value->userid, 'ontologyid' => $ontologyid->instance));
                //sme ja nasle klasata
                /*
                  $podredeni_klasi[$i]->id[]=$value->id;
                  $podredeni_klasi[$i]->userid[]=$value->userid;
                  $podredeni_klasi[$i]->rankovi[]=$rankovi[$value->userid];
                  $podredeni_klasi[$i]->rank=$podredeni_klasi[$i]->rank+$rankovi[$value->userid];
                 */
                for ($j = 0; $j < count($podredeni_klasi[$i]->id); $j++) {
                    if ($podredeni_klasi[$i]->rankovi[$j] < $rank->rating) {
                        //novata klasa treba da se stavi na j-ta pozicija
                        $podredeni_klasi[$i]->id = insertArrayIndex($podredeni_klasi[$i]->id, $value->id, $j);
                        $podredeni_klasi[$i]->userid = insertArrayIndex($podredeni_klasi[$i]->userid, $value->userid, $j);
                        $podredeni_klasi[$i]->rankovi = insertArrayIndex($podredeni_klasi[$i]->rankovi, $rank->rating, $j);
                        $podredeni_klasi[$i]->rank = $podredeni_klasi[$i]->rank + $rank->rating;
                        $goima = true;
                        //  echo $podredeni_klasi[$i]->rank;
                        break;
                    }
                }
                if (!$goima) { //ima najmal rejting od site pa go dolepuvame na kraj na nizata
                    $podredeni_klasi[$i]->id[] = $value->id;
                    $podredeni_klasi[$i]->userid[] = $value->userid;
                    $podredeni_klasi[$i]->rankovi[] = $rank->rating;
                    $podredeni_klasi[$i]->rank = $podredeni_klasi[$i]->rank + $rank->rating;
                    // echo $podredeni_klasi[$i]->rank;
                    $goima = true;
                }
                break;
            }
        }
        if (!$goima) { //klasata seuste ne e dodadena pa ja dodavame
            //$podredeni_klasi[count($podredeni_klasi)]=$value;
            $rank = $DB->get_record('ontology_student_rank', array('userid' => $value->userid, 'ontologyid' => $ontologyid->instance));
            $n = count($podredeni_klasi);
            $podredeni_klasi[$n]->name = $value->name;
            $podredeni_klasi[$n]->superclass = $value->superclass;
            $podredeni_klasi[$n]->id = array();
            $podredeni_klasi[$n]->id[] = $value->id;
            $podredeni_klasi[$n]->userid = array();
            $podredeni_klasi[$n]->userid[] = $value->userid;
            $podredeni_klasi[$n]->rank = $rank->rating;
            $podredeni_klasi[$n]->rankovi = array();
            $podredeni_klasi[$n]->rankovi[] = $rank->rating;
            //   echo 'ne Golemina: '.count($podredeni_klasi).'<br/>';
        }
    }
}



//echo count($podredeni_klasi);
//echo $podredeni_klasi[1]->id[0];
//sortiranje po rank
if ($_GET["tip"] == 1) {
    $ranks = array();
    foreach ($podredeni_klasi as $key => $value)
        $ranks[] = $value->rank;
    array_multisort($ranks, SORT_NUMERIC, SORT_DESC, $podredeni_klasi);
} else {
    //sortiranje po ime
    $names = array();
    foreach ($podredeni_klasi as $key => $value)
        $names[] = $value->name;
    array_multisort($names, SORT_STRING, SORT_ASC, $podredeni_klasi);
    //sortDataSet($podredeni_klasi, 'rank', SORT_DESC, SORT_NUMERIC); 
}
?>
<script>

    $(function() {
        $("#nazad").button();
    });
</script>
<form name="klasi" id="klasi">
    <table>
        <tr>
            <td valign="top">
                <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 650px;">
                    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center><?php echo get_string('new_classes_in_ontology', 'ontology'); ?></center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">



                        <table align="center">
<?php
if (count($podredeni_klasi) == 0) {
    echo '<tr> <td colspan=3>' . get_string('no_new_classes_for_adding_to_ontology', 'ontology') . '</td> </tr>';
}
for ($i = 0; $i < count($podredeni_klasi); $i++) {
    ?>
                                <tr style="border:1px solid #7CAFFC; color: blue; background: #EEEEEE;">
                                    <td colspan="2">
    <?php
    echo get_string('class_name', 'ontology') . ': <b> ' . get_name_of_class($podredeni_klasi[$i]->id[0]) . '</b>';

    echo get_string('superclass', 'ontology') . ': <b>' . get_name_of_class($podredeni_klasi[$i]->superclass) . ' </b>' . get_string('rating', 'ontology') . ' ' . $podredeni_klasi[$i]->rank;
    ?>  


                                    </td>
                                    <td>
                                <?php
                                echo get_string('students', 'ontology') . ":";

                                for ($j = 0; $j < count($podredeni_klasi[$i]->userid); $j++) {
                                    global $DB;
                                    $user = $DB->get_record('user', array('id' => $podredeni_klasi[$i]->userid[$j]), '*', MUST_EXIST);
                                    //get_user_profile ($podredeni_klasi[$i]->userid[$j]).
                                    echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                }
                                ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_a_description', 'ontology'); ?>: </b> </td>
                                </tr>
    <?php
    $podredeni_opisi = array();
    //gi pominuvame site opisi i proveruvame dali imame isti
    //gi grupirame site isti opisi
    for ($j = 0; $j < count($podredeni_klasi[$i]->userid); $j++) {
        $goima = false;
        for ($k = 0; $k < count($podredeni_opisi); $k++) {
            if ($podredeni_opisi[$k]->opis == get_opis_of_class($podredeni_klasi[$i]->id[$j])) {
                $podredeni_opisi[$k]->userid[] = $podredeni_klasi[$i]->userid[$j];
                $goima = true;
                break;
            }
        }
        if (!$goima) {
            $n = count($podredeni_opisi);
            //echo get_opis_of_class($podredeni_klasi[$i]->id[$j]);
            $podredeni_opisi[$n]->opis = get_opis_of_class($podredeni_klasi[$i]->id[$j]);
            $podredeni_opisi[$n]->userid = array();
            $podredeni_opisi[$n]->userid[] = $podredeni_klasi[$i]->userid[$j];
            $podredeni_opisi[$n]->id = $podredeni_klasi[$i]->id[$j];
        }
    }
    for ($j = 0; $j < count($podredeni_opisi); $j++) {
        echo '<tr> <td width="20"> </td> <td> <input type="radio" id="opisk' . $i . '" name="opisk' . $i . '" value="' . $podredeni_opisi[$j]->id . '"';
        if ($j == 0)
            echo ' CHECKED';
        echo '> ' . $podredeni_opisi[$j]->opis . '</input> </td> <td>';
        for ($k = 0; $k < count($podredeni_opisi[$j]->userid); $k++) {
            $user = $DB->get_record('user', array('id' => $podredeni_opisi[$j]->userid[$k]), '*', MUST_EXIST);
            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
            //echo get_user_profile ($podredeni_opisi[$j]->userid[$k]).' ';
        }
        echo '</td> </tr>';
    }
    // echo '<tr> <td width="20"> </td> <td> <input type="radio" name="opisk'.$i.'" value="'.$podredeni_klasi[$i]->id[$j].'"> '.get_opis_of_class($podredeni_klasi[$i]->id[$j]).'</input> </td> <td>'.get_user_profile ($podredeni_klasi[$i]->userid[$j]).'</td> </tr>';
    ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b><?php echo get_string('choose_superclass_expressions', 'ontology') . ':'; ?></b> </td>
                                </tr>         
                                <?php
                                global $DB;
                                $allexpressions = array();
                                for ($j = 0; $j < count($podredeni_klasi[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'type' => '1', 'ontology_classid' => $podredeni_klasi[$i]->id[$j]));
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
                                    echo '<tr> <td width="20"> </td> <td>' . get_string('there_are_no_superclass_expressions', 'ontology') . '</td> </tr>';
                                } else {
                                    for ($j = 0; $j < count($podredeni_izrazi); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="superk' . $i . '" id="superk' . $i . '" value="' . $podredeni_izrazi[$j]->id . '">  ';
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
                                /* global  $DB;
                                  $ima=false;
                                  for ($j=0;$j<count($podredeni_klasi[$i]->id);$j++)
                                  {
                                  $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'type'=>'1', 'ontology_classid' => $podredeni_klasi[$i]->id[$j]));
                                  foreach ($expressions as $key=>$value)
                                  {
                                  echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="superk'.$i.'" value="'.$key.'">  ';
                                  get_expression_in_color($value->expression);
                                  $ima=true;
                                  echo '</input> </td> <td>'.get_user_profile($value->userid).'</td></tr>';
                                  }
                                  }
                                  if (!$ima)
                                  {
                                  echo '<tr> <td width="20"> </td> <td> Нема изрази за надкласи </td> </tr>';
                                  } */
                                // echo count($expressions);
                                ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_equivalent_class_expressions', 'ontology'); ?>: </b> </td>
                                </tr>         
                                <?php
                                global $DB;
                                $allexpressions = array();
                                for ($j = 0; $j < count($podredeni_klasi[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'type' => '2', 'ontology_classid' => $podredeni_klasi[$i]->id[$j]));
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
                                    echo '<tr> <td width="20"> </td> <td>' . get_string('there_are_no_equivalent_class_expressions', 'ontology') . '</td> </tr>';
                                } else {
                                    for ($j = 0; $j < count($podredeni_izrazi); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="ekvik' . $i . '" id="ekvik' . $i . '" value="' . $podredeni_izrazi[$j]->id . '">  ';
                                        get_expression_in_color($podredeni_izrazi[$j]->expression);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_izrazi[$j]->userid); $k++) {
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $podredeni_izrazi[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                        }
                                        echo '</td></tr>';
                                    }
                                }
                                ?>
                                <tr>
                                    <td width="20"> </td>
                                    <td> <b> <?php echo get_string('choose_disjoint_class_expressions', 'ontology'); ?>: </b> </td>
                                </tr>         
                                <?php
                                global $DB;
                                $allexpressions = array();
                                for ($j = 0; $j < count($podredeni_klasi[$i]->id); $j++) {
                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'type' => '3', 'ontology_classid' => $podredeni_klasi[$i]->id[$j]));
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
                                    echo '<tr> <td width="20"> </td> <td>' . get_string('there_are_no_disjoint_class_expressions', 'ontology') . '</td> </tr>';
                                } else {
                                    for ($j = 0; $j < count($podredeni_izrazi); $j++) {
                                        echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="disjk' . $i . '" id="disjk' . $i . '" value="' . $podredeni_izrazi[$j]->id . '">  ';
                                        get_expression_in_color($podredeni_izrazi[$j]->expression);
                                        echo '</input> </td> <td>';
                                        for ($k = 0; $k < count($podredeni_izrazi[$j]->userid); $k++) {
                                            $user = $DB->get_record('user', array('id' => $podredeni_izrazi[$j]->userid[$k]), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                        }
                                        echo '</td></tr>';
                                    }
                                }



                                //proverka za neregularnosti vo bazata na znaenje
                                //    $expressions=$DB->get_records('ontology_class_expression',array('status'=>'2','course_modulesid'=>$id));
                                $expressions = $DB->get_records_sql('SELECT * FROM mdl_ontology_class_expression WHERE (status=2 OR status=3) AND course_modulesid=?', array($id));
                                $prvpat = true;
                                $zabrishenje = "";
                                foreach ($expressions as $key => $value) {
                                    $izraz = explode(' ', $value->expression);
                                    $epominato = false;
                                    for ($j = 0; $j < count($izraz); $j++) {
                                        if (strlen($izraz[$j]) > 1) {
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'k') {//proverka dali e klasa
                                                for ($k = 0; $k < count($podredeni_klasi[$i]->id); $k++) {
                                                    if ($podredeni_klasi[$i]->id[$k] == substr($izraz[$j], 2)) {
                                                        if ($prvpat) {
                                                            echo '<tr> <td> </td> <td style="color: red"> <b>' . get_string('caution_By_removing_the_class_the_following_expressions_will_also_be_removed', 'ontology') . '</b> </td> </tr>';
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
                                        //$podredeni_klasi[$i]->id[$k]
                                    }
                                }

                                $expressions = $DB->get_records_sql('SELECT * FROM mdl_ontology_property_expression WHERE (status=2 OR status=3) AND course_modulesid=?', array($id));
                                $zabrishenje2 = "";
                                foreach ($expressions as $key => $value) {
                                    $izraz = explode(' ', $value->expression);
                                    $epominato = false;
                                    for ($j = 0; $j < count($izraz); $j++) {
                                        if (strlen($izraz[$j]) > 1) {
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'k') {//proverka dali e klasa
                                                for ($k = 0; $k < count($podredeni_klasi[$i]->id); $k++) {
                                                    if ($podredeni_klasi[$i]->id[$k] == substr($izraz[$j], 2)) {
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
                                            if ($izraz[$j][0] == '^' && $izraz[$j][1] == 'k') {//proverka dali e klasa
                                                for ($k = 0; $k < count($podredeni_klasi[$i]->id); $k++) {
                                                    if ($podredeni_klasi[$i]->id[$k] == substr($izraz[$j], 2)) {
                                                        if ($prvpat) {
                                                             echo '<tr> <td> </td> <td style="color: red"> <b> ' . get_string('caution_By_removing_the_individual_the_following_expressions_will_also_be_removed', 'ontology') . ': </b> </td> </tr>';
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

                                $idia = $podredeni_klasi[$i]->id[0];
                                for ($j = 1; $j < count($podredeni_klasi[$i]->id); $j++) {
                                    $idia = $idia . ' ' . $podredeni_klasi[$i]->id[$j];
                                }
                                ?>
                                <tr>
                                    <td colspan="3">
                                <?php
                                echo '<input type="hidden" id="klasi' . $i . '" value="' . $idia . '" > </input>';
                                echo '<input type="hidden" id="brishi' . $i . '" value="' . $zabrishenje . '" > </input>';
                                echo '<input type="hidden" id="brishi2' . $i . '" value="' . $zabrishenje2 . '" > </input>';
                                echo '<input type="hidden" id="brishi3' . $i . '" value="' . $zabrishenje3 . '" > </input>';
                                echo '<div align="center" >';
                                $superclassaccepted = $DB->get_records('ontology_class', array('id' => $podredeni_klasi[$i]->superclass, 'status' => '2'));
                                if (count($superclassaccepted) == 0)
                                    echo '<input type="button" id="add' . $i . '" value="' . get_string('add_to_ontology', 'ontology') . '" onclick="insertClass(this);" disabled="true"  style="width:500px;"/> <br />';
                                else
                                    echo '<input type="button" id="add' . $i . '" value="' . get_string('add_to_ontology', 'ontology') . '" onclick="insertClass(this);"  style="width:500px;"/><br />';
                                echo '<input type="button" id="klaster' . $i . '" value="' . get_string('add_class_to_merging_list', 'ontology') . '" onclick="klasterClass(this);"  style="width:500px;"/><br />';
                                echo '<input type="button" id="spoj' . $i . '" value="' . get_string('merge_it_with_another_already_accepted_class_from_ontology', 'ontology') . '" onclick="spojClass(this)"  style="width:500px;"/><br />';
                                echo '<input type="button" id="del' . $i . '" value="' . get_string('remove', 'ontology') . '" onclick="deleteClass(this);"  style="width:500px;"/></div>'
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
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center> <?php echo get_string('list_of_classes_for_merging', 'ontology'); ?></center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">
                            <?php
                            if ($klastertxt == "")
                                echo get_string('there_are_no_classes_in_the_list', 'ontology');
                            else {
                                $prethodna = "";
                                for ($i = 0; $i < count($klaster); $i++) {
                                    //$klasa=$DB->get_record('ontology_class',array('id'=>$klaster[$i]));
                                    //$klasa->name.'<br/>';
                                    if (get_name_of_class($klaster[$i]) != $prethodna)
                                        echo get_name_of_class($klaster[$i]) . '<br/>';
                                    $prethodna = get_name_of_class($klaster[$i]);
                                }
                                echo '<input type="button" value="' . get_string('edit_the_list', 'ontology') . '" onclick="KlasteriraniKlasi();"> </input>';
                            }
                            ?>
                    </div></div>

            </td>
    </table>
    <table ><tr><td>
                <div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest" style="width: 650px;">
                    <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                        <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>

                            <?php
                            echo get_string('update_existing_classes_in_ontology', 'ontology');
                            ?>
                            </center></span>
                    </div>
                    <div class="ui-dialog-content ui-widget-content">
                        <table align="center">

                            <?php
                            $ontology = $DB->get_record('modules', array('name' => 'ontology')); //id na modulot ontologija
                            $classes = $DB->get_records_sql('SELECT * FROM mdl_ontology_class WHERE status=2 AND course_modulesid IN (SELECT id FROM mdl_course_modules WHERE instance=? AND module=?)', array($ontologyid->instance, $ontology->id));
                            $i = 0;
                            foreach ($classes as $key => $value) {
                                $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'ontology_classid' => $key));
                                if (count($expressions) > 0) {
                                    echo '<tr style="border:1px solid #111; color: blue; background: #E8E8E8;"> <td colspan="2"> ';
                                    echo get_string('class_name', 'ontology') . ': <b> ' . get_name_of_class($key) . '</b> ' . get_string('superclass', 'ontology') . ': <b id="nadklasa' . $i . '">' . get_name_of_class($value->superclass) . '</b> </td> <td> </td> </tr>';

                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'ontology_classid' => $key, 'type' => '1'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_superclass_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($expressions) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td>  </tr>';
                                    else {
                                        foreach ($expressions as $key2 => $value2) {
                                            echo '<tr> <td width="20"> </td> <td> <input type="checkbox" name="updatek' . $i . '" id="updatek' . $i . '" value="' . $key2 . '" onClick="azuriranje_superklasa(' . $i . ');   " />  ';
                                            $pass = "N";
                                            if (is_numeric(substr($value2->expression, 2, -2))) {
                                                if (strcmp(substr($value2->expression, 0, 2), "^k") == 0) {
                                                    $k_id = substr($value2->expression, 2, -2);
                                                    $k_id_redica = $DB->get_records('ontology_class', array('id' => $k_id));
                                                    if (count($k_id_redica) > 0)
                                                        $pass = "Y";
                                                }
                                            }
                                            if (strcmp($pass, "Y") == 0)
                                                echo '<span id="name' . $key2 . '" title="' . $value2->expression . ' ">';
                                            else
                                                echo '<span id="name' . $key2 . '" title="     " >';
                                            get_expression_in_color($value2->expression);
                                            echo '</span>';
                                            echo '</td> <td>';
                                            //echo get_user_profile($podredeni_izrazi[$j]->userid[$k]).' ';
                                            $user = $DB->get_record('user', array('id' => $value2->userid), '*', MUST_EXIST);
                                            echo ' ' . $OUTPUT->user_picture($user, array('size' => 20));
                                            echo '</td></tr>';
                                        }
                                    }
                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'ontology_classid' => $key, 'type' => '2'));
                                    echo '<tr id="izrazi_za_ekvivalentni' . $i . '"> <td> </td> <td> <b> ' . get_string('new_equivalent_class_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
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

                                    $expressions = $DB->get_records('ontology_class_expression', array('status' => '3', 'ontology_classid' => $key, 'type' => '3'));
                                    echo '<tr> <td> </td> <td> <b> ' . get_string('new_disjoint_class_expressions', 'ontology') . ': </b> </td> <td> </td> </tr>';
                                    if (count($expressions) == 0)
                                        echo '<tr> <td> </td> <td> ' . get_string('there_are_no_new_expressions', 'ontology') . ' </td> <td> </td> </tr>';
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
                                    echo '<tr> <td colspan=3>';
                                    echo '<input type="button" id="updins' . $i . '" value="' . get_string('accept_the_changes', 'ontology') . '" onClick="updateClass(this);" />';
                                    echo '<input type="button" id="upddel' . $i . '" value="' . get_string('reject_all_changes', 'ontology') . '" onClick="updateDeleteClass(this);"/>';
                                    echo '<hr/> </td> </tr>';
                                    $i++;
                                }
                            }
                            if ($i == 0) {
                                echo '<tr> <td colspan=2> ' . get_string('there_are_no_updates_on_existing_classes', 'ontology') . ' </td> </tr>';
                            }
                            ?>
                        </table>

                    </div></div></td></tr></table>
    <table><tr><td>
<?php
echo '<input type="button" id="nazad" value="' . get_string('back', 'ontology') . '" onclick="nazad_pregleduvanje();" class="back"/>';
?>
            </td></tr></table>
</form>

<style>
    .latest { overflow:visible; position:static; }
</style>
<?php 
   // echo $OUTPUT->footer();