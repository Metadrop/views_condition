/**
 * @file
 * Views Condition Summary.
 */

(function ($, window, Drupal) {

  'use strict';

  /**
   * Provide the summary information for the block settings vertical tabs.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior for the block settings summaries.
   */
  Drupal.behaviors.viewsConditionSettingsSummary = {
    attach: function () {
      // The drupalSetSummary method required for this behavior is not available
      // on the Blocks administration page, so we need to make sure this
      // behavior is processed only if drupalSetSummary is defined.
      if (typeof $.fn.drupalSetSummary === 'undefined') {
        return;
      }

      /**
       * Create a summary for checkboxes in the provided context.
       *
       * @param {HTMLDocument|HTMLElement} context
       *   A context where one would find checkboxes to summarize.
       *
       * @return {string}
       *   A string with the summary.
       */
      function checkboxesSummary(context) {
        var views = $(context).find('[data-drupal-selector="edit-visibility-views-condition-view-pages"]').is(':checked');
        var negate = $(context).find('[data-drupal-selector="edit-visibility-views-condition-negate"]').is(':checked');

        if (views && negate) {
          return Drupal.t('Do not apply to view pages.');
        }

        if ((!views && negate) || (views && !negate)) {
          return Drupal.t('Apply to view pages.');
        }

        return Drupal.t('Not restricted');
      }

      $('[data-drupal-selector="edit-visibility-views-condition"]').drupalSetSummary(checkboxesSummary);
    }
  };

})(jQuery, window, Drupal);
