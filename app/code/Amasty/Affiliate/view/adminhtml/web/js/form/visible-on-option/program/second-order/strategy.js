define(['ko'], function (ko) {
    'use strict';

    return {
        defaults: {
            imports: {},
            visibilityConditions: {},
        },

        initObservable: function () {
            this._super().observe(Object.keys(this.imports));

            this.visible = ko.computed(() => {
                return Object.keys(this.imports).every((option) => {
                    if (!this?.[option] || !this?.[option]()) {
                        return false;
                    }
                    return !!Object.values(this.visibilityConditions?.[option]).includes(this?.[option]());
                });
            });

            return this;
        }
    };
});
