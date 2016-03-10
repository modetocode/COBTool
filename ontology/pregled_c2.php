<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
global $DB;
$id = $_GET["id"];
$tip = $_GET["tip"];
$podtip = $_GET["podtip"];
$red = $_GET["red"];
$u_id = $_GET["userid"];
$m_id = $_GET["moduleid"];
$class = $DB->get_record("ontology_class", array("id" => $id));
//$superclass=$DB->get_records("ontology_class_expression",array("ontology_classid"=>$id,"type"=>"1","status"=>"2"));
$superclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "1", "userid" => $u_id, "course_modulesid" => $m_id));
//$equivalentclass=$DB->get_records("ontology_class_expression",array("ontology_classid"=>$id,"type"=>"2","status"=>"2"));
$equivalentclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "2", "userid" => $u_id, "course_modulesid" => $m_id));
//$disjointclass=$DB->get_records("ontology_class_expression",array("ontology_classid"=>$id,"type"=>"3","status"=>"2"));
$disjointclass2 = $DB->get_records("ontology_class_expression", array("ontology_classid" => $id, "type" => "3", "userid" => $u_id, "course_modulesid" => $m_id));
?>
<div id="Region">

    <?php
    if ($class->id == 1) {
        echo '<table>';
        echo '<tr>';
        echo '<td>';
        echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
        echo '</td>';
        echo "<td>";
        echo '<span style="color: #FF9D00;">';
        echo $class->name;
        echo '</span>';
        echo "</td>";
    } else {
        if ($class->status == 2 || $class->status == 3 || $class->status == 5) {
            echo '<table bgcolor=#E0F4D7>';
            echo '<tr>';
            echo '<td>';
            echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
            echo '</td>';
            echo '<td>';
            echo '<span style="color: #FF9D00;">';
            echo $class->name;
            echo '</span>';
            echo '</td>';
        } else {
            if ($tip == '4') {
                echo '<table bgcolor=#E0F4D7>';
                echo '<tr>';
                echo '<td>';
                echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                echo '</td>';
                echo '<td>';
                echo '<span style="color: #FF9D00;">';
                echo $class->name;
                echo '</span>';
                echo '</td>';
                $redica = $DB->get_record("ontology_class", array("id" => $class->id));
                //echo $redica->status;
                $redica->status = 3;
                if ($podtip == 1) {
                    $redica->points = 1;
                } else {
                    $redica->points = 2;
                }
                $DB->update_record("ontology_class", $redica);
            } else {
                if ($class->status == 4 && $class->points == 0) {
                    echo '<table bgcolor=#FEE7E7>';
                    echo '<tr>';
                    echo '<td>';
                    echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                    echo '</td>';
                    echo '<td>';
                    echo '<span style="color: #FF9D00;">';
                    echo $class->name;
                    echo '</span>';
                    echo '</td>';
                } else {
                    if ($tip == '5') {
                        echo '<table bgcolor=#FEE7E7>';
                        echo '<tr>';
                        echo '<td>';
                        echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                        echo '</td>';
                        echo '<td>';
                        echo '<span style="color: #FF9D00;">';
                        echo $class->name;
                        echo '</span>';
                        echo '</td>';
                        $redica = $DB->get_record("ontology_class", array("id" => $class->id));
                        //echo $redica->status;
                        $redica->status = 4;
                        $redica->points = 0;
                        $DB->update_record("ontology_class", $redica);
                    } else {
                        if ($class->status == 4 && $class->points < 0) {
                            echo '<table bgcolor=#FEE7E7>';
                            echo '<tr>';
                            echo '<td>';
                            echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                            echo '</td>';
                            echo '<td>';
                            echo '<span style="color: #FF9D00;">';
                            echo $class->name;
                            echo '</span>';
                            echo '</td>';
                        } else {
                            if ($tip == '6') {
                                echo '<table bgcolor=#FEE7E7>';
                                echo '<tr>';
                                echo '<td>';
                                echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                                echo '</td>';
                                echo '<td>';
                                echo '<span style="color: #FF9D00;">';
                                echo $class->name;
                                echo '</span>';
                                echo '</td>';
                                $redica = $DB->get_record("ontology_class", array("id" => $class->id));
                                //echo $redica->status;
                                $redica->status = 4;
                                $redica->points = -2;
                                $DB->update_record("ontology_class", $redica);
                            } else {
                                echo '<table>';
                                echo '<tr>';
                                echo '<td>';
                                echo '<b>' . get_string('Class_name', 'ontology') . ':</b>';
                                echo '</td>';
                                echo '<td>';
                                echo '<span style="color: #FF9D00;">';
                                echo $class->name . ' ';
                                echo '</span>';
                                echo '<img src="Check-icon.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(0)">&nbsp';
                                echo '<img src="Check-icon2.png" style="width:10; height:10px;cursor: hand;"  NAME="check" onclick="b1_poz(1)">&nbsp';
                                echo '<img src="Delete-icon.png" style="width:10; height:10px;cursor: hand;" NAME="delete" onclick="b1_odb()">&nbsp';
                                echo '<img src="minus-icon.png" style="width:10; height:10px;cursor: hand;" NAME="minus" onclick="b1_neg()">&nbsp';
                                echo '</td>';
                            }
                        }
                    }
                }
            }
        }
    }
    ?>

</tr>
<tr>
    <td>
        <b> <?php echo get_string('Class_description', 'ontology') . ':'; ?> </b>  
    </td>
    <td>
    <?php
    if ($class->description == "")
        echo get_string('No_description', 'ontology');
    else {
        echo $class->description;
    }
    ?> 
    </td>
</tr>
</table>
<hr />




<b> <?php echo get_string('Superclasses', 'ontology') . ':'; ?> </b><br />
<table>
    <?php
    if (count($superclass2) == 0) {
        echo '<tr>';
        echo '<td>';
        echo get_string('No_superclasses', 'ontology');
        echo "<br />";
        echo '</td>';
        echo '</tr>';
    } else {

        foreach ($superclass2 as $key => $value) {

            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                echo '<tr>';
                echo '<td bgcolor=#E0F4D7>';
                get_expression_in_color($value->expression);
                echo '</td>';
                echo '</tr>';
            } else {
                if ($tip == '1' && $value->id == $red) {
                    echo '<tr>';
                    echo '<td  bgcolor=#E0F4D7>';
                    get_expression_in_color($value->expression);
                    echo '</td>';
                    echo '</tr>';
                    $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                    $redica->status = 3;
                    if ($podtip == 2) {
                        $redica->points = 1;
                    } else {
                        $redica->points = 2;
                    }
                    $DB->update_record("ontology_class_expression", $redica);
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
                        if ($tip == '2' && $value->id == $red) {
                            echo '<tr>';
                            echo '<td  bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                            $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                            $redica->status = 4;
                            $redica->points = 0;
                            $DB->update_record("ontology_class_expression", $redica);
                        } else {

                            if ($tip == '3' && $value->id == $red) {
                                echo '<tr>';
                                echo '<td  bgcolor=#FEE7E7>';
                                get_expression_in_color($value->expression);
                                echo '</td>';
                                echo '</tr>';
                                $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                                $redica->status = 4;
                                $redica->points = -2;
                                $DB->update_record("ontology_class_expression", $redica);
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
<!--<br />
<input type="button" id="dodavanje1" value="Додади нова суперкласа" onClick="execute_ajax1();"/>
<input type="button" id="brishi1" value="Откажи" onClick="izbrishi();" style="visibility: hidden;"/>-->
<div id="Region1">
    <input type="hidden" id="steklista1" />
</div>
<input type="hidden" id="tekstlista1" />
<input type="hidden" id="inputlista1" />
<br /> <b> <?php echo get_string('Equivalent_Classes', 'ontology') . ':'; ?> </b> <br />
<table>
    <?php
    if (count($equivalentclass2) == 0) {
        echo get_string('No_equivalent_classes', 'ontology');
        echo "<br />";
    } else {

        foreach ($equivalentclass2 as $key => $value) {
            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                echo '<tr>';
                echo '<td bgcolor=#E0F4D7>';
                get_expression_in_color($value->expression);
                echo '</td>';
                echo '</tr>';
            } else {
                if ($tip == '1' && $value->id == $red) {
                    echo '<tr>';
                    echo '<td bgcolor=#E0F4D7>';
                    get_expression_in_color($value->expression);
                    echo '</td>';
                    echo '</tr>';
                    $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                    $redica->status = 3;
                    if ($podtip == 2) {
                        $redica->points = 1;
                    } else {
                        $redica->points = 2;
                    }
                    $DB->update_record("ontology_class_expression", $redica);
                } else {
                    if ($value->status == 4) {
                        if ($value->points == 0) {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        } else {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        if ($tip == '2' && $value->id == $red) {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                            $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                            $redica->status = 4;
                            $redica->points = 0;
                            $DB->update_record("ontology_class_expression", $redica);
                        } else {

                            if ($tip == '3' && $value->id == $red) {
                                echo '<tr>';
                                echo '<td bgcolor=#FEE7E7>';
                                get_expression_in_color($value->expression);
                                echo '</td>';
                                echo '</tr>';
                                $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                                $redica->status = 4;
                                $redica->points = -2;
                                $DB->update_record("ontology_class_expression", $redica);
                            } else {
                                echo "<tr>";
                                echo "<td>";
                                get_expression_in_color($value->expression);
                                echo "</td>";
                                echo "<td>";
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
<br />
<!--<input type="button" id="dodavanje2" value="Додади нова еквивалентна класа" onClick="execute_ajax2();"/>
<input type="button" id="brishi2" value="Откажи" onClick="izbrishi();" style="visibility: hidden;"/>-->
<div id="Region2">
    <input type="hidden" id="steklista2" />
</div>
<input type="hidden" id="tekstlista2" />
<input type="hidden" id="inputlista2" />


<br /> <b> <?php echo get_string('Disjoint_Classes', 'ontology') . ':'; ?> </b> <br />
<table>
    <?php
    if (count($disjointclass2) == 0) {
        echo get_string('No_disjoint_classes', 'ontology');
        echo "<br />";
    } else {
        foreach ($disjointclass2 as $key => $value) {
            if ($value->status == 2 || $value->status == 3 || $value->status == 5) {
                echo '<tr>';
                echo '<td bgcolor=#E0F4D7>';
                get_expression_in_color($value->expression);
                echo '</td>';
                echo '</tr>';
            } else {
                if ($tip == '1' && $value->id == $red) {
                    echo '<tr>';
                    echo '<td bgcolor=#E0F4D7>';
                    get_expression_in_color($value->expression);
                    echo '</td>';
                    echo '</tr>';
                    $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                    $redica->status = 3;
                    if ($podtip == 2) {
                        $redica->points = 1;
                    } else {
                        $redica->points = 2;
                    }
                    $DB->update_record("ontology_class_expression", $redica);
                } else {
                    if ($value->status == 4) {
                        if ($value->points == 0) {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        } else {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        if ($tip == '2' && $value->id == $red) {
                            echo '<tr>';
                            echo '<td bgcolor=#FEE7E7>';
                            get_expression_in_color($value->expression);
                            echo '</td>';
                            echo '</tr>';
                            $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                            $redica->status = 4;
                            $redica->points = 0;
                            $DB->update_record("ontology_class_expression", $redica);
                        } else {

                            if ($tip == '3' && $value->id == $red) {
                                echo '<tr>';
                                echo '<td bgcolor=#FEE7E7>';
                                get_expression_in_color($value->expression);
                                echo '</td>';
                                echo '</tr>';
                                $redica = $DB->get_record("ontology_class_expression", array("id" => $value->id));
                                $redica->status = 4;
                                $redica->points = -2;
                                $DB->update_record("ontology_class_expression", $redica);
                            } else {
                                echo "<tr>";
                                echo "<td>";
                                get_expression_in_color($value->expression);
                                echo "</td>";
                                echo "<td>";
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
<br />
<!--<input type="button" id="dodavanje3" value="Додади нова дисјунктна класа" onClick="execute_ajax3();"/>
<input type="button" id="brishi3" value="Откажи" onClick="izbrishi();" style="visibility: hidden;"/>-->
<div id="Region3">
    <input type="hidden" id="steklista3" />
</div>
<input type="hidden" id="tekstlista3" />
<input type="hidden" id="inputlista3" />

</div>


