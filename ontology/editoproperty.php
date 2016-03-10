<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET['id'];
$moduleid = $_GET['cmid'];
$userid = $_GET['userid'];
$p = $DB->get_record('ontology_property_individual', array('id' => $id));
$atributi = substr('0000000' . $p->attributes, -7);
?>
<br /> <br />
<h4><?php echo get_string('Edit_oproperty', 'ontology') . ':'; ?>&nbsp;<?php echo $p->name; ?></h4>
<br />
<table>
    <tr>
        <td valign="top">
            <?php echo get_string('Choose_new_superproperty', 'ontology') ?>:<br />
            <?php object_property_hierarhy4("oproperties", "oproperty1", 0, $moduleid, $userid, false, $p->id);
            ?>
        </td>
        <td>
            <table>
                <tr>
                    <td>
                        <?php echo get_string('Name', 'ontology') ?>:
                    </td>
                    <td>
                        <input type="text" class="ui-widget ui-state-hover" id="ime" value="<?php echo $p->name; ?>" name="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo get_string('Description', 'ontology') ?>:
                    </td>
                    <td>
                        <input type="text" class="ui-widget ui-state-hover" id="opis" value="<?php echo $p->description; ?>" name="opis"/> <label title="erroropis" id="erroropis" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_description', 'ontology') ?></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($p->inverse == 0) {
                            ?>
                            <input type="checkbox" title="inverznachk" name="inverznachk" id="inverznachk" onchange="setInverzna();"/> <?php echo get_string('Inverse_property', 'ontology') ?>  <input type="text" class="ui-widget ui-state-hover" id="inverzna" name="inverzna" style="visibility: hidden;"/> 
                            <br />
                            <br />
                            <?php
                        } else {
                            $opi = $DB->get_record('ontology_property_individual', array('id' => $p->inverse));
                            ?>
                            <input type="checkbox" title="inverznachk" name="inverznachk" checked="true" id="inverznachk" onchange="setInverzna();"/> <?php echo get_string('Inverse_property', 'ontology') ?>  <input type="text" class="ui-widget ui-state-hover" id="inverzna" value="<?php echo $opi->name; ?>" name="inverzna"/>
                            <br />
                            <br />
    <?php
}
?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"> 
<?php echo get_string('Characteristics', 'ontology') ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
<?php
if ($atributi[0] == '1') {
    ?>
                            <input type="checkbox" title="funkcionalna" checked="true" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology'); ?>
                            <?php
                        } else {
                            ?>
                            <input type="checkbox" title="funkcionalna" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology'); ?>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($atributi[1] == '1') {
                            ?>
                            <input type="checkbox" title="inverznafunkcionalna" checked="true" name="inverznafunkcionalna" id="inverznafunkcionalna"/> <?php echo get_string('Inverse_Functional', 'ontology'); ?>
                            <?php
                        } else {
                            ?> 
                            <input type="checkbox" title="inverznafunkcionalna" name="inverznafunkcionalna" id="inverznafunkcionalna"/> <?php echo get_string('Inverse_Functional', 'ontology'); ?>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>

                    <td colspan="2">
                        <?php
                        if ($atributi[2] == '1') {
                            ?>
                            <input type="checkbox" title="tranzitivna" checked="true" name="tranzitivna" id="tranzitivna" /> <?php echo get_string('Transitive', 'ontology'); ?>
    <?php
} else {
    ?> 
                            <input type="checkbox" title="tranzitivna" name="tranzitivna" id="tranzitivna" /> <?php echo get_string('Transitive', 'ontology'); ?>
                            <?php
                        }
                        ?> 
                    </td>
                </tr>
                <tr>  

                    <td colspan="2">
                        <?php
                        if ($atributi[3] == '1') {
                            ?>
                            <input type="checkbox" title="simetricna" checked="true" name="simetricna" id="simetricna"/> <?php echo get_string('Symetric', 'ontology'); ?>
                            <?php
                        } else {
                            ?> 
                            <input type="checkbox" title="simetricna" name="simetricna" id="simetricna"/> <?php echo get_string('Symetric', 'ontology'); ?>
    <?php
}
?>

                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($atributi[4] == '1') {
                            ?>
                            <input type="checkbox" title="asimetricna" checked="true" name="asimetricna" id="asimetricna"/> <?php echo get_string('Asymetric', 'ontology'); ?>
                            <?php
                        } else {
                            ?> 
                            <input type="checkbox" title="asimetricna" name="asimetricna" id="asimetricna"/> <?php echo get_string('Asymetric', 'ontology'); ?>
                            <?php
                        }
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php
                        if ($atributi[5] == '1') {
                            ?>
                            <input type="checkbox" title="refleksivna" checked="true" name="refleksivna" id="refleksivna"/> <?php echo get_string('Reflexive', 'ontology'); ?>
                            <?php
                        } else {
                            ?> 
                            <input type="checkbox" title="refleksivna" name="refleksivna" id="refleksivna"/> <?php echo get_string('Reflexive', 'ontology'); ?>
                            <?php
                        }
                        ?>  
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
<?php
if ($atributi[6] == '1') {
    ?>
                            <input type="checkbox" title="irefleksivna" checked="true" name="irefleksivna" id="irefleksivna"/> <?php echo get_string('Ireflexive', 'ontology'); ?>
                            <?php
                        } else {
                            ?> 
                            <input type="checkbox" title="irefleksivna" name="irefleksivna" id="irefleksivna"/> <?php echo get_string('Ireflexive', 'ontology'); ?>
                            <?php
                        }
                        ?>  
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="button" title="ok" name="ok" value="OK" onclick="editoproperty()"/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<input type="hidden" name="opropertyid1" value="<?php echo $p->superproperty; ?>" id="opropertyid1" />

                        <?php
                        require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
                        $tip = $_GET["tip"];
                        if ($tip == '1') {
                            require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
                            require_once(dirname(__FILE__) . '/lib.php');
                            global $DB;
                            $id = $_GET["moduleid"];
                            $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
                            $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
                            $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
                            require_login($course, true, $cm);
                            add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
                            $id = $_GET["id"];

                            $property = $DB->get_record("ontology_property_individual", array("id" => $id));

                            $property->superproperty = $_GET["superproperty"];

                            $property->description = $_GET["description"];

                            $property->attributes = $_GET["attributes"];
                            if (is_Teacher())
                                $property->status = '2';
                            else
                                $property->status = '1';
                            $property->points = '0';
                            $property->userid = $_GET["userid"];
                            $property->course_modulesid = $_GET["moduleid"];
                            if ($_GET["inverse"] != "" && $property->inverse != 0) {
                                $name = $_GET['inverse'];
                                for ($i = 0; $i < strlen($name); $i++) {
                                    if ($name[$i] == ' ') {
                                        $name[$i] = '_';
                                    }
                                }
                                $inverse = $DB->get_record('ontology_property_individual', array("id" => $property->inverse));
                                $inverse->name = $name;
                                $DB->update_record('ontology_property_individual', $inverse);
                            } else {
                                if ($_GET["inverse"] != "" && $property->inverse == 0) {
                                    $name = $_GET['inverse'];
                                    for ($i = 0; $i < strlen($name); $i++) {
                                        if ($name[$i] == ' ') {
                                            $name[$i] = '_';
                                        }
                                    }
                                    $property->name = $name;
                                    $property->inverse = '0';
                                    $DB->insert_record('ontology_property_individual', $property);
                                    $inverse = $DB->get_record_sql('SELECT * FROM mdl_ontology_property_individual WHERE id IN (SELECT max(id) FROM mdl_ontology_property_individual);');
                                    $property->inverse = $inverse->id;
                                    $inverse->inverse = $property->id;
                                    $DB->update_record('ontology_property_individual', $inverse);
                                } else {
                                    if ($property->inverse != 0)
                                        $DB->delete_records('ontology_property_individual', array("id" => $property->inverse));
                                    $property->inverse = '0';
                                }
                            }
                            $name = $_GET['name'];
                            for ($i = 0; $i < strlen($name); $i++) {
                                if ($name[$i] == ' ') {
                                    $name[$i] = '_';
                                }
                            }
                            $property->name = $name;
                            $DB->update_record('ontology_property_individual', $property);
                        }