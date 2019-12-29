/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
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

    this.buttonAdd = function (id, position, group, className, name, icon, attr, callback, parentButtonWrap) {

        if(this.options.buttonsBun.indexOf(id) !== -1) {
            return;
        }

        if(this.buttonsList[id] !== undefined) {
            return;
        }

        let groupHtml = QuantumToolbarElement.querySelector('.' + position + ' .' + group);
        let htmlButtons;
        let wrapButton = document.createElement('div');
        let button = document.createElement('button');
        wrapButton.setAttribute('class', 'btn-wrap');
        button.setAttribute('class', 'btn ' + className);
        button.innerHTML = "<span class='quantummanager-icon " + icon + "'></span><span>" + name + "</span>";

        if(attr === null)  {
            attr = {};
        }

        if(groupHtml === null) {
            let createDivGroup = document.createElement('div');
            createDivGroup.setAttribute('class', 'quantumtoolbar-module-buttons-group ' + group);
            QuantumToolbarElement.querySelector('.' + position).append(createDivGroup);
            groupHtml = QuantumToolbarElement.querySelector('.' + position + ' .' + group);
        }

        if(position === 'left') {
            htmlButtons = QuantumToolbarElement.querySelector('.left');
        }

        if(position === 'center') {
            htmlButtons = QuantumToolbarElement.querySelector('.center');
        }

        if(position === 'right') {
            htmlButtons = QuantumToolbarElement.querySelector('.right');
        }

        for (let name in attr) {
            button.setAttribute(name, attr[name]);
        }

        button.addEventListener('click', callback);


        if(parentButtonWrap === undefined) {
            wrapButton.append(button);
            groupHtml.append(wrapButton);
        } else {
            let dropdown = parentButtonWrap.querySelector('.btn-dropdown');

            if(dropdown === null) {
                dropdown = document.createElement('div');
                dropdown.setAttribute('class', 'btn-dropdown');
                parentButtonWrap.append(dropdown);
            }

            dropdown.append(button);
        }

        this.buttonsList[id] = button;
        return this.buttonsList[id];
    };

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

};