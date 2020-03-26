<?php

/**
 * @file
 * Template file for node_field_field theme. Render single node field.
 */
?>
<?php if (!$hidden): ?>
  <div class="node-field <?php print $class; ?>">
    <?php if ($show_title) : ?>
      <label class="title"><?php print $title; ?></label>
    <?php endif; ?>
    <div class="body"><?php print $value; ?></div>
  </div>
<?php endif; ?>
