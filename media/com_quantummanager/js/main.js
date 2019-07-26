/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.QuantummanagerLists = [];

window.QuantumEvents = function () {

    this.listEvents = [];

    this.add = function (element, event, callback) {
        if (typeof element === 'string') {
            callback = event;
            event = element;
            element = null;
        }

        let eventObj = {};
        eventObj.n = event;
        eventObj.e = element;
        eventObj.c = callback;
        this.listEvents.push(eventObj);
    };


    this.trigger = function (event, filemanager, target) {
        let returns = [];
        for (let i=0;i<this.listEvents.length;i++) {
            if(this.listEvents[i].n === event) {
                returns.push(this.listEvents[i].c(filemanager, this.listEvents[i].e, target));
            }
        }
        return returns;
    };

};

window.QuantumEventsDispatcher = {
    listEvents: [],
    add: function (element, event, callback) {
        if (typeof element === 'string') {
            callback = event;
            event = element;
            element = null;
        }

        let eventObj = {};
        eventObj.n = event;
        eventObj.e = element;
        eventObj.c = callback;
        this.listEvents.push(eventObj);
    },
    build: function () {
        for(let j=0;j<this.listEvents.length;j++) {
            for(let i=0;i<QuantummanagerLists.length;i++) {
                QuantummanagerLists[i].events.listEvents.push(this.listEvents[j]);
            }
        }

    }

};


document.addEventListener('DOMContentLoaded' ,function () {
    let quantummanagerAll = document.querySelectorAll('.quantummanager');
    let id = 0;

    for (let i=0;i<quantummanagerAll.length;i++) {
        let modules = quantummanagerAll[i].querySelectorAll('.quantummanager-module');
        let filemanager = {};
        filemanager.id = id;
        filemanager.events = new QuantumEvents;
        filemanager.element = quantummanagerAll[i];
        filemanager.data = {};

        for(let j=0;j<modules.length;j++) {
            let type = modules[j].getAttribute('data-type');
            let dataOptions = modules[j].getAttribute('data-options');
            let options = {};

            if(dataOptions !== null) {
                let optionsSplit = dataOptions.split(';');
                for(let k=0;k<optionsSplit.length;k++) {
                    let option = optionsSplit[k].split(':');
                    options[option[0]] = option[1];
                }
            }

            filemanager[type] = new window[type](filemanager, modules[j], options);
            filemanager[type].init();
        }

        for(let j=0;j<modules.length;j++) {
            let type = modules[j].getAttribute('data-type');

            if(filemanager[type].initAfter !== undefined) {
                filemanager[type].initAfter();
            }

        }

        quantummanagerAll[i].setAttribute('data-index', i);
        QuantummanagerLists.push(filemanager);
        id = id + 1;


        let quantummanagerHelp = filemanager.element.querySelector('.quantummanager-jedreview');
        let helpButtonClose = filemanager.element.querySelector('.quantummanager-jedreview .btn-close');
        if(quantummanagerHelp !== null) {
            QuantumUtils.replaceImgToSvg('.quantummanager-jedreview');
            helpButtonClose.addEventListener('click', function (ev) {
                quantummanagerHelp.remove();
                jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantummanager.hideJedReview");
                ev.preventDefault();
            });
        }
    }

    QuantumEventsDispatcher.build();




});

