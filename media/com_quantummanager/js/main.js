/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

document.addEventListener('DOMContentLoaded' ,function () {
    window.QuantumManagerLoadComplete = true;
    window.QuantumManagerInit();
});

//support subform add row
if(window.jQuery !== undefined) {
    jQuery(document).on('subform-row-add', function(event, row) {
        window.QuantumManagerInit(row);
    });
}

document.addEventListener('subform-row-add', function (event) {
    window.QuantumManagerInit(event.target);
});

window.QuantumManagerInit = function(container) {

    if(container === null || container === undefined) {
        container = document;
    }

    let quantummanagerAll = container.querySelectorAll('.quantummanager');
    let scopesEnabled = QuantumSettings.scopeEnabled.split(',');
    let quantummanagerForBuild = [];
    let activeIndex = null;

    for (let i=0;i<quantummanagerAll.length;i++) {

        if(quantummanagerAll[i].hasAttribute('data-index')) {
            continue;
        }

        let modules = quantummanagerAll[i].querySelectorAll('.quantummanager-module');
        let filemanager = {};
        filemanager.id = QuantummanagerLists.length;
        filemanager.events = new QuantumEvents;
        filemanager.element = quantummanagerAll[i];
        filemanager.data = {};
        filemanager.data.scope = 'images';

        if(localStorage !== undefined) {
            let scope = localStorage.getItem('quantummanagerScope');

            if(scope !== null) {
                filemanager.data.scope = scope;
            }
        }

        if(scopesEnabled.length === 0) {
            return;
        }

        if(scopesEnabled.indexOf(filemanager.data.scope) === -1) {
            filemanager.data.scope = scopesEnabled[0];
        }

        if(scopesEnabled.indexOf('sessionroot') !== -1)
        {
            filemanager.data.scope = 'sessionroot';
        }

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

        quantummanagerAll[i].setAttribute('data-index', filemanager.id);
        QuantummanagerLists.push(filemanager);
        quantummanagerForBuild.push(filemanager);

        let quantummanagerHelp = filemanager.element.querySelector('.quantummanager-jedreview');
        let helpButtonClose = filemanager.element.querySelector('.quantummanager-jedreview .qm-btn-close');
        if(quantummanagerHelp !== null) {
            QuantumUtils.replaceImgToSvg('.quantummanager-jedreview');
            helpButtonClose.addEventListener('click', function (ev) {
                quantummanagerHelp.remove();
                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantummanager.hideJedReview"));
                ev.preventDefault();
            });
        }

    }


    let loadQuantum = setInterval(function () {
        if(QuantumManagerLoadComplete) {
            QuantumEventsDispatcher.build(quantummanagerForBuild);
            QuantumEventsDispatcher.trigger('afterMainInit');
            clearInterval(loadQuantum)
        }
    }, 1);


    let activeDetected = function (ev) {
        let element = ev.target.closest('.quantummanager');

        if(element === null || element === undefined) {
            activeIndex = null;
            return;
        }

        if(!element.hasAttribute('data-index')) {
            activeIndex = null;
            return;
        }

        activeIndex = parseInt(element.getAttribute('data-index'));
    };

    document.addEventListener('click', activeDetected);
    document.addEventListener('dblclick', activeDetected);
    document.addEventListener('contextmenu', activeDetected);
    document.addEventListener('touchstart', activeDetected);

    document.addEventListener('keydown', function(ev) {

        if(ev.target.tagName.toLowerCase() === 'input') {
            return;
        }

        if(
            QuantummanagerLists[activeIndex] === undefined ||
            QuantummanagerLists[activeIndex] === null
        ) {
            return;
        }

        let results = QuantummanagerLists[activeIndex].events.trigger('hotKeysResolve', QuantummanagerLists[activeIndex], ev.key);

        if(results.indexOf(false) !== -1) {
            ev.preventDefault();
        }

    });

    if(
        QuantumSettings['bufferPaste'] !== undefined &&
        QuantumSettings['bufferPaste'] !== null &&
        QuantumSettings['bufferPaste'] === '1'
    ) {
        document.addEventListener('paste', function (ev) {

            if(
                QuantummanagerLists[activeIndex] === undefined ||
                QuantummanagerLists[activeIndex] === null
            ) {
                return;
            }

            if(
                QuantummanagerLists[activeIndex].Qantumupload === undefined ||
                QuantummanagerLists[activeIndex].Qantumupload === null
            ) {
                return;
            }

            let items = (ev.clipboardData || ev.originalEvent.clipboardData).items;
            for (let index in items) {
                let item = items[index];
                if (item.kind === 'file') {
                    let blob = item.getAsFile();
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        let ext = event.target.result.substring("data:image/".length, event.target.result.indexOf(";base64"));
                        QuantummanagerLists[activeIndex].Qantumupload.uploadFiles([new File([QuantumUtils.dataURItoBlob(event.target.result)], 'clipboard_' + QuantumUtils.randomInteger(1111111, 9999999) + '.' + ext)]);
                    }
                    reader.readAsDataURL(blob);
                }
            }
        });
    }


};