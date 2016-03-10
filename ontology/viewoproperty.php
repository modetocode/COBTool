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
$karak[] = get_string('Functional', 'ontology');
$karak[] = get_string('Inverse_Functional', 'ontology');
$karak[] = get_string('Transitive', 'ontology');
$karak[] = get_string('Symetric', 'ontology');
$karak[] = get_string('Asymetric', 'ontology');
$karak[] = get_string('Reflexive', 'ontology');
$karak[] = get_string('Ireflexive', 'ontology');


$property = $DB->get_record("ontology_property_individual", array("id" => $id));
$domain = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$domain2 = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "1", "status" => "1", "userid" => $USER->id));
$rang = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "2", "status" => "2"));
$rang2 = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "2", "status" => "1", "userid" => $USER->id));
$equproperties = $DB->get_records("ontology_property_equivalent", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$equproperties2 = $DB->get_records("ontology_property_equivalent", array("ontology_propertyid" => $id, "type" => "1", "status" => "1", "userid" => $USER->id));
$disproperties = $DB->get_records("ontology_property_disjoint", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$disproperties2 = $DB->get_records("ontology_property_disjoint", array("ontology_propertyid" => $id, "type" => "1", "status" => "1", "userid" => $USER->id));
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
    if ($property->status == 1) {
        if ($tip == '5' && $red == $property->id) {
            $DB->delete_records("ontology_property_expression", array("ontology_propertyid" => $property->id));
            $DB->delete_records("ontology_property_equivalent", array("ontology_propertyid" => $property->id));
            $DB->delete_records("ontology_property_disjoint", array("ontology_propertyid" => $property->id));
            $DB->delete_records("ontology_property_individual", array("id" => $property->id));
        } else {
            ?>
            <b> <?php echo get_string('Oproperty_name', 'ontology') . ':'; ?> </b> <span style="color: #006FFF;"><?php echo $property->name; ?> </span>&nbsp;
            <?php
            if (count($DB->get_records("ontology_property_individual", array("superproperty" => $property->id))) == 0)
                echo '<img src="Delete-icon.png" title="' . get_string('delete_the_property', 'ontology') . '" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(5,' . $property->id . ');"/>';
            ?> <br />
            <b> <?php echo get_string('Oproperty_description', 'ontology') . ':'; ?> </b>  <?php if ($property->description == "") echo get_string('No_description', 'ontology'); else echo $property->description; ?> <br />
            <b> <?php echo get_string('Inverse_property', 'ontology') . ':'; ?> </b> <?php if ($property->inverse == '0') echo get_string('No_inverse_property', 'ontology'); else echo get_name_of_oproperty($property->inverse); ?> <br />
            <b> <?php echo get_string('Characteristics', 'ontology') . ':'; ?> </b> 
            <?php
            for ($i = 0; $i < strlen($property->attributes); $i++)
                if ($property->attributes[$i] == '1') {
                    echo $karak[$i] . '; ';
                }
            if ($property->attributes == '0')
                echo get_string('No_characteristics', 'ontology');
        }
    }
    else {
        ?>
        <b> <?php echo get_string('Oproperty_name', 'ontology') . ':'; ?> </b> <span style="color: #006FFF;"><?php echo $property->name; ?> </span> <br />
        <b> <?php echo get_string('Oproperty_description', 'ontology') . ':'; ?> </b>  <?php if ($property->description == "") echo get_string('No_description', 'ontology'); else echo $property->description; ?> <br />
        <b> <?php echo get_string('Inverse_property', 'ontology') . ':'; ?> </b> <?php if ($property->inverse == '0') echo get_string('No_inverse_property', 'ontology'); else echo get_name_of_oproperty($property->inverse); ?> <br />
        <b> <?php echo get_string('Characteristics', 'ontology') . ':'; ?> </b> 
        <?php
        for ($i = 0; $i < strlen($property->attributes); $i++)
            if ($property->attributes[$i] == '1') {
                echo $karak[$i] . '; ';
            }
        if ($property->attributes == '0')
            echo get_string('No_characteristics', 'ontology');
    }
    ?>
    <br />
    <?php
   
    if ($property->status == 1 || ($isTeacher && $property->name != 'Основно')) {
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

    <b> <?php echo get_string('Property_domain', 'ontology') . ':'; ?></b><br /> 
    <?php
    if (count($domain) == 0 && count($domain2) == 0) {
        echo get_string('No_domains', 'ontology');
        echo "<br />";
    } else {
        foreach ($domain as $key => $value) {
            if ($tip == '1' && $value->id == $red) {
                $DB->delete_records("ontology_property_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($domain2 as $key => $value) {
            if ($tip == '1' && $value->id == $red) {
                $DB->delete_records("ontology_property_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(1,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($property->name != 'Основно') {
        ?>
        <br />
        <input type="button" id="dodavanje1" style="width: 160px;" value="<?php echo get_string('Add_new_domain', 'ontology') ?>" onclick="execute_ajax1();"/>
        <input type="button" id="brishi1" value="<?php echo get_string('Cancel', 'ontology') ?>" onclick="izbrishi();" style="visibility: hidden;"/>
        <div id="Region1">
            <input type="hidden" id="steklista1" />
        </div>
        <input type="hidden" id="tekstlista1" />
        <input type="hidden" id="inputlista1" />
        <?php
    }
    ?>
    <br /> <b> <?php echo get_string('Property_range', 'ontology') . ':'; ?> </b><br /> 
    <?php
    if (count($rang) == 0 && count($rang2) == 0) {
        echo get_string('No_range', 'ontology');
        echo "<br />";
    } else {
        foreach ($rang as $key => $value) {
            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_property_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($rang2 as $key => $value) {
            if ($tip == '2' && $value->id == $red) {
                $DB->delete_records("ontology_property_expression", array("id" => $value->id));
            } else {
                get_expression_in_color($value->expression);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(2,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($property->name != 'Основно') {
        ?>
        <br />
        <input type="button" id="dodavanje2" style="width: 160px;" value="<?php echo get_string('Add_new_range', 'ontology') ?>" onclick="execute_ajax2();"/>
        <input type="button" id="brishi4" value="<?php echo get_string('Cancel', 'ontology') ?>" onclick="izbrishi();" style="visibility: hidden;"/>
        <div id="Region2">
            <input type="hidden" id="steklista2" />
        </div>
        <input type="hidden" id="tekstlista2" />
        <input type="hidden" id="inputlista2" />
        <?php
    }
    ?>
    <br />
    <table>
        <tr>
            <td valign="top">      
                <b> <?php echo get_string('Equivalent_Properties', 'ontology') . ':'; ?> </b> <br />
    <?php
    if (count($equproperties) == 0 && count($equproperties2) == 0) {
        echo get_string('No_equivalent_properties', 'ontology');
        echo "<br />";
    } else {
        foreach ($equproperties as $key => $value) {
            if ($tip == '3' && $value->id == $red) {
                $DB->delete_records("ontology_property_equivalent", array("id" => $value->id));
            } else {
                echo get_name_of_oproperty($value->ontology_propertyid2);
                if ($isTeacher)
                    echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
        foreach ($equproperties2 as $key => $value) {
            if ($tip == '3' && $value->id == $red) {
                $DB->delete_records("ontology_property_equivalent", array("id" => $value->id));
            } else {
                echo get_name_of_oproperty($value->ontology_propertyid2);
                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onClick="brisi(3,' . $value->id . ');"/>';
                echo "<br/>";
            }
        }
    }
    if ($property->name != 'Основно') {
        ?>
                    <br /> 
                    <input type="button" id="dodavanjeequsvojstvo" style="width: 290px;" value="<?php echo get_string('Add_new_equivalent_property', 'ontology') ?>" onclick="dodadi_equsvojstvo();"/> <br/>
    <?php echo object_property_hierarhy('listaequsvojstvo', 'equsvojstvo', 0, $moduleid, $USER->id, 1);
    ?>
                    <br />
                    <input type="button" id="brishi2" value="<?php echo get_string('Cancel', 'ontology') ?>" onclick="izbrishi();" style="visibility: hidden;"/><br />
                    <input type="hidden" id="equsvojstvoid"  value="" />
                    <?php
                }
                ?>


            </td >
            <td valign="top">
                <b> <?php echo get_string('Disjoint_Properties', 'ontology') . ':'; ?> </b> <br />
                <?php
                if (count($disproperties) == 0 && count($disproperties2) == 0) {
                    echo get_string('No_disjoint_properties', 'ontology');
                    echo "<br />";
                } else {
                    foreach ($disproperties as $key => $value) {
                        if ($tip == '4' && $value->id == $red) {
                            $DB->delete_records("ontology_property_disjoint", array("id" => $value->id));
                        } else {
                            echo get_name_of_oproperty($value->ontology_propertyid2);
                            if ($isTeacher)
                                echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onclick="brisi(4,' . $value->id . ');"/>';
                            echo "<br/>";
                        }
                    }
                    foreach ($disproperties2 as $key => $value) {
                        if ($tip == '4' && $value->id == $red) {
                            $DB->delete_records("ontology_property_disjoint", array("id" => $value->id));
                        } else {
                            echo get_name_of_oproperty($value->ontology_propertyid2);
                            echo ' <img src="Delete-icon.png" style="width: 10px; height: 10px; cursor:hand;" onclick="brisi(4,' . $value->id . ');"/>';
                            echo "<br/>";
                        }
                    }
                }
                if ($property->name != 'Основно') {
                    ?>
                    <br /> 
                    <input type="button" id="dodavanjedissvojstvo" style="width: 290px;" value="<?php echo get_string('Add_new_disjoint_property', 'ontology') ?>" onclick="dodadi_dissvojstvo();"/>  <br />
                    <?php echo object_property_hierarhy('listadissvojstvo', 'dissvojstvo', 0, $moduleid, $USER->id, 1);
                    ?>
                    <br />
                    <input type="button" id="brishi3" value="<?php echo get_string('Cancel', 'ontology') ?>" onclick="izbrishi();" style="visibility: hidden;"/><br />
                    <input type="hidden" id="dissvojstvoid"  value="" />
                    <?php
                }
                ?>
            </td>
        </tr>
    </table> 


</div>


