<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET["id"];
$moduleid = $_GET['cmid'];
$userid = $_GET['userid'];
$property = $DB->get_record('ontology_property_data', array('id' => $id));
?>
<br/><br/>
<h4><?php echo get_string('Edit_dproperty', 'ontology') . ':'; ?>&nbsp;<?php echo $property->name; ?></h4>
<br />
<table>
    <tr>
        <td valign="top">
            <?php echo get_string('Choose_new_superproperty', 'ontology') ?>:<br />
            <?php data_property_hierarhy4("dproperties", "dproperty1", 0, $moduleid, $userid, false, $property->id);
            ?>
        </td>
        <td>
            <table>
                <tr>
                    <td>
                        <?php echo get_string('Name', 'ontology') ?>:
                    </td>
                    <td>
                        <input type="text" class="ui-widget ui-state-hover" id="ime" value="<?php echo $property->name; ?>" name="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo get_string('Description', 'ontology') ?>:
                    </td>
                    <td>
                        <input type="text" class="ui-widget ui-state-hover" id="opis" value="<?php echo $property->description; ?>" name="opis"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo get_string('Property_range', 'ontology') ?>:
                    </td>
                    <td>
                        <?php
                        $v[] = "boolean";
                        $v[] = "byte";
                        $v[] = "dateTime";
                        $v[] = "decimal";
                        $v[] = "double";
                        $v[] = "float";
                        $v[] = "integer";
                        $v[] = "long";
                        $v[] = "real";
                        $v[] = "short";
                        $v[] = "string";
                        $v[] = "unsignedByte";
                        $v[] = "unsignedInt";
                        $v[] = "unsignedLong";
                        $v[] = "unsignedShort";
                        ?>
                        <select id="rang" name="rang">
                            <?php
                            for ($i = 0; $i < count($v); $i++) {
                                if ($property->rang == $v[$i]) {
                                    ?>
                                    <option value="<?php echo $v[$i]; ?>" selected="true" id="<?php echo $v[$i]; ?>" title="<?php echo $v[$i]; ?>"><?php echo $v[$i]; ?></option>
                                    <?php
                                } else {
                                    ?>
                                    <option value="<?php echo $v[$i]; ?>" id="<?php echo $v[$i]; ?>" title="<?php echo $v[$i]; ?>"><?php echo $v[$i]; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <?php echo get_string('Characteristics', 'ontology') ?>: <br /> <br />
                        <?php
                        if ($property->attributes == 1) {
                            ?>
                            <input type="checkbox" title="funkcionalna" checked="true" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology') ?>
                            <?php
                        } else {
                            ?>
                            <input type="checkbox" title="funkcionalna" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology') ?>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input type="button" title="ok" name="ok" value="OK" onclick="editdproperty()"/>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<input type="hidden" name="dpropertyid1" value="<?php echo $property->superproperty; ?>" id="dpropertyid1" />

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
    global $DB;
    $id = $_GET["id"];
    $property = $DB->get_record('ontology_property_data', array('id' => $id));
    $property->superproperty = $_GET["superproperty"];
    $name = $_GET['name'];
    for ($i = 0; $i < strlen($name); $i++) {
        if ($name[$i] == ' ') {
            $name[$i] = '_';
        }
    }
    $property->name = $name;
    $property->description = $_GET["description"];
    //$property->range='range';
    $property->rang = $_GET["range"];
    $property->attributes = $_GET["attributes"];
    if (is_Teacher())
        $property->status = '2';
    else
        $property->status = '1';
    $property->points = '0';
    $property->userid = $_GET["userid"];
    $property->course_modulesid = $_GET["moduleid"];
    $DB->update_record('ontology_property_data', $property);
}
else
if ($tip == '2') {//ekvivalentni klasi
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    global $DB;
    $equproperty->ontology_propertyid = $_GET["propertyid"];
    $equproperty->ontology_propertyid2 = $_GET["propertyid2"];
    $equproperty->type = '2';
    if (is_Teacher())
        $equproperty->status = '2';
    else
        $equproperty->status = '1';
    $equproperty->points = '0';
    $equproperty->userid = $_GET["userid"];
    $equproperty->course_modulesid = $_GET["moduleid"];
    $DB->insert_record('ontology_property_equivalent', $equproperty);
}
else {//disjunktni klasi
    if ($tip == '3') {
        require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
        require_once(dirname(__FILE__) . '/lib.php');
        global $DB;
        $id = $_GET["moduleid"];
        $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
        $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
        require_login($course, true, $cm);
        add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
        global $DB;
        $disproperty->ontology_propertyid = $_GET["propertyid"];
        $disproperty->ontology_propertyid2 = $_GET["propertyid2"];
        $disproperty->type = '2';
        if (is_Teacher())
            $disproperty->status = '2';
        else
            $disproperty->status = '1';
        $disproperty->points = '0';
        $disproperty->userid = $_GET["userid"];
        $disproperty->course_modulesid = $_GET["moduleid"];
        $DB->insert_record('ontology_property_disjoint', $disproperty);
    }
}
?>