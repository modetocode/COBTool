<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
?>

<br /> <br /> <b><?php echo get_string('Adding_new_oproperty', 'ontology') . ':'; ?></b>
<table>
    <tr>
        <td>
            <?php echo get_string('Name', 'ontology') ?>:
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" id="ime" name="ime"/> <label title="errorime" id="errorime" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_name', 'ontology') ?></label>
        </td>
    </tr>
    <tr>
        <td>
            <?php echo get_string('Description', 'ontology') ?>:
        </td>
        <td>
            <input class=" ui-widget ui-state-hover" type="text" id="opis" name="opis"/> <label title="erroropis" id="erroropis" style="color: red; visibility: hidden;" > *<?php echo get_string('Enter_description', 'ontology') ?></label>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="inverznachk" name="inverznachk" id="inverznachk" onchange="setInverzna();"/> <?php echo get_string('Inverse_property', 'ontology') ?> <input type="text" id="inverzna" name="inverzna" style="visibility: hidden;"/> 
            <br />
            <br />
        </td>
    </tr>
    <tr>
        <td colspan="1"> 
            <?php echo get_string('Characteristics', 'ontology') . ":" ?>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="funkcionalna" name="funkcionalna" id="funkcionalna"/> <?php echo get_string('Functional', 'ontology'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="inverznafunkcionalna" name="inverznafunkcionalna" id="inverznafunkcionalna"/> <?php echo get_string('Inverse_Functional', 'ontology'); ?>
        </td>
    </tr>
    <tr>

        <td colspan="2">
            <input type="checkbox" title="tranzitivna" name="tranzitivna" id="tranzitivna" /> <?php echo get_string('Transitive', 'ontology'); ?>
        </td>
    </tr>
    <tr>  

        <td colspan="2">
            <input type="checkbox" title="simetricna" name="simetricna" id="simetricna"/> <?php echo get_string('Symetric', 'ontology'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="asimetricna" name="asimetricna" id="asimetricna"/> <?php echo get_string('Asymetric', 'ontology'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="refleksivna" name="refleksivna" id="refleksivna"/> <?php echo get_string('Reflexive', 'ontology'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="checkbox" title="irefleksivna" name="irefleksivna" id="irefleksivna"/> <?php echo get_string('Ireflexive', 'ontology'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="button" title="ok" name="ok" value="OK" onclick="insertoproperty()"/>
        </td>
    </tr>
</table>

<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
$tip = $_GET["tip"];
if ($tip == '1') {
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);


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
    if ($_GET["inverse"] != "") {
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
        $inverse->inverse = ($inverse->id) + 1;
        $DB->update_record('ontology_property_individual', $inverse);
    } else {
        $property->inverse = '0';
    }
    $name = $_GET['name'];
    for ($i = 0; $i < strlen($name); $i++) {
        if ($name[$i] == ' ') {
            $name[$i] = '_';
        }
    }
    $property->name = $name;
    $DB->insert_record('ontology_property_individual', $property);
} else
if ($tip == '2') {//ekvivalentni svojstva
    require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
    require_once(dirname(__FILE__) . '/lib.php');
    global $DB;
    $id = $_GET["moduleid"];
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
    require_login($course, true, $cm);
    add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);
    $equproperty->ontology_propertyid = $_GET["propertyid"];
    $equproperty->ontology_propertyid2 = $_GET["propertyid2"];
    $equproperty->type = '1';
    if (is_Teacher())
        $equproperty->status = '2';
    else
        $equproperty->status = '1';
    $equproperty->points = '0';
    $equproperty->userid = $_GET["userid"];
    $equproperty->course_modulesid = $_GET["moduleid"];
    $DB->insert_record('ontology_property_equivalent', $equproperty);
}
else {//disjunktni svojstva
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
        $disproperty->type = '1';
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
