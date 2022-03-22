/*!
 * Lazy Load - JavaScript plugin for lazy loading images
 *
 * Copyright (c) 2007-2019 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://appelsiini.net/projects/lazyload
 *
 * Version: 2.0.0-rc.2
 *
 */

(function (root, factory) {
    if (typeof exports === "object") {
        module.exports = factory(root);
    } else if (typeof define === "function" && define.amd) {
        define([], factory);
    } else {
        root.LazyLoad = factory(root);
    }
}) (typeof global !== "undefined" ? global : this.window || this.global, function (root) {

    "use strict";

    if (typeof define === "function" && define.amd){
        root = window;
    }

    const defaults = {
        src: "data-src",
        srcset: "data-srcset",
        selector: ".lazyload",
        root: null,
        rootMargin: "0px",
        threshold: 0
    };

    /**
     * Merge two or more objects. Returns a new object.
     * @private
     * @param {Boolean}  deep     If true, do a deep (or recursive) merge [optional]
     * @param {Object}   objects  The objects to merge together
     * @returns {Object}          Merged values of defaults and options
     */
    const extend = function ()  {

        let extended = {};
        let deep = false;
        let i = 0;
        let length = arguments.length;

        /* Check if a deep merge */
        if (Object.prototype.toString.call(arguments[0]) === "[object Boolean]") {
            deep = arguments[0];
            i++;
        }

        /* Merge the object into the extended object */
        let merge = function (obj) {
            for (let prop in obj) {
                if (Object.prototype.hasOwnProperty.call(obj, prop)) {
                    /* If deep merge and property is an object, merge properties */
                    if (deep && Object.prototype.toString.call(obj[prop]) === "[object Object]") {
                        extended[prop] = extend(true, extended[prop], obj[prop]);
                    } else {
                        extended[prop] = obj[prop];
                    }
                }
            }
        };

        /* Loop through each object and conduct a merge */
        for (; i < length; i++) {
            let obj = arguments[i];
            merge(obj);
        }

        return extended;
    };

    function LazyLoad(images, options) {
        this.settings = extend(defaults, options || {});
        this.images = images || document.querySelectorAll(this.settings.selector);
        this.observer = null;
        this.init();
    }

    LazyLoad.prototype = {

        turn_execute_time: 0,
        turn_execute: null,
        turns_list: [],
        turns_thread: 0,
        turns_thread_max: 2,

        init: function() {

            /* Without observers load everything and bail out early. */
            if (!root.IntersectionObserver) {
                this.loadImages();
                return;
            }

            let self = this;
            let observerConfig = {
                root: this.settings.root,
                rootMargin: this.settings.rootMargin,
                threshold: [this.settings.threshold]
            };

            this.observer = new IntersectionObserver(function(entries) {
                Array.prototype.forEach.call(entries, function (entry) {
                    if (entry.isIntersecting) {
                        self.observer.unobserve(entry.target);

                        self.addTurn(function(callback_end) {
                            let src = entry.target.getAttribute(self.settings.src);
                            if ("img" === entry.target.tagName.toLowerCase()) {
                                if (src) {
                                    entry.target.src = src;
                                    entry.target.onload = function () {
                                        callback_end();
                                    }
                                    entry.target.error = function () {
                                        callback_end();
                                    }
                                }
                            }
                        })

                    }
                });
            }, observerConfig);

            Array.prototype.forEach.call(this.images, function (image) {
                self.observer.observe(image);
            });
        },

        changeImages(images) {
            this.images = images;
        },

        loadAndDestroy: function () {
            if (!this.settings) { return; }
            this.loadImages();
            this.destroy();
        },

        loadImages: function () {
            if (!this.settings) { return; }

            let self = this;
            Array.prototype.forEach.call(this.images, function (image) {
                self.addTurn(function(callback_end) {
                    let src = image.getAttribute(self.settings.src);
                    if ("img" === image.tagName.toLowerCase()) {
                        if (src) {
                            image.src = src;
                            image.onload = function () {
                                callback_end();
                            }
                            image.error = function () {
                                callback_end();
                            }
                        }
                    }
                });
            });
        },

        destroy: function () {
            if (!this.settings) { return; }
            this.observer.disconnect();
            this.settings = null;
        },

        clearTurn: function () {
            let self = this;
            self.turns_list = [];
            self.turns_thread = 0;
            clearInterval(self.turn_execute);
            self.turn_execute = null;
            self.turn_execute_time = 0;
            console.log(self.turns_list);
        },


        addTurn: function(callback) {
            let self = this;
            console.log(self.turns_list);
            self.turns_list.push(callback);

            if(self.turn_execute === null && self.turns_list.length > 0) {
                self.turn_execute_time = 0;
                self.turn_execute = setInterval(function() {

                    if(self.turns_list.length === 0 && self.turn_execute_time >= 5000) {
                        self.clearTurn();
                        return;
                    }

                    if(self.turns_thread >= self.turns_thread_max) {
                        return;
                    }

                    let task;

                    if(self.turns_list.length > 30) {
                        task = self.turns_list.pop();
                    } else {
                        task = self.turns_list.shift();
                    }

                    if(typeof task === 'function') {
                        task(function() {
                            self.turns_thread--;

                            if(self.turns_thread < 0) {
                                self.turns_thread = 0;
                            }
                        });
                        self.turns_thread++;
                    }

                    self.turn_execute_time += 100;
                }, 100);
            }
        }
    };

    root.lazyload = function(images, options) {
        return new LazyLoad(images, options);
    };

    if (root.jQuery) {
        const $ = root.jQuery;
        $.fn.lazyload = function (options) {
            options = options || {};
            options.attribute = options.attribute || "data-src";
            new LazyLoad($.makeArray(this), options);
            return this;
        };
    }

    return LazyLoad;
});