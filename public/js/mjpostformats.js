/*
* Name Style: mjpostformats.js
* v1.0.0 (http://www.morgan-jourdin.fr/)
* Copyright Morgan JOURDIN.
*
*/

var mjPostFormats = function() {
    this.init();
};

mjPostFormats.prototype = {
  init: function() {
    var self = this;

    self.forceAjaxACF();
  },

  forceAjaxACF: function() {
    var test = '';
    jQuery(document).ready(function(){
      test = jQuery('#post-formats-select input:checked');
      if(test.length > 0){
        jQuery('#post-formats-select input:checked').prop("checked", false).trigger('click');
      }
    });
  }
}

mj = new mjPostFormats();
