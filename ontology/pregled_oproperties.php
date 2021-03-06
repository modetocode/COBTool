<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Prints a particular instance of newmodule
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_newmodule
 * @copyright 2010 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/// (Replace newmodule with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

//$id = optional_param('id', 0, PARAM_INT); // course_module ID, or

$id = $_GET["id"];
$n = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module
$u_id = $_GET["userid"];
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
?>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ">
            <a style="padding: .5em 1em;" href="pregled_classes.php?id=<?php echo $id; ?>&userid=<?php echo $u_id; ?>"><?php echo get_string('Classes', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a style="padding: .5em 1em;" href="#"><?php echo get_string('Oproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href="pregled_dproperties.php?id=<?php echo $id; ?>&userid=<?php echo $u_id; ?>"><?php echo get_string('Dproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href="pregled_individuals.php?id=<?php echo $id; ?>&userid=<?php echo $u_id; ?>"><?php echo get_string('Individuals', 'ontology'); ?></a>
        </li>
    </ul>

    <?php
    ?>
    <script>
        $(function() {
            $("input:button").button();
        });
    </script>

    <script type="text/javascript">
        function oproperty(field) {
            document.getElementById("opropertyid").value = field.value;
            //document.getElementById("button2").style.visibility = '';
            ajax();
        }

        function dodadi_equsvojstvo() {
            document.getElementById("dodavanjeequsvojstvo").style.visibility="hidden";
            document.getElementById("listaequsvojstvo").style.visibility="visible";
            document.getElementById("brishi2").style.visibility="visible";
        }
        function dodadi_dissvojstvo() {
            document.getElementById("dodavanjedissvojstvo").style.visibility="hidden";
            document.getElementById("listadissvojstvo").style.visibility="visible";
            document.getElementById("brishi3").style.visibility="visible";
        }
        function equsvojstvo(field) {
            document.getElementById("equsvojstvoid").value = field.value;
            //zapishi vo baza
            ajax_write(2);
            //refresh
            ajax();
            setTimeout("ajax()",200);
        }
        function dissvojstvo(field) {
            document.getElementById("dissvojstvoid").value = field.value;
            //zapisi vo baza
            ajax_write(3);
            //refreshiraj
            ajax();
            setTimeout("ajax()",200);
        }
        function ajax()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=0&red=0&podtip=0",true);
            xmlhttp.send();
        }
        function ajax_write(tip)
        {//zapishuvanje vo bazata na podatoci 
            //tip==1 zapishuvanje na fiksnite podatoci za svojstvo
            //tip==2 zapishuvanje na novo ekvivalentno svojstvo
            //tip==3 zapishuvanje na novo disjuntkno svojstvo
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                }
            }
            if (tip==1)
            {
         
                var attributes="";
        
                if (document.getElementById("funkcionalna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0";  
            
                if (document.getElementById("inverznafunkcionalna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0";
            
                if (document.getElementById("tranzitivna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0"; 
            
                if (document.getElementById("simetricna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0"; 
            
                if (document.getElementById("asimetricna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0"; 
            
                if (document.getElementById("refleksivna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0";
            
                if (document.getElementById("irefleksivna").checked)
                    attributes=attributes+"1";
                else
                    attributes=attributes+"0";   
                var moduleid=document.getElementById("moduleid").value;
                var userid=document.getElementById("userid").value;
                var name=document.getElementById("ime").value;
                var description=document.getElementById("opis").value;
                var inverse=document.getElementById("inverzna").value;
                var superproperty=document.getElementById("opropertyid").value;
                xmlhttp.open("GET","insertoproperty.php?tip=1"+"&moduleid="+moduleid+"&userid="+userid+"&attributes="+attributes+"&name="+name+"&description="+description+"&inverse="+inverse+"&superproperty="+superproperty,true);
            
            }
            else
                if (tip==2)
            {
                var propertyid=document.getElementById("opropertyid").value;
                var propertyid2=document.getElementById("equsvojstvoid").value;
                xmlhttp.open("GET","insertoproperty.php?tip=2"+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&propertyid="+propertyid+"&propertyid2="+propertyid2,true);
            }
            else
            {
                var propertyid=document.getElementById("opropertyid").value;
                var propertyid2=document.getElementById("dissvojstvoid").value;
                xmlhttp.open("GET","insertoproperty.php?tip=3"+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&propertyid="+propertyid+"&propertyid2="+propertyid2,true);
            }
        
            xmlhttp.send();
        }
        function execute_ajax1()
        {
            document.getElementById("dodavanje1").style.visibility="hidden";
            document.getElementById("brishi1").style.visibility="visible";
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                }
            }
            var jfunction="lista1";
            var stek = document.getElementById("stek"+jfunction).value;
            var input = document.getElementById("input"+jfunction).value;
            var opropertyid = document.getElementById("opropertyid").value;
            var userid=document.getElementById("userid").value;
            var id=document.getElementById("moduleid").value;
            xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+opropertyid+"&tip=2",true);
            xmlhttp.send();
        }
        function lista11(field)
        { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 1
            if (document.getElementById("inputlista1").value=="")
                document.getElementById("inputlista1").value=field.value;
            else
                document.getElementById("inputlista1").value=document.getElementById("inputlista1").value+" "+field.value;
            var i;
            for(i = 0; i < field.options.length; i++)
                if (field.options[i].selected&&field.value!='$')
            {
                if (document.getElementById("tekstlista1").value=="")
                    document.getElementById("tekstlista1").value=field.options[i].text;
                else
                    document.getElementById("tekstlista1").value=document.getElementById("tekstlista1").value+" "+field.options[i].text;
                break;
            }
            if(field.value=='$')
            { 
                execute_ajax1();
                ajax();
                setTimeout("ajax()",200);
            }
            else{
                execute_ajax1();
                    
            }
    
        }

        function lista12()
        { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
            document.getElementById("inputlista1").value=document.getElementById("inputlista1").value+" ^b"+document.getElementById("broj").value;
            document.getElementById("tekstlista1").value=document.getElementById("tekstlista1").value+" "+document.getElementById("broj").value;
            execute_ajax1();
        }
        function execute_ajax2()
        {
            document.getElementById("dodavanje2").style.visibility="hidden";
            document.getElementById("brishi4").style.visibility="visible";
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region2").innerHTML=xmlhttp.responseText;
                }
            }
            var jfunction="lista2";
            var stek = document.getElementById("stek"+jfunction).value;
            var input = document.getElementById("input"+jfunction).value;
            var opropertyid = document.getElementById("opropertyid").value;
            var userid=document.getElementById("userid").value;
            var id=document.getElementById("moduleid").value;
            xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+opropertyid+"&tip=2",true);
            xmlhttp.send();
        }
        function lista21(field)
        { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 1
            if (document.getElementById("inputlista2").value=="")
                document.getElementById("inputlista2").value=field.value;
            else
                document.getElementById("inputlista2").value=document.getElementById("inputlista2").value+" "+field.value;
            var i;
            for(i = 0; i < field.options.length; i++)
                if (field.options[i].selected&&field.value!='$')
            {
                if (document.getElementById("tekstlista2").value=="")
                    document.getElementById("tekstlista2").value=field.options[i].text;
                else
                    document.getElementById("tekstlista2").value=document.getElementById("tekstlista2").value+" "+field.options[i].text;
                break;
            }
            if(field.value=='$')
            { 
                execute_ajax2();
                ajax();
                setTimeout("ajax()",200);
            }
            else{
                execute_ajax2();
                    
            }
    
        }

        function lista22()
        { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
            document.getElementById("inputlista2").value=document.getElementById("inputlista2").value+" ^b"+document.getElementById("broj").value;
            document.getElementById("tekstlista2").value=document.getElementById("tekstlista2").value+" "+document.getElementById("broj").value;
            execute_ajax1();
        }
        function izbrishi()
        {
            ajax();
        }

        function prikazFormaDodadiOsvojstvo()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET","insertoproperty.php?tip=0&id="+document.getElementById("opropertyid").value,true);
            xmlhttp.send();
        }

        function insertoproperty()
        {
            if (document.getElementById("ime").value=="")
                document.getElementById("errorime").style.visibility="Visible";
            else
            {
                ajax_write(1);
            }  
            ajax1();
        }

        function setInverzna()
        {
            if (document.getElementById("inverznachk").checked)
            {
                document.getElementById("inverzna").style.visibility="Visible";
            }
            else
            {
                document.getElementById("inverzna").style.visibility="Hidden";
            }
        }
        function ajax1()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("forma").innerHTML=xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET","p.php?id="+document.getElementById("moduleid").value,true);
            xmlhttp.send();
        }
        function p()
        {
            location.reload(true);
        }
        function pocetok()
        {
            location.replace("view.php?id="+document.getElementById("moduleid").value);
        }

        function b1_poz(podtip)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                    //nazad();
                }
            }
    
            if(podtip==1)
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=1&red=0&podtip=1",true);
            }
            else
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=1&red=0&podtip=0",true);
            }
            xmlhttp.send();
    
        }
        function b1_odb()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                   // nazad();
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=2&red=0&podtip=0",true);
            xmlhttp.send();
    
        }
        function b1_neg()
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                   // nazad();
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=3&red=0&podtip=0",true);
            xmlhttp.send();
    
        }

        function b_poz(podtip,red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
            if(podtip==2)
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=4&podtip=2&red="+red,true);
            }
            else
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=4&podtip=0&red="+red,true);
            }
            xmlhttp.send();
    
        }
        function b_odb(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=5&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function b_neg(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=6&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function b2_poz(podtip,red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
            if(podtip==3)
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=7&podtip=3&red="+red,true);
            }
            else
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=7&podtip=0&red="+red,true);
            }
            xmlhttp.send();
    
        }
        function b2_odb(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=8&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function b2_neg(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=9&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function b3_poz(podtip,red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
            if(podtip==4)
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=10&podtip=4&red="+red,true);
            }
            else
            {
                xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=10&podtip=0&red="+red,true);
            }
            xmlhttp.send();
    
        }
        function b3_odb(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=11&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function b3_neg(red)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp=new XMLHttpRequest();
            }
            else
            {// code for IE6, IE5
                xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function()
            {
                if (xmlhttp.readyState==4)
                {
                    // document.getElementById("lista").destroy();
                    document.getElementById("Region").innerHTML=xmlhttp.responseText;
                }
            }
    
   
            xmlhttp.open("GET","pregled_oproperty.php?id="+document.getElementById("opropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&tip=12&podtip=0&red="+red,true);
            xmlhttp.send();
    
        }
        function nazad(){
            window.location= <?php echo "\"pregled_oproperties.php?id=" . $id . "&userid=" . $u_id . "\"" ?>
        }
        function return_to_students(){
            window.location= <?php echo "\"teacherstudents.php?id=" . $id . "\"" ?>
        }
    </script>
    <?php
    $u = $DB->get_record("user", array("id" => $u_id));
    ?>
    <form  method="POST">
        <div id="forma"> <br />
            <b style=" padding: 8px;"><?php echo get_string('For_student', 'ontology'); ?>:</b> <?php echo $u->firstname . ' ' . $u->lastname; ?>
            <table>
                <tr>
                    <td valign="top">
                        <b> <?php echo get_string('Oproperty_data', 'ontology'); ?></b> <br/>
                        <?php echo get_string('Choose_oproperty', 'ontology') . ':'; ?> <br/>

                        <?php echo object_property_hierarhy2("oproperties", "oproperty", 0, $id, $u_id, 0); ?> <br/>
                        <input type="hidden" name="opropertyid" id="opropertyid" /> <br/>
                        <input type="hidden" name="userid" value="<?php echo $u_id; ?>" id="userid" />
                        <input type="hidden" name="moduleid" value="<?php echo $id; ?>" id="moduleid"/>
                        <div>
                            <input type="button" value="<?php echo get_string('Back', 'ontology'); ?>" onclick="return_to_students();"/>
                        </div>
                    </td>
                    <td valign="center" >
                        <div id="Region">

                        </div>
                    </td>

                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>

        </div>
    </form>
</div>
<?php
echo $OUTPUT->footer();