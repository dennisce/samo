/**
 * @file
 * Form handling for the field_multiple_extended module.
 */

(function ($, Drupal) {
  'use strict';

  /**
   * Drupal behaviords for field_multiple_extended.
   */
  Drupal.behaviors.fieldMultipleExtended = {
    attach: function (context, settings) {
      if ('fieldMultipleExtended' in settings) {
        $.each(settings.fieldMultipleExtended, function (wrapperBaseId, wrapperSettings) {
          if ('htmlId' in wrapperSettings) {
            var $table = $('#' + wrapperSettings.htmlId, context).find('table.field-multiple-table');
            if ($table.length > 0) {
              if ('min' in wrapperSettings) {
                $table.data('minRequired', wrapperSettings.min);
                refreshTable($table);
              }
            }
          }
        });
      }
    }
  };

  /**
   * Override tableDrag.onDrop().
   */
  Drupal.tableDrag.prototype.onDrop = function () {
    refreshTable(this.table);
  };

  /**
   * Refresh the table for the min required rows.
   *
   * @param object table
   *   The table element or jQuery object.
   */
  function refreshTable(table) {
    var $table = $(table);
    var minRequired = $table.data('minRequired');
    if (minRequired > 0) {
      var $rows = $('tr.draggable', table);
      $rows.removeClass('field-item-required');
      if ($rows.length > 0) {
        for (var r = 0; r < minRequired; r++) {
          $rows.filter(':eq(' + r + ')').addClass('field-item-required');
        }
      }
    }
  }

})(jQuery, Drupal);
