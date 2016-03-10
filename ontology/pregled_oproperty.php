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
$podtip = $_GET["podtip"];
$red = $_GET["red"];
$u_id = $_GET["userid"];

$karak[] = get_string('Functional', 'ontology');
$karak[] = get_string('Inverse_Functional', 'ontology');
$karak[] = get_string('Transitive', 'ontology');
$karak[] = get_string('Symetric', 'ontology');
$karak[] = get_string('Asymetric', 'ontology');
$karak[] = get_string('Reflexive', 'ontology');
$karak[] = get_string('Ireflexive', 'ontology');


$property = $DB->get_record("ontology_property_individual", array("id" => $id));
$domain = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$domain2 = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "1", "userid" => $u_id, "course_modulesid" => $moduleid));
$rang = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "2", "status" => "2"));
$rang2 = $DB->get_records("ontology_property_expression", array("ontology_propertyid" => $id, "type" => "2", "userid" => $u_id, "course_modulesid" => $moduleid));
$equproperties = $DB->get_records("ontology_property_equivalent", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$equproperties2 = $DB->get_records("ontology_property_equivalent", array("ontology_propertyid" => $id, "type" => "1", "userid" => $u_id, "course_modulesid" => $moduleid));
$disproperties = $DB->get_records("ontology_property_disjoint", array("ontology_propertyid" => $id, "type" => "1", "status" => "2"));
$disproperties2 = $DB->get_records("ontology_property_disjoint", array("ontology_propertyid" => $id, "type" => "1", "userid" => $u_id, "course_modulesid" => $moduleid));
?>

<div id="Region">
    <?php
    if ($property->id == 1) {
        ?>
        <table>
            <tr>
                <td>
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
                    echo '</td>';
                    echo '</tr>';
                    echo '</table>';
                }
                else {
                    if ($property->status == 2 || $property->status == 3 || $property->status == 5) {
                        ?>
                        <table>
                            <tr>
                                <td bgcolor="#E0F4D7">
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
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }
    else {
        if ($tip == '1') {
            ?>
                                        <table>
                                            <tr>
                                                <td bgcolor="#E0F4D7">
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
            echo '</td>';
            echo '</tr>';
            echo '</table>';
            $property->status = 3;
            if ($podtip == 1) {
                $property->points = 1;
            } else {
                $property->points = 2;
            }
            $DB->update_record("ontology_property_individual", $property);
        } else {
            if ($property->status == 4 && $property->points == 0) {
                ?>
                                                        <table>
                                                            <tr>
                                                                <td bgcolor="#FEE7E7">
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
                                                        echo '</td>';
                                                        echo '</tr>';
                                                        echo '</table>';
                                                    }
                                                    else {
                                                        if ($tip == '2') {
                                                            ?>
                                                                        <table>
                                                                            <tr>
                                                                                <td bgcolor="#FEE7E7">
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
                                                                        echo '</td>';
                                                                        echo '</tr>';
                                                                        echo '</table>';
                                                                        $property->status = 4;
                                                                        $property->points = 0;
                                                                        $DB->update_record("ontology_property_individual", $property);
                                                                    }
                                                                    else {
                                                                        if ($property->status == 4 && $property->points < 0) {
                                                                            ?>
                                                                                        <table>
                                                                                            <tr>
                                                                                                <td bgcolor="#FEE7E7">
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
                                                                                        echo '</td>';
                                                                                        echo '</tr>';
                                                                                        echo '</table>';
                                                                                    }
                                                                                    else {
                                                                                        if ($tip == '3') {
                                                                                            ?>
                                                                                                        <table>
                                                                                                            <tr>
                                                                                                                <td bgcolor="#FEE7E7">
                                                                                                                    <b> <?php echo get_string('Oproperty_name', 'ontology') . ':'; ?>: </b> <span style="color: #006FFF;"><?php echo $property->name; ?> </span> <br />
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
                                                                                                        echo '</td>';
                                                                                                        echo '</tr>';
                                                                                                        echo '</table>';
                                                                                                        $property->status = 4;
                                                                                                        $property->points = -2;
                                                                                                        $DB->update_record("ontology_property_individual", $property);
                                                                                                    }
                                                                                                    else {
                                                                                                        ?>
                                                                                                                    <table>
                                                                                                                        <tr>
                                                                                                                            <td>
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
                                                                                                                    echo '</td>';
                                                                                                                    echo '</tr>';
                                                                                                                    echo '<tr align="center">';
                                                                                                                    echo '<td>';
                                                                                                                    echo '<img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(0)">&nbsp';
                                                                                                                    echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(1)">&nbsp';
                                                                                                                    echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b1_odb()">&nbsp';
                                                                                                                    echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b1_neg()">&nbsp';
                                                                                                                    echo '</td>';
                                                                                                                    echo '</tr>';
                                                                                                                    echo '</table>';
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        ?>
                                                                                                    <hr />

                                                                                                    <b> <?php echo get_string('Property_domain', 'ontology') . ':'; ?> </b><br /> 
                                                                                                    <?php
                                                                                                    if (count($domain) == 0 && count($domain2) == 0) {
                                                                                                        echo get_string('No_domains', 'ontology');
                                                                                                        echo "<br />";
                                                                                                    } else {
                                                                                                        ?>

                                                                                                        <table>
                                                                                                        <?php
                                                                                                        foreach ($domain as $key => $value) {
                                                                                                            get_expression_in_color($value->expression);
                                                                                                            echo "<br/>";
                                                                                                        }
                                                                                                        foreach ($domain2 as $key => $value) {
                                                                                                            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                                                                                                                echo '<tr>';
                                                                                                                echo '<td bgcolor=#E0F4D7>';
                                                                                                                get_expression_in_color($value->expression);
                                                                                                                //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                echo '</td>';
                                                                                                                echo '</tr>';
                                                                                                            } else {
                                                                                                                if ($tip == '4' && $value->id == $red) {
                                                                                                                    echo '<tr>';
                                                                                                                    echo '<td bgcolor=#E0F4D7>';
                                                                                                                    get_expression_in_color($value->expression);
                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                    echo '</td>';
                                                                                                                    echo '</tr>';
                                                                                                                    $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                    $redica->status = 3;
                                                                                                                    if ($podtip == 2) {
                                                                                                                        $redica->points = 1;
                                                                                                                    } else {
                                                                                                                        $redica->points = 2;
                                                                                                                    }

                                                                                                                    $DB->update_record("ontology_property_expression", $redica);
                                                                                                                } else {
                                                                                                                    if ($value->status == 4 && $value->points == 0) {
                                                                                                                        echo '<tr>';
                                                                                                                        echo '<td bgcolor=#FEE7E7>';
                                                                                                                        get_expression_in_color($value->expression);
                                                                                                                        //echo ' <span style="color: red;"><b>одбиено</b></span>';
                                                                                                                        echo '</td>';
                                                                                                                        echo '</tr>';
                                                                                                                    } else {
                                                                                                                        if ($tip == '5' && $value->id == $red) {
                                                                                                                            echo '<tr>';
                                                                                                                            echo '<td bgcolor=#FEE7E7>';
                                                                                                                            get_expression_in_color($value->expression);
                                                                                                                            echo '</td>';
                                                                                                                            echo '</tr>';
                                                                                                                            $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                            $redica->status = 4;
                                                                                                                            $redica->points = 0;
                                                                                                                            $DB->update_record("ontology_property_expression", $redica);
                                                                                                                        } else {
                                                                                                                            if ($value->status == 4 && $value->points < 0) {
                                                                                                                                echo '<tr>';
                                                                                                                                echo '<td bgcolor=#FEE7E7>';
                                                                                                                                get_expression_in_color($value->expression);
                                                                                                                                //echo ' <span style="color: red;"><b>казнето</b></span>';
                                                                                                                                echo '</td>';
                                                                                                                                echo '</tr>';
                                                                                                                            } else {
                                                                                                                                if ($tip == '6' && $value->id == $red) {
                                                                                                                                    echo '<tr>';
                                                                                                                                    echo '<td bgcolor=#FEE7E7>';
                                                                                                                                    get_expression_in_color($value->expression);
                                                                                                                                    echo '</td>';
                                                                                                                                    echo '</tr>';
                                                                                                                                    $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                                    $redica->status = 4;
                                                                                                                                    $redica->points = -2;
                                                                                                                                    $DB->update_record("ontology_property_expression", $redica);
                                                                                                                                } else {
                                                                                                                                    echo '<tr>';
                                                                                                                                    echo '<td>';
                                                                                                                                    get_expression_in_color($value->expression);
                                                                                                                                    echo '<img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b_poz(0,' . $value->id . ')">&nbsp';
                                                                                                                                    echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b_poz(2,' . $value->id . ')">&nbsp';
                                                                                                                                    echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b_odb(' . $value->id . ')">&nbsp';
                                                                                                                                    echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b_neg(' . $value->id . ')">&nbsp';
                                                                                                                                    echo '</td>';
                                                                                                                                    echo '</tr>';
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                    ?>
                                                                                                    </table>
                                                                                                    <div id="Region1">
                                                                                                        <input type="hidden" id="steklista1" />
                                                                                                    </div>
                                                                                                    <input type="hidden" id="tekstlista1" />
                                                                                                    <input type="hidden" id="inputlista1" />

                                                                                                    <br />

                                                                                                    <b> <?php echo get_string('Property_range', 'ontology') . ':'; ?> </b><br /> 
                                                                                                        <?php
                                                                                                        if (count($rang) == 0 && count($rang2) == 0) {
                                                                                                            echo get_string('No_range', 'ontology');
                                                                                                            echo "<br />";
                                                                                                        } else {
                                                                                                            ?>
                                                                                                        <table>
                                                                                                            <?php
                                                                                                            foreach ($rang as $key => $value) {
                                                                                                                get_expression_in_color($value->expression);
                                                                                                                echo "<br/>";
                                                                                                            }
                                                                                                            foreach ($rang2 as $key => $value) {
                                                                                                                if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                                                                                                                    echo '<tr>';
                                                                                                                    echo '<td bgcolor=#E0F4D7>';
                                                                                                                    get_expression_in_color($value->expression);
                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                    //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                    echo '</td>';
                                                                                                                    echo '</tr>';
                                                                                                                } else {
                                                                                                                    if ($tip == '4' && $value->id == $red) {
                                                                                                                        echo '<tr>';
                                                                                                                        echo '<td bgcolor=#E0F4D7>';
                                                                                                                        get_expression_in_color($value->expression);
                                                                                                                        //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                        echo '</td>';
                                                                                                                        echo '</tr>';
                                                                                                                        $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                        $redica->status = 3;
                                                                                                                        if ($podtip == 2) {
                                                                                                                            $redica->points = 1;
                                                                                                                        } else {
                                                                                                                            $redica->points = 2;
                                                                                                                        }
                                                                                                                        $DB->update_record("ontology_property_expression", $redica);
                                                                                                                    } else {
                                                                                                                        if ($value->status == 4 && $value->points == 0) {
                                                                                                                            echo '<tr>';
                                                                                                                            echo '<td bgcolor=#FEE7E7>';
                                                                                                                            get_expression_in_color($value->expression);
                                                                                                                            //echo ' <span style="color: red;"><b>одбиено</b></span>';
                                                                                                                            echo '</td>';
                                                                                                                            echo '</tr>';
                                                                                                                        } else {
                                                                                                                            if ($tip == '5' && $value->id == $red) {
                                                                                                                                echo '<tr>';
                                                                                                                                echo '<td bgcolor=#FEE7E7>';
                                                                                                                                get_expression_in_color($value->expression);
                                                                                                                                echo '</td>';
                                                                                                                                echo '</tr>';
                                                                                                                                $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                                $redica->status = 4;
                                                                                                                                $redica->points = 0;
                                                                                                                                $DB->update_record("ontology_property_expression", $redica);
                                                                                                                            } else {
                                                                                                                                if ($value->status == 4 && $value->points < 0) {
                                                                                                                                    echo '<tr>';
                                                                                                                                    echo '<td bgcolor=#FEE7E7>';
                                                                                                                                    get_expression_in_color($value->expression);
                                                                                                                                    //echo ' <span style="color: red;"><b>казнето</b></span>';
                                                                                                                                    echo '</td>';
                                                                                                                                    echo '</tr>';
                                                                                                                                } else {
                                                                                                                                    if ($tip == '6' && $value->id == $red) {
                                                                                                                                        echo '<tr>';
                                                                                                                                        echo '<td bgcolor=#FEE7E7>';
                                                                                                                                        get_expression_in_color($value->expression);
                                                                                                                                        echo '</td>';
                                                                                                                                        echo '</tr>';
                                                                                                                                        $redica = $DB->get_record("ontology_property_expression", array("id" => $value->id));
                                                                                                                                        $redica->status = 4;
                                                                                                                                        $redica->points = -2;
                                                                                                                                        $DB->update_record("ontology_property_expression", $redica);
                                                                                                                                    } else {
                                                                                                                                        echo '<tr>';
                                                                                                                                        echo '<td>';
                                                                                                                                        get_expression_in_color($value->expression);
                                                                                                                                        echo '<img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b_poz(0,' . $value->id . ')">&nbsp';
                                                                                                                                        echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b_poz(2,' . $value->id . ')">&nbsp';
                                                                                                                                        echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b_odb(' . $value->id . ')">&nbsp';
                                                                                                                                        echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b_neg(' . $value->id . ')">&nbsp';
                                                                                                                                        echo '</td>';
                                                                                                                                        echo '</tr>';
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                        ?>
                                                                                                    </table>
                                                                                                    <div id="Region2">
                                                                                                        <input type="hidden" id="steklista2" />
                                                                                                    </div>
                                                                                                    <input type="hidden" id="tekstlista2" />
                                                                                                    <input type="hidden" id="inputlista2" />

                                                                                                    <br />
                                                                                                    <table>
                                                                                                        <tr>
                                                                                                            <td>      
                                                                                                                <table>
                                                                                                                    <b> <?php echo get_string('Equivalent_Properties', 'ontology') . ':'; ?> </b> <br />
                                                                                                        <?php
                                                                                                        if (count($equproperties) == 0 && count($equproperties2) == 0) {
                                                                                                            echo get_string('No_equivalent_properties', 'ontology');
                                                                                                            echo "<br />";
                                                                                                        } else {
                                                                                                            ?>

                                                                                                            <?php
                                                                                                            foreach ($equproperties as $key => $value) {
                                                                                                                echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                echo "<br/>";
                                                                                                            }
                                                                                                            foreach ($equproperties2 as $key => $value) {
                                                                                                                if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                                                                                                                    echo '<tr>';
                                                                                                                    echo '<td bgcolor=#E0F4D7>';
                                                                                                                    echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                    //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                    echo '</td>';
                                                                                                                    echo '</tr>';
                                                                                                                } else {
                                                                                                                    if ($tip == '7' && $value->id == $red) {
                                                                                                                        echo '<tr>';
                                                                                                                        echo '<td bgcolor=#E0F4D7>';
                                                                                                                        echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                        //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                        //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                        echo '</td>';
                                                                                                                        echo '</tr>';
                                                                                                                        $redica = $DB->get_record("ontology_property_equivalent", array("id" => $value->id));
                                                                                                                        $redica->status = 3;
                                                                                                                        if ($podtip == 3) {
                                                                                                                            $redica->points = 1;
                                                                                                                        } else {
                                                                                                                            $redica->points = 2;
                                                                                                                        }
                                                                                                                        $DB->update_record("ontology_property_equivalent", $redica);
                                                                                                                    } else {
                                                                                                                        if ($value->status == 4 && $value->points == 0) {
                                                                                                                            echo '<tr>';
                                                                                                                            echo '<td bgcolor=#FEE7E7>';
                                                                                                                            echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                            //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                            //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                            echo '</td>';
                                                                                                                            echo '</tr>';
                                                                                                                        } else {
                                                                                                                            if ($tip == '8' && $value->id == $red) {
                                                                                                                                echo '<tr>';
                                                                                                                                echo '<td bgcolor=#FEE7E7>';
                                                                                                                                echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                echo '</td>';
                                                                                                                                echo '</tr>';
                                                                                                                                $redica = $DB->get_record("ontology_property_equivalent", array("id" => $value->id));
                                                                                                                                $redica->status = 4;
                                                                                                                                $redica->points = 0;
                                                                                                                                $DB->update_record("ontology_property_equivalent", $redica);
                                                                                                                            } else {
                                                                                                                                if ($value->status == 4 && $value->points < 0) {
                                                                                                                                    echo '<tr>';
                                                                                                                                    echo '<td bgcolor=#FEE7E7>';
                                                                                                                                    echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                    //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                    echo '</td>';
                                                                                                                                    echo '</tr>';
                                                                                                                                } else {
                                                                                                                                    if ($tip == '9' && $value->id == $red) {
                                                                                                                                        echo '<tr>';
                                                                                                                                        echo '<td bgcolor=#FEE7E7>';
                                                                                                                                        echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                        //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                        //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                        echo '</td>';
                                                                                                                                        echo '</tr>';
                                                                                                                                        $redica = $DB->get_record("ontology_property_equivalent", array("id" => $value->id));
                                                                                                                                        $redica->status = 4;
                                                                                                                                        $redica->points = -2;
                                                                                                                                        $DB->update_record("ontology_property_equivalent", $redica);
                                                                                                                                    } else {
                                                                                                                                        echo '<tr>';
                                                                                                                                        echo '<td>';
                                                                                                                                        echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                        echo ' <img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b2_poz(0,' . $value->id . ')">&nbsp';
                                                                                                                                        echo ' <img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b2_poz(3,' . $value->id . ')">&nbsp';
                                                                                                                                        echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b2_odb(' . $value->id . ')">&nbsp';
                                                                                                                                        echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b2_neg(' . $value->id . ')">&nbsp';
                                                                                                                                        echo '</td>';
                                                                                                                                        echo '</tr>';
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                        ?>
                                                                                                                </table>
                                                                                                                <br /> 

                                                                                                                <br />
                                                                                                                <input type="hidden" id="equsvojstvoid"  value="" />



                                                                                                            </td >
                                                                                                            <td valign="top">
                                                                                                                <table>
                                                                                                                    <b> <?php echo get_string('Disjoint_Properties', 'ontology') . ':'; ?> </b> <br />
                                                                                                                    <?php
                                                                                                                    if (count($disproperties) == 0 && count($disproperties2) == 0) {
                                                                                                                        echo get_string('No_disjoint_properties', 'ontology');
                                                                                                                        echo "<br />";
                                                                                                                    } else {
                                                                                                                        ?>


                                                                                                                        <?php
                                                                                                                        foreach ($disproperties as $key => $value) {
                                                                                                                            echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                            echo "<br/>";
                                                                                                                        }
                                                                                                                        foreach ($disproperties2 as $key => $value) {
                                                                                                                            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                                                                                                                                echo '<tr>';
                                                                                                                                echo '<td bgcolor=#E0F4D7>';
                                                                                                                                echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                echo '</td>';
                                                                                                                                echo '</tr>';
                                                                                                                            } else {
                                                                                                                                if ($tip == '10' && $value->id == $red) {
                                                                                                                                    echo '<tr>';
                                                                                                                                    echo '<td bgcolor=#E0F4D7>';
                                                                                                                                    echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                    //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                    echo '</td>';
                                                                                                                                    echo '</tr>';
                                                                                                                                    $redica = $DB->get_record("ontology_property_disjoint", array("id" => $value->id));
                                                                                                                                    $redica->status = 3;
                                                                                                                                    if ($podtip == 4) {
                                                                                                                                        $redica->points = 1;
                                                                                                                                    } else {
                                                                                                                                        $redica->points = 2;
                                                                                                                                    }
                                                                                                                                    $DB->update_record("ontology_property_disjoint", $redica);
                                                                                                                                } else {
                                                                                                                                    if ($value->status == 4 && $value->points == 0) {
                                                                                                                                        echo '<tr>';
                                                                                                                                        echo '<td bgcolor=#FEE7E7>';
                                                                                                                                        echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                        //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                        //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                        echo '</td>';
                                                                                                                                        echo '</tr>';
                                                                                                                                    } else {
                                                                                                                                        if ($tip == '11' && $value->id == $red) {
                                                                                                                                            echo '<tr>';
                                                                                                                                            echo '<td bgcolor=#FEE7E7>';
                                                                                                                                            echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                            //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                            //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                            echo '</td>';
                                                                                                                                            echo '</tr>';
                                                                                                                                            $redica = $DB->get_record("ontology_property_disjoint", array("id" => $value->id));
                                                                                                                                            $redica->status = 4;
                                                                                                                                            $redica->points = 0;
                                                                                                                                            $DB->update_record("ontology_property_disjoint", $redica);
                                                                                                                                        } else {
                                                                                                                                            if ($value->status == 4 && $value->points < 0) {
                                                                                                                                                echo '<tr>';
                                                                                                                                                echo '<td bgcolor=#FEE7E7>';
                                                                                                                                                echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                                //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                                //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                                echo '</td>';
                                                                                                                                                echo '</tr>';
                                                                                                                                            } else {
                                                                                                                                                if ($tip == '12' && $value->id == $red) {
                                                                                                                                                    echo '<tr>';
                                                                                                                                                    echo '<td bgcolor=#FEE7E7>';
                                                                                                                                                    echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                                    //echo ' <img src="Check-icon.png" style="width:10; height:10px;"  NAME="check">';
                                                                                                                                                    //echo '<span style="color: green;"><b>прифатено</b></span>';
                                                                                                                                                    echo '</td>';
                                                                                                                                                    echo '</tr>';
                                                                                                                                                    $redica = $DB->get_record("ontology_property_disjoint", array("id" => $value->id));
                                                                                                                                                    $redica->status = 4;
                                                                                                                                                    $redica->points = -2;
                                                                                                                                                    $DB->update_record("ontology_property_disjoint", $redica);
                                                                                                                                                } else {
                                                                                                                                                    echo '<tr>';
                                                                                                                                                    echo '<td>';
                                                                                                                                                    echo get_name_of_oproperty($value->ontology_propertyid2);
                                                                                                                                                    echo ' <img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b3_poz(0,' . $value->id . ')">&nbsp';
                                                                                                                                                    echo ' <img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b3_poz(4,' . $value->id . ')">&nbsp';
                                                                                                                                                    echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b3_odb(' . $value->id . ')">&nbsp';
                                                                                                                                                    echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b3_neg(' . $value->id . ')">&nbsp';
                                                                                                                                                    echo '</td>';
                                                                                                                                                    echo '</tr>';
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                    ?>
                                                                                                                </table>
                                                                                                                <input type="hidden" id="dissvojstvoid"  value="" />
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    </table> 



                                                                                                    </div>


