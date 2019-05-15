/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Quantumtoolbar = function(Filemanager, QuantumToolbarElement, options) {

    this.options = options;
    this.buttonsList = {};

    this.init = function () {

        if(this.options.buttonsBun !== undefined) {
            this.options.buttonsBun = this.options.buttonsBun.split(',');
            if(this.options.buttonsBun === '') {
                this.options.buttonsBun = [];
            }
        } else {
            this.options.buttonsBun = [];
        }

    };

    this.buttonAdd = function (id, $position, className, name, icon, attr, callback) {

        if(this.options.buttonsBun.indexOf(id) !== -1) {
            return;
        }

        let htmlButtons;
        let button = document.createElement('button');
        button.setAttribute('class', 'btn ' + className);
        button.innerHTML = "<span class='quantummanager-icon " + icon + "'></span><span>" + name + "</span>";

        if(attr === null)  {
            attr = {};
        }

        if($position === 'left') {
            htmlButtons = QuantumToolbarElement.querySelector('.left');
        }

        if($position === 'right') {
            htmlButtons = QuantumToolbarElement.querySelector('.right');
        }

        for (let name in attr) {
            button.setAttribute(name, attr[name]);
        }

        button.addEventListener('click', callback);
        htmlButtons.append(button);
        this.buttonsList[id] = button
    };

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

};