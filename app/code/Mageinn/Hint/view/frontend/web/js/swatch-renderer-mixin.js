define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRendererTooltip', widget, {

            options: {
                delay: 200,                             //how much ms before tooltip to show
                tooltipClass: 'swatch-option-tooltip'  //configurable, but remember about css
            },

            /** Rewrite init method
             * @private
             */
            _init: function () {
                var $widget = this,
                    $this = this.element,
                    $element = $('.' + $widget.options.tooltipClass),
                    timer,
                    type = parseInt($this.attr('option-type'), 10),
                    label = $this.attr('option-label'),
                    thumb = $this.attr('option-tooltip-thumb'),
                    value = $this.attr('option-tooltip-value'),
                    hint = $this.attr('option-tooltip-hint'),
                    $image,
                    $title,
                    $corner;

                if (!$element.size()) {
                    $element = $('<div class="' +
                        $widget.options.tooltipClass +
                        '"><div class="image"></div><div class="title"></div><div class="corner"></div></div>'
                    );
                    $('body').append($element);
                }

                $image = $element.find('.image');
                $title = $element.find('.title');
                $corner = $element.find('.corner');

                $this.hover(function () {
                    if (!$this.hasClass('disabled') && $this.attr('option-label')) {
                        timer = setTimeout(
                            function () {
                                var leftOpt = null,
                                    leftCorner = 0,
                                    left,
                                    $window;

                                if (type === 2) {
                                    // Image
                                    $image.css({
                                        'background': 'url("' + thumb + '") no-repeat center', //Background case
                                        'background-size': 'initial'
                                    });
                                    $image.show();
                                } else if (type === 1) {
                                    // Color
                                    $image.css({
                                        background: value
                                    });
                                    $image.show();
                                } else if (type === 0 || type === 3) {
                                    // Default
                                    $image.hide();
                                }
                                if (hint !== undefined) {
                                    $title.html(label+'<br />'+hint).text();
                                } else {
                                    $title.text(label);
                                }

                                leftOpt = $this.offset().left;
                                left = leftOpt + $this.width() / 2 - $element.width() / 2;
                                $window = $(window);

                                // the numbers (5 and 5) is magick constants for offset from left or right page
                                if (left < 0) {
                                    left = 5;
                                } else if (left + $element.width() > $window.width()) {
                                    left = $window.width() - $element.width() - 5;
                                }

                                // the numbers (6,  3 and 18) is magick constants for offset tooltip
                                leftCorner = 0;

                                if ($element.width() < $this.width()) {
                                    leftCorner = $element.width() / 2 - 3;
                                } else {
                                    leftCorner = (leftOpt > left ? leftOpt - left : left - leftOpt) + $this.width() / 2 - 6;
                                }

                                $corner.css({
                                    left: leftCorner
                                });
                                $element.css({
                                    left: left,
                                    top: $this.offset().top - $element.height() - $corner.height() - 18
                                }).show();
                            },
                            $widget.options.delay
                        );
                    }
                }, function () {
                    $element.hide();
                    clearTimeout(timer);
                });

                $(document).on('tap', function () {
                    $element.hide();
                    clearTimeout(timer);
                });

                $this.on('tap', function (event) {
                    event.stopPropagation();
                });

                return this._super();
            }
        });

        $.widget('mage.SwatchRenderer', widget, {

           /**
            * Render controls
            *
            * @private
            */
           _RenderControls: function () {
              var $widget = this,
                 container = this.element,
                 classes = this.options.classes,
                 chooseText = this.options.jsonConfig.chooseText;

              $widget.optionsMap = {};

              $.each(this.options.jsonConfig.attributes, function () {
                 var item = this,
                    controlLabelId = 'option-label-' + item.code + '-' + item.id,
                    options = $widget._RenderSwatchOptions(item, controlLabelId),
                    select = $widget._RenderSwatchSelect(item, chooseText),
                    input = $widget._RenderFormInput(item),
                    listLabel = '',
                    label = '';

                 // Show only swatch controls
                 if ($widget.options.onlySwatches && !$widget.options.jsonSwatchConfig.hasOwnProperty(item.id)) {
                    return;
                 }

                 if ($widget.options.enableControlLabel) {
                    if(item.hint) {
                       label +=
                          '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                          item.label +
                          '</span>' +
                          //hint
                          '<span class="mageinn-hint">' +
                          '<span class="mageinn-hint-toggle"></span>' +
                          '<div class="mageinn-hint-content">' + item.hint + '</div>' +
                          '</span>' +

                          '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                    }else{
                       label +=
                          '<span id="' + controlLabelId + '" class="' + classes.attributeLabelClass + '">' +
                          item.label +
                          '</span>' +
                          '<span class="' + classes.attributeSelectedOptionLabelClass + '"></span>';
                    }
                 }

                 if ($widget.inProductList) {
                    $widget.productForm.append(input);
                    input = '';
                    listLabel = 'aria-label="' + item.label + '"';
                 } else {
                    listLabel = 'aria-labelledby="' + controlLabelId + '"';
                 }

                 // Create new control
                 container.append(
                    '<div class="' + classes.attributeClass + ' ' + item.code + '" ' +
                    'attribute-code="' + item.code + '" ' +
                    'attribute-id="' + item.id + '">' +
                    label +
                    '<div aria-activedescendant="" ' +
                    'tabindex="0" ' +
                    'aria-invalid="false" ' +
                    'aria-required="true" ' +
                    'role="listbox" ' + listLabel +
                    'class="' + classes.attributeOptionsWrapper + ' clearfix">' +
                    options + select +
                    '</div>' + input +
                    '</div>'
                 );

                 $widget.optionsMap[item.id] = {};

                 // Aggregate options array to hash (key => value)
                 $.each(item.options, function () {
                    if (this.products.length > 0) {
                       $widget.optionsMap[item.id][this.id] = {
                          price: parseInt(
                             $widget.options.jsonConfig.optionPrices[this.products[0]].finalPrice.amount,
                             10
                          ),
                          products: this.products
                       };
                    }
                 });
              });

              // Connect Tooltip
              container
                 .find('[option-type="1"], [option-type="2"], [option-type="0"], [option-type="3"]')
                 .SwatchRendererTooltip();

              // Hide all elements below more button
              $('.' + classes.moreButton).nextAll().hide();

              // Handle events like click or change
              $widget._EventListener();

              // Rewind options
              $widget._Rewind(container);

              //Emulate click on all swatches from Request
              $widget._EmulateSelected($.parseQuery());
              $widget._EmulateSelected($widget._getSelectedAttributes());
           },

            /**
             * Render swatch options by part of config
             *
             * @param {Object} config
             * @returns {String}
             * @private
             */
            _RenderSwatchOptions: function (config) {
                var optionConfig = this.options.jsonSwatchConfig[config.id],
                    optionClass = this.options.classes.optionClass,
                    moreLimit = parseInt(this.options.numberToShow, 10),
                    moreClass = this.options.classes.moreButton,
                    moreText = this.options.moreButtonText,
                    countAttributes = 0,
                    html = '';

                if (!this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                    return '';
                }

                $.each(config.options, function () {
                    var id,
                        type,
                        value,
                        thumb,
                        hint,
                        label,
                        attr;

                    if (!optionConfig.hasOwnProperty(this.id)) {
                        return '';
                    }

                    // Add more button
                    if (moreLimit === countAttributes++) {
                        html += '<a href="#" class="' + moreClass + '">' + moreText + '</a>';
                    }

                    id = this.id;
                    type = parseInt(optionConfig[id].type, 10);
                    value = optionConfig[id].hasOwnProperty('value') ? optionConfig[id].value : '';
                    thumb = optionConfig[id].hasOwnProperty('thumb') ? optionConfig[id].thumb : '';
                    hint = optionConfig[id].hasOwnProperty('hint') ? optionConfig[id].hint : '';
                    label = this.label ? this.label : '';
                    attr =
                        ' option-type="' + type + '"' +
                        ' option-id="' + id + '"' +
                        ' option-label="' + label + '"' +
                        ' option-tooltip-thumb="' + thumb + '"' +
                        ' option-tooltip-hint="' + hint + '"' +
                        ' option-tooltip-value="' + value + '"';

                    if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                        attr += ' option-empty="true"';
                    }

                    if (type === 0) {
                        // Text
                        html += '<div class="' + optionClass + ' text" ' + attr + '>' + (value ? value : label) +
                            '</div>';
                    } else if (type === 1) {
                        // Color
                        html += '<div class="' + optionClass + ' color" ' + attr +
                            '" style="background: ' + value +
                            ' no-repeat center; background-size: initial;">' + '' +
                            '</div>';
                    } else if (type === 2) {
                        // Image
                        html += '<div class="' + optionClass + ' image" ' + attr +
                            '" style="background: url(' + value + ') no-repeat center; background-size: initial;">' + '' +
                            '</div>';
                    } else if (type === 3) {
                        // Clear
                        html += '<div class="' + optionClass + '" ' + attr + '></div>';
                    } else {
                        // Defaualt
                        html += '<div class="' + optionClass + '" ' + attr + '>' + label + '</div>';
                    }
                });

                return html;
            }
        });

        return $.mage.SwatchRenderer;
    }
});