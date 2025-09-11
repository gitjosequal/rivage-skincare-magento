define([
    'ko',
    'underscore',
    'uiRegistry'
], function (ko, _, registsry) {
    'use strict';

    return {
        defaults: {
            valuesForOptions: [],
            dependLinks: {},
            dependObservers: {},
            oneOfConditions: false,
        },

        visibilityComputedFunction: function () {
            let isVisible = !this.oneOfConditions;

            _.each(this.dependObservers, function (link) {
                let isShown = link() in this.valuesForOptions ?? false;

                if (this.oneOfConditions) {
                    isVisible = isVisible || isShown;
                } else {
                    isVisible = isVisible && isShown;
                }
            }, this);

            return isVisible;
        },


        initObservable: function () {
            let links = {};
            this._super();

            _.each(this.dependLinks, function (value, name) {
                let data = value.split(':');

                this.dependObservers[name] = ko.observable();
                links[value] = 'dependObservers.' + name;

                registsry.get(data[0], function (target) {
                    let initValue = target.get(data[1]);

                    this.dependObservers[name](initValue);
                }.bind(this));
            }, this);

            this.setListeners(links);
            this.visible = ko.computed(this.visibilityComputedFunction.bind(this));

            return this;
        }
    };
});
