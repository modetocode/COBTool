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
if (!is_Teacher()) {
    studentska_proverka($course, true, $cm);
    $eProfesor=0;
}
else 
    $eProfesor=1;


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
<script type="text/javascript">
    function superklasa(field) {
        document.getElementById("classid").value = field.value;
        if (document.getElementById("classid").value!='')
        {
            document.getElementById("button2").style.visibility = '';
     
            ajax();
        }
    }
   
    function superklasa1(field) {
        document.getElementById("classid1").value = field.value;
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
        xmlhttp.open("GET","viewclass.php?id="+document.getElementById("classid").value+"&courseid="+<?php echo $course->id; ?>,true);
        xmlhttp.send();
    
    }
    function setStyle()
    {
        $("input:button").button();
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
        var classid = document.getElementById("classid").value;
        var userid=document.getElementById("userid").value;
        var id=document.getElementById("moduleid").value;
        xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+classid+"&tip=1",true);
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



    function execute_ajax2()
    {
        document.getElementById("dodavanje2").style.visibility="hidden";
        document.getElementById("brishi2").style.visibility="visible";
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
                setStyle();
            }
        }
        var jfunction="lista2";
        var stek = document.getElementById("stek"+jfunction).value;
        var input = document.getElementById("input"+jfunction).value;
        var classid = document.getElementById("classid").value;
        var userid=document.getElementById("userid").value;
        var id=document.getElementById("moduleid").value;
        xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+classid+"&tip=1",true);
        xmlhttp.send();
        
    }
    function lista21(field)
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
        if (field.value!="")
        {
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
    }

    function lista22()
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
        document.getElementById("inputlista2").value=document.getElementById("inputlista2").value+" ^b"+document.getElementById("broj").value;
        document.getElementById("tekstlista2").value=document.getElementById("tekstlista2").value+" "+document.getElementById("broj").value;
        execute_ajax2();
    }

    function execute_ajax3()
    {
        document.getElementById("dodavanje3").style.visibility="hidden";
        document.getElementById("brishi3").style.visibility="visible";
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
                document.getElementById("Region3").innerHTML=xmlhttp.responseText;
                setStyle();
            }
        }
    
        var jfunction="lista3";
        var stek = document.getElementById("stek"+jfunction).value;
        var input = document.getElementById("input"+jfunction).value;
        var classid = document.getElementById("classid").value;
        var userid=document.getElementById("userid").value;
        var id=document.getElementById("moduleid").value;
        xmlhttp.open("GET","parser.php?stek="+stek+"&input="+input+"&userid="+userid+"&id="+id+"&jfunction="+jfunction+"&classid="+classid+"&tip=1",true);
        xmlhttp.send();
        
    }
    function lista31(field)
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 3
        if (field.value!="")
        {
            if (document.getElementById("inputlista3").value=="")
                document.getElementById("inputlista3").value=field.value;
            else
                document.getElementById("inputlista3").value=document.getElementById("inputlista3").value+" "+field.value;
            var i;
            for(i = 0; i < field.options.length; i++)
                if (field.options[i].selected&&field.value!='$')
            {
                if (document.getElementById("tekstlista3").value=="")
                    document.getElementById("tekstlista3").value=field.options[i].text;
                else
                    document.getElementById("tekstlista3").value=document.getElementById("tekstlista3").value+" "+field.options[i].text;
                break;
            }
            if(field.value=='$')
            { 
                execute_ajax3();
                ajax();
                setTimeout("ajax()",200);
            }
            else{
                execute_ajax3();
                    
            }
        }
    }

    function lista32()
    { //imeto na funkcijata e vrednosta na promenlivata jfunction spoena so 2
        document.getElementById("inputlista3").value=document.getElementById("inputlista3").value+" ^b"+document.getElementById("broj").value;
        document.getElementById("tekstlista3").value=document.getElementById("tekstlista3").value+" "+document.getElementById("broj").value;
        execute_ajax3();
    }
    function prikazFormaDodadiKlasa()
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
        xmlhttp.open("GET","insertclass.php?id="+document.getElementById("classid").value,true);
        xmlhttp.send();
    
    }
    function insertclass()
    {
        if(document.getElementById("ime").value=="")
        {
            document.getElementById("errorime").style.visibility="Visible";
        }
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
                    // document.getElementById("Region").innerHTML=xmlhttp.responseText;
                    setStyle();
                    p();
                }
            }
            xmlhttp.open("GET","insertclass.php?id="+document.getElementById("classid").value+"&tip=1&moduleid="+document.getElementById("moduleid").value+"&ime="+encodeURIComponent(document.getElementById("ime").value)+"&opis="+encodeURIComponent(document.getElementById("opis").value)+"&userid="+document.getElementById("userid").value,true);
            xmlhttp.send();
            // ajax1();
            
        
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
        xmlhttp.open("GET","viewclass.php?id="+document.getElementById("classid").value+"&tip="+tip+"&red="+red+"&courseid="+courseid,true);
        xmlhttp.send();
       
    }

    function nazad()
    {
        window.location= <?php echo "\"view.php?id=" . $id . "\"" ?>
    }

    function prikazFormaEditKlasa()
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
        xmlhttp.open("GET","editclass.php?id="+document.getElementById("classid").value+"&cmid="+document.getElementById("moduleid").value+"&userid="+document.getElementById("userid").value,true);
        xmlhttp.send();
    
    }

    function editclass()
    {
        if(document.getElementById("ime").value=="")
        {
            document.getElementById("errorime").style.visibility="Visible";
        }
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
                    // document.getElementById("Region").innerHTML=xmlhttp.responseText;
                    setStyle();
                    //ajax1();
                    window.location= <?php echo "\"classes.php?id=" . $id . "\"" ?>;
                }
           
         
            }
            xmlhttp.open("GET","editclass.php?id="+document.getElementById("classid").value+"&id1="+document.getElementById("classid1").value+"&tip=1&moduleid="+document.getElementById("moduleid").value+"&ime="+encodeURIComponent(document.getElementById("ime").value)+"&opis="+encodeURIComponent(document.getElementById("opis").value)+"&userid="+document.getElementById("userid").value,true);
            xmlhttp.send();
            
        
        }
    }
    function refresh() {
        window.location= <?php echo "\"classes.php?id=" . $id . "\"" ?>
    }
</script>


<div class="ui-tabs ui-widget ui-widget-content ui-corner-all" id="tabs">
    <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
        <li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
            <a style="padding: .5em 1em;" href="#"><?php echo get_string('Classes', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "oproperties.php?id=" . $id ?> "><?php echo get_string('Oproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "dproperties.php?id=" . $id ?> "><?php echo get_string('Dproperties', 'ontology'); ?></a>
        </li>
        <li class="ui-state-default ui-corner-top">
            <a style="padding: .5em 1em;" href=" <?php echo "individuals.php?id=" . $id ?> "><?php echo get_string('Individuals', 'ontology'); ?></a>
        </li>
    </ul>


    <form>
        <div id="forma">
            <table>
                <tr>
                    <td valign="top">
                        <b> <?php echo get_string('Class_data', 'ontology'); ?> </b> 
                        <br/>
                        <?php echo get_string('Choose_class', 'ontology') . ':'; ?> <br/>

                        <?php echo class_hierarhy("superclass", "superklasa", 0, $id, $USER->id); ?> <br/>

                        <input type="button" name="button2" id="button2" value="<?php echo get_string('Add_new_subclass', 'ontology'); ?>" style="visibility: hidden; width: 230px;" onclick="prikazFormaDodadiKlasa();"/>
                        <br /><input type="button" name="button3" id="button3" value="<?php echo get_string('Edit', 'ontology'); ?>" style="visibility: hidden; width: 230px;" onclick="prikazFormaEditKlasa();"/>
                        <br /><br />
                        <input type="button" onclick="nazad();" value="<?php echo get_string('Back', 'ontology'); ?>"  style="width: 230px;"/>
                        <br />
                        <a href="HelpClasses.php?id=<?php echo $id; ?>"  style="width: 230px;" class="help"><?php echo get_string('Term_guide', 'ontology'); ?></a>

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
        <input type="hidden" name="classid" id="classid" /> <br/>
        <input type="hidden" name="userid" value="<?php echo $USER->id; ?>" id="userid"/>
        <input type="hidden" name="moduleid" value="<?php echo $id; ?>" id="moduleid"/>
        <input type="hidden" id="courseid" value="<?php echo $course->id; ?>" />
    </form>
</div>
<script>
    $(function() {
        $(".help").button({
            icons: {
                primary: "ui-icon-help"
            }
        });
    });
</script>
<?php
echo $OUTPUT->footer();