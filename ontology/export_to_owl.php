<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module
$PAGE->requires->js('/mod/ontology/js/jquery-1.5.1.min.js', true);
$PAGE->requires->js('/mod/ontology/js/jquery-ui-1.8.14.custom.min.js', true);
$PAGE->requires->css('/mod/ontology/css/redmond/jquery-ui-1.8.14.custom.css', true);
if ($id) {
    $cm = get_coursemodule_from_id('ontology', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $ontology = $DB->get_record('ontology', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $ontology = $DB->get_record('ontology', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $ontology->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('ontology', $ontology->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

add_to_log($course->id, 'ontology', 'view', "view.php?id=$cm->id", $ontology->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/ontology/view.php', array('id' => $cm->id));
$PAGE->set_title($ontology->name);
$PAGE->set_heading($course->shortname);
$PAGE->set_button(update_module_button($cm->id, $course->id, get_string('modulename', 'ontology')));

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
echo '<div class="ui-widget-header">';
echo $OUTPUT->heading('Запишување во owl');
echo '</div>';
?>
<script>
    $(function() {
        $("input:button").button();
        $("input:text").button();
        $( "a",".links").button();
    });
</script>
<?php
echo '<br/>' . get_string('get_exported_owl', 'ontology') . '<br/><br/>';
echo '<textarea readonly="readonly" cols="100" rows="20">' . writeOWL() . '</textarea>';

function writeOWL() {
    global $DB;
    $res = '<?xml version="1.0"?>' . "\n";
    $res = $res . '<!DOCTYPE rdf:RDF [' . "\n";
    $res = $res . '<!ENTITY owl "http://www.w3.org/2002/07/owl#" >' . "\n";
    $res = $res . '<!ENTITY xsd "http://www.w3.org/2001/XMLSchema#" >' . "\n";
    $res = $res . '<!ENTITY rdfs "http://www.w3.org/2000/01/rdf-schema#" >' . "\n";
    $res = $res . '<!ENTITY rdf "http://www.w3.org/1999/02/22-rdf-syntax-ns#" >' . "\n";
    $res = $res . ']>' . "\n";
    $res = $res . '<rdf:RDF' . "\n";
    $res = $res . 'xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"' . "\n";
    $res = $res . 'xmlns:owl="http://www.w3.org/2002/07/owl#"' . "\n";
    $res = $res . 'xmlns:xsd="http://www.w3.org/2001/XMLSchema#"' . "\n";
    $res = $res . 'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">' . "\n";

    $res = $res . '<owl:Ontology rdf:about=""/>' . "\n";

    $id = $_GET['id'];
    $ontid = $DB->get_record('course_modules', array('id' => $id));
    $site_mid = $DB->get_records('course_modules', array('instance' => $ontid->instance));
    $res = $res . '<!-- Classes -->' . "\n";
    foreach ($site_mid as $tmp) {
        $classes = $DB->get_records('ontology_class', array('course_modulesid' => $tmp->id, 'status' => '2'));

        foreach ($classes as $class) {
            if ($class->name == "Основна")
                $res = $res . "<owl:Class rdf:about=\"&owl;Thing\"> \n";
            else
                $res = $res . "<owl:Class rdf:about=\"#" . $class->name . "\"> \n";

            $disjS = $DB->get_records('ontology_class_expression', array('type' => 3, 'status' => 2, 'ontology_classid' => $class->id));
            foreach ($disjS as $disj) {
                $res = $res . "<owl:disjointWith> \n";
                $res = $res . get_xml_from_expression($disj->expression) . "\n";
                $res = $res . "</owl:disjointWith> \n";
            }

            $ekviS = $DB->get_records('ontology_class_expression', array('type' => 2, 'status' => 2, 'ontology_classid' => $class->id));
            foreach ($ekviS as $ekvisj) {
                $res = $res . "<owl:equivalentClass> \n";
                $res = $res . get_xml_from_expression($ekvisj->expression) . "\n";
                $res = $res . "</owl:equivalentClass> \n";
            }

            if ($class->superclass != 0) {
                $imesup = $DB->get_record('ontology_class', array('id' => $class->superclass));
                if ($imesup->name == "Основна")
                    $res = $res . "<rdfs:subClassOf rdf:resource=\"&owl;Thing\"/>\n";
                else
                    $res = $res . "<rdfs:subClassOf rdf:resource=\"#" . $imesup->name . "\"/>\n";
            }

            $superS = $DB->get_records('ontology_class_expression', array('type' => 1, 'status' => 2, 'ontology_classid' => $class->id));
            foreach ($superS as $super) {
                $res = $res . "<rdfs:subClassOf> \n";
                $res = $res . get_xml_from_expression($super->expression) . "\n";
                $res = $res . '</rdfs:subClassOf>' . "\n";
            }

            $res = $res . "</owl:Class> \n";
        }
    }
    $res = $res . '<!-- Object Properties -->' . "\n";
    foreach ($site_mid as $tmp) {
        $properties = $DB->get_records('ontology_property_individual', array('course_modulesid' => $tmp->id, 'status' => '2'));

        foreach ($properties as $property) {
            if ($property->name != "Основно") {
                $res = $res . "<owl:ObjectProperty rdf:about=\"#" . $property->name . "\"> \n";

                $domainexp = $DB->get_records('ontology_property_expression', array('type' => 1, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($domainexp as $domain) {
                    $res = $res . "<rdfs:domain> \n";
                    $res = $res . get_xml_from_expression($domain->expression) . "\n";
                    $res = $res . '</rdfs:domain>' . "\n";
                }

                $rangexp = $DB->get_records('ontology_property_expression', array('type' => 2, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($rangexp as $rang) {
                    $res = $res . "<rdfs:range \n";
                    $res = $res . get_xml_from_expression($rang->expression) . "\n";
                    $res = $res . '</rdfs:range>' . "\n";
                }

                $disjS = $DB->get_records('ontology_property_disjoint', array('type' => 1, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($disjS as $disj) {
                    $property2 = $DB->get_record('ontology_property_individual', array('id' => $disj->ontology_propertyid2));
                    $res = $res . "<owl:propertyDisjointWith rdf:resource=\"#" . $property2->name . "\"/>\n";
                }

                $ekviS = $DB->get_records('ontology_property_equivalent', array('type' => 1, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($ekviS as $ekvisj) {
                    $property2 = $DB->get_record('ontology_property_individual', array('id' => $ekvisj->ontology_propertyid2));
                    $res = $res . "<owl:equivalentProperty rdf:resource=\"#" . $property2->name . "\"/>\n";
                }

                if ($property->superproperty != 0) {
                    $imesup = $DB->get_record('ontology_property_individual', array('id' => $property->superproperty));
                    if ($imesup->name != 'Основно')
                        $res = $res . "<rdfs:subPropertyOf rdf:resource=\"#" . $imesup->name . "\"/>\n";
                }

                if ($property->inverse != 0) {
                    $imesup = $DB->get_record('ontology_property_individual', array('id' => $property->inverse));
                    $res = $res . "<owl:inverseOf rdf:resource=\"#" . $imesup->name . "\"/>\n";
                }
                $atributi = substr('0000000' . $property->attributes, -7);
                if ($atributi[0] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;FunctionalProperty"/>' . "\n";
                if ($atributi[1] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;InverseFunctionalProperty"/>' . "\n";
                if ($atributi[2] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;TransitiveProperty"/>' . "\n";
                if ($atributi[3] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;SymmetricProperty"/>' . "\n";
                if ($atributi[4] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;AsymmetricProperty"/>' . "\n";
                if ($atributi[5] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;ReflexiveProperty"/>' . "\n";
                if ($atributi[6] == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;IrreflexiveProperty"/>' . "\n";

                $res = $res . "</owl:ObjectProperty> \n";
            }
        }
    }
    $res = $res . '<!-- Data Properties -->' . "\n";
    foreach ($site_mid as $tmp) {
        $properties = $DB->get_records('ontology_property_data', array('course_modulesid' => $tmp->id, 'status' => '2'));

        foreach ($properties as $property) {
            if ($property->name != "Основно") {
                $res = $res . "<owl:DatatypeProperty rdf:about=\"#" . $property->name . "\"> \n";
                $domainexp = $DB->get_records('ontology_property_expression', array('type' => 3, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($domainexp as $domain) {
                    $res = $res . "<rdfs:domain \n";
                    $res = $res . get_xml_from_expression($domain->expression) . "\n";
                    $res = $res . '</rdfs:domain>' . "\n";
                }

                if ($property->rang != "") {
                    $res = $res . '<rdfs:range rdf:resource="&xsd;' . $property->rang . '"/>' . "\n";
                }

                $disjS = $DB->get_records('ontology_property_disjoint', array('type' => 2, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($disjS as $disj) {
                    $property2 = $DB->get_record('ontology_property_data', array('id' => $disj->ontology_propertyid2));
                    $res = $res . "<owl:propertyDisjointWith rdf:resource=\"#" . $property2->name . "\"/>\n";
                }

                $ekviS = $DB->get_records('ontology_property_equivalent', array('type' => 2, 'status' => 2, 'ontology_propertyid' => $property->id));
                foreach ($ekviS as $ekvisj) {
                    $property2 = $DB->get_record('ontology_property_data', array('id' => $ekvisj->ontology_propertyid2));
                    $res = $res . "<owl:equivalentProperty rdf:resource=\"#" . $property2->name . "\"/>\n";
                }

                if ($property->superproperty != 0) {
                    $imesup = $DB->get_record('ontology_property_data', array('id' => $property->superproperty));
                    if ($imesup->name != 'Основно')
                        $res = $res . "<rdfs:subPropertyOf rdf:resource=\"#" . $imesup->name . "\"/>\n";
                }
                if ($property->attributes == '1')
                    $res = $res . '<rdf:type rdf:resource="&owl;FunctionalProperty"/>' . "\n";

                $res = $res . "</owl:DatatypeProperty> \n";
            }
        }
    }
    $res = $res . '<!--  Individuals --> ' . "\n";
    foreach ($site_mid as $tmp) {
        $individuals = $DB->get_records('ontology_individual', array('course_modulesid' => $tmp->id, 'status' => '2'));

        foreach ($individuals as $individual) {
            $res = $res . '<owl:Thing rdf:about="#' . $individual->name . '">' . "\n";
            $properties = $DB->get_records('ontology_individual_property_individual', array('status' => 2, 'ontology_individualid' => $individual->id));
            foreach ($properties as $property) {
                $imesup = $DB->get_record('ontology_property_individual', array('id' => $property->ontology_propertyid));
                $imesup2 = $DB->get_record('ontology_individual', array('id' => $property->ontology_individualid2));
                $res = $res . '<' . $imesup->name . 'rdf:resource="#' . $imesup2->name . '"/>' . "\n";
            }
            $properties = $DB->get_records('ontology_individual_property_data', array('status' => 2, 'ontology_individualid' => $individual->id));
            foreach ($properties as $property) {
                $imesup = $DB->get_record('ontology_property_data', array('id' => $property->ontology_propertyid));
                $res = $res . '<' . $imesup->name . '>' . "\n";
                $res = $res . $property->data . "\n";
                $res = $res . '</' . $imesup->name . '>' . "\n";
            }
            $res = $res . '</owl:Thing>' . "\n";
        }
    }

    $res = $res . '</rdf:RDF>' . "\n";
    return $res;
}

function validExpression($niza) {
    $lim = count($niza) - 1;
    $parehthessis_S = 0;
    $parehthessis_L = 0;
    $in = false;
    for ($i = 1; $i < $lim; $i++) {
        //if on top level there is a closing of parentesis
        //there must be an error
        if ($niza[$i] == "{")
            $parehthessis_L++;
        else if ($niza[$i] == "(")
            $parehthessis_S++;
        else if ($niza[$i] == "}")
            $parehthessis_L--;
        else if ($niza[$i] == ")")
            $parehthessis_S--;
        if ($parehthessis_S < 0 || $parehthessis_L < 0)
            return false;
    }//except when they end with different numbers
    return ($parehthessis_S == 0 && $parehthessis_L == 0);
}

function get_xml_from_expression($expression) {
    //    echo "case:".$expression."\n";
    $niza = explode(" ", $expression);
    //    special case, when the first and last item of the array are open and closed parenthessis
    $count = count($niza);
    $len = strlen($expression);
    if ($niza[0] == "(" && $niza[$count - 1] == ")") {
        //      echo "P ";
        if (validExpression($niza)) {
            //          echo "in ".substr($expression,-$len+2,$len-4)."\n";
            return get_xml_from_expression(substr($expression, -$len + 2, $len - 4));
        }
    }
    if ($niza[$count - 1] == "$") {
        return get_xml_from_expression(substr($expression, 0, $len - 2));
    }

    $return = "";
    $operator = "none";
    $parehthessis_S = 0;
    $parehthessis_L = 0;
    $operand_L = "";
    $operand_R = "";

    for ($i = 0; $i < $count; $i++) {
        if ($parehthessis_L == 0 && $parehthessis_S == 0) {
            if ($niza[$i] == "and" || $niza[$i] == "or") {
                if ($operator != "none") {
                    $operand_L = $operand_L . " " . $operator . " " . $operand_R;
                    $operand_R = "";
                }
                $operator = $niza[$i];
            } else {
                if ($operator == "none") {
                    $operand_L = $operand_L . " " . $niza[$i];
                } else {
                    $operand_R = $operand_R . " " . $niza[$i];
                }
            }
            if ($niza[$i] == "{")
                $parehthessis_L++;
            else if ($niza[$i] == "(")
                $parehthessis_S++;
        }else {
            while ($parehthessis_L > 0 || $parehthessis_S > 0) {

                if ($operator == "none") {
                    $operand_L = $operand_L . " " . $niza[$i];
                } else {
                    $operand_R = $operand_R . " " . $niza[$i];
                }


                if ($niza[$i] == "{")
                    $parehthessis_L++;
                elseif ($niza[$i] == "(")
                    $parehthessis_S++;
                elseif ($niza[$i] == "}")
                    $parehthessis_L--;
                elseif ($niza[$i] == ")")
                    $parehthessis_S--;

                $i++;
            }
            $i--;
        }
    }
    $operand_L = trim($operand_L);
    $operand_R = trim($operand_R);

    //    echo $operator."\n";
    //the bottom case of the recursion
    if ($operator == "none") {
        $return = get_xml_leaf_from_expression(trim($expression));
    } elseif ($operator == "and") {
        $return = "<owl:Class>\n<owl:intersectionOf rdf:parseType=\"Collection\">\n";
        $return = $return . get_xml_from_expression($operand_L) . "\n" . get_xml_from_expression($operand_R);
        $return = $return . "</owl:intersectionOf>\n</owl:Class>\n";
    } elseif ($operator == "or") {
        $return = "<owl:Class>\n<owl:unionOf rdf:parseType=\"Collection\">\n";
        $return = $return . get_xml_from_expression($operand_L) . "\n" . get_xml_from_expression($operand_R);
        $return = $return . "</owl:unionOf>\n</owl:Class>\n";
    }

    return $return;
    //     $return=$return."\n\n\n<owl:Class>\n\n<owl:intersectionOf rdf:parseType=\"Collection\">\n";
}

function get_xml_leaf_from_expression($expression) {
    global $DB;
    $zborovi = array();
    $zborovi = explode(" ", $expression);
    if (count($zborovi) == 0)
        return "";
    $first = $zborovi[0];
    $type;
    //  echo 'ova e first:'.$expression.'\n';
    if (!($first[0] == "^" || $first[0] == "{")) {
        $first = $zborovi[1];
        $j = 1;
        $begin = '<owl:Class>' . "\n" . '<owl:complementOf>."\n"';
        $end = '</owl:complementOf>' . "\n" . '</owl:Class>' . "\n";
        if ($zborovi[1] == "(") {
            $tmp = $zborovi[1];
            for ($i = 2; $i < count($zborovi); $i++)
                $tmp = $tmp . ' ' . $zborovi[$i];
            return $begin . get_xml_from_expression($tmp) . $end;
        }
    } else {
        $begin = '';
        $end = '';
        $j = 0;
    }
    if ($first[0] == "{") {
        $type = 2;
    } else if ($first[1] == "k") {
        $type = 1;
    } else if ($first[1] == "o") {
        $type = 3;
    } else {
        $type = 4;
    }

    if ($type == 1) {
        //klasa
        $class = $DB->get_record('ontology_class', array('id' => substr($first, 2 - strlen($first))));
        if ($class->name == "Основна")
            return $begin . '<rdf:Description rdf:about="&owl;Thing"/>' . "\n" . $end;
        else
            return $begin . '<rdf:Description rdf:about="#' . $class->name . '"/>' . "\n" . $end;
    }
    else if ($type == 2) {
        //mnoz instanci
        $str = '<owl:Class>' . "\n" . '<owl:oneOf rdf:parseType="Collection">' . "\n";
        for ($i = 1 + $j; $i < count($zborovi) - 1; $i++) {
            $instanca = $DB->get_record('ontology_individual', array('id' => substr($zborovi[$i], 2 - strlen($zborovi[$i]))));
            $str = $str . '<rdf:Description rdf:about="#' . $instanca->name . '"/>' . "\n";
        }
        $str = $str . "</owl:oneOf> \n </owl:Class> \n";
        return $begin . $str . $end;
    } else if ($type == 3) {
        $svojstvo = $DB->get_record("ontology_property_individual", array('id' => substr($first, 2 - strlen($first))));
        $str = '<owl:Restriction>' . "\n" . '<owl:onProperty rdf:resource="#' . $svojstvo->name . '"/>' . "\n";
        if ($zborovi[1 + $j] == "min") {
            $str = $str . '<owl:minCardinality rdf:datatype="&xsd;nonNegativeInteger"> ' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:minCardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "max") {
            $str = $str . '<owl:maxCardinality rdf:datatype="&xsd;nonNegativeInteger">' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:maxCardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "exactly") {
            $str = $str . '<owl:cardinality rdf:datatype="&xsd;nonNegativeInteger">' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:cardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "value") {
            $instanca = $DB->get_record('ontology_individual', array('id' => substr($zborovi[2], 2 - strlen($zborovi[2]))));
            $str = $str . '<owl:hasValue rdf:resource="#' . $instanca->name . '"/>' . "\n";
        } else if ($zborovi[1 + $j] == "some") {
            $tmp = $zborovi[2 + $j];
            for ($i = 3 + $j; $i < count($zborovi); $i++) {
                $tmp = $tmp . ' ' . $zborovi[$i];
            }
            $str = $str . '<owl:someValuesFrom>' . "\n" . '<owl:Class>' . "\n";
            $str = $str . get_xml_from_expression($tmp);
            $str = $str . '</owl:Class>' . "\n" . '</owl:someValuesFrom>' . "\n";
        } else if ($zborovi[1 + $j] == "only") {
            $tmp = $zborovi[2 + $j];
            for ($i = 3 + $j; $i < count($zborovi); $i++) {
                $tmp = $tmp . ' ' . $zborovi[$i];
            }
            $str = $str . '<owl:allValuesFrom>' . "\n" . '<owl:Class>' . "\n";
            $str = $str . get_xml_from_expression($tmp);
            $str = $str . '</owl:Class>' . "\n" . '</owl:allValuesFrom>' . "\n";
        }
        $str = $str . '</owl:Restriction>' . "\n";
        return $begin . $str . $end;
    } else {
        $svojstvo = $DB->get_record("ontology_property_data", array('id' => substr($first, 2 - strlen($first))));
        $str = '<owl:Restriction>' . "\n" . '<owl:onProperty rdf:resource="#' . $svojstvo->name . '"/>' . "\n";
        if ($zborovi[1 + $j] == "min") {
            $str = $str . '<owl:minCardinality rdf:datatype="&xsd;nonNegativeInteger">' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:minCardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "max") {
            $str = $str . '<owl:maxCardinality rdf:datatype="&xsd;nonNegativeInteger">' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:maxCardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "exactly") {
            $str = $str . '<owl:cardinality rdf:datatype="&xsd;nonNegativeInteger">' . "\n" . substr($zborovi[2], 2) . "\n" . '</owl:cardinality>' . "\n";
        } else if ($zborovi[1 + $j] == "value") {

            $str = $str . '<owl:hasValue rdf:datatype="&xsd;' . $zborovi[3 + $j] . '">' . "\n" . substr($zborovi[2 + $j], 2) . "\n" . '</owl:hasValue>' . "\n";
        } else if ($zborovi[1 + $j] == "some") {
            $str = $str . '<owl:someValuesFrom>' . "\n" . '<rdf:Description>' . "\n" . '<rdf:type rdf:resource="&rdfs;Datatype"/>' . "\n" . '<owl:onDatatype rdf:resource="&xsd;' . $svojstvo->rang . '"/>' . "\n";
            if (count($zborovi) >= (4 + $j)) {
                $str = $str . '<owl:withRestrictions rdf:parseType="Collection">' . "\n";
                for ($i = 4 + $j; $i < count($zborovi) - 1; $i = $i + 2) {
                    $str = $str . '<rdf:Description>' . "\n";
                    if ($zborovi[$i] == '<')
                        $str = $str . '<xsd:minExclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . '</xsd:minExclusive>' . "\n";
                    else if ($zborovi[$i] == '>')
                        $str = $str . '<xsd:maxExclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . '</xsd:maxExclusive>' . "\n";
                    else if ($zborovi[$i] == '>=')
                        $str = $str . '<xsd:maxInclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . '</xsd:maxInclusive>' . "\n";
                    else if ($zborovi[$i] == '>')
                        $str = $str . '<xsd:minInclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . '</xsd:minInclusive>' . "\n";
                    else if ($zborovi[$i] == 'lenght')
                        $str = $str . '<xsd:length rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:length>' . "\n";
                    else if ($zborovi[$i] == 'maxlenght')
                        $str = $str . '<xsd:maxLength rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:maxLength>' . "\n";
                    else if ($zborovi[$i] == 'minlenght')
                        $str = $str . '<xsd:minLength rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:minLength>' . "\n";
                    else if ($zborovi[$i] == 'totalDigits')
                        $str = $str . '<xsd:totalDigits rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:totalDigits>' . "\n";
                    $str = $str . '</rdf:Description>' . "\n";
                }
                $str = $str . '</owl:withRestrictions>' . "\n";
            }
            $str = $str . '</rdf:Description>' . "\n" . '</owl:someValuesFrom>' . "\n";
        }
        else if ($zborovi[1 + $j] == "only") {
            $str = $str . '<owl:allValuesFrom>' . "\n" . '<rdf:Description>' . "\n" . '<rdf:type rdf:resource="&rdfs;Datatype"/>' . "\n" . '<owl:onDatatype rdf:resource="&xsd;' . $svojstvo->rang . '"/>' . "\n";
            if (count($zborovi) >= (4 + $j)) {
                $str = $str . '<owl:withRestrictions rdf:parseType="Collection">' . "\n";
                for ($i = 4 + $j; $i < count($zborovi) - 1; $i = $i + 2) {
                    $str = $str . '<rdf:Description>' . "\n";
                    if ($zborovi[$i] == '<')
                        $str = $str . '<xsd:minExclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:minExclusive>' . "\n";
                    else if ($zborovi[$i] == '>')
                        $str = $str . '<xsd:maxExclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:maxExclusive>' . "\n";
                    else if ($zborovi[$i] == '>=')
                        $str = $str . '<xsd:maxInclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:maxInclusive>' . "\n";
                    else if ($zborovi[$i] == '>')
                        $str = $str . '<xsd:minInclusive rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:minInclusive>' . "\n";
                    else if ($zborovi[$i] == 'lenght')
                        $str = $str . '<xsd:length rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:length>' . "\n";
                    else if ($zborovi[$i] == 'maxlenght')
                        $str = $str . '<xsd:maxLength rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:maxLength>' . "\n";
                    else if ($zborovi[$i] == 'minlenght')
                        $str = $str . '<xsd:minLength rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:minLength>' . "\n";
                    else if ($zborovi[$i] == 'totalDigits')
                        $str = $str . '<xsd:totalDigits rdf:datatype="&xsd;integer">' . "\n" . substr($zborovi[$i + 1], 2) . "\n" . '</xsd:totalDigits>' . "\n";
                    $str = $str . '</rdf:Description>' . "\n";
                }
                $str = $str . '</owl:withRestrictions>' . "\n";
            }
            $str = $str . '</rdf:Description>' . "\n" . '</owl:allValuesFrom>' . "\n";
        }
        $str = $str . '</owl:Restriction>' . "\n";
        return $begin . $str . $end;
    }
}
?>
<script type="text/javascript">
    function nazad()
    {
        window.location= <?php echo "\"view.php?id=" . $id . "\"" ?>
    }
</script>
<br />
<input type="button" onclick="nazad();" value="<?php echo get_string('Back', 'ontology'); ?>"  style="width: 230px;"/>
<?php
// Finish the page
echo $OUTPUT->footer();
?>