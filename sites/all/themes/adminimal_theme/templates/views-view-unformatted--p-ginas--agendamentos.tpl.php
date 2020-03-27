<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
global $base_url;
$lib = $base_url."/".libraries_get_path('calendarUtil');

$uid = Array();
$uid = explode("/", $_GET['q']);

// if(isset($uid[2]) && $uid[0] == 'agendas'){
//   header("Location: ".$base_url."/"."agendas/".$uid[1]."#overlay=admin/create/agendamento/".$uid[2]."/".$uid[1]);
// } else {
//   //print_r($uid);
// }

$urlSrv = (count($uid)==0 || $uid[1] == 'ALL')?$base_url."/calendar/getEventos":$base_url."/calendar/getEventosByProfissional?uid=".$uid[1];

if(count($uid)==0 || $uid[1] == 'ALL'){
  $businessHours = "";
} else {
  $user = user_load($uid[1]);  
  
  $businessHours = "businessHours : [";

  foreach($user->field_dia_da_semana['und'] as $i => $val){
    $businessHours .= "
      {
        daysOfWeek: [".$val['value']."],
        startTime: '".$user->field_inicio_de_turno['und'][$i]['safe_value']."',
        endTime: '".$user->field_fim_de_turno['und'][$i]['safe_value']."'
      },
    ";
  }

  $businessHours .= "],
  ";
  
}
?>
<link href='<?=$lib?>/core/all.css' rel='stylesheet'>
<link href='<?=$lib?>/core/main.css' rel='stylesheet' />
<link href='<?=$lib?>/daygrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/timegrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/list/main.css' rel='stylesheet'>

<script src='<?=$lib?>/core/main.js'></script>
<script src='<?=$lib?>/interaction/main.js'></script>
<script src='<?=$lib?>/daygrid/main.js'></script>
<script src='<?=$lib?>/timegrid/main.js'></script>
<script src='<?=$lib?>/list/main.js'></script>
<script src='<?=$lib?>/bootstrap/main.js'></script>
<script src='<?=$lib?>/core/locales/pt-br.js'></script>
<script>

  var j = jQuery.noConflict();
  var calendar;
  j(document).ready(function (){
    var calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
      <?=$businessHours?>
      aspectRatio: 2,
      locale:'pt-br',
      weekends: true,
      plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'bootstrap' ],
      defaultView: 'dayGridMonth',
      themeSystem: 'bootstrap',
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      customButtons: {
        refresh: {
          text: 'Atualizar',
          click: function() {
            calendar.refetchEvents();
          }
        }
      },
      footer:{
        center: 'refresh'
      },
      defaultDate: Date.now(),
      navLinks: true, 
      selectable: false,
      selectMirror: false,
      editable: true,
      eventLimit: false,
      events: "<?=$urlSrv?>",
      eventDrop: function(evt){
        alterEvent(evt);
      },
      eventResize: function(evt) {
        alterEvent(evt)
      }
      
    });

    calendar.render();
    
    calendar.on('dateClick', function(info) {
      
      setCookie('dateClick',1,info.dateStr);
      location.href="#overlay=admin/create/paciente/<?=$_GET['q']?>";
      
    });
  });

  function alterEvent(evt){
    var data = evt.event.start;
    var month = data.getMonth() + 1;

    var inicio = data.getFullYear()+"-"+month+"-"+data.getDate()+" "+data.getHours()+":"+data.getMinutes()+":"+data.getSeconds();

    data = evt.event.end;
    month = data.getMonth() + 1;
    fim = data.getFullYear()+"-"+month+"-"+data.getDate()+" "+data.getHours()+":"+data.getMinutes()+":"+data.getSeconds()

    var evento = {
      start : inicio,
      end   : fim,
      id    : evt.event.id
      };
    j.post("<?=$base_url?>/calendar/alterEvento", evento, function(ret){

        console.log(ret);
        
    }, "json");
  }  

</script>
<div id="sidebarControl">
  <img src="<?=$base_url?>/sites/all/themes/adminimal_theme/images/setaMenuLateralRecolher.png" />
</div>
<div id='calendar-container'>
  <div id='calendar'></div>
</div>

<?php 

