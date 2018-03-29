/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/url'
], function($, $t, modal, urlBuilder) {
    "use strict";
    $.widget('mage.catalogAddToCart', {

        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },

        _create: function() {
            if (this.options.bindSubmit) {
                this._bindSubmit();
            }
        },

        _bindSubmit: function() {
            var self = this;
            this.element.on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        isLoaderEnabled: function() {
            return this.options.processStart && this.options.processStop;
        },

        /**
         * Handler for the form 'submit' event
         *
         * @param {Object} form
         */
        submitForm: function (form) {
            var addToCartButton, self = this;

            if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
                self.element.off('submit');
                // disable 'Add to Cart' button
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);
                addToCartButton.prop('disabled', true);
                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                form.submit();
            } else {
                self.ajaxSubmit(form);
            }
        },

        ajaxSubmit: function(form) {
            var self = this;
            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',
                beforeSend: function() {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },
                success: function(res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        window.location = res.backUrl;
                        return;
                    }
                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }
                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }
                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);

                }
            });
        },

        disableAddToCartButton: function(form) {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Agregando...');
            var addToCartButton = $(form).find(this.options.addToCartButtonSelector);
            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        },

        enableAddToCartButton: function(form) {
            var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Adicionado');
            var self = this,
                addToCartButton = $(form).find(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            setTimeout(function() {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Agregar al carro');
                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }
    });

    $(document).on('ajaxComplete', function (event, xhr, settings) {

        if (!$('body').hasClass('weltpixel-quickview-catalog-product-view')) {
            var cartMessage = '';

            if (settings.type.match(/get/i) && _.isObject(xhr.responseJSON)) {
                var result = xhr.responseJSON;
                if (_.isObject(result.messages)) {
                    var messageLength = result.messages.messages.length;
                    var message = result.messages.messages[0];
                    if (messageLength && message.type == 'success') {
                        cartMessage = message.text;
                    }
                }
                if (_.isObject(result.cart) && _.isObject(result.messages)) {
                    var messageLength = result.messages.messages.length;
                    var message = result.messages.messages[0];
                    if (messageLength && message.type == 'success') {
                        cartMessage = message.text;
                    }
                }

                if  (cartMessage) {
                    // Tag addtocart modal

                    /*parent.window.dataLayer.push({
                        'event': 'vpv',
                        'page': '/addtocart/modal'
                    });*/


                    var popup = $('<div />').html('').modal({
                        modalClass: 'custom-modal-xpec',
                        autoOpen: true,
                        clickableOverlay: true,
                        responsive: true,
                        title: $.mage.__(cartMessage),
                        buttons: [
                            {
                                text: 'IR AL CARRO',
                                'class': 'action secundary',
                                click: function () {
                                    // Tag goto cart
                                    /*parent.window.dataLayer.push({
                                        'event': 'ev-int',
                                        'ev-cat': 'Add To Cart',
                                        'ev-act': 'Modal',
                                        'ev-lab': 'Ir al carro'
                                    });*/

                                    parent.window.location = window.checkout.shoppingCartUrl;
                                }
                            },
                            {
                                text: 'PAGAR',
                                'class': 'action primary',
                                click: function () {
                                    // Tag goto checkout
                                    /*parent.window.dataLayer.push({
                                        'event': 'ev-int',
                                        'ev-cat': 'Add To Cart',
                                        'ev-act': 'Modal',
                                        'ev-lab': 'Pagar'
                                    });*/
                                    parent.window.location = window.checkout.checkoutUrl;
                                }
                            }
                        ],
                        opened: function(obj) {
                            setTimeout(function(){
                                $(".action-close").trigger("click");
                            }, 7 * 1000000);
                        }
                    });
                    popup.modal('openModal');
                }
            }
        }
    });

    return $.mage.catalogAddToCart;
});
