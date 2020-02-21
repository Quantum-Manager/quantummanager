/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

document.addEventListener('DOMContentLoaded', function () {

    let formFields = JSON.parse(QuantumContentPlugin.fields);
    let buttonInsert = document.createElement('button');
    let buttonCancel = document.createElement('button');
    let pathFile;
    let altFile;

    buttonInsert.setAttribute('class', 'btn btn-primary');
    buttonInsert.setAttribute('type', 'button');
    buttonCancel.setAttribute('class', 'btn');
    buttonCancel.setAttribute('modal', 'modal');
    buttonCancel.setAttribute('data-dismiss', 'modal');
    buttonCancel.setAttribute('type', 'button');

    setTimeout(function () {
        for(let i=0;i<QuantummanagerLists.length;i++) {
            QuantummanagerLists[i].Quantumtoolbar.buttonAdd('insertFileEditor', 'center', 'file-actions', 'btn-insert btn-primary btn-hide', QuantumwindowLang.buttonInsert, 'quantummanager-icon-insert-inverse', {}, function (ev) {

                let fm = QuantummanagerLists[i];

                let fields;
                let titleScope = '';
                let header = QuantumUtils.createElement('div', {'class': 'modalcontentinsert-header'});
                let body = QuantumUtils.createElement('div', {'class':'table-file-for-insert'});
                let filesFind = fm.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
                let files = [];
                let templateList = JSON.parse(QuantumContentPlugin.templatelist);

                header = header.addChild('div');
                header = header.addChild('select', {'class':'select-file-for-insert'});

                if(templateList[fm.data.scope] === undefined) {
                    return;
                }

                for(let key in templateList[fm.data.scope].templatelist) {
                    header.add('option', {'value': templateList[fm.data.scope].templatelist[key]}, templateList[fm.data.scope].templatelist[key]);
                }

                header = header.getParent();
                header = header.add('button', {
                    'class':'btn btn-large button-file-for-insert',
                    'events': [
                        ['click', function (ev) {
                            let wrap =  this.closest('.quatummanagermodal-wrap');
                            let template = wrap.querySelector('.select-file-for-insert').value;
                            let trs = wrap.querySelectorAll('.table-file-for-insert-tr');
                            let paramsForRequest = {
                                'template': template,
                                'files': []
                            };
                            for(let i=0;i<trs.length;i++) {
                                let currentParams = {
                                    'file': trs[i].getAttribute('data-file'),
                                    'fields': {}
                                };
                                let inputAll = trs[i].querySelectorAll('input,select');
                                for(let j=0;j<inputAll.length;j++) {
                                    let value = inputAll[j].value;
                                    if(value === '') {
                                        value = inputAll[j].getAttribute('data-default');
                                    }

                                    currentParams.fields[inputAll[j].getAttribute('name')] = value;
                                }
                                paramsForRequest.files.push(currentParams);
                            }

                            jQuery.post(QuantumUtils.getFullUrl('/administrator/index.php?option=com_ajax&plugin=quantummanagercontent&group=editors-xtd&format=raw&scope=' + fm.data.scope
                                + '&path=' +  encodeURIComponent(fm.data.path) +
                                '&v=' + QuantumUtils.randomInteger(111111, 999999)), {
                                    params:  JSON.stringify(paramsForRequest)
                                }
                            ).done(function (response) {

                                let editor = getUrlParameter('e_name');
                                let tag = response;

                                if (window.Joomla && Joomla.editors.instances.hasOwnProperty(editor)) {
                                    Joomla.editors.instances[editor].replaceSelection(tag)
                                } else {
                                    window.parent.jInsertEditorText(tag, editor);
                                }

                                window.parent.jModalClose();

                            });

                        }]
                    ]
                }, 'Прикрепить файлы');
                header = header.getParent();

                header = header.add('div', {'class': 'help-settings'}, QuantumwindowLang.helpSettings);

                for(let i=0;i<filesFind.length;i++) {
                    if (filesFind[i].querySelector('input').checked) {
                        files.push(filesFind[i]);
                    }
                }

                if(files.length === 0) {
                    return;
                }

                if(formFields[fm.data.scope] !== undefined) {
                    fields = formFields[fm.data.scope]['fieldsform'];
                    titleScope = formFields[fm.data.scope]['title'];
                } else {
                    fields = {};
                    titleScope = QuantumwindowLang.defaultScope;
                }

                for(let i=0;i<files.length;i++) {
                    let file = files[i].getAttribute('data-file');
                    let exs = files[i].getAttribute('data-exs');
                    let name = files[i].getAttribute('data-name');
                    let preview = '';

                    if(files[i].getAttribute('data-filep') === null || files[i].getAttribute('data-filep') === '') {
                        preview = fm.Quantumviewfiles.generateIconFile(exs);
                    } else {
                        preview = QuantumUtils.createElement('img', {'class': 'table-file-for-insert-preview-file', 'src': files[i].getAttribute('data-filep') + '&path=' + encodeURIComponent(fm.data.path)}).build();
                    }

                    body = body.addChild('div', {'class': 'table-file-for-insert-tr', 'data-file': file})
                        .addChild('div', {'class': 'handle-wrap'})
                            .add('div', {'class': 'quantummanager-icon quantummanager-icon-switch-vertical handle'})
                            .getParent()
                        .addChild('div', {'class': 'table-file-for-insert-preview'}, preview)
                            .add('div', {'class': 'table-file-for-insert-preview-name'}, file)
                            .getParent()
                        .addChild('div', {'class': 'table-file-for-insert-fields'});

                            if(Object.keys(fields).length > 0) {
                                for(let i in fields) {
                                    if([
                                        'text',
                                        'number',
                                        'color',
                                        'email',
                                        'url',
                                        'date',
                                        'datetime-local',
                                        'time',
                                    ].indexOf(fields[i].type) !== -1)
                                    {
                                        body = body.add('input', {
                                            'data-default': fields[i].default,
                                            'type': fields[i].type,
                                            'name': '{' + fields[i].nametemplate + '}',
                                            'value': '',
                                            'placeholder': fields[i].name,
                                        });
                                    }

                                    if(['list'].indexOf(fields[i].type) !== -1)
                                    {
                                        body = body.addChild('select', {
                                            'data-default': fields[i].default,
                                            'type': fields[i].type,
                                            'name': '{' + fields[i].nametemplate + '}',
                                            'value': '',
                                            'placeholder': fields[i].name,
                                        });

                                        if(typeof fields[i].forlist === 'string') {
                                            fields[i].forlist = fields[i].forlist.split('\r');
                                        }

                                        for(let key in fields[i].forlist) {
                                            body = body.add('option', {
                                                'value': fields[i].forlist[key],
                                            }, fields[i].forlist[key]);
                                        }

                                        body = body.getParent();

                                    }

                                }
                            } else {
                                body = body.add('input', {
                                    'data-default': QuantumwindowLang.defaultNameValue,
                                    'type': 'text',
                                    'name': '{name}',
                                    'value': '',
                                    'placeholder': QuantumwindowLang.defaultName,
                                });
                            }
                            body = body.getParent();
                        body = body.getParent();
                }

                header = header.build();
                body = body.build();
                QuantumUtils.modal(fm, header, body, '', 'modalcontentinsert');

                new Sortable(body, {
                    group: "name",
                    sort: true,
                    delay: 0,
                    delayOnTouchOnly: false,
                    touchStartThreshold: 0,
                    disabled: false,
                    store: null,
                    animation: 150,
                    easing: "cubic-bezier(1, 0, 0, 1)",
                    handle: ".handle",
                    preventOnFilter: true,
                    draggable: ".table-file-for-insert-tr",
                    dataIdAttr: 'data-id',
                    swapThreshold: 1,
                    invertedSwapThreshold: 1,
                    direction: 'vertical',
                });
            });
        }
    }, 300);

    QuantumEventsDispatcher.add('clickFile', function (fm) {
        let file = fm.Quantumviewfiles.file;

        if(file === undefined) {

            let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');
            if(form !== null) {
                form.classList.remove('active');
            }

            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
            return;
        } else {
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.remove('btn-hide');
        }

    });

    QuantumEventsDispatcher.add('uploadComplete', function (fm) {

        if(fm.Qantumupload.filesLists.length === 0) {
            return
        }

        let name = fm.Qantumupload.filesLists[0];
        pathFile = fm.data.path + '/' + fm.Qantumupload.filesLists[0];
        name.split('.').pop();
        altFile = name[0];
        fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.remove('btn-hide');
    });

    QuantumEventsDispatcher.add('reloadPaths', function (fm) {
        fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');

        let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');

        if(form !== null) {
            form.classList.remove('active');
        }

    });

    QuantumEventsDispatcher.add('updatePath', function (fm) {
        fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');

        let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');
        if(form !== null) {
            form.classList.remove('active');
        }

    });

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        let results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

});