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
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
            return;
        }

        let name = file.querySelector('.file-name').innerHTML;
        let check = file.querySelector('.import-files-check-file');
        let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');

        if(form === null) {
            let oldForm = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');

            if(oldForm !== null) {
                oldForm.remove();
            }

            if(formFields[fm.data.scope] === undefined) {
                return;
            }

            let fields = formFields[fm.data.scope];
            let html = document.createElement('form');
            html.setAttribute('class', 'modal-form-insert');

            for(let i in fields) {
                html.innerHTML += '<input data-default="' + fields[i].default + '" type="' + fields[i].type +'" name="{' + fields[i].nametemplate + '}" value="" placeholder="' + fields[i].name + '" />';
            }

            fm.Quantumviewfiles.element.appendChild(html);
            form = html;
        }

        if(check.checked) {
            pathFile = fm.data.path + '/' + name;
            name = name.split('.');
            name.pop();
            altFile = name.join('.');
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.remove('btn-hide');
            form.classList.add('active');
        } else {
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
            form.classList.remove('active');
        }
    });

    QuantumEventsDispatcher.add('updatePath', function (fm) {
        let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');
        if(form !== null) {
            form.classList.remove('active');
        }
    });

    QuantumEventsDispatcher.add('reloadPaths', function (fm) {
        let form = fm.Quantumviewfiles.element.querySelector('.modal-form-insert');
        if(form !== null) {
            form.classList.remove('active');
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
    });

    QuantumEventsDispatcher.add('updatePath', function (fm) {
        fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
    });

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        let results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

});