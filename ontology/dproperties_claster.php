<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$ids = $_GET['ids'];
global $DB;
global $USER;
$uid = $USER->id;
//echo 'do ovde3 </br>';
//echo $ids."</br>";
$raw;
$names = array();
$names_rank = array();
$descriptions = array();
$descriptions_rank = array();
$superproperty = array();
$superproperty_rank = array();
$prop_of_prop = array();
$prop_of_prop_rank = array();
$rang = array();
$rang_rank = array();
$rlen = 0;
$dlen = 0;
$slen = 0;
$sslen = 0;
$sttl = 0;
$maxDescLen = 0;
$cmid = $_GET['ontid'];

foreach (explode(' ', $ids) as $tmp) {
//    echo '  klasa so id:'.$tmp;
    $raw = $DB->get_record('ontology_property_data', array('id' => $tmp));
    $ontologyid = $DB->get_record('course_modules', array('id' => $cmid));
    $ontologyid = $ontologyid->instance;
    $rank = $DB->get_record('ontology_student_rank', array('userid' => $raw->userid, 'ontologyid' => $ontologyid));
    $rank = $rank->rating;
    //sostavuvanje na niza so iminja
//    echo ' '.$raw->name;
//    echo '  prevaruvanje ime:'.array_search($raw->name,$names,true);
    if (!is_number(array_search($raw->name, $names, true))) {
//        echo '  dodadeno ime'.$raw->name;
        array_push($names, $raw->name);
        array_push($names_rank, $rank);
    } else {
        $names_rank[array_search($raw->name, $names, true)]+=$rank;
    }

    //sostavuvanje na niza so opisi
//    echo ' '.$raw->description;
//    echo '  prevaruvanje opis:'.array_search($raw->description,$descrions,true);
    if (!is_number(array_search($raw->description, $descriptions, true))) {
//        echo '  dodaden opis'.$raw->description;
        $dlen++;
        array_push($descriptions, $raw->description);
        array_push($descriptions_rank, $rank);
    } else {
        $descriptions_rank[array_search($raw->description, $descriptions, true)]+=$rank;
    }


    //sostavuvanje na niza so superklasi
    if (!is_number(array_search($raw->superproperty, $superproperty, true))) {
        $slen++;
        array_push($superproperty, $raw->superproperty);
//        echo $raw->superproperty.'</br>';
        array_push($superproperty_rank, $rank);
    } else {
        $superproperty_rank[array_search($raw->superproperty, $superproperty, true)]+=$rank;
    }
    //   echo $rank.'</br>';
    //za svojstvata na svojstvata
    if (!is_number(array_search($raw->attributes, $prop_of_prop, true))) {
        $sslen++;
        array_push($prop_of_prop, $raw->attributes);
        array_push($prop_of_prop_rank, $rank);
    } else {
        $prop_of_prop_rank[array_search($raw->attributes, $prop_of_prop, true)]+=$rank;
    }

    if (!is_number(array_search($raw->rang, $rang, true))) {
        $rlen++;
        array_push($rang, $raw->rang);
        array_push($rang_rank, $rank);
    } else {
        $rang_rank[array_search($raw->rang, $rang, true)]+=$rank;
    }

    $sttl+=$rank;
//    echo '</br>';
}

array_multisort($names_rank, SORT_NUMERIC, SORT_DESC, $names);
array_multisort($descriptions_rank, SORT_NUMERIC, SORT_DESC, $descriptions);
array_multisort($superproperty_rank, SORT_NUMERIC, SORT_DESC, $superproperty);
array_multisort($prop_of_prop_rank, SORT_NUMERIC, SORT_DESC, $prop_of_prop);
array_multisort($rang_rank, SORT_NUMERIC, SORT_DESC, $rang);

$sum = array_sum($prop_of_prop_rank);
for ($i = 0; $i < $sslen; $i++) {
    $prop_of_prop_rank[$i] = ($prop_of_prop_rank[$i] / $sum) * 100;
}
echo '<div class="ui-dialog ui-widget ui-widget-content ui-corner-all latest"  style="margin-top: -40px; width:300px">
                <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                    <span id="ui-dialog-title-dialog" class="ui-dialog-title"><center>';

echo get_string('Dproperties_merge', 'ontology');
echo '</center></span>
                </div>
            <div class="ui-dialog-content ui-widget-content"> ';
echo '<b>' . get_string('Name', 'ontology') . ':</b><br />';
echo '<input type="text" id="imeT" value="' . $names[0] . '"  class=" ui-widget ui-state-hover" style="width:150px;"> ';
echo '<select id="ime" onchange="ime_change()">';
foreach ($names as $name) {
    echo '<option value="' . $name . '">' . $name . '</option>';
}
echo '</select>';

echo '<br /><b>' . get_string('Description', 'ontology') . ':</b><br />';
echo '<table style="margin-left:-6px"><tr><td colspan="2">';
echo '<input type="text" id="opisT" value="' . $descriptions[0] . '" size="' . $maxDescLen . '"  class=" ui-widget ui-state-hover" style="width:150px;"> </br>';
echo '</td></tr><tr><td>';
echo '<select id="opis" size=' . $dlen . ' onchange="opis_change()">';
$br = 0;
foreach ($descriptions as $des) {
    if ($br == 0) {
        echo '<option selected=true value="' . $des . '">' . $des . '</option>';
    } else {
        echo '<option value="' . $des . '">' . $des . '</option>';
    }
    $br++;
}
echo '</select>';
echo '</td><td>';
echo '<select id="opisproc" size=' . $dlen . ' onchange="opisproc_change()">';
$br = 0;
foreach ($descriptions_rank as $des) {
    if ($br == 0) {
        echo '<option selected=true value="' . $des . '">' . substr(100 * $des / $sttl, 0, 4) . '%</option>';
    } else {
        echo '<option value="' . $des . '">' . substr(100 * $des / $sttl, 0, 4) . '%</option>';
    }
    $br++;
}
echo '</select>';
echo '</td></tr></table>';
//status mora da im e 3
//superklasi
echo '<b>' . get_string('Superproperty', 'ontology') . ':</b><br />';
$isselected = data_property_hierarhy3("superproperty", "superproperty_submit", 0, $cmid, $USER->id, false, $superproperty);


$brd = 0;
echo '<br/><b>' . get_string('Property_domains', 'ontology') . ':</b><br />';
echo '<form>';
foreach (explode(' ', $ids) as $tmp) {
    $raws = $DB->get_records('ontology_property_expression', array('ontology_propertyid' => $tmp, 'type' => 3));
    foreach ($raws as $raw) {
        echo '<input type="checkbox" name="exp_cbox" value=' . $raw->id . ' /> ';
        echo get_expression_in_color($raw->expression);
        echo '</br>';
        $brd++;
    }
}
if ($brd == 0) {
    echo get_string('No_domains', 'ontology');
}
echo '</form>';


echo '<br/> <b>' . get_string('Property_range', 'ontology') . ': </b>';
echo '<select id="rang">';
for ($i = 0; $i < $rlen; $i++) {
    if ($i == 0)
        echo '<option selected=true value="' . $rang[$i] . '">' . $rang[$i] . '</option>';
    else
        echo '<option value="' . $rang[$i] . '">' . $rang[$i] . '</option>';
}
echo '</select>';
echo '<br /><br /><b>' . get_string('Characteristics', 'ontology') . ':</b><br />';
?>

<input type="checkbox" id="funkc" <?php if ($prop_of_prop_rank[0] == '1') {
    echo 'checked';
} ?> /> 
<?php echo get_string('Functional', 'ontology'); ?> 
<select id="procent" onchange="smeni_vrednosti()">   

    <?php
    for ($i = 0; $i < $sslen; $i++) {
        if ($i == 0)
            echo '<option selected="true" value="' . $prop_of_prop[$i] . '">' . $prop_of_prop_rank[$i] . '%' . '</option>';
        else
            echo '<option value="' . $prop_of_prop[$i] . '">' . $prop_of_prop_rank[$i] . '%' . '</option>';
    }
    echo '</select>';

    echo '<table> <tr> <td valign="top">';
    echo '<b>' . get_string('Equivalent_Properties', 'ontology') . ':</b><br />';
    echo '<form>';
    $bre = 0;
    $pom_ekviv = array();
    foreach (explode(' ', $ids) as $tmp) {
        $raws = $DB->get_records('ontology_property_equivalent', array('ontology_propertyid' => $tmp, 'type' => 2));
        foreach ($raws as $raw) {
            if (!is_number(array_search(get_name_of_dproperty($raw->ontology_propertyid2), $pom_ekviv, true))) {
                array_push($pom_ekviv, get_name_of_dproperty($raw->ontology_propertyid2));
                //        $equivproperty=$DB->get_record('ontology_property_individual',array('id' => $raw->ontology_propertyid2));
                echo '<input type="checkbox" name="equiv_cbox" value=' . $raw->id . ' /> ';
                echo get_name_of_dproperty($raw->ontology_propertyid2);
                //        echo $equivproperty->name;
                echo '</br>';
                $bre++;
            } else {
                
            }
        }
    }
    if ($bre == 0) {
        echo get_string('No_equivalent_properties', 'ontology');
    }
    echo '</form>';

    echo '</td><td valign="top">';

    echo '<b>' . get_string('Disjoint_Properties', 'ontology') . ':</b><br />';
    echo '<form>';
    $brd = 0;
    foreach (explode(' ', $ids) as $tmp) {
        $raws = $DB->get_records('ontology_property_disjoint', array('ontology_propertyid' => $tmp, 'type' => 2));
        foreach ($raws as $raw) {
            if (!is_number(array_search(get_name_of_dproperty($raw->ontology_propertyid2), $pom_ekviv, true))) {
                array_push($pom_ekviv, get_name_of_dproperty($raw->ontology_propertyid2));
                //       $disjproperty=$DB->get_record('ontology_property_individual',array('id' => $raw->ontology_propertyid2));
                echo '<input type="checkbox" name="disj_cbox" value=' . $raw->id . ' /> ';
                echo get_name_of_dproperty($raw->ontology_propertyid2);
                //        echo $disjproperty->name;
                echo '</br>';
                $brd++;
            } else {
                
            }
        }
    }
    if ($brd == 0) {
        echo get_string('No_disjoint_properties', 'ontology');
    }
    echo '</form>';

    echo '</td></tr></table>'
    ?>

    <button onclick="canceled()"> <?php echo get_string('Cancel', 'ontology'); ?> </button>
    <button id="write_btn"
    <?php if (!$isselected) echo "style=\"visibility: hidden;\""; ?> 
            onclick="writeDataPropertyToDB()"> <?php echo get_string('Save', 'ontology'); ?></button></div></div>
<div id="ff">
    <input type="hidden" name="moduleid" value="<?php echo $cmid; ?>" id="moduleid"/>
    <input type="hidden" name="userid" value="<?php echo $uid; ?>" id="userid"/>
    <input type="hidden" name="site_ida" value="<?php echo $ids; ?>" id="site_ida"/>
    <div id="refresh">
    </div>
</div>

<style>
    .latest { overflow:visible; position:static; }
</style>
