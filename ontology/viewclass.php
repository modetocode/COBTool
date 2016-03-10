<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
global $USER;
$id = $_GET["id"];
$tip = $_GET["tip"];
$red = $_GET["red"];
$courseid = $_GET["courseid"];
$class = $DB->get_record("ontology_class", array("id" => $id));
$superclass = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "1", "status" => "2"));
$superclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "1", "status" => "1", "userid" => $USER->id));
$equivalentclass = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "2", "status" => "2"));
$equivalentclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "2", "status" => "1", "userid" => $USER->id));
$disjointclass = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "3", "status" => "2"));
$disjointclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "3", "status" => "1", "userid" => $USER->id));
?>

<div id="Region"  class="ui-corner-all">

    <?php
    $isTeacher = false;
    $rAssign = $DB->get_records('role_assignments', array('userid' => $USER->id));
    foreach ($rAssign as $tmp) {
        $cont = $DB->get_records('context', array('id' => $tmp->contextid, 'instanceid' => $courseid));
        if ($cont != null)
            if ($tmp->roleid == 3)
                $isTeacher = true;
    }
    if ($class->status == 1) {
        if ($tip == '5' && $class->id == $red) {
            $DB->delete_records("ontology_class_expression", array("ontology_classid" => $class->id));
            $DB->delete_records("ontology_class", array("id" => $class->id));
        } else {
            ?>
            <b> <?php echo get_string('Class_name', 'ontology'); ?> : </b> <span style="color: #FF9D00;"><?php echo $class->name; ?> </span> &nbsp;
            <?php
            if (count($DB->get_records("ontology_class", array("superclass" => $class->id))) == 0)
                echo '<img src="Delete-icon.png" title="' . get_string('delete_the_class', 'ontology') . '" style="width: 10px; height: 10px; cursor:hand;" onclick="brisi(5,' . $class->id . ');"/>';
            ?> <br />

            <b> <?php echo get_string('Class_description', 'ontology'); ?> </b>  <?php if ($class->description == "") echo get_string('No_description', 'ontology'); else echo $class->description; ?> <br />
            <?php
        }
    }
    else {
        ?>
        <b> <?php echo get_string('Class_name', 'ontology'); ?> : </b> <span style="color: #FF9D00;"><?php echo $class->name; ?> </span> 
        <br />
        <b> <?php echo get_string('Class_description', 'ontology'); ?> : </b>  <?php if ($class->description == "") echo get_string('No_description', 'ontology'); else echo $class->description; ?> <br />

        <?php
    }
    if ($class->status == 1 || ($isTeacher && $class->name != "Основна")) {
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
    <b> <?php echo get_string('Superclasses', 'ontology'); ?>: </b> <br /> 
    <?php
    if ($class->name != "Основна")
        echo  get_name_of_class($class->superclass).'<br/>';
    if (count($superclass) == 0 && count($superclass2) == 0) {
        echo get_string('No_superclasses', 'ontology');
        echo "<br />";
    } else {
        foreach ($superclass as $key => $value) {
            if ($tip == '1' && $value->id == $red) 
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
            else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                     echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($superclass2 as $key => $value) {
            if ($tip == '1' && $value->id == $red) {
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($class->name != "Основна") {
        ?>
        <br />
        <input type="button" id="dodavanje1" style="width: 270px;" value="<?php echo get_string('Add_new_superclass', 'ontology'); ?>" onclick="execute_ajax1();"/>
        <input type="button" id="brishi1" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/>
        <div id="Region1">
            <input type="hidden" id="steklista1" />
        </div>
        <input type="hidden" id="tekstlista1" />
        <input type="hidden" id="inputlista1" />

        <?php
    }
    ?>  
    <br />
    <span> <b> <?php echo get_string('Equivalent_Classes', 'ontology') . ':'; ?> </b> </span> <br />
    <?php
    if (count($equivalentclass) == 0 && count($equivalentclass2) == 0) {
        echo get_string('No_equivalent_classes', 'ontology');
        echo "<br />";
    } else {
        foreach ($equivalentclass as $key => $value) {
            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($equivalentclass2 as $key => $value) {
            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($class->name != "Основна") {
        ?>
        <br />
        <input  type="button" id="dodavanje2" style="width: 270px;" value="<?php echo get_string('Add_new_equivalent_class', 'ontology'); ?>" onclick="execute_ajax2();"/>
        <input type="button" id="brishi2" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/>
        <div id="Region2">
            <input type="hidden" id="steklista2" />
        </div>
        <input type="hidden" id="tekstlista2" />
        <input type="hidden" id="inputlista2" />
        <?php
    }
    ?>      

    <br /> <b> <?php echo get_string('Disjoint_Classes', 'ontology') . ':'; ?> </b> <br />
    <?php
    if (count($disjointclass) == 0 && count($disjointclass2) == 0) {
        echo get_string('No_disjoint_classes', 'ontology');
        echo "<br />";
    } else {
        foreach ($disjointclass as $key => $value) {
             if ($tip == '3' && $value->id == $red) {
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
             } else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                echo "<br/>";
             }
        }
        foreach ($disjointclass2 as $key => $value) {
            if ($tip == '3' && $value->id == $red) {
                $DB->delete_records("ontology_class_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($class->name != "Основна") {
        ?>
        <br />
        <input type="button" id="dodavanje3" style="width: 270px;" value="<?php echo get_string('Add_new_disjoint_class', 'ontology'); ?>" onclick="execute_ajax3();"/>
        <input type="button" id="brishi3" value="<?php echo get_string('Cancel', 'ontology'); ?>" onclick="izbrishi();" style="visibility: hidden;"/>
        <div id="Region3">
            <input type="hidden" id="steklista3" />
        </div>
        <input type="hidden" id="tekstlista3" />
        <input type="hidden" id="inputlista3" />
        <?php
    }
    ?>

</div>


