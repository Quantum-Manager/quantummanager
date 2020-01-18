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
                let header = QuantumUtils.createElement('div');
                let body = QuantumUtils.createElement('div', {'class':'table-file-for-insert'});
                let filesFind = fm.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
                let files = [];
                let templateList = JSON.parse(QuantumContentPlugin.templatelist);

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
                                let inputAll = trs[i].querySelectorAll('input');
                                for(let j=0;j<inputAll.length;j++) {
                                    let value = inputAll[j].value;
                                    if(value === '') {
                                        value = inputAll[j].getAttribute('data-default');
                                    }

                                    currentParams.fields[inputAll[j].getAttribute('name')] = value;
                                }
                                paramsForRequest.files.push(currentParams);
                            }


                            jQuery.get(QuantumUtils.getFullUrl('/administrator/index.php?option=com_ajax&plugin=quantummanagercontent&group=editors-xtd&format=raw&scope=' + fm.data.scope
                                + '&path=' +  encodeURIComponent(fm.data.path)
                                + '&params=' + JSON.stringify(paramsForRequest)
                                + '&v=' + QuantumUtils.randomInteger(111111, 999999))
                            ).done(function (response) {

                                var editor = getUrlParameter('e_name');
                                var tag = response;

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
                    let name = files[i].getAttribute('data-name');
                    let preview = files[i].getAttribute('data-filep') + '&path=' + encodeURIComponent(fm.data.path);

                    body = body.addChild('div', {'class': 'table-file-for-insert-tr', 'data-file': file})
                        .addChild('div', {'class': 'table-file-for-insert-preview'})
                            .add('img', {'class': 'table-file-for-insert-preview-file', 'src': preview})
                            .add('div', {'class': 'table-file-for-insert-preview-name'}, name)
                            .getParent()
                        .addChild('div', {'class': 'table-file-for-insert-fields'});

                            if(Object.keys(fields).length > 0) {
                                for(let i in fields) {
                                    body = body.add('input', {
                                        'data-default': fields[i].default,
                                        'type': fields[i].type,
                                        'name': '{' + fields[i].nametemplate + '}',
                                        'value': '',
                                        'placeholder': fields[i].name,
                                    });
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

                QuantumUtils.modal(fm, header.build(), body.build());
                return;

                let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');
                let params = '';

                if(form !== null) {

                    let inputAll = form.querySelectorAll('input');

                    for(let j=0;j<inputAll.length;j++) {
                        if(inputAll[j].value === '') {
                            inputAll[j].value = inputAll[j].getAttribute('data-default');
                        }
                    }

                    params = new URLSearchParams(new FormData(form)).toString();
                }

                jQuery.get(QuantumUtils.getFullUrl('/administrator/index.php?option=com_ajax&plugin=quantummanagercontent&group=editors-xtd&format=raw&scope=' + QuantummanagerLists[i].data.scope
                    + '&path=' +  encodeURIComponent(pathFile)
                    + '&file=' + encodeURIComponent('test.jpg')
                    + '&v=' + QuantumUtils.randomInteger(111111, 999999)) + '&' + params
                ).done(function (response) {

                    var editor = getUrlParameter('e_name');
                    var tag = response;

                    if (window.Joomla && Joomla.editors.instances.hasOwnProperty(editor)) {
                        Joomla.editors.instances[editor].replaceSelection(tag)
                    } else {
                        window.parent.jInsertEditorText(tag, editor);
                    }

                    window.parent.jModalClose();

                });

                ev.preventDefault();
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

        /*
        let name = file.querySelector('.file-name').innerHTML;
        let check = file.querySelector('.import-files-check-file');
        let oldForm = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');

        if(oldForm !== null) {
            oldForm.remove();
        }

        let fields;
        let titleScope = '';
        let html = '';
        let form = document.createElement('form');
        form.setAttribute('class', 'modal-form-insert active');

        if(formFields[fm.data.scope] !== undefined) {
            fields = formFields[fm.data.scope]['fieldsform'];
            titleScope = formFields[fm.data.scope]['title'];
        } else {
            fields = {};
            titleScope = QuantumwindowLang.defaultScope;
        }

        html = "<div class='modal-form-insert-fields'>";

        if(Object.keys(fields).length > 0) {
            for(let i in fields) {
                html += '<input data-default="' + fields[i].default + '" type="' + fields[i].type +'" name="{' + fields[i].nametemplate + '}" value="" placeholder="' + fields[i].name + '">';
            }
        } else {
            html += '<input data-default="' + QuantumwindowLang.defaultNameValue + '" type="text" name="{name}" value="" placeholder="' + QuantumwindowLang.defaultName + '">';
        }


        html += "</div>";
        html += '<div class="modal-form-insert-footer">' + QuantumwindowLang.helpTemplate + '<b>' + titleScope + '</b>' + '. ' +  QuantumwindowLang.helpSettings + '</div>';

        form.innerHTML = html;

        fm.Quantumviewfiles.element.appendChild(form);

        if(check.checked) {
            pathFile = fm.data.path + '/' + name;
            name = name.split('.');
            name.pop();
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.remove('btn-hide');
            form.classList.add('active');
        } else {
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
            form.classList.remove('active');
        }*/

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