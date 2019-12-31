document.addEventListener('DOMContentLoaded' ,function () {

    let loadManager = false;
    let fieldWrap = false;
    let buttonInsert;
    let pathFile;
    let wrapClick;

    document.querySelector('body').addEventListener('click', function (ev) {
        wrapClick = ev.target;
    });

    UIkit.util.on('div', 'beforeshow', function (ev) {
        let element = ev.target;
        if(element.classList.contains('uk-modal')) {
            if(element.querySelector('.yo-finder-body') !== null) {
                element.remove();
                fieldWrap = wrapClick.closest('div');
                if(fieldWrap.classList.contains('yo-thumbnail')) {
                    fieldWrap = fieldWrap.parentElement;
                }
                setTimeout(function () {
                    UIkit.modal('.quantummanageryoothemepro').show();
                }, 200)
            }
        }
    });

    getQuantummanager();

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
                            if(scripts[i].innerHTML.indexOf('csrf.token') === -1) {
                                eval(scripts[i].innerHTML);
                            }

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