<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
$lib = libraries_get_path('calendarUtil');
?>
<link href='<?=$lib?>/core/main.css' rel='stylesheet' />
<link href='<?=$lib?>/daygrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/timegrid/main.css' rel='stylesheet' />
<link href='<?=$lib?>/list/main.css' rel='stylesheet' />
<script src='<?=$lib?>/core/main.js'></script>
<script src='<?=$lib?>/interaction/main.js'></script>
<script src='<?=$lib?>/daygrid/main.js'></script>
<script src='<?=$lib?>/timegrid/main.js'></script>
<script src='<?=$lib?>/list/main.js'></script>
<script>


  var j = jQuery.noConflict();
  
  j(document).ready(function (){
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [ 'interaction', 'dayGrid', 'timeGrid' ],
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      defaultDate: '2020-02-12',
      navLinks: true, // can click day/week names to navigate views
      selectable: true,
      selectMirror: true,
      editable: true,
      eventLimit: true, // allow "more" link when too many events
      events: [],
    });

    calendar.render();
    j.get( "calendar/getEventos", function(data) {
      var ret = JSON.parse(data);
      console.log(ret);
      ret.forEach(function(evt){
        calendar.addEvent(JSON.parse(evt));
      });
      
    });
    calendar.on('dateClick', function(info) {
      // Eventos de clique no dia
    });

    calendar.on('select', function(info) {
      
      
      
    });

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