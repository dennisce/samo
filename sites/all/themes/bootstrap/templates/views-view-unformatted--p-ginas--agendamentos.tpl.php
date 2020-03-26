<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
$lib = libraries_get_path('calendarUtil');
?>
<link href='https://use.fontawesome.com/releases/v5.0.6/css/all.css' rel='stylesheet'>
<link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' rel='stylesheet' />
<link href='<?=$lib?>/core/main.css' rel='stylesheet' />
<link href='<?=$lib?>/daygrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/timegrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/list/main.css' rel='stylesheet'>
<link href='<?=$lib?>/bootstrap/main.css' rel='stylesheet' />

<script src='<?=$lib?>/core/main.js'></script>
<script src='<?=$lib?>/interaction/main.js'></script>
<script src='<?=$lib?>/daygrid/main.js'></script>
<script src='<?=$lib?>/timegrid/main.js'></script>
<script src='<?=$lib?>/list/main.js'></script>
<script src='<?=$lib?>/bootstrap/main.js'></script>
<script src='<?=$lib?>/core/locales/pt-br.js'></script>
<script>


  var j = jQuery.noConflict();
  
  j(document).ready(function (){
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
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
      defaultDate: Date.now(),
      navLinks: true, // can click day/week names to navigate views
      selectable: false,
      selectMirror: false,
      editable: true,
      eventLimit: false, // allow "more" link when too many events
      
      events: "calendar/getEventos",
    });

    calendar.render();
    
    calendar.on('dateClick', function(info) {
      console.log(info);
      location.href="#overlay=node/add/agendamento";
    });
/*
    calendar.on('select', function(info) {
      
      
      
    });
*/
  });
  

</script>

<div id='calendar-container'>
  
  <div id='calendar'></div>
</div>

<?php /*
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<pre id="aqui">
  
</pre>
<?php foreach ($rows as $id => $row): ?>
  <div<?php if ($classes_array[$id]): ?> class="<?php print $classes_array[$id]; ?>"<?php endif; ?>>
    <?php print $row; ?>
  </div>
<?php endforeach; ?>
*/