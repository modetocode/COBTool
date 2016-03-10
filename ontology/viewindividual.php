<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET["id"];
$moduleid = $_GET["moduleid"];
$tip = $_GET["tip"];
$red = $_GET["red"];
$courseid = $_GET["courseid"];

$individual = $DB->get_record("ontology_individual", array("id" => $id));
$superclass = $DB->get_records("ontology_individual_expression", array("ontology_individualid" => $id, "status" => "2"));
$superclass2 = $DB->get_records("ontology_individual_expression", array("ontology_individualid" => $id, "status" => "1", "userid" => $USER->id));
$oproperties = $DB->get_records("ontology_individual_property_individual", array("ontology_individualid" => $id, "status" => 2));
$oproperties2 = $DB->get_records("ontology_individual_property_individual", array("ontology_individualid" => $id, "status" => "1", "userid" => $USER->id));
$dproperties = $DB->get_records("ontology_individual_property_data", array("ontology_individualid" => $id, "status" => 2));
$dproperties2 = $DB->get_records("ontology_individual_property_data", array("ontology_individualid" => $id, "status" => "1", "userid" => $USER->id));
?>

<div id="Region">
    <?php
    $isTeacher = false;
    $rAssign = $DB->get_records('role_assignments', array('userid' => $USER->id));
    foreach ($rAssign as $tmp) {
        $cont = $DB->get_records('context', array('id' => $tmp->contextid, 'instanceid' => $courseid));
        if ($cont != null)
            if ($tmp->roleid == 3) {
                $isTeacher = true;
            }
    }
    if ($individual->status == 1) {
        if ($tip == '4' && $red == $individual->id) {
            $DB->delete_records("ontology_individual_expression", array("ontology_individualid" => $individual->id));
            $DB->delete_records("ontology_individual_property_individual", array("ontology_individualid" => $individual->id));
            $DB->delete_records("ontology_individual_property_data", array("ontology_individualid" => $individual->id));
            $DB->delete_records("ontology_individual", array("id" => $individual->id));
        } else {
            ?>
            <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> &nbsp; <?php echo '<img src="Delete-icon.png" title="' . get_string('delete_the_individual', 'ontology') . '" style="width: 10px; height: 10px; cursor: hand;" onClick="brisi(4,' . $individual->id . ');"/>'; ?> <br />
            <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
            <?php
        }
    }
    else {
        ?>
        <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
        <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
        <?php
    }
    ?>
<?php
if ($individual->status == 1 || $isTeacher) {
    ?>
        <input type="hidden" id="iseditable" value="1"/>
        <?php
    } else {
        ?>
        <input type="hidden" id="iseditable" value="0"/>
        <?php
    }
    ?>
    <hr />

    <b> <?php echo get_string('Instance_class', 'ontology'); ?>: </b><br /> 
    <?php
    if (count($superclass) == 0 && count($superclass2) == 0) {
        echo get_string('No_classes', 'ontology');
        echo "<br />";
    } else {
        foreach ($superclass as $key => $value) {
             if ($tip == '1' && $value->id == $red) {
                $DB->delete_records("ontology_individual_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($superclass2 as $key => $value) {
            if ($tip == '1' && $value->id == $red) {
                $DB->delete_records("ontology_individual_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    ?>
    <br />
    <input type="button" id="dodavanje1" style="width: 270px;" value="<?php echo get_string('Add_new_class', 'ontology'); ?>" onclick="execute_ajax1();"/>
    <input type="button" id="brishi1" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/>
    <div id="Region1">
        <input type="hidden" id="steklista1" />
    </div>
    <input type="hidden" id="tekstlista1" />
    <input type="hidden" id="inputlista1" />

    <br />
    <table>
        <tr>
            <td valign="top">      <b> <?php echo get_string('Oproperties', 'ontology') . ':'; ?> </b> <br />
    <?php
    if (count($oproperties) == 0 && count($oproperties2) == 0) {
        echo get_string('No_oproperties', 'ontology');
        echo "<br />";
    } else {
        foreach ($oproperties as $key => $value) {
            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_individual_property_individual", array("id" => $value->id));
            } else {
                echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($oproperties2 as $key => $value) {

            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_individual_property_individual", array("id" => $value->id));
            } else {
                echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    ?>
                <br /> 
                <input type="button" id="dodavanjeosvojstvo" style="width: 270px;" value="<?php echo get_string('Add_new_oproperty', 'ontology'); ?>" onclick="dodadi_osvojstvo();"/> 
                <input type="button" id="brishi2" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/><br />
                <?php
                echo object_property_hierarhy('listaosvojstvo', 'osvojstvo', 0, $moduleid, $USER->id, 1);
                echo individual_hierarhy('listaindividui', 'individual2', 0, $moduleid, $USER->id, 1);
                ?>
                <br />
                <input type="hidden" id="osvojstvoid"  value=""/>
                <input type="hidden" id="individualid2" value=""/>


            </td>
            <td valign="top">
                <b> <?php echo get_string('Dproperties', 'ontology') . ':'; ?> </b> <br />
                <?php
                if (count($dproperties) == 0 && count($dproperties2) == 0) {
                    echo get_string('No_dproperties', 'ontology');
                    echo "<br />";
                } else {
                    foreach ($dproperties as $key => $value) {
                        if ($tip == '3' && $value->id == $red) {
                            $DB->delete_records("ontology_individual_property_data", array("id" => $value->id));
                        } else {
                            echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                            if ($isTeacher)
                                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                            echo "<br/>";
                        }
                    }
                    foreach ($dproperties2 as $key => $value) {
                        if ($tip == '3' && $value->id == $red) {
                            $DB->delete_records("ontology_individual_property_data", array("id" => $value->id));
                        } else {
                            echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                            echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                            echo "<br/>";
                        }
                    }
                }
                ?>
                <br /> 
                <input type="button" id="dodavanjedsvojstvo" style="width: 270px;" value="<?php echo get_string('Add_new_dproperty', 'ontology'); ?>" onclick="dodadi_dsvojstvo();"/> 
                <input type="button" id="brishi3" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/><br />
                <?php echo data_property_hierarhy('listadsvojstvo', 'dsvojstvo', 0, $moduleid, $USER->id, 1);
                ?>
                <input type="text" id="data" value="" style="visibility: hidden;"/>
                <input type="button" id="btndata" value="<?php echo get_string('OK', 'ontology') ?>" style="visibility: hidden;" onclick="data_changed();"/>
                <br />
                <input type="hidden" id="dsvojstvoid"  value="" />
                <input type="hidden" id="individualid3" value="" />
            </td>
        </tr>
    </table> 



</div>


