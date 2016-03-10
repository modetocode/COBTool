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
if (!is_Teacher())
    studentska_proverka($course, true, $cm);

/// Print the page header

$PAGE->set_url('/mod/ontology/view.php', array('id' => $cm->id));
$PAGE->set_title($ontology->name);
$PAGE->set_heading($course->shortname);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
// Output starts here
echo $OUTPUT->header();
?>
<script>
    $(function() {
        $( "input:button").button();
        $( "a",".links").button();
    });
</script>
<script Language="JavaScript">
    function dproperty(field) {
        document.getElementById("dpropertyid").value = field.value;
        if (document.getElementById("dpropertyid").value!="")
        {
            document.getElementById("button2").style.visibility = '';
            ajax();
        }
    }
    function dproperty1(field){
        document.getElementById("dpropertyid1").value = field.value;
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
        if (document.getElementById("equsvojstvoid").value!="")
        {
            //zapishi vo baza
            ajax_write(2);
            //refresh
            ajax();
            setTimeout("ajax()",200);
        }
    }
    function dissvojstvo(field) {
        document.getElementById("dissvojstvoid").value = field.value;
        if (document.getElementById("dissvojstvoid").value)
        {
            //zapisi vo baza
            ajax_write(3);
            //refreshiraj
            ajax();
            setTimeout("ajax()",200);
        }
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
                setStyle();
                if(document.getElementById('iseditable').value=='1')
                {
                    document.getElementById('button3').style.visibility="";
                }
                else
                {
                    document.getElementById('button3').style.visibility="hidden";
                }
            }
        }
        xmlhttp.open("GET","viewdproperty.php?id="+document.getElementById("dpropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&courseid="+<?php echo $course->id; ?>,true);
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
                // document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                p();
            }
        }
        if (tip==1)
        {
         
            var attributes="";
        
            if (document.getElementById("funkcionalna").checked)
                attributes=attributes+"1";
            else
                attributes=attributes+"0";    
            var moduleid=document.getElementById("moduleid").value;
            var userid=document.getElementById("userid").value;
            var name=encodeURIComponent(document.getElementById("ime").value);
            var description=encodeURIComponent(document.getElementById("opis").value);
            var range=document.getElementById("rang").value;
            var superproperty=document.getElementById("dpropertyid").value;
            document.getElementById("userid").value=superproperty;
            xmlhttp.open("GET","insertdproperty.php?tip=1"+"&moduleid="+moduleid+"&userid="+userid+"&attributes="+attributes+"&name="+name+"&description="+description+"&range="+range+"&superproperty="+superproperty,true);
            
        }
        else
            if (tip==2)
        {
            var propertyid=document.getElementById("dpropertyid").value;
            var propertyid2=document.getElementById("equsvojstvoid").value;
            xmlhttp.open("GET","insertdproperty.php?tip=2"+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&propertyid="+propertyid+"&propertyid2="+propertyid2,true);
        }
        else
        {
            var propertyid=document.getElementById("dpropertyid").value;
            var propertyid2=document.getElementById("dissvojstvoid").value;
            xmlhttp.open("GET","insertdproperty.php?tip=3"+"&moduleid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value+"&propertyid="+propertyid+"&propertyid2="+propertyid2,true);
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
                setStyle();
            }
        }
        var jfunction="lista1";
        var stek = document.getElementById("stek"+jfunction).value;
        var input = document.getElementById("input"+jfunction).value;
        var dpropertyid = document.getElementById("dpropertyid").value;
        var userid=document.getElementById("userid").value;
        var id=document.getElementById("moduleid").value;
        xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+dpropertyid+"&tip=3",true);
        xmlhttp.send();
    
    }
    function lista11(field)
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 1
        if (field.value!="")
        {
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
    }

    function lista12()
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
        document.getElementById("inputlista1").value=document.getElementById("inputlista1").value+" ^b"+document.getElementById("broj").value;
        document.getElementById("tekstlista1").value=document.getElementById("tekstlista1").value+" "+document.getElementById("broj").value;
        execute_ajax1();
    }

    function izbrishi()
    {
        ajax();
    }

    function prikazFormaDodadiPsvojstvo()
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
                setStyle();
            }
        }
        xmlhttp.open("GET","insertdproperty.php?tip=0&id="+document.getElementById("dpropertyid").value,true);
        xmlhttp.send();
    
    }

    function insertdproperty()
    {
        if (document.getElementById("ime").value=="")
            document.getElementById("errorime").style.visibility="Visible";
        else
        {
            ajax_write(1);
        }  
        //  ajax1();
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
                setStyle();
            }
        }
        xmlhttp.open("GET","p.php?id="+document.getElementById("moduleid").value,true);
        xmlhttp.send();
    
    }
    function setStyle()
    {
        $("input:button").button();
    }  
    function p()
    {
        location.reload(true);
    }
    function pocetok()
    {
        location.replace("view.php?id="+document.getElementById("moduleid").value);
    }

    function brisi(tip,red)
    {
        var courseid=document.getElementById("courseid").value;
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
                setStyle();
                if(tip==5)
                    setTimeout("p()",200);
            }
        }
        xmlhttp.open("GET","viewdproperty.php?id="+document.getElementById("dpropertyid").value+"&moduleid="+document.getElementById("moduleid").value+"&tip="+tip+"&red="+red+"&courseid="+courseid,true);
        xmlhttp.send();
        
    }

    function nazad()
    {
        window.location= <?php echo "\"view.php?id=" . $id . "\"" ?>
    }

    function prikazFormaEditPsvojstvo()
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
                setStyle();
            }
        }
        xmlhttp.open("GET","editdproperty.php?tip=0&id="+document.getElementById("dpropertyid").value+"&cmid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value,true);
        xmlhttp.send();
    
    }

    function editdproperty()
    {
        if (document.getElementById("ime").value=="")
            document.getElementById("errorime").style.visibility="Visible";
        else
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
                    // document.getElementById("Region1").innerHTML=xmlhttp.responseText;
                    window.location= <?php echo "\"dproperties.php?id=" . $id . "\"" ?>;
                }
            }
             
            var attributes="";
            
            if (document.getElementById("funkcionalna").checked)
                attributes=attributes+"1";
            else
                attributes=attributes+"0";    
            var moduleid=document.getElementById("moduleid").value;
            var userid=document.getElementById("userid").value;
            var name=encodeURIComponent(document.getElementById("ime").value);
            var description=encodeURIComponent(document.getElementById("opis").value);
            var range=document.getElementById("rang").value;
            var superproperty=document.getElementById("dpropertyid1").value;
            var id=document.getElementById("dpropertyid").value;
            xmlhttp.open("GET","editdproperty.php?tip=1&id="+id+"&moduleid="+moduleid+"&userid="+userid+"&attributes="+attributes+"&name="+name+"&description="+description+"&range="+range+"&superproperty="+superproperty,true);
            xmlhttp.send();    
        }  
        //ajax1();
    }
</script>
<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "classes.php?id=" . $id ?> "><?php echo get_string('Classes', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "oproperties.php?id=" . $id ?> "><?php echo get_string('Oproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a style="padding: .5em 1em;" href="#"><?php echo get_string('Dproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "individuals.php?id=" . $id ?> "><?php echo get_string('Individuals', 'ontology'); ?></a>
        </li>
    </ul>
    <form  method="POST">
        <div id="forma">
            <table>
                <tr>
                    <td valign="top">
                        <b> <?php echo get_string('Dproperty_data', 'ontology'); ?> </b> <br/>
<?php echo get_string('Choose_dproperty', 'ontology') . ':'; ?> <br/>

                        <?php echo data_property_hierarhy("dproperties", "dproperty", 0, $id, $USER->id, 0); ?> <br/>

                        <input type="Button" name="button2" id="button2" value="<?php echo get_string('Add_new_subdproperty', 'ontology'); ?>" onclick="prikazFormaDodadiPsvojstvo();" style="visibility: hidden; width: 290px;" />
                        <br /><input type="button" name="button3" id="button3" value="<?php echo get_string('Edit', 'ontology'); ?>" style="visibility: hidden; width: 290px;" onclick="prikazFormaEditPsvojstvo();"/>
                        <br /><br />

                        <input type="button" value="<?php echo get_string('Back', 'ontology'); ?>" onclick="nazad();" style="width: 290px;"/> <br />
                        <a href="HelpDP.php?id=<?php echo $id; ?>" class="help" style="width: 290px;"><?php echo get_string('Term_guide', 'ontology'); ?></a>

                    </td>
                    <td valign="top" >
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
        <input type="hidden" name="dpropertyid" id="dpropertyid" />
        <input type="hidden" name="userid" value="<?php echo $USER->id; ?>" id="userid" />
        <input type="hidden" name="moduleid" value="<?php echo $id; ?>" id="moduleid"/>
        <input type="hidden" id="courseid" value="<?php echo $course->id; ?>" />
    </form>
</div>
<script>
    $(function() {
        $(".help").button(
        {
            icons: {
                primary: "ui-icon-help"
            }
        }
    );
       
    });
</script>
<?php
echo $OUTPUT->footer();