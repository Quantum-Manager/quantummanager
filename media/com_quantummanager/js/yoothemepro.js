document.addEventListener('DOMContentLoaded' ,function () {

    let loadManager = false;
    let fieldWrap = false;
    let buttonInsert;
    let pathFile;

    document.querySelector('body').addEventListener('click', function (ev) {
        let flag = false;
        let el = ev.target;
        let image = el;

        if(el.tagName.toLowerCase() === 'a') {
            let find = checkFieldFile(el);
            if(find.flag) {
                flag = true;
                fieldWrap = find.wrap;
            }
        }

        if(['svg', 'span'].indexOf(el.tagName.toLowerCase())) {
            let parent = el.parentElement;
            let find = checkFieldFile(parent);
            if(find.flag) {
                image = parent;
                fieldWrap = find.wrap;
                flag = true;
            }
        }

        if(flag) {
            let intervalShowModal = setInterval(function () {
                let modal = document.querySelector('.uk-modal:not(#quantummanageryoothemepro)');
                if(modal !== null) {
                    modal.remove();
                    clearInterval(intervalShowModal);
                    showModal();
                }
            }, 50);
        }
    });

    getQuantummanager();

    function showModal() {
        setTimeout(function () {
            UIkit.modal('.quantummanageryoothemepro').show();
        }, 200);
    }

    function checkFieldFile(el) {
        let wrap = false;
        let flag = true;
        let listClasses = [
            'uk-placeholder',
            'uk-text-center',
            'uk-display-block',
            'uk-margin-remove'
        ];

        wrap = el.parentElement;

        for(let i=0;i<listClasses.length;i++) {
            if(!el.classList.contains(listClasses[i])) {
                wrap = false;
                flag = false;
                break;
            }
        }

        if(el.parentElement.classList.contains('yo-thumbnail')) {
            wrap = el.parentElement.parentElement;
        }

        return {
            wrap: wrap,
            flag: flag
        };
    }

    function getQuantummanager() {
        let xhr = new XMLHttpRequest();
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                loadManager = true;

                let quantumHead = xhr.responseText.match(/<head>(?:.|\n|\r)+?<\/head>/);
                let quantumHeadElement = document.createElement('div');
                quantumHeadElement.innerHTML = quantumHead[0].replace(/head/ig, 'div');


                let quantumBody = xhr.responseText.match(/<body.*?>(.|\s)+<\/body>/);
                let quantumBodyElement = document.createElement('div');
                quantumBodyElement.innerHTML = quantumBody[0];
                quantumBodyElement.setAttribute('class', 'fm-quantummanager');

                let arrayForCompilation = [
                    quantumHeadElement, quantumBodyElement
                ];

                document.querySelector('body').append(quantumBodyElement);

                let styles = Array.prototype.slice.call(quantumHeadElement.getElementsByTagName("link"));
                for (let i = 0; i < styles.length; i++) {

                    let continueFlag = false;
                    let disabledLists = [
                        'template',
                    ];

                    for (let k=0;k<disabledLists.length;k++) {
                        if(styles[i].href.indexOf(disabledLists[k]) !== -1) {
                            continueFlag = true;
                        }
                    }

                    if(continueFlag) {
                        continue;
                    }

                    document.getElementsByTagName("head")[0].appendChild(styles[i]);

                }

                styles = Array.prototype.slice.call(quantumHeadElement.getElementsByTagName("style"));
                for (let i = 0; i < styles.length; i++) {
                    document.getElementsByTagName("head")[0].appendChild(styles[i]);
                }

                let loadCount = 0;
                let allScripts = 0;
                for(let j=0;j<arrayForCompilation.length;j++) {
                    let scripts = Array.prototype.slice.call(arrayForCompilation[j].getElementsByTagName("script"));
                    allScripts += scripts.length;
                    for (let i = 0; i < scripts.length; i++) {

                        let continueFlag = false;
                        let disabledLists = [
                            'jquery',
                            'yoothemepro',
                            'bootstrap',
                            'template.js',
                        ];

                        for (let k=0;k<disabledLists.length;k++) {
                            if(scripts[i].src.indexOf(disabledLists[k]) !== -1) {
                                allScripts--;
                                continueFlag = true;
                            }
                        }

                        if(continueFlag) {
                            continue;
                        }

                        if (scripts[i].src !== "") {
                            let tag = document.createElement("script");
                            tag.src = scripts[i].src;
                            tag.onload = function () {
                                loadCount++;
                            };
                            document.getElementsByTagName("head")[0].appendChild(tag);
                        } else {
                            allScripts--;
                            eval(scripts[i].innerHTML);
                        }

                    }
                }

                buttonInsert = document.querySelector('.quantummanageryoothemepro .button-insert');
                buttonInsert.setAttribute('disabled', 'disabled');

                buttonInsert.addEventListener('click', function () {
                    let fm = QuantummanagerLists[0];

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(pathFile)
                        + '&scope=' + fm.data.scope + '&v=' + QuantumUtils.randomInteger(111111, 999999)))
                        .done(function (response) {
                            response = JSON.parse(response);
                            let input = fieldWrap.querySelector('input');
                            if(input !== null) {
                                input.focus();
                                input.value = response.path;
                                let evt = document.createEvent("HTMLEvents");
                                evt.initEvent("input");
                                input.dispatchEvent(evt);
                            }
                            fieldWrap = false;
                            UIkit.modal('.quantummanageryoothemepro').hide();
                    });

                });

                let intervalCheckLoad = setInterval(function () {
                    if(allScripts === loadCount) {

                        QuantumEventsDispatcher.add('clickFile', function (fm) {
                            let file = fm.Quantumviewfiles.file;
                            if(file === undefined) {
                                buttonInsert.setAttribute('disabled', 'disabled');
                                return;
                            }

                            let name = file.querySelector('.file-name').innerHTML;
                            pathFile = fm.data.path + '/' + name;
                            buttonInsert.removeAttribute('disabled');
                        });

                        QuantumEventsDispatcher.add('dblclickFile', function (fm, n, el) {
                            let name = el.querySelector('.file-name').innerHTML;
                            pathFile = fm.data.path + '/' + name;

                            let evt = document.createEvent("HTMLEvents");
                            evt.initEvent("click");
                            buttonInsert.dispatchEvent(evt);
                        });

                        QuantumEventsDispatcher.add('reloadPaths', function (fm) {
                            buttonInsert.setAttribute('disabled', 'disabled');
                        });

                        QuantumEventsDispatcher.add('updatePath', function (fm) {
                            buttonInsert.setAttribute('disabled', 'disabled');
                        });

                        QuantumEventsDispatcher.add('uploadComplete', function (fm) {

                            if(fm.Qantumupload.filesLists.length === 0) {
                                return
                            }

                            let name = fm.Qantumupload.filesLists[0];
                            pathFile = fm.data.path + '/' + fm.Qantumupload.filesLists[0];
                            buttonInsert.removeAttribute('disabled');
                        });

                        QuantumManagerLoadComplete = true;
                        QuantumManagerInit();

                        clearInterval(intervalCheckLoad);
                    }
                }, 50);

            }
        };

        xhr.open('GET', 'index.php?option=com_ajax&plugin=quantumyoothemepro&group=system&format=html&tmpl=component');
        xhr.send();
    }

});