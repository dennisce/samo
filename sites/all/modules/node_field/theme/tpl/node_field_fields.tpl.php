<?php

/**
 * @file
 * Template file for node_field_fields theme. Render all node fields.
 */
?>
<?php if (!empty($children)) : ?>
  <div class="node-fields">
    <?php print $children; ?>
  </div>
<?php endif; ?>
