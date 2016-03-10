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
$u_id = $_GET["userid"];
$tip = $_GET["tip"];
$podtip = $_GET["podtip"];
$red = $_GET["red"];

$individual = $DB->get_record("ontology_individual", array("id" => $id));
//$superclass=$DB->get_records("ontology_individual_expression",array("ontology_individualid"=>$id,"status"=>"2"));
$superclass2 = $DB->get_records("ontology_individual_expression", array("ontology_individualid" => $id, "userid" => $u_id, "course_modulesid" => $moduleid));
//$oproperties=$DB->get_records("ontology_individual_property_individual", array("ontology_individualid"=> $id, "status"=>2));
$oproperties2 = $DB->get_records("ontology_individual_property_individual", array("ontology_individualid" => $id, "userid" => $u_id, "course_modulesid" => $moduleid));
//$dproperties=$DB->get_records("ontology_individual_property_data", array("ontology_individualid"=> $id, "status"=>2));
$dproperties2 = $DB->get_records("ontology_individual_property_data", array("ontology_individualid" => $id, "userid" => $u_id, "course_modulesid" => $moduleid));
?>

<div id="Region">
    <?php
    if ($individual->status == 2 || $individual->status == 3 || $individual->status == 5) {
        ?>
        <table>
            <tr>
                <td bgcolor="#E0F4D7">
                    <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                    <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                </td>
            </tr>
        </table>
        <hr />
        <?php
    }
    else {
        if ($tip == '1') {
            ?>
            <table>
                <tr>
                    <td bgcolor="#E0F4D7">
                        <b> <?php echo get_string('Individual_name', 'ontology'); ?> :</b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                        <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                    </td>
                </tr>
            </table>
            <hr />
        <?php
        $individual->status = 3;
        if ($podtip == 1) {
            $individual->points = 1;
        } else {
            $individual->points = 2;
        }

        $DB->update_record("ontology_individual", $individual);
    } else {
        if ($individual->status == 4 && $individual->points == 0) {
            ?>
                <table>
                    <tr>
                        <td bgcolor="#FEE7E7">
                            <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                            <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                        </td>
                    </tr>
                </table>
                <hr />
            <?php
        }
        else {
            if ($tip == '2') {
                ?>
                    <table>
                        <tr>
                            <td bgcolor="#FEE7E7">
                                <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                                <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                            </td>
                        </tr>
                    </table>
                    <hr />
                    <?php
                    $individual->status = 4;
                    $individual->points = 0;
                    $DB->update_record("ontology_individual", $individual);
                }
                else {
                    if ($individual->status == 4 && $individual->points < 0) {
                        ?>
                        <table>
                            <tr>
                                <td bgcolor="#FEE7E7">
                                    <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                                    <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                                </td>
                            </tr>
                        </table>
                        <hr />
                        <?php
                    }
                    else {
                        if ($tip == '3') {
                            ?>
                            <table>
                                <tr>
                                    <td bgcolor="#FEE7E7">
                                        <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                                        <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                                    </td>
                                </tr>
                            </table>
                            <hr />
                            <?php
                            $individual->status = 4;
                            $individual->points = -2;
                            $DB->update_record("ontology_individual", $individual);
                        }
                        else {
                            ?>
                            <table>
                                <tr>
                                    <td>
                                        <b> <?php echo get_string('Individual_name', 'ontology'); ?> : </b> <span style="color: #FF0404;"><?php echo $individual->name; ?> </span> <br />
                                        <b> <?php echo get_string('Individual_description', 'ontology'); ?>: </b>  <?php if ($individual->description == "") echo get_string('No_description', 'ontology'); else echo $individual->description; ?> <br />
                                    </td>
                                </tr>
                            <?php
                            echo '<tr>';
                            echo '<td>';
                            echo ' <img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(0)">&nbsp';
                            echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(1)">&nbsp';
                            echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b1_odb()">&nbsp';
                            echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b1_neg()">&nbsp';
                            echo '</td>';
                            echo '</tr>';
                            ?>
                            </table>
                            <hr />
                        <?php
                    }
                }
            }
        }
    }
}
?>
    <b> <?php echo get_string('Instance_class', 'ontology'); ?>: </b><br /> 
        <?php
        if (count($superclass2) == 0) {
            echo get_string('No_classes', 'ontology');
            echo "<br />";
        } else {
            ?>
        <table>
        <?php
        foreach ($superclass2 as $key => $value) {
            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                echo '<tr>';
                echo '<td bgcolor=#E0F4D7>';
                get_expression_in_color($value->expression);
                echo '</td>';
                echo '</tr>';
            } else {
                if ($tip == '4' && $value->id == $red) {
                    echo '<tr>';
                    echo '<td  bgcolor=#E0F4D7>';
                    get_expression_in_color($value->expression);
                    echo '</td>';
                    echo '</tr>';
                    $redica = $DB->get_record("ontology_individual_expression", array("id" => $value->id));
                    $redica->status = 3;
                    if ($podtip == 2) {
                        $redica->points = 1;
                    } else {
                        $redica->points = 2;
                    }

                    $DB->update_record("ontology_individual_expression", $redica);
                } else {
                    if ($value->status == 4) {
                        if ($value->points == 0) {
                            echo '<tr>';
                            echo '<td  bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        } else {
                            echo '<tr>';
                            echo '<td  bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        if ($tip == '5' && $value->id == $red) {
                            echo '<tr>';
                            echo '<td  bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                            $redica = $DB->get_record("ontology_individual_expression", array("id" => $value->id));
                            $redica->status = 4;
                            $redica->points = 0;
                            $DB->update_record("ontology_individual_expression", $redica);
                        } else {

                            if ($tip == '6' && $value->id == $red) {
                                echo '<tr>';
                                echo '<td  bgcolor=#FEE7E7>';
                                get_expression_in_color($value->expression);
                                echo '</td>';
                                echo '</tr>';
                                $redica = $DB->get_record("ontology_individual_expression", array("id" => $value->id));
                                $redica->status = 4;
                                $redica->points = -2;
                                $DB->update_record("ontology_individual_expression", $redica);
                            } else {
                                echo "<tr>";
                                echo '<td>';
                                get_expression_in_color($value->expression);
                                echo '<img src="Check-icon.png" style="width:10; height:10px;cursor: hand;" NAME="check" onclick="b_poz(0,' . $value->id . ')">&nbsp';
                                echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;" NAME="check" onclick="b_poz(2,' . $value->id . ')">&nbsp';
                                echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b_odb(' . $value->id . ')">&nbsp';
                                echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b_neg(' . $value->id . ')">&nbsp';
                                echo "</td>";
                                echo "</tr>";
                                //echo "<br/>";
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
    <table>
        <tr>
            <td  valign="top">      <b> <?php echo get_string('Oproperties', 'ontology') . ':'; ?> </b> <br />
        <?php
        if (count($oproperties2) == 0) {
            echo get_string('No_oproperties', 'ontology');
            echo "<br />";
        } else {
            ?>
                    <table>
            <?php
            foreach ($oproperties2 as $key => $value) {
                if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                    echo "<tr>";
                    echo "<td bgcolor=#E0F4D7>";
                    echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                    echo "</td>";
                    echo "</tr>";
                } else {
                    if ($tip == '7' && $red == $value->id) {
                        echo "<tr>";
                        echo "<td bgcolor=#E0F4D7>";
                        echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                        echo "</td>";
                        echo "</tr>";
                        $redica = $DB->get_record("ontology_individual_property_individual", array("id" => $value->id));
                        $redica->status = 3;
                        if ($podtip == 3) {
                            $redica->points = 1;
                        } else {
                            $redica->points = 2;
                        }

                        $DB->update_record("ontology_individual_property_individual", $redica);
                    } else {
                        if ($value->status == 4 && $value->points == 0) {
                            echo "<tr>";
                            echo "<td bgcolor=#FEE7E7>";
                            echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                            echo "</td>";
                            echo "</tr>";
                        } else {
                            if ($tip == '8' && $red == $value->id) {
                                echo "<tr>";
                                echo "<td bgcolor=#FEE7E7>";
                                echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                                echo "</td>";
                                echo "</tr>";
                                $redica = $DB->get_record("ontology_individual_property_individual", array("id" => $value->id));
                                $redica->status = 4;
                                $redica->points = 0;
                                $DB->update_record("ontology_individual_property_individual", $redica);
                            } else {
                                if ($value->status == 4 && $value->points < 0) {
                                    echo "<tr>";
                                    echo "<td bgcolor=#FEE7E7>";
                                    echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                                    echo "</td>";
                                    echo "</tr>";
                                } else {
                                    if ($tip == '9' && $red == $value->id) {
                                        echo "<tr>";
                                        echo "<td bgcolor=#FEE7E7>";
                                        echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                                        echo "</td>";
                                        echo "</tr>";
                                        $redica = $DB->get_record("ontology_individual_property_individual", array("id" => $value->id));
                                        $redica->status = 4;
                                        $redica->points = -2;
                                        $DB->update_record("ontology_individual_property_individual", $redica);
                                    } else {
                                        echo "<tr>";
                                        echo "<td>";
                                        echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_oproperty($value->ontology_propertyid) . ' ' . get_name_of_individual($value->ontology_individualid2);
                                        echo ' <img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b2_poz(0,' . $value->id . ')">&nbsp';
                                        echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b2_poz(3,' . $value->id . ')">&nbsp';
                                        echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b2_odb(' . $value->id . ')">&nbsp';
                                        echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b2_neg(' . $value->id . ')">&nbsp';
                                        echo "</td>";
                                        echo "</tr>";
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

                <input type="hidden" id="osvojstvoid"  value="">
                <input type="hidden" id="individualid2" value="">


            </td>
            <td>
                <b> <?php echo get_string('Dproperties', 'ontology') . ':'; ?> </b> <br />
                    <?php
                    if (count($dproperties2) == 0) {
                        echo get_string('No_dproperties', 'ontology');
                        echo "<br />";
                    } else {
                        ?>
                    <table>
                        <?php
                        foreach ($dproperties2 as $key => $value) {
                            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                                echo "<tr>";
                                echo "<td bgcolor=#E0F4D7>";
                                echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                echo "</td>";
                                echo "</tr>";
                            } else {
                                if ($tip == '10' && $red == $value->id) {
                                    echo "<tr>";
                                    echo "<td bgcolor=#E0F4D7>";
                                    echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                    echo "</td>";
                                    echo "</tr>";
                                    $redica = $DB->get_record("ontology_individual_property_data", array("id" => $value->id));
                                    $redica->status = 3;
                                    if ($podtip == 4) {
                                        $redica->points = 1;
                                    } else {
                                        $redica->points = 2;
                                    }

                                    $DB->update_record("ontology_individual_property_data", $redica);
                                } else {
                                    if ($value->status == 4 && $value->points == 0) {
                                        echo "<tr>";
                                        echo "<td bgcolor=#FEE7E7>";
                                        echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                        echo "</td>";
                                        echo "</tr>";
                                    } else {
                                        if ($tip == '11' && $red == $value->id) {
                                            echo "<tr>";
                                            echo "<td bgcolor=#FEE7E7>";
                                            echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                            echo "</td>";
                                            echo "</tr>";
                                            $redica = $DB->get_record("ontology_individual_property_data", array("id" => $value->id));
                                            $redica->status = 4;
                                            $redica->points = 0;
                                            $DB->update_record("ontology_individual_property_data", $redica);
                                        } else {
                                            if ($value->status == 4 && $value->points < 0) {
                                                echo "<tr>";
                                                echo "<td bgcolor=#FEE7E7>";
                                                echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                                echo "</td>";
                                                echo "</tr>";
                                            } else {
                                                if ($tip == '12' && $red == $value->id) {
                                                    echo "<tr>";
                                                    echo "<td bgcolor=#FEE7E7>";
                                                    echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                                    echo "</td>";
                                                    echo "</tr>";
                                                    $redica = $DB->get_record("ontology_individual_property_data", array("id" => $value->id));
                                                    $redica->status = 4;
                                                    $redica->points = -2;
                                                    $DB->update_record("ontology_individual_property_data", $redica);
                                                } else {
                                                    echo "<tr>";
                                                    echo "<td>";
                                                    echo get_name_of_individual($value->ontology_individualid) . ' ' . get_name_of_dproperty($value->ontology_propertyid) . ' ' . $value->data;
                                                    echo ' <img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b3_poz(0,' . $value->id . ')">&nbsp';
                                                    echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b3_poz(4,' . $value->id . ')">&nbsp';
                                                    echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b3_odb(' . $value->id . ')">&nbsp';
                                                    echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b3_neg(' . $value->id . ')">&nbsp';
                                                    echo "</td>";
                                                    echo "</tr>";
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
                <input type="hidden" id="dsvojstvoid"  value="">
                <input type="hidden" id="individualid3" value="">
            </td>
        </tr>
    </table> 



</div>


