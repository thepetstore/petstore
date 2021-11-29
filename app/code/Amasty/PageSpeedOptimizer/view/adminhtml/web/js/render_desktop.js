define([
    'uiComponent',
    'jquery'
], function(Component, $) {

    return Component.extend({
        defaults: {
            device: '',
            imports: {
                'contentData': '${ "diagnostic" }:${ $.device }'
            },
            template: 'Amasty_PageSpeedOptimizer/tabs/content',
            recommendations: '.-desktop .amoptimizer-recommendation-block',
            links: '.-desktop .amoptimizer-link-block',
            css: {
                hide: '-hide',
                active: '-active'
            }
        },

        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            this._super().observe(['contentData']);

            return this;
        },

        linkClick: function (item, e) {
            var elem = $('.-desktop [data-amoptimizer-js="'+ item.id +'"]');

            $(this.links).removeClass(this.css.active);
            $(e.currentTarget).addClass(this.css.active);
            $(this.recommendations).addClass(this.css.hide);
            elem.removeClass(this.css.hide);
        }
    });
});
