document.addEventListener('DOMContentLoaded', function () {

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
            QuantummanagerLists[i].Quantumtoolbar.buttonAdd('insertFileEditor', 'left', 'btn-insert btn-primary btn-hide', 'Вставить файл', 'quantummanager-icon-insert-inverse', {}, function (ev) {

                let tag = '',
                    attr = [],
                    figclass = '',
                    captionclass = '',
                    // Get the image tag field information
                    url = pathFile,
                    alt = altFile,
                    align = '',
                    title = '',
                    caption = '',
                    c_class = '',
                    editor = getUrlParameter('e_name');

                if (url)
                {
                    // Set alt attribute
                    attr.push('alt="' + alt + '"');

                    // Set align attribute
                    if (align && !caption)
                    {
                        attr.push('class="pull-' + align + '"');
                    }

                    // Set title attribute
                    if (title)
                    {
                        attr.push('title="' + title + '"');
                    }

                    tag = '<img src="' + url + '" ' + attr.join(' ') + '/>';

                    // Process caption
                    if (caption)
                    {
                        if (align)
                        {
                            figclass = ' class="pull-' + align + '"';
                        }

                        if (c_class)
                        {
                            captionclass = ' class="' + c_class + '"';
                        }

                        tag = '<figure' + figclass + '>' + tag + '<figcaption' + captionclass + '>' + caption + '</figcaption></figure>';
                    }
                }

                if (window.Joomla && Joomla.editors.instances.hasOwnProperty(editor)) {
                    Joomla.editors.instances[editor].replaceSelection(tag)
                } else {
                    window.parent.jInsertEditorText(tag, editor);
                }

                window.parent.jModalClose();

                ev.preventDefault();
            });
        }
    }, 300);

    QuantumEventsDispatcher.add('clickFile', function (fm) {
        let name = fm.Quantumviewfiles.file.querySelector('.file-name').innerHTML;
        let check = fm.Quantumviewfiles.file.querySelector('.import-files-check-file');

        if(check.checked) {
            pathFile = fm.data.path + '/' + name;
            name.split('.').pop();
            altFile = name[0];
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.remove('btn-hide');
        } else {
            fm.Quantumtoolbar.buttonsList['insertFileEditor'].classList.add('btn-hide');
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

    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

});