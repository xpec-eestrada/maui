define([
    "jquery"
], function ($) {
    'use strict';

    $.widget('mage.amShopbySwatchesChoose', {
        options: {
            listSwatches: {}
        },
        _create: function () {
            var self = this;
            setTimeout(function() {
                $.each(self.options.listSwatches, function (attributeCode, optionId) {
                    self.element.find('.swatch-attribute-options [option-id="' + optionId + '"]').trigger('click');
                });
            }, 100);
        }
    });
});
