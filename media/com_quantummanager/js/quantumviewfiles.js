/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumviewfiles = function (Filemanager, ViewfilesElement, options) {

    let self = this;
    this.element = ViewfilesElement;
    this.options = options;
    this.ds;
    this.metaReset = false;
    this.priorityMetaFile = undefined;
    this.metaFileLoad = {};
    this.viewMeta = ViewfilesElement.querySelector('.view-wrap .meta-file');
    this.metaLoadDirCurrent = '';
    this.path = '';
    this.file = '';
    this.directory = '';
    this.lastTypeViewFiles = '';
    this.lazyload = null;
    this.bufferTopDirectories = {};
    this.listFiles = '';
    this.history = [];
    this.breadcrumbsLists = [];
    this.breadcrumbsWaitLoad = false;
    this.searchNameValue = '';
    this.cacheMetaPath = '';
    this.cacheMeta = '';
    this.bufferCut = false;
    this.bufferFromPath;
    this.buffer = [];
    this.sortField = '';
    this.sortDir = '';
    this.contextMenu = '';
    this.menuItemsArea = [
        {
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextReload, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/refresh-button.svg'),
            onClick: function () {
                Filemanager.events.trigger('reloadPaths', Filemanager);
            }
        },
        {
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextFolderCreate, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/folder-symbol.svg'),
            onClick: function () {
                QuantumUtils.prompt(QuantumviewfilesLang.directoryName, '', function (nameDirectory) {
                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.createDirectory&path=" + encodeURIComponent(Filemanager.data.path)
                        + '&name=' + encodeURIComponent(nameDirectory)
                        + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                });
            }
        },
        {
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextPaste, 'ENT_QUOTES'),
            tip: '',
            check: function () {
                if (self.buffer.length === 0) {
                    return false;
                }
            },
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/clipboard-paste-button.svg'),
            onClick: function () {
                if (self.buffer.length === 0) {
                    return;
                }

                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.paste" +
                    '&pathFrom=' + encodeURIComponent(self.bufferFromPath) +
                    '&pathTo=' + encodeURIComponent(Filemanager.data.path) +
                    '&list=' + encodeURIComponent(JSON.stringify(self.buffer)) +
                    "&scopeFrom=" + encodeURIComponent(self.bufferFromScope) +
                    "&scopeTo=" + encodeURIComponent(Filemanager.data.scope) +
                    "&cut=" + encodeURIComponent(self.bufferCut))
                ).done(function (response) {
                    Filemanager.events.trigger('reloadPaths', Filemanager);

                    self.bufferCut = false;
                    self.bufferFromScope = '';
                    self.bufferFromPath = '';
                    self.buffer = [];
                });
            }
        }
    ];
    this.directoryContext = '';
    this.menuItemsDirectories = [
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextRename, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/create-new-pencil-button.svg'),
            onClick: function () {
                let name = self.directoryContext.querySelector('.directory-name').innerHTML;

                QuantumUtils.prompt(QuantumviewfilesLang.directoryName, name, function (result) {
                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.renameDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&oldName=' + encodeURIComponent(name) + '&name=' + encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                        response = JSON.parse(response);
                        if (response.status === undefined) {
                            return;
                        }

                        if (response.status === 'ok') {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        }
                    });
                });

            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonDuplicate, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/copy-content.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.directoryContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.duplicate&path=" + encodeURIComponent(Filemanager.data.path)
                    + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                    + '&list=' + JSON.stringify(objectsPush)
                    + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {

                    Filemanager.events.trigger('reloadPaths', Filemanager);

                });

            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCopy, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/copy-content.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.directoryContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }


                self.bufferCut = 0;
                self.bufferFromScope = Filemanager.data.scope;
                self.bufferFromPath = Filemanager.data.path;
                self.buffer = objectsPush;

                Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
                self.trigger('updateContextMenuArea');
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCut, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/cut-content-button.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.directoryContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }


                self.bufferCut = 1;
                self.bufferFromScope = Filemanager.data.scope;
                self.bufferFromPath = Filemanager.data.path;
                self.buffer = objectsPush;

                Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
                self.trigger('updateContextMenuArea');
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextDelete, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/rubbish-bin-delete-button.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let nameFile = '';
                let self_name = self.directoryContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                if (objectsPush.length === 0) {
                    return;
                }

                if (objectsPush.length === 1) {
                    nameFile = ' ' + objectsPush[0];
                } else {
                    nameFile = '';
                }

                QuantumUtils.confirm(QuantumviewfilesLang.contextDelete + nameFile + '?', function (result) {
                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&list=' + encodeURIComponent(JSON.stringify(objectsPush)))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                }, function () {

                    if (self.hotKeyPrev === 'enter') {
                        return true;
                    }

                    return false;
                });

            }
        },
    ];
    this.fileContext = '';
    this.menuItemsFile = [
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextPreviewFile, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/visibility-button.svg'),
            onClick: function () {
                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                    response = JSON.parse(response);
                    if (response.path === undefined) {
                        return;
                    }

                    QuantumUtils.windowOpen("previewFile", response.path + '/' + self.fileContext.getAttribute('data-file'));
                });
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextRename, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/create-new-pencil-button.svg'),
            onClick: function () {

                QuantumUtils.prompt(QuantumviewfilesLang.fileName, self.fileContext.getAttribute('data-name'), function (result) {
                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.renameFile&path=" + encodeURIComponent(Filemanager.data.path) + '&file=' + encodeURIComponent(self.fileContext.getAttribute('data-file')) + '&name=' + encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                        response = JSON.parse(response);
                        if (response.status === undefined) {
                            return;
                        }

                        if (response.status === 'ok') {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        }
                    });
                });

            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonDuplicate, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/copy-content.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.fileContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.duplicate&path=" + encodeURIComponent(Filemanager.data.path)
                    + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                    + '&list=' + JSON.stringify(objectsPush)
                    + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {

                    Filemanager.events.trigger('reloadPaths', Filemanager);

                });
            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCopy, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/copy-content.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.fileContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                self.bufferCut = 0;
                self.bufferFromScope = Filemanager.data.scope;
                self.bufferFromPath = Filemanager.data.path;
                self.buffer = objectsPush;

                Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
                self.trigger('updateContextMenuArea');
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCut, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/cut-content-button.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let self_name = self.fileContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                self.bufferCut = 1;
                self.bufferFromScope = Filemanager.data.scope;
                self.bufferFromPath = Filemanager.data.path;
                self.buffer = objectsPush;

                Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
                self.trigger('updateContextMenuArea');
            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextCopyLink, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/link-button.svg'),
            onClick: function () {
                QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                    response = JSON.parse(response);
                    if (response.path === undefined) {
                        return;
                    }

                    let file = response.path + '/' + self.fileContext.getAttribute('data-file');
                    let buttonsId = 'button-' + QuantumUtils.randomInteger(111111, 999999);
                    QuantumUtils.alert('<input type="text" value="' + file + '" class="input-copy" /><button style="display: none" data-clipboard-text="' + file + '" class="' + buttonsId + '"></button>', [
                        {
                            name: QuantumviewfilesLang.contextCopyLink,
                            callback: function () {
                                let button = document.querySelector('.' + buttonsId);
                                new ClipboardJS(button);
                                button.click();
                            }
                        },
                    ]);

                });
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextDelete, 'ENT_QUOTES'),
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/rubbish-bin-delete-button.svg'),
            onClick: function () {
                let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                let objectsPush = [];
                let nameFile = '';
                let self_name = self.fileContext.getAttribute('data-fullname');
                objectsPush.push(self_name);

                for (let i = 0; i < objectAll.length; i++) {
                    if (objectAll[i].querySelector('input').checked) {
                        let object_name = objectAll[i].getAttribute('data-fullname');
                        if (object_name !== self_name) {
                            objectsPush.push(object_name);
                        }
                    }
                }

                if (objectsPush.length === 0) {
                    return;
                }

                if (objectsPush.length === 1) {
                    nameFile = ' ' + objectsPush[0];
                } else {
                    nameFile = '';
                }

                QuantumUtils.confirm(QuantumviewfilesLang.contextDelete + nameFile + '?', function (result) {
                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&list=' + encodeURIComponent(JSON.stringify(objectsPush)))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                }, function () {

                    if (self.hotKeyPrev === 'enter') {
                        return true;
                    }

                    return false;
                });

            }
        },
    ];
    this.IdsButtonForFile = [
        {
            'id': 'viewfilesWatermark',
            'exs': ['png', 'jpg', 'jpeg', 'webp'],
            'for': 'file',
            'count': 'some'
        },
        {
            'id': 'viewfilesDelete',
            'count': 'some'
        },
        {
            'id': 'viewfilesDuplicate',
            'count': 'some'
        },
        {
            'id': 'viewfilesCopy',
            'count': 'some'
        },
        {
            'id': 'viewfilesCut',
            'count': 'some'
        },
        {
            'id': 'viewfilesRename',
        },
        {
            'id': 'viewfilesFilePreview',
            'for': 'file'
        },
        {
            'id': 'viewfilesCopyLink',
            'for': 'file'
        },
        {
            'id': 'viewfilesPreviews',
            'exs': ['png', 'jpg', 'jpeg', 'webp'],
            'for': 'file',
            'count': 'some'
        }
    ];
    this.hotKeyPrev = '';
    this.hotKeys = {
        'Mac OS': {
            'delete': ['delete'],
            'rename': ['f2'],
            'copy': ['meta+c', 'meta+с'],
            'cut': ['meta+x', 'meta+ч'],
            'paste': ['meta+v', 'meta+м'],
            'back': ['backspace'],
            'reload': ['f5'],
        },
        'Windows': {
            'delete': ['delete'],
            'rename': ['f2'],
            'copy': ['ctrl+c', 'control+c', 'control+с'],
            'cut': ['ctrl+x', 'control+x', 'control+ч'],
            'paste': ['ctrl+v', 'control+v', 'control+м'],
            'back': ['backspace'],
            'reload': ['f5']
        },
        'Linux': {
            'delete': ['delete'],
            'rename': ['f2'],
            'copy': ['ctrl+c', 'control+c', 'control+с'],
            'cut': ['ctrl+x', 'control+x', 'control+ч'],
            'paste': ['ctrl+v', 'control+v', 'control+м'],
            'back': ['backspace'],
            'reload': ['f5']
        }
    };

    this.init = function () {

        this.path = this.options.directory;
        let openLastDir = localStorage.getItem('quantummanagerLastDir');
        let openLastDirHash = localStorage.getItem('quantummanagerLastDirHash');
        let sortField = localStorage.getItem('quantummanagerSortField');
        let sortDir = localStorage.getItem('quantummanagerSortDir');

        if (openLastDir !== null) {
            if (openLastDirHash === this.options.hash) {
                this.path = openLastDir;
            }
        }

        if (sortField !== null && sortDir !== null) {
            self.sortField = sortField;
            self.sortDir = sortDir;
        }

        this.loadDirectory();
        let searchByName = ViewfilesElement.querySelector('.filter-search input');

        if (!parseInt(self.options.onlyfiles)) {

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesBack',
                'left',
                'navigations',
                'qm-btn-back hidden-label',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonBack, 'ENT_QUOTES'),
                'quantummanager-icon-back',
                {},
                function (ev) {
                    let directory;

                    if (self.history.length > 0) {
                        directory = self.history[self.history.length - 1];
                        self.history.splice(self.history.length - 2, 2);
                        self.loadDirectory(directory);
                        Filemanager.data.path = directory;
                    }

                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesBack');
                    ev.preventDefault();
                });

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesUp',
                'left',
                'navigations',
                'qm-btn-up hidden-label',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonUp, 'ENT_QUOTES'),
                'quantummanager-icon-up',
                {},
                function (ev) {
                    if (Filemanager.data.path === undefined) {
                        ev.preventDefault();
                        return;
                    }

                    let currDirectories = Filemanager.data.path.split('/');
                    if (currDirectories.length > 1) {
                        currDirectories.pop();
                        Filemanager.data.path = currDirectories.join('/');
                        self.trigger('updatePath');
                    }

                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesUp');
                    ev.preventDefault();
                });

            let buttonGrid = Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesGrid',
                'center',
                'list-view',
                'qm-btn-grid hidden-label',
                '',
                'quantummanager-icon-grid',
                {'data-tooltip': QuantumviewfilesLang.changeGridViews},
                function (ev) {
                    Filemanager.Quantumviewfiles.ListviewToGrid();
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesGrid');
                    ev.preventDefault();
                }).parentElement;

            let buttonsGrid = [
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8',
                '9',
                '10',
            ];

            for (let i = 0; i < buttonsGrid.length; i++) {
                Filemanager.Quantumtoolbar.buttonAdd(
                    'viewfilesGrid-' + i,
                    'center',
                    'list-view',
                    'qm-btn-grid',
                    buttonsGrid[i],
                    '',
                    {},
                    function (ev) {
                        Filemanager.Quantumviewfiles.gridColumnSet = 'list-grid-1-' + buttonsGrid[i];
                        Filemanager.Quantumviewfiles.ListviewToGrid();
                        Filemanager.Quantumtoolbar.trigger('buttonViewfilesGrid');
                        ev.preventDefault();
                    }, buttonGrid);
            }


            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesTable',
                'center',
                'list-view',
                'qm-btn-table hidden-label',
                '',
                'quantummanager-icon-table',
                {},
                function (ev) {
                    Filemanager.Quantumviewfiles.ListviewToTable();
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesTable');
                    ev.preventDefault();
                });

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesCreateDirectory',
                'center',
                'file-actions',
                'qm-btn-create-directory hidden-label',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCreateDirectory, 'ENT_QUOTES'),
                'quantummanager-icon-directory',
                {},
                function (ev) {
                    QuantumUtils.prompt(QuantumviewfilesLang.directoryName, '', function (nameDirectory) {
                        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.createDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&name=' + encodeURIComponent(nameDirectory) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        });
                    });
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesCreateDirectory');
                    ev.preventDefault();
                });

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesDelete',
                'center',
                'file-actions',
                'qm-btn-delete qm-btn-hide hidden-label',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonDelete, 'ENT_QUOTES'),
                'quantummanager-icon-delete',
                {},
                function (ev) {

                    let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                    let files = [];

                    for (let i = 0; i < filesAll.length; i++) {
                        if (filesAll[i].querySelector('input').checked) {
                            files.push(filesAll[i].getAttribute('data-fullname'));
                        }
                    }

                    if (files.length === 0) {
                        return;
                    }

                    if (self.objectSelect === undefined || self.objectSelect === null) {
                        return;
                    }

                    let alert = '';
                    if (self.objectSelect.classList.contains('file-item')) {
                        alert = QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextDelete, 'ENT_QUOTES') + ' ' + self.file.getAttribute('data-file') + '?';
                    } else {
                        alert = QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextDelete, 'ENT_QUOTES') + ' ' + self.directory.getAttribute('data-fullname') + '?';
                    }


                    if (files.length > 1) {
                        alert = QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextSomeDelete, 'ENT_QUOTES') + '?';
                    }


                    QuantumUtils.confirm(alert, function (result) {

                        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&list=' + encodeURIComponent(JSON.stringify(files)) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        });

                        Filemanager.Quantumviewfiles.showMetaDirectory(true);
                        Filemanager.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('qm-btn-hide');
                    }, function () {

                        if (self.hotKeyPrev === 'enter') {
                            return true;
                        }

                        return false;
                    });

                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesDelete');

                });


            let buttonFilter = Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilter',
                'center',
                'file-actions',
                '',
                '',
                'quantummanager-icon-filter',
                {},
                function (ev) {
                })
                .parentElement;

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterNone',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortNone, 'ENT_QUOTES'),
                '',
                {},
                function (ev) {
                    self.setSort('', '');
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterName',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortName, 'ENT_QUOTES'),
                '',
                {},
                function (ev) {
                    self.setSort('name', self.sortDir);
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterSize',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortSize, 'ENT_QUOTES'),
                '',
                {},
                function (ev) {
                    self.setSort('size', self.sortDir);
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterUpdated',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortUpdated, 'ENT_QUOTES'),
                '',
                {},
                function (ev) {
                    self.setSort('dateM', self.sortDir);
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterDivider',
                'center',
                'file-actions',
                'qm-btn-width-small qm-btn-divider',
                '',
                '',
                {},
                function (ev) {
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterAsc',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortAsc, 'ENT_QUOTES'),
                'quantummanager-icon-arrow-up',
                {},
                function (ev) {
                    self.setSort(self.sortField, 'asc');
                },
                buttonFilter
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilterDesc',
                'center',
                'file-actions',
                'qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSortDesc, 'ENT_QUOTES'),
                'quantummanager-icon-arrow-down',
                {},
                function (ev) {
                    self.setSort(self.sortField, 'desc');
                },
                buttonFilter
            );


            let buttonOther = Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesOther',
                'center',
                'file-actions',
                'qm-btn-more',
                '',
                'quantummanager-icon-more',
                {},
                function (ev) {

                }).parentElement;


            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesSelectAll',
                'center',
                'file-actions',
                'qm-btn-select-all qm-btn-width-small',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonSelectAll, 'ENT_QUOTES'),
                'quantummanager-icon-select-all',
                {},
                function (ev) {
                    let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');

                    for (let i = 0; i < objectAll.length; i++) {
                        self.selectFile(objectAll[i], true);
                    }

                    self.trigger('clickObject');

                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesPaste',
                'center',
                'file-actions',
                'qm-btn-paste qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonPaste, 'ENT_QUOTES'),
                'quantummanager-icon-paste',
                {},
                function (ev) {
                    if (self.buffer.length === 0) {
                        return;
                    }

                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.paste" +
                        '&pathFrom=' + encodeURIComponent(self.bufferFromPath) +
                        '&pathTo=' + encodeURIComponent(Filemanager.data.path) +
                        '&list=' + encodeURIComponent(JSON.stringify(self.buffer)) +
                        "&scopeFrom=" + encodeURIComponent(self.bufferFromScope) +
                        "&scopeTo=" + encodeURIComponent(Filemanager.data.scope) +
                        "&cut=" + encodeURIComponent(self.bufferCut))
                    ).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);

                        self.bufferCut = false;
                        self.bufferFromScope = '';
                        self.bufferFromPath = '';
                        self.buffer = [];

                        Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.add('qm-btn-hide');

                    });
                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesDuplicate',
                'center',
                'file-actions',
                'qm-btn-duplicate qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonDuplicate, 'ENT_QUOTES'),
                'quantummanager-icon-copy',
                {},
                function (ev) {
                    let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                    let objectsPush = [];

                    for (let i = 0; i < objectAll.length; i++) {
                        if (objectAll[i].querySelector('input').checked) {
                            objectsPush.push(objectAll[i].getAttribute('data-fullname'));
                        }
                    }

                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.duplicate&path=" + encodeURIComponent(Filemanager.data.path)
                        + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                        + '&list=' + JSON.stringify(objectsPush)
                        + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {

                        Filemanager.events.trigger('reloadPaths', Filemanager);

                    });

                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesCopy',
                'center',
                'file-actions',
                'qm-btn-copy qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCopy, 'ENT_QUOTES'),
                'quantummanager-icon-copy',
                {},
                function (ev) {
                    let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                    let objectsPush = [];

                    for (let i = 0; i < objectAll.length; i++) {
                        if (objectAll[i].querySelector('input').checked) {
                            objectsPush.push(objectAll[i].getAttribute('data-fullname'));
                        }
                    }

                    self.bufferCut = 0;
                    self.bufferFromScope = Filemanager.data.scope;
                    self.bufferFromPath = Filemanager.data.path;
                    self.buffer = objectsPush;

                    Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
                    self.trigger('updateContextMenuArea');

                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesCut',
                'center',
                'file-actions',
                'qm-btn-cut qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonCut, 'ENT_QUOTES'),
                'quantummanager-icon-cut',
                {},
                function (ev) {
                    let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                    let objectsPush = [];

                    for (let i = 0; i < objectAll.length; i++) {
                        if (objectAll[i].querySelector('input').checked) {
                            objectsPush.push(objectAll[i].getAttribute('data-fullname'));
                        }
                    }

                    self.bufferCut = 1;
                    self.bufferFromScope = Filemanager.data.scope;
                    self.bufferFromPath = Filemanager.data.path;
                    self.buffer = objectsPush;

                    Filemanager.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');

                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesRename',
                'center',
                'file-actions',
                'qm-btn-file-rename qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextRename, 'ENT_QUOTES'),
                'quantummanager-icon-edit',
                {},
                function (ev) {

                    if (self.objectSelect.classList.contains('file-item')) {
                        QuantumUtils.prompt(QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.fileName, 'ENT_QUOTES'), self.file.getAttribute('data-name'), function (result) {
                            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.renameFile&path=" + encodeURIComponent(Filemanager.data.path) + '&file=' + encodeURIComponent(self.file.getAttribute('data-file')) + '&name=' + encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                                response = JSON.parse(response);
                                if (response.status === undefined) {
                                    return;
                                }

                                if (response.status === 'ok') {
                                    Filemanager.events.trigger('reloadPaths', Filemanager);
                                }
                            });
                        });
                    } else {
                        let name = self.directory.querySelector('.directory-name').innerHTML;

                        QuantumUtils.prompt(QuantumviewfilesLang.directoryName, name, function (result) {
                            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.renameDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&oldName=' + encodeURIComponent(name) + '&name=' + encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                                response = JSON.parse(response);
                                if (response.status === undefined) {
                                    return;
                                }

                                if (response.status === 'ok') {
                                    Filemanager.events.trigger('reloadPaths', Filemanager);
                                }
                            });
                        });
                    }


                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesFilePreview',
                'center',
                'file-actions',
                'qm-btn-file-preview qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextPreviewFile, 'ENT_QUOTES'),
                'quantummanager-icon-eye',
                {},
                function (ev) {

                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path)
                        + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                        + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {

                        response = JSON.parse(response);
                        if (response.path === undefined) {
                            return;
                        }

                        QuantumUtils.windowOpen("previewFile", response.path + '/' + self.file.getAttribute('data-file'));
                    });

                },
                buttonOther
            );

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesCopyLink',
                'center',
                'file-actions',
                'qm-btn-copylink qm-btn-width-small qm-btn-hide',
                QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.contextCopyLink, 'ENT_QUOTES'),
                'quantummanager-icon-link',
                {},
                function (ev) {

                    QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path)
                        + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                        + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {

                        response = JSON.parse(response);
                        if (response.path === undefined) {
                            return;
                        }

                        let file = response.path + '/' + self.file.getAttribute('data-file');
                        let buttonsId = 'button-' + QuantumUtils.randomInteger(111111, 999999);
                        QuantumUtils.alert('<input type="text" value="' + file + '" class="input-copy" /><button style="display: none" data-clipboard-text="' + file + '" class="' + buttonsId + '"></button>', [
                            {
                                name: QuantumviewfilesLang.contextCopyLink,
                                callback: function () {
                                    let button = document.querySelector('.' + buttonsId);
                                    new ClipboardJS(button);
                                    button.click();
                                    QuantumUtils.notify({fm: Filemanager, text: QuantumviewfilesLang.copied});
                                }
                            },
                        ]);

                    });

                },
                buttonOther
            );


            if (typeof QuantumviewfilesPreviews === 'object' && Object.keys(QuantumviewfilesPreviews).length > 0) {
                let buttonPreviews = Filemanager.Quantumtoolbar.buttonAdd(
                    'viewfilesPreviews',
                    'center',
                    'file-actions',
                    'qm-btn-more hidden-label',
                    QuantumviewfilesLang.buttonPreviews,
                    'quantummanager-icon-previews',
                    {},
                    function (ev) {
                    }).parentElement;

                let i = 0;
                for (let k in QuantumviewfilesPreviews) {
                    Filemanager.Quantumtoolbar.buttonAdd(
                        'viewfilesPreview' + i,
                        'center',
                        'file-actions',
                        'qm-btn-preview qm-btn-width-small qm-btn-hide',
                        QuantumviewfilesPreviews[k].label,
                        '',
                        {},
                        function (ev) {
                            let objectAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                            let objectsPush = [];

                            for (let i = 0; i < objectAll.length; i++) {
                                if (objectAll[i].querySelector('input').checked) {
                                    objectsPush.push(objectAll[i].getAttribute('data-fullname'));
                                }
                            }

                            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.createPreview" +
                                '&list=' + encodeURIComponent(JSON.stringify(objectsPush)) +
                                '&path=' + encodeURIComponent(Filemanager.data.path) +
                                "&scope=" + encodeURIComponent(Filemanager.data.scope) +
                                "&preview=" + encodeURIComponent(QuantumviewfilesPreviews[k].label))
                            ).done(function (response) {
                                if (parseInt(self.options.previewsfolder) && parseInt(self.options.previewsfolderopen)) {
                                    Filemanager.data.path += '/_thumb';
                                }

                                response = JSON.parse(response);
                                let selectFiles = [];
                                for (let k in response) {
                                    selectFiles.push(response[k].name);
                                }

                                self.loadDirectory(Filemanager.data.path, function () {
                                    Filemanager.Quantumviewfiles.scrollTopFilesCheck(selectFiles);

                                    let filesAll = Filemanager.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
                                    let find = false;
                                    let element;

                                    for (let i = 0; i < filesAll.length; i++) {
                                        if (filesAll[i].querySelector('input').checked) {
                                            self.priorityMetaFile = filesAll[i];
                                            Filemanager.Quantumviewfiles.selectFile(filesAll[i], true);

                                        }
                                    }

                                    let countSelected = self.getCountSelected();

                                    if (countSelected > 1) {
                                        self.showMetaCountFile(countSelected);
                                    }

                                    Filemanager.Quantumviewfiles.initBreadcrumbs(Filemanager.Quantumviewfiles.buildBreadcrumbs);

                                })

                            });

                            ev.preventDefault();
                        },
                        buttonPreviews
                    );

                    self.IdsButtonForFile.push({
                        'id': 'viewfilesPreview' + i,
                        'exs': ['png', 'jpg', 'jpeg', 'webp'],
                        'for': 'file',
                        'count': 'some'
                    });

                    i++;
                }
            }


            if (self.options.watermark === '1') {
                Filemanager.Quantumtoolbar.buttonAdd(
                    'viewfilesWatermark',
                    'center',
                    'file-actions',
                    'qm-btn-delete qm-btn-hide hidden-label',
                    QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.buttonWatermark, 'ENT_QUOTES'),
                    'quantummanager-icon-watermark',
                    {},
                    function (ev) {

                        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
                        let files = [];

                        for (let i = 0; i < filesAll.length; i++) {
                            if (filesAll[i].querySelector('input').checked) {
                                files.push(filesAll[i].getAttribute('data-file'));
                            }
                        }

                        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.watermark&path=" + encodeURIComponent(Filemanager.data.path) + '&list=' + encodeURIComponent(JSON.stringify(files)) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        });

                        Filemanager.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('qm-btn-hide');
                        Filemanager.Quantumtoolbar.trigger('buttonViewfilesWatermark');
                        ev.preventDefault();
                    });
            }

            Filemanager.Quantumtoolbar.buttonAdd(
                'viewfilesReloadPaths',
                'center',
                'file-other',
                'qm-btn-reload',
                '',
                'quantummanager-icon-reload',
                {},
                function (ev) {
                    Filemanager.events.trigger('reloadPaths', Filemanager);
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesReloadPaths');
                    ev.preventDefault();
                });

        }

        searchByName.addEventListener('keyup', function () {
            self.searchByName(this.value);
        });

        self.contextMenu = new Contextual({
            items: []
        });

        self.contextMenu.hide();

        ViewfilesElement.querySelector('.view-wrap .view').addEventListener('contextmenu', function (ev) {
            ev.preventDefault();

            self.contextMenu.clear();

            for (let i = 0; i < self.menuItemsArea.length; i++) {
                self.contextMenu.add(new ContextualItem(self.menuItemsArea[i]));
            }

            self.contextMenu.show(ev);
        });

        if (self.viewMeta !== null) {
            self.viewMeta.addEventListener('mouseup', function () {
                let text = '';
                if (window.getSelection) {
                    text = window.getSelection().toString();
                } else if (document.selection) {
                    text = document.selection.createRange().text;
                }

                if (text.replace(' ', '') !== '') {
                    QuantumUtils.copyInBuffer(text);
                    QuantumUtils.notify({fm: Filemanager, text: QuantumviewfilesLang.copied});
                }

            });
        }
    };

    this.initAfter = function () {
        if (Filemanager.Qantumupload !== undefined) {
            if (Filemanager.Qantumupload.options.dropAreaHidden === '1') {
                Filemanager.Quantumtoolbar.buttonAdd('viewfilesUploadFile', 'center', 'file-other', 'qm-btn-upload-file hidden-label', QuantumviewfilesLang.buttonUpload, 'quantummanager-icon-upload', {}, function (ev) {
                    Filemanager.Qantumupload.selectFiles();
                    ev.preventDefault();
                });
            }
        }

        if (parseInt(this.options.help)) {
            Filemanager.Quantumtoolbar.buttonAdd('viewfilesHelp', 'right', 'file-other', 'qm-btn-back hidden-label', QuantumviewfilesLang.help, 'quantummanager-icon-info', {}, function (ev) {
                QuantumUtils.alert('<div class="quantummanager-help quantummanager-flex quantummanager-flex-divider">' +
                    '<div>' +
                    '<div>' +
                    '<ul class="quantummanager-list quantummanager-text-left">' +
                    '<li><a href="https://www.norrnext.com/quantum-manager" target="_blank">' + QuantumviewfilesLang.helpButtonProductPage + '</a></li>' +
                    '<li><a href="https://docs.norrnext.com/quantum-manager/" target="_blank">' + QuantumviewfilesLang.helpButtonDocumentation + '</a></li>' +
                    '<li><a href="https://norrnext.com/joomla-extensions/quantum-manager#translations" target="_blank">' + QuantumviewfilesLang.helpButtonLocalizations + '</a></li>' +
                    '<li><a href="https://github.com/Quantum-Manager/tracker" target="_blank">' + QuantumviewfilesLang.helpButtonSupport + '</a></li>' +
                    '<li><a href="https://extensions.joomla.org/extension/quantum-manager/" target="_blank">' + QuantumviewfilesLang.helpButtonReview + '</a></li>' +
                    '</ul>' +
                    '</div>' +
                    '<div>' +
                    '<h3 class="quantummanager-text-left">' + QuantumviewfilesLang.helpHotkeys + '</h3>' +
                    '<ul class="quantummanager-list quantummanager-text-left">' +
                    '<li>Ctrl+C/Cmd+C - ' + QuantumviewfilesLang.helpHotkeysCopy + '</li>' +
                    '<li>Ctrl+X/Cmd+X - ' + QuantumviewfilesLang.helpHotkeysCut + '</li>' +
                    '<li>Ctrl+V/Cmd+V - ' + QuantumviewfilesLang.helpHotkeysPaste + '</li>' +
                    '<li>F2 - ' + QuantumviewfilesLang.helpHotkeysRename + '</li>' +
                    '<li>F5 - ' + QuantumviewfilesLang.helpHotkeysRefresh + '</li>' +
                    '<li>Delete - ' + QuantumviewfilesLang.helpHotkeysDelete + '</li>' +
                    '<li>Backspace - ' + QuantumviewfilesLang.helpHotkeysBack + '</li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '<div class="quantummanager-about quantummanager-text-left"><div class="text">Quantum Manager ' + QuantumviewfilesVerison + ' ' + QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.helpText, 'ENT_QUOTES') +
                    '</div><div class="copyright">' + QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.helpCopyright, 'ENT_QUOTES') +
                    '</div><div class="copyright-images">' + QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.helpCopyrightImages, 'ENT_QUOTES') +
                    '</div><div class="love">' + QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.helpLove, 'ENT_QUOTES') +
                    ' <img src="' + QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/favorite-heart-button.svg') + '" class="svg" />' +
                    '</div>' +
                    '</div>'
                );
                setTimeout(function () {
                    QuantumUtils.replaceImgToSvg('.quantummanager-about .love');
                }, 100);
            });
        }

    };

    this.loadDirectory = function (path, callback) {

        let self = this;
        let scope = Filemanager.data.scope;

        if (path === null || path === undefined) {
            path = this.path;
        } else {
            this.path = path;
        }

        if (Filemanager.data.path === undefined || Filemanager.data.path !== path) {
            Filemanager.data.path = path;
        }

        ViewfilesElement.querySelector('.view').innerHTML = '';
        this.preoloader();

        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getFiles&path=" + encodeURIComponent(path) + '&scope=' + encodeURIComponent(scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
            try {
                response = JSON.parse(response);
            } catch (e) {
                ViewfilesElement.querySelector('.view-wrap .view').innerHTML = "<div class='error'><div>" + QuantumUtils.htmlspecialcharsDecode(QuantumviewfilesLang.error) + "</div></div>";
                return;
            }

            if (response.error !== undefined) {
                Filemanager.data.path = self.options.directory;
                self.trigger('updatePath');
                return;
            }

            let htmlfilesAndDirectories = '<div class="field-list-files"><div class="list">';
            let files = response.files;
            let directories = response.directories;

            if (!parseInt(self.options.onlyfiles)) {
                for (let i = 0; i < directories.length; i++) {
                    let addClass = '';
                    if (directories[i]['is_writable'] === 0) {
                        addClass = 'lock ';
                    }

                    if (directories[i]['is_empty'] === 0) {
                        addClass += 'empty';
                    }

                    htmlfilesAndDirectories += "<div class='object-select directory-item " + addClass + "' data-iswritable='" + directories[i]['is_writable'] + "' data-fullname='" + directories[i]['name'] + "'><input type=\"checkbox\" class=\"import-files-check-file\"><span class='check'></span><div class='directory'><div class='directory-icon'><span></span></div><div class='directory-name'>" + directories[i]['name'] + "</div></div></div>";
                }
            }

            if (self.sortField !== '' && self.sortDir !== '') {
                if (self.sortDir === 'asc') {
                    files.sort(function (a, b) {
                        if (a[self.sortField] > b[self.sortField]) {
                            return 1;
                        }

                        if (a[self.sortField] < b[self.sortField]) {
                            return -1;
                        }

                        return 0;
                    });
                }

                if (self.sortDir === 'desc') {
                    files.sort(function (a, b) {
                        if (a[self.sortField] > b[self.sortField]) {
                            return -1;
                        }

                        if (a[self.sortField] < b[self.sortField]) {
                            return 1;
                        }

                        return 0;
                    });
                }
            }

            for (let i = 0; i < files.length; i++) {
                let type = files[i]['exs'];
                let addClass = '';
                if (files[i]['is_writable'] === 0) {
                    addClass = 'lock';
                }

                htmlfilesAndDirectories += "<div class='object-select file-item " + addClass + "' data-iswritable='" + files[i]['is_writable'] + "' data-size='" + files[i]['size'] + "' data-name='" + files[i]['name'] + "' data-exs='" + files[i]['exs'] + "' data-fileP='" + files[i]['fileP'] + "' data-dateC='" + files[i]['dateC'] + "' data-dateM='" + files[i]['dateM'] + "' data-file='" + files[i]['file'] + "' data-fullname='" + files[i]['file'] + "'><input type=\"checkbox\" class=\"import-files-check-file\"><span class='check'></span><div class='file'><div class='context-menu-open'><span></span></div><div class='file-exs icon-file-" + type + "'><div class='av-folderlist-label'></div></div><div class='file-name'>" + files[i]['file'] + "</div></div></div>";
            }

            htmlfilesAndDirectories += "</div></div>";

            if (files.length === 0 && directories.length === 0) {
                htmlfilesAndDirectories = "<div class='empty'><div>" + QuantumviewfilesLang.empty + "</div></div>";
            }

            ViewfilesElement.querySelector('.view-wrap .view').innerHTML = '';
            ViewfilesElement.querySelector('.view-wrap .view').innerHTML = htmlfilesAndDirectories;
            self.reloadTypeViewFiles(path);
            self.listFiles = ViewfilesElement.querySelector('.field-list-files');

            if (self.bufferTopDirectories[path] !== undefined) {
                if (self.listFiles !== null && self.listFiles.scrollTop !== undefined) {
                    self.listFiles.scrollTop = self.bufferTopDirectories[path];
                }
            }

            let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
            let directoriesAll = ViewfilesElement.querySelectorAll('.field-list-files .directory-item');
            let timer = 0;
            let delay = 200;
            let prevent = false;
            self.fileMenu = new Contextual({
                items: []
            });
            self.fileMenu.hide();

            for (let i = 0; i < filesAll.length; i++) {
                let inputCheck = filesAll[i].querySelector('.import-files-check-file');

                filesAll[i].addEventListener('click', function (ev) {
                    if (ev.target.classList.contains('check')) {
                        self.ds.multiSelectMode = true;
                    }
                });

                filesAll[i].addEventListener('dblclick', function () {
                    let element = this;
                    clearTimeout(timer);
                    prevent = true;
                    self.fileDblclick(element, self);
                });

                filesAll[i].addEventListener('contextmenu', function (ev) {
                    self.fileContext = filesAll[i];
                    self.contextMenu.clear();
                    let exs = self.fileContext.getAttribute('data-exs').toLocaleLowerCase();
                    let writeable = parseInt(self.fileContext.getAttribute('data-iswritable'));
                    let tmpContextMenu = [];
                    let tmpContextMenuSource = self.menuItemsFile;
                    let addContextMenu = self.trigger('addContextMenuFile');
                    let tmpAddContextMenu = [];

                    if (addContextMenu !== undefined && (typeof addContextMenu === 'object')) {

                        for (let i = 0; i < addContextMenu.length; i++) {

                            if (addContextMenu[i][0].fileExs.indexOf(exs) === -1) {
                                continue;
                            }

                            if (writeable !== addContextMenu[i][0].writeable) {
                                continue;
                            }

                            tmpAddContextMenu = tmpAddContextMenu.concat(addContextMenu[i]);
                        }

                    }

                    for (let i = 0; i < tmpContextMenuSource.length; i++) {

                        if ((writeable === 0) && (tmpContextMenuSource[i].writeable === 1)) {
                            continue;
                        }

                        tmpAddContextMenu = tmpAddContextMenu.concat(tmpContextMenuSource[i]);
                    }

                    tmpContextMenu = tmpAddContextMenu.concat(tmpContextMenu);

                    for (let i = 0; i < tmpContextMenu.length; i++) {
                        self.contextMenu.add(new ContextualItem(tmpContextMenu[i]));
                    }

                    self.contextMenu.show(ev);
                    ev.preventDefault();
                });
            }

            if (!parseInt(self.options.onlyfiles)) {
                self.directoryMenu = new Contextual({
                    items: []
                });
                self.contextMenu.hide();

                for (let i = 0; i < directoriesAll.length; i++) {
                    let event_name = 'click';

                    if (
                        self.options.folderdblclick !== undefined &&
                        self.options.folderdblclick === '1') {
                        event_name = 'dblclick';
                    }

                    let event_click = function (ev) {
                        if (!ev.target.classList.contains('check')) {
                            let directory = ev.target.closest('.directory').querySelector('.directory-name').innerHTML;
                            Filemanager.data.path = self.path + '/' + directory;

                            if (localStorage !== undefined) {
                                localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
                                localStorage.setItem('quantummanagerLastDirHash', self.options.hash);
                            }

                            self.clearAllSelectFile();
                            self.directory = this;
                            self.trigger('clickObject');
                            self.trigger('updatePath');
                            self.trigger('clickDirectory');
                        } else {
                            self.ds.multiSelectMode = true;
                        }
                    };
                    let event_contextmenu = function (ev) {
                        self.directoryContext = directoriesAll[i];
                        self.contextMenu.clear();
                        let writeable = parseInt(self.directoryContext.getAttribute('data-iswritable'));
                        let tmpContextMenu = [];
                        let tmpContextMenuSource = self.menuItemsDirectories;
                        let addContextMenu = self.trigger('addContextMenuDirectory');
                        let tmpAddContextMenu = [];

                        for (let i = 0; i < tmpContextMenuSource.length; i++) {

                            if ((writeable === 0) && (tmpContextMenuSource[i].writeable === 1)) {
                                continue;
                            }

                            tmpContextMenu = tmpContextMenu.concat(tmpContextMenuSource[i]);
                        }

                        if (addContextMenu !== undefined && (typeof addContextMenu === 'object')) {

                            for (let i = 0; i < addContextMenu.length; i++) {
                                tmpAddContextMenu = tmpAddContextMenu.concat(addContextMenu[i]);
                            }

                        }

                        tmpContextMenu = tmpAddContextMenu.concat(tmpContextMenu);

                        for (let i = 0; i < tmpContextMenu.length; i++) {
                            self.contextMenu.add(new ContextualItem(tmpContextMenu[i]));
                        }

                        if (tmpContextMenu.length > 0) {
                            self.contextMenu.show(ev);
                        }

                        ev.preventDefault();
                    };
                    let time_start = 0;

                    directoriesAll[i].addEventListener(event_name, event_click);
                    directoriesAll[i].addEventListener('contextmenu', event_contextmenu);
                    directoriesAll[i].addEventListener('touchstart', function (ev) {
                        time_start = Date.now();
                    });
                    directoriesAll[i].addEventListener('touchend', function (ev) {
                        console.log((Date.now() - time_start));
                        if ((Date.now() - time_start) > 150) {
                            event_contextmenu(ev);
                        } else {
                            event_click(ev);
                        }
                    });
                }
            }

            if (self.searchNameValue !== '') {
                self.searchByName(self.searchNameValue);
            }

            self.buildBreadcrumbs();
            self.metaFileLoad = {};

            if ((filesAll.length > 0) || (directoriesAll.length > 0)) {
                self.ds = undefined;
                self.dsP;
                self.dsElemet;
                self.dsCount = 0;

                self.ds = new DragSelect({
                    selectables: ViewfilesElement.querySelectorAll('.field-list-files .object-select'),
                    area: ViewfilesElement.querySelector('.field-list-files .list'),
                    customStyles: false,
                    multiSelectKeys: ['ctrlKey', 'shiftKey', 'metaKey'],
                    multiSelectMode: false,
                    autoScrollSpeed: 3,
                    onDragStart: function (ev) {
                        if (ev.target.classList.contains('list')) {
                            self.dsP = new Promise((resolve, reject) => {
                                self.ds.clearSelection();
                                resolve();
                            });
                        }
                    },
                    onElementSelect: function (element, ev) {
                        self.selectFile(element, self);
                    },
                    onElementUnselect: function (element) {
                        self.unSelectFile(element, self);
                    },
                    callback: function (elements) {

                        let countSelected = self.getCountSelected();
                        let objectSelect = elements[0];
                        let setFile = true;

                        if (countSelected) {
                            if (countSelected === 1) {
                                if (self.objectSelect === objectSelect) {
                                    self.unSelectFile(objectSelect, self);
                                    self.ds.clearSelection();
                                    self.objectSelect = undefined;
                                    setFile = false;
                                    countSelected = 0;
                                }
                            }

                        }


                        if (setFile) {
                            if (elements.length) {
                                self.objectSelect = objectSelect;
                            } else {
                                self.objectSelect = undefined;
                            }
                        }


                        if (countSelected) {

                            if (countSelected > 1) {
                                self.showMetaCountFile(countSelected);
                            } else {
                                if (self.objectSelect !== undefined) {
                                    if (self.objectSelect.classList.contains('file-item')) {
                                        self.showMetaFile(self.objectSelect);
                                    } else {
                                        self.showMetaCountFile(countSelected);
                                    }
                                }
                            }

                        } else {
                            self.metaReset = true;
                            self.showMetaDirectory();
                        }

                        self.trigger('clickObject', objectSelect);


                        if (objectSelect !== undefined) {
                            if (objectSelect.classList.contains('file-item')) {
                                self.file = objectSelect;
                                self.trigger('clickFile', objectSelect);
                            } else {
                                self.directory = objectSelect;
                                self.trigger('clickDirectory', objectSelect);
                            }
                        } else {
                            self.file = objectSelect;
                            self.directory = objectSelect;

                            self.trigger('clickFile', objectSelect);
                            self.trigger('clickDirectory', objectSelect);
                        }

                    }
                });
            } else {
                if (self.ds !== undefined && self.ds !== null) {
                    self.ds.clearSelection();
                }
            }

            self.showMetaDirectory(true);


            if (self.path.split('/').length === 1) {
                Filemanager.Quantumtoolbar.buttonsList['viewfilesUp'].setAttribute('disabled', 'disabled');
            } else {
                Filemanager.Quantumtoolbar.buttonsList['viewfilesUp'].removeAttribute('disabled');
            }

            if (callback !== undefined) {
                callback();
            }

        });

    };

    this.getCountSelected = function () {

        if (this.ds !== undefined) {
            return this.ds.getSelection().length;
        }

        return 0;
    };

    this.getSelectObjects = function () {
        let output = [];
        let objectAll = this.element.querySelectorAll('.field-list-files .object-select');

        for (let i = 0; i < objectAll.length; i++) {
            if (!objectAll[i].querySelector('input').checked) {
                continue;
            }

            output.push(objectAll[i]);
        }

        return output;
    }

    this.getSelectFolders = function () {
        let output = [];
        let objectAll = this.getSelectObjects();

        for (let i = 0; i < objectAll.length; i++) {
            if (!objectAll[i].classList.contains('directory-item')) {
                continue;
            }

            output.push(objectAll[i]);
        }

        return output;
    }

    this.getSelectFiles = function () {
        let output = [];
        let objectAll = this.getSelectObjects();

        for (let i = 0; i < objectAll.length; i++) {
            if (!objectAll[i].classList.contains('file-item')) {
                continue;
            }

            output.push(objectAll[i]);
        }

        return output;
    }

    this.selectFile = function (element, triggerFlag) {
        let self = this;
        let tmpInput = element.closest('.object-select').querySelector('.import-files-check-file');
        tmpInput.checked = true;

        let r = self.ds.addSelection(element);
        let countSelected = self.getCountSelected();

        if (countSelected > 1) {
            self.showMetaCountFile(countSelected);
        } else {
            self.showMetaFile(element);
        }


        self.ds.multiSelectMode = false;

        if (triggerFlag !== null && triggerFlag === true) {
            if (element.classList.contains('file-item')) {
                self.objectSelect = element;
                self.file = element;
                self.trigger('clickFile', self);
                self.trigger('clickObject', self);
            } else {
                self.objectSelect = element;
                self.directory = element;
                self.trigger('clickDirectory', self);
                self.trigger('clickObject', self);
            }

        }

    };

    this.unSelectFile = function (element, qvf) {

        let self = this;
        let countSelected = self.getCountSelected();
        let tmpInput = element.closest('.object-select').querySelector('.import-files-check-file');
        tmpInput.checked = false;

        if (countSelected) {

            if (countSelected > 1) {
                self.showMetaCountFile(countSelected);
            } else {
                //self.showMetaFile(self.file);
            }

        } else {
            //self.metaReset = true;
            //self.showMetaDirectory();
        }

    };

    this.fileDblclick = function (element, qvf) {
        qvf.trigger('dblclickObject', element);
    };

    this.preoloader = function () {
        ViewfilesElement.querySelector('.view').innerHTML = "<div class=\"loader\">" + QuantumviewfilesLang.loading + "<span></span><span></span><span></span><span></span></div>";
    };

    this.hideMeta = function () {
        let self = this;

        if (self.options.metafile === '1') {
            self.viewMeta.classList.add('hidden');
        }

    };

    this.showMetaFile = function (element) {
        let self = this;
        let tmpLength = 0;

        if (self.options.metafile === '1') {

            if (element.classList.contains('directory-item')) {
                return;
            }

            let url = QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getMetaFile&path=" + encodeURIComponent(self.path) + '&name=' + encodeURIComponent(element.getAttribute('data-file')) + '&scope=' + Filemanager.data.scope);

            if (self.metaFileLoad[url] === undefined) {
                self.metaFileLoad[url] = 1;
            } else {
                self.metaFileLoad[url]++;
            }


            QuantumUtils.ajaxGet(url).done(function (response) {

                self.metaFileLoad[url]--;

                if (self.metaFileLoad[url] < 0) {
                    self.metaFileLoad[url] = 0;
                }

                for (let i in self.metaFileLoad) {
                    if (!isNaN(self.metaFileLoad[i])) {
                        tmpLength += self.metaFileLoad[i];
                    }
                }

                if (self.getCountSelected() > 1) {
                    return;
                }

                response = JSON.parse(response);
                if (response.global !== undefined || response.find !== undefined) {
                    self.viewMeta.classList.remove('hidden');

                    let html = '<div>';

                    if (response.preview !== undefined) {
                        if (response.preview.link === 'none') {
                            html += '<div class="meta-preview">' + self.generateIconFile() + '</div>';
                        } else {
                            html += '<div class="meta-preview meta-preview-open"><img src="' + response.preview.link + '" draggable="false" /></div>';
                        }

                        if (response.preview.name !== undefined) {
                            html += '<div class="meta-preview-name"><span class="quantummanager-icon quantummanager-icon-copy meta-preview-name-copy"></span><span>' + response.preview.name + '</span></div>';
                        }
                    }

                    if (response.global !== undefined) {
                        html += '<div class="meta-table">';
                        for (let i in response.global) {

                            if (response.global[i].key === undefined) {
                                continue;
                            }

                            if (response.global[i].key !== '') {
                                html += '<div><div>' + response.global[i].key + '</div><div>' + response.global[i].value + '</div></div>';
                            } else {
                                html += '<div class="only-value"><div>' + response.global[i].value + '</div></div>';
                            }

                        }
                        html += '</div>';
                    }

                    if (response.find !== undefined) {
                        if (Object.keys(response.find).length > 0) {
                            html += '<span class="show-all-tags">' + QuantumviewfilesLang.metaFileShow + '</span>';
                            html += '<div class="meta-table meta-find meta-hidden">';
                            for (let i in response.find) {

                                if (response.find[i].key === undefined) {
                                    continue;
                                }

                                html += '<div><div>' + response.find[i].key + '</div><div>' + response.find[i].value + '</div></div>';
                            }
                            html += '</div>';
                        }
                    }

                    html += '</div>';
                    self.viewMeta.querySelector('.meta-file-list').innerHTML = html;

                    let previewOpen = self.viewMeta.querySelector('.meta-preview-open');
                    let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');
                    let metaPreviewNameCopy = self.viewMeta.querySelector('.meta-preview-name-copy');

                    if (previewOpen !== null) {
                        let previewOpenImg = previewOpen.querySelector('img');
                        previewOpenImg.addEventListener('click', function () {

                            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                                response = JSON.parse(response);
                                if (response.path === undefined) {
                                    return;
                                }

                                QuantumUtils.windowOpen("previewFile", response.path + '/' + self.file.getAttribute('data-file'));
                            });

                        });
                    }

                    if (buttonToggleTags !== null) {
                        let metaFind = self.viewMeta.querySelector('.meta-find');
                        buttonToggleTags.addEventListener('click', function () {
                            if (this.classList.contains('active')) {
                                this.classList.remove('active');
                                this.innerHTML = QuantumviewfilesLang.metaFileShow;
                                metaFind.classList.add('meta-hidden');
                            } else {
                                this.classList.add('active');
                                this.innerHTML = QuantumviewfilesLang.metaFileHide;
                                metaFind.classList.remove('meta-hidden');
                            }
                        });
                    }

                    if (metaPreviewNameCopy !== null) {
                        metaPreviewNameCopy.addEventListener('click', function () {
                            let self = this;
                            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path)
                                + '&scope=' + encodeURIComponent(Filemanager.data.scope)
                                + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (responsePath) {

                                responsePath = JSON.parse(responsePath);
                                if (responsePath.path === undefined) {
                                    return;
                                }

                                let file = responsePath.path + '/' + response.preview.name;

                                QuantumUtils.copyInBuffer(file);
                                QuantumUtils.notify({fm: Filemanager, text: QuantumviewfilesLang.copied});

                            });
                        });
                    }

                } else {
                    self.viewMeta.classList.add('hidden');
                }

                if (tmpLength === 0) {
                    let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');
                    for (let i = 0; i < filesAll.length; i++) {
                        if (filesAll[i].querySelector('input').checked) {
                            return;
                        }
                    }
                    self.showMetaDirectory();
                }

            });
        }
    };

    this.showMetaDirectory = function (cacheReset) {
        let self = this;

        if (self.options.metafile === '1') {

            if (!cacheReset && self.getCountSelected() >= 1) {
                return;
            }

            if (cacheReset === null || cacheReset === undefined || cacheReset === false) {
                if (self.path === self.cacheMetaPath) {
                    self.viewMeta.querySelector('.meta-file-list').innerHTML = self.cacheMeta;
                    let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');

                    if (buttonToggleTags !== null) {
                        let metaFind = self.viewMeta.querySelector('.meta-find');
                        buttonToggleTags.addEventListener('click', function () {
                            if (this.classList.contains('active')) {
                                this.classList.remove('active');
                                this.innerHTML = QuantumviewfilesLang.metaFileShow;
                                metaFind.classList.add('meta-hidden');
                            } else {
                                this.classList.add('active');
                                this.innerHTML = QuantumviewfilesLang.metaFileHide;
                                metaFind.classList.remove('meta-hidden');
                            }
                        });
                    }

                    return;
                }
            }

            if (self.metaLoadDirCurrent === (Filemanager.data.scope + '/' + self.path)) {
                return;
            }

            self.metaLoadDirCurrent = Filemanager.data.scope + '/' + self.path;

            QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getMetaFile&path=" + encodeURIComponent(self.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {

                response = JSON.parse(response);
                if (response.global !== undefined || response.find !== undefined) {
                    self.viewMeta.classList.remove('hidden');

                    let html = '<div>';

                    if (response.preview !== undefined) {
                        html += '<div class="meta-preview meta-preview-folder"><img src="' + response.preview.link + '" draggable="false" /></div>';

                        if (response.preview.name !== undefined) {
                            html += '<div class="meta-preview-name">' + response.preview.name + '</div>';
                        }
                    }

                    if (response.global !== undefined) {
                        html += '<div class="meta-table">';
                        for (let i in response.global) {

                            if (response.global[i].key === undefined) {
                                continue;
                            }

                            if (response.global[i].key !== '') {
                                html += '<div><div>' + response.global[i].key + '</div><div>' + response.global[i].value + '</div></div>';
                            } else {
                                html += '<div class="only-value"><div>' + response.global[i].value + '</div></div>';
                            }
                        }
                        html += '</tbody></table>';
                    }

                    if (response.find !== undefined) {
                        if (Object.keys(response.find).length > 0) {
                            html += '<span class="show-all-tags">' + QuantumviewfilesLang.metaFileShow + '</span>';
                            html += '<div class="meta-find meta-hidden">';
                            for (let i in response.find) {

                                if (response.find[i].key === undefined) {
                                    continue;
                                }

                                html += '<div><div>' + response.find[i].key + '</div><div>' + response.find[i].value + '</div></div>';
                            }
                            html += '</div>';
                        }
                    }

                    html += '</div>';

                    self.cacheMeta = html;
                    self.cacheMetaPath = self.path;
                    self.metaLoadDirCurrent = '';

                    if (self.getCountSelected() === 0) {
                        self.viewMeta.querySelector('.meta-file-list').innerHTML = html;
                        let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');

                        if (buttonToggleTags !== null) {
                            let metaFind = self.viewMeta.querySelector('.meta-find');
                            buttonToggleTags.addEventListener('click', function () {
                                if (this.classList.contains('active')) {
                                    this.classList.remove('active');
                                    this.innerHTML = QuantumviewfilesLang.metaFileShow;
                                    metaFind.classList.add('meta-hidden');
                                } else {
                                    this.classList.add('active');
                                    this.innerHTML = QuantumviewfilesLang.metaFileHide;
                                    metaFind.classList.remove('meta-hidden');
                                }
                            });
                        }
                    }

                } else {
                    self.viewMeta.classList.add('hidden');
                }
            });
        }
    };

    this.showMetaCountFile = function (countElement) {
        let self = this;

        if (self.options.metafile === '1') {

            let objectAll = self.ds.getSelection();
            let size = 0;
            let imgs = '';
            let exsImage = ['jpg', 'png', 'svg', 'jpeg', 'gif', 'webp'];
            let objectOnlyFiles = true;

            for (let i = objectAll.length - 1; i >= 0; i--) {
                let input = objectAll[i].querySelector('.import-files-check-file');
                if (input.checked) {

                    if (objectAll[i].querySelector('.directory') !== null) {
                        objectOnlyFiles = false;
                    }

                    if (objectOnlyFiles) {
                        let dataExs = objectAll[i].getAttribute('data-exs').toLocaleLowerCase();
                        size += parseInt(objectAll[i].getAttribute('data-size'));

                        if (exsImage.indexOf(dataExs) !== -1) {
                            let fileP = objectAll[i].getAttribute('data-filep');

                            if (fileP.indexOf('index.php') !== -1) {
                                fileP += "&path=" + encodeURIComponent(Filemanager.data.path);
                            }

                            imgs += "<img src='" + fileP + "' draggable=\"false\" />"
                        } else {
                            imgs += self.generateIconFile(dataExs);
                        }
                    } else {
                        if (objectAll[i].querySelector('.directory') !== null) {
                            imgs += "<img src='/media/com_quantummanager/images/icons/j4-folder.svg' draggable=\"false\" />";
                        } else {
                            let dataExs = objectAll[i].getAttribute('data-exs').toLocaleLowerCase();

                            if (exsImage.indexOf(dataExs) !== -1) {
                                let fileP = objectAll[i].getAttribute('data-filep');

                                if (fileP.indexOf('index.php') !== -1) {
                                    fileP += "&path=" + encodeURIComponent(Filemanager.data.path)
                                }

                                imgs += "<img src='" + fileP + "' draggable=\"false\" />";
                            } else {
                                imgs += self.generateIconFile(dataExs);
                            }
                        }

                    }


                }
            }

            let html = '<div>';
            html += '<div class="meta-preview meta-preview-album">' + imgs + '</div>';
            html += '<div class="meta-table">';

            if (objectOnlyFiles) {
                html += '<div><div>' + QuantumviewfilesLang.metaSelectCount + '</div><div>' + countElement + '</div></div>';
            } else {
                html += '<div><div>' + QuantumviewfilesLang.metaSelectObjectCount + '</div><div>' + countElement + '</div></div>';
            }

            if (objectOnlyFiles) {
                html += '<div><div>' + QuantumviewfilesLang.metaSelectSize + '</div><div>' + QuantumUtils.bytesToSize(size) + '</div></div>';
            }

            html += '</tbody></table>';
            html += '</div>';

            self.viewMeta.classList.remove('hidden');
            self.viewMeta.querySelector('.meta-file-list').innerHTML = html;

        }

    };

    this.searchByName = function (search) {
        search = search.toLocaleLowerCase();
        this.searchNameValue = search;
        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
        let directoryAll = ViewfilesElement.querySelectorAll('.field-list-files .directory-item');
        let searchAlgh = 'default';
        let searchRegex = function (name) {
            return name.match(search);
        };
        let searchDefault = function (name) {
            return (name.indexOf(search) !== -1)
        };

        if (search === '') {
            for (let i = 0; i < filesAll.length; i++) {
                filesAll[i].style.display = 'flex';
            }
            for (let i = 0; i < directoryAll.length; i++) {
                directoryAll[i].style.display = 'block';
            }
        } else {

            if (
                search.indexOf('*') !== -1 ||
                search.indexOf('regex:') !== -1
            ) {
                searchAlgh = 'regex';

                if (search.indexOf('regex:') === -1) {
                    search = search.replace('.', '\\.');
                    search = search.replace('*', '.*?');
                    search = '^' + search + '$';
                }

                search = search.replace('regex:', '');
            }

            for (let i = 0; i < filesAll.length; i++) {
                let nameFile = filesAll[i].querySelector('.file-name').innerHTML.toLowerCase();
                let check = false;

                if (searchAlgh === 'default') {
                    check = searchDefault(nameFile);
                }

                if (searchAlgh === 'regex') {
                    check = searchRegex(nameFile);
                }

                if (check) {
                    filesAll[i].style.display = 'flex';
                } else {
                    filesAll[i].style.display = 'none';
                }
            }

            for (let i = 0; i < directoryAll.length; i++) {
                let nameDirectory = directoryAll[i].querySelector('.directory-name').innerHTML.toLowerCase();
                let check = false;

                if (searchAlgh === 'default') {
                    check = searchDefault(nameDirectory);
                }

                if (searchAlgh === 'regex') {
                    check = searchRegex(nameDirectory);
                }

                if (check) {
                    directoryAll[i].style.display = 'block';
                } else {
                    directoryAll[i].style.display = 'none';
                }
            }
        }
    };

    this.setSort = function (name, dir) {

        if (name !== '' && dir === '') {
            dir = 'desc';
        }

        self.sortField = name;
        self.sortDir = dir;
        self.trigger('reloadPaths');

        if (localStorage !== undefined) {
            localStorage.setItem('quantummanagerSortField', self.sortField);
            localStorage.setItem('quantummanagerSortDir', self.sortDir);
        }

    }

    this.initBreadcrumbs = function (callback) {
        let self = this;
        let fm = Filemanager;
        let scope = fm.data.scope;

        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=root&scope=" + encodeURIComponent(scope) + '&root=root'))
            .done(function (response) {
                response = JSON.parse(response);
                self.breadcrumbsLists = response.directories[0];

                if (callback !== undefined) {
                    callback(self, fm)
                }

                self.trigger('afterInitBreadcrumbs', self);
            }).fail(function () {
            self.breadcrumbsWaitLoad = false;
        });
    };

    this.buildBreadcrumbs = function (el, fm) {

        let self = this;

        if (el !== undefined) {
            self = el;
        }

        if (fm === undefined) {
            fm = Filemanager;
        }

        if (self.breadcrumbsLists === undefined || self.breadcrumbsLists.length === 0) {
            self.initBreadcrumbs(self.buildBreadcrumbs);
            return;
        }

        let currPaths = self.path.split('/');
        let htmlBreadcrumbs = ViewfilesElement.querySelector('.breadcumbs');
        let html = "";
        let lastElement;
        let clPaths = [];
        let liDropdown = [];
        let pathAtr = '';
        let title = '';

        for (let i = 0; i < currPaths.length; i++) {
            if (i === 0) {
                title = currPaths[i];

                if (self.breadcrumbsLists.title !== undefined) {
                    title = self.breadcrumbsLists.title;
                }

                pathAtr = currPaths[i];
                html += "<li class='clPath root' data-path='" + pathAtr + "'><span>" + title + "</span></li>";
                lastElement = self.breadcrumbsLists.subpath;

                if (self.breadcrumbsLists.subpath.length > 0) {
                    html += '<li class="carret dropdown"><span></span><div class="dropdown-content"><ul>';
                    for (let j = 0; j < self.breadcrumbsLists.subpath.length; j++) {
                        html += "<li><span class='clPath' data-path='" + pathAtr + "/" + self.breadcrumbsLists.subpath[j].path + "'>" + self.breadcrumbsLists.subpath[j].path + "</span></li>";
                    }
                    html += '</ul></div></li>';
                }

            } else {
                let otherPaths = [];
                let findPath = '';
                let findElement = lastElement;
                for (let j = 0; j < lastElement.length; j++) {
                    if (lastElement[j].path === currPaths[i]) {
                        findPath += currPaths[i];
                        findElement = lastElement[j].subpath
                    } else {
                        otherPaths.push(lastElement[j].path);
                    }
                }

                lastElement = findElement;

                if (otherPaths.length > 0) {
                    html += '<li class="dropdown"><span class="clPath" data-path="' + pathAtr + '/' + findPath + '">' + findPath + '</span><div class="dropdown-content"><ul>';
                } else {
                    html += '<li><span class="clPath" data-path="' + pathAtr + '/' + findPath + '">' + findPath + '</span>';
                }

                for (let j = 0; j < otherPaths.length; j++) {
                    html += "<li><span class='clPath' data-path='" + pathAtr + "/" + otherPaths[j] + "'>" + otherPaths[j] + "</span></li>";
                }

                if (otherPaths.length > 0) {
                    html += "</ul></div></li>";
                } else {
                    html += "</li>";
                }


                if (lastElement.length > 0) {
                    html += '<li class="carret dropdown"><span></span><div class="dropdown-content"><ul>';
                    for (let j = 0; j < lastElement.length; j++) {
                        html += "<li><span class='clPath' data-path='" + pathAtr + "/" + findPath + '/' + lastElement[j].path + "'>" + lastElement[j].path + "</span></li>";
                    }
                    html += '</ul></div></li>';
                }

                pathAtr += '/' + findPath;

            }
        }

        html += "";
        htmlBreadcrumbs.innerHTML = html;
        clPaths = htmlBreadcrumbs.querySelectorAll('.clPath');
        liDropdown = htmlBreadcrumbs.querySelectorAll('li.dropdown');

        for (let i = 0; i < clPaths.length; i++) {
            clPaths[i].addEventListener('click', function (ev) {
                fm.data.path = this.getAttribute('data-path');

                if (localStorage !== undefined) {
                    localStorage.setItem('quantummanagerLastDir', fm.data.path);
                    localStorage.setItem('quantummanagerLastDirHash', self.options.hash);
                }

                self.trigger('updatePath');
                ev.preventDefault();
            });
        }


    };

    this.scrollTopFilesCheck = function (files) {
        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
        let self = this;
        let find = false;
        for (let i = 0; i < filesAll.length; i++) {
            let input = filesAll[i].querySelector('.import-files-check-file');
            let nameFile = filesAll[i].querySelector('.file-name').innerHTML;

            if (typeof files === 'string') {
                if (files === nameFile) {
                    if (!input.checked) {
                        input.checked = !input.checked;
                    }
                    setTimeout(function () {
                        self.listFiles.scrollTop = filesAll[i].getBoundingClientRect().top - 460;
                    }, 300)
                } else {
                    input.checked = false;
                }
            }

            if (typeof files === 'object') {

                if (files.indexOf(nameFile) !== -1) {
                    if (!input.checked) {
                        input.checked = !input.checked;
                    }

                    if (!find) {
                        find = true;
                        setTimeout(function () {
                            self.listFiles.scrollTop = filesAll[i].getBoundingClientRect().top - 460;
                        }, 300);
                    }

                } else {
                    input.checked = false;
                }

            }


        }
    };

    this.clearAllSelectFile = function () {
        let self = this;
        if (self.ds !== undefined) {
            let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .object-select');

            self.ds.clearSelection();
            for (let i = 0; i < filesAll.length; i++) {
                self.unSelectFile(filesAll[i], self)
            }

            self.showMetaDirectory();
        }
    };

    this.ListviewToGrid = function () {
        this.lastTypeViewFiles = 'list-grid';
        this.reloadTypeViewFiles();
    };

    this.ListviewToTable = function () {
        this.lastTypeViewFiles = 'list-table';
        this.reloadTypeViewFiles();
    };

    this.reloadTypeViewFiles = function (path) {
        let self = this;
        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
        let viewFiles = ViewfilesElement.querySelector('.field-list-files .list');

        if (this.lastTypeViewFiles === '') {
            this.lastTypeViewFiles = 'list-grid';
            let currLastTypeViewFiles = localStorage.getItem('quantummanagerLastTypeViewFiles');
            if (currLastTypeViewFiles !== null) {
                this.lastTypeViewFiles = currLastTypeViewFiles;
            }
        }

        if (path === undefined || path === null) {
            path = this.path;
        }

        if (viewFiles === null || viewFiles === undefined) {
            return;
        }

        if (this.lastTypeViewFiles === 'list-grid') {
            this.gridColumn = 'list-grid-1-10';
            let gridColumnCache = '';

            if (this.gridColumnSet === undefined || this.gridColumnSet === '') {
                if (localStorage !== undefined) {
                    gridColumnCache = localStorage.getItem('quantummanagerLastTypeViewFilesGrid');
                }

                if (gridColumnCache !== null) {
                    this.gridColumn = gridColumnCache;
                }

                if (viewFiles.classList.contains('list-grid-1-10')) {
                    this.gridColumn = 'list-grid-1-9';
                }

                if (viewFiles.classList.contains('list-grid-1-9')) {
                    this.gridColumn = 'list-grid-1-8';
                }

                if (viewFiles.classList.contains('list-grid-1-8')) {
                    this.gridColumn = 'list-grid-1-7';
                }

                if (viewFiles.classList.contains('list-grid-1-7')) {
                    this.gridColumn = 'list-grid-1-6';
                }

                if (viewFiles.classList.contains('list-grid-1-6')) {
                    this.gridColumn = 'list-grid-1-5';
                }

                if (viewFiles.classList.contains('list-grid-1-5')) {
                    this.gridColumn = 'list-grid-1-4';
                }

                if (viewFiles.classList.contains('list-grid-1-4')) {
                    this.gridColumn = 'list-grid-1-3';
                }

                if (viewFiles.classList.contains('list-grid-1-3')) {
                    this.gridColumn = 'list-grid-1-2';
                }

                if (viewFiles.classList.contains('list-grid-1-2')) {
                    this.gridColumn = 'list-grid-1-10';
                }
            } else {
                this.gridColumn = this.gridColumnSet;
                this.gridColumnSet = '';
            }


            if (localStorage !== undefined) {
                localStorage.setItem('quantummanagerLastTypeViewFilesGrid', this.gridColumn);
            }

            viewFiles.setAttribute('class', 'list list-grid ' + this.gridColumn);

        }

        if (this.lastTypeViewFiles === 'list-table') {
            viewFiles.setAttribute('class', 'list list-table');
        }

        if (localStorage !== undefined) {
            localStorage.setItem('quantummanagerLastTypeViewFiles', this.lastTypeViewFiles);
        }


        for (let i = 0; i < filesAll.length; i++) {

            if (this.path !== path) {
                break;
            }

            if (
                this.lastTypeViewFiles === 'list-grid' ||
                this.lastTypeViewFiles === 'list-table'
            ) {
                let fileP = filesAll[i].getAttribute('data-fileP');
                let fileName = filesAll[i].getAttribute('data-name');
                let fileExs = filesAll[i].getAttribute('data-exs');
                let filePreview = filesAll[i].querySelector('.file-exs');
                let exsImage = ['jpg', 'png', 'svg', 'jpeg', 'gif', 'webp'];
                let fields = filesAll[i].querySelector('.fields');

                if (fields !== null) {
                    fields.remove();
                }

                if (exsImage.indexOf(fileExs.toLowerCase()) !== -1) {

                    let file;
                    let image = document.createElement('img');

                    if (fileP.indexOf('index.php') === -1) {
                        file = fileP + '?' + QuantumUtils.randomInteger(111111, 999999);
                        image.setAttribute('src', file);
                    } else {
                        file = fileP + '&path=' + path + '&v=' + QuantumUtils.randomInteger(111111, 999999);
                        image.setAttribute('class', 'lazyLoad');
                        image.setAttribute('data-src', file);
                    }

                    filePreview.innerHTML = '';
                    filePreview.append(image);
                } else {
                    filePreview.innerHTML = self.generateIconFile(fileExs);
                    filePreview.classList.add('file-icons');
                }

            }

            if (this.lastTypeViewFiles === 'list-table') {
                let htmlFields = '';
                let filePreview = filesAll[i].querySelector('.file-exs');
                let checkFields = filesAll[i].querySelector('.fields');
                /*filePreview.style.backgroundImage = "";
                filePreview.innerHTML = '';*/

                if (checkFields === null) {
                    htmlFields += '<div class="fields">';
                    htmlFields += '<div data-type="size">' + QuantumUtils.bytesToSize(filesAll[i].getAttribute('data-size')) + '</div>';
                    htmlFields += '<div data-type="date">' + QuantumUtils.fromUnixTimeToDate(filesAll[i].getAttribute('data-datec')) + '</div>';
                    htmlFields += '</div>';
                    filesAll[i].innerHTML += htmlFields;
                }

            }

        }

        self.clearAllSelectFile();

        this.trigger('afterReloadTypeViewFiles', this);

    };

    this.generateIconFile = function (exs) {
        return '<svg class="svg-icon ' + exs + '" x="0" y="0" viewBox="0 0 309.267 309.267">' +
            '<use class="main" xlink:href=\'#iconFileMain\' />' +
            '<use class="tail" xlink:href=\'#iconFileTail\' />' +
            '<text  x="150" y="200">' + exs + '</text>' +
            '</svg>';
    };

    this.trigger = function (event, target) {
        return Filemanager.events.trigger(event, Filemanager, target);
    };

    Filemanager.events.add(this, 'clickObject', function (fm, el) {
        let objectSelect = fm.Quantumviewfiles.objectSelect;
        let exs = '';
        let type = '';

        if (objectSelect === undefined) {

            if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('qm-btn-hide');
            }

        } else {

            if (objectSelect.classList.contains('file-item')) {
                type = 'file';
            } else {
                type = 'directory';
            }

            if (objectSelect.classList.contains('file-item')) {
                exs = objectSelect.getAttribute('data-exs').toLocaleLowerCase();
            }
        }

        let filesAll = el.element.querySelectorAll('.field-list-files .object-select');
        let find = false;

        for (let i = 0; i < filesAll.length; i++) {
            if (filesAll[i].querySelector('input').checked) {
                find = true;
            }
        }

        if (['png', 'jpg', 'jpeg', 'webp'].indexOf(exs) !== -1) {
            if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.remove('qm-btn-hide');
            }
        }

        if (fm.Quantumtoolbar !== undefined) {
            for (let i = 0; i < self.IdsButtonForFile.length; i++) {

                if (
                    fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id] === undefined ||
                    fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id] === null
                ) {
                    continue;
                }

                let checkSelect = false;

                if (self.IdsButtonForFile[i].count === undefined) {
                    if (self.getCountSelected() === 1) {
                        checkSelect = true;
                    }
                } else {
                    if (self.IdsButtonForFile[i].count === 'some') {
                        if (self.getCountSelected() >= 1) {
                            checkSelect = true;
                        }
                    }
                }

                if (find && checkSelect) {
                    if (self.IdsButtonForFile[i].exs !== undefined) {
                        if (self.IdsButtonForFile[i].exs.indexOf(exs) === -1) {
                            fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id].classList.add('qm-btn-hide');
                            continue;
                        }
                    }


                    if (self.IdsButtonForFile[i].for !== undefined) {
                        if (self.IdsButtonForFile[i].for === type) {
                            fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id].classList.remove('qm-btn-hide');
                        }
                    } else {
                        fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id].classList.remove('qm-btn-hide');
                    }

                } else {
                    fm.Quantumtoolbar.buttonsList[self.IdsButtonForFile[i].id].classList.add('qm-btn-hide');
                }
            }
        }

    });

    Filemanager.events.add(this, 'updatePath', function (fm, el) {

        //вырубаем кнопки для выделенного

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('qm-btn-hide');
        }

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('qm-btn-hide');
        }

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesPaste'] !== undefined) {
            if (self.buffer.length === 0) {
                fm.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.add('qm-btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
            }
        }

        //запоминаем позицию прокрутки в директории
        if (el.listFiles !== '' && el.listFiles !== null) {
            el.bufferTopDirectories[el.path] = el.listFiles.scrollTop;
        }

        //добавление в историю
        el.history.push(el.path);

        //переключаем путь и открываем папку
        el.path = fm.data.path;
        el.loadDirectory(el.path);

    });

    Filemanager.events.add(this, 'afterInitBreadcrumbs', function (fm, el) {
        if (localStorage !== undefined) {

            let findPath = function (pathSearch, pathParent, find, level) {
                if (level === 0) {
                    if (find === pathSearch.path) {
                        Filemanager.data.path = find;

                        if (el.path !== find) {
                            el.path = find;
                            el.trigger('updatePath');
                        }

                        return;
                    } else {
                        findPath(pathSearch.subpath, pathSearch.path, find, level + 1);
                        return;
                    }
                }

                for (let i = 0; i < pathSearch.length; i++) {
                    if (find === (pathParent + '/' + pathSearch[i].path)) {
                        Filemanager.data.path = find;

                        if (el.path !== find) {
                            el.path = find;
                            el.trigger('updatePath');
                        }

                        return;
                    } else {
                        findPath(pathSearch[i].subpath, pathParent + '/' + pathSearch[i].path, find, level + 1);
                    }
                }

            };

            findPath(el.breadcrumbsLists, '', el.path, 0);

        }
    });

    Filemanager.events.add(this, 'reloadPaths', function (fm, el) {

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('qm-btn-hide');
        }

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('qm-btn-hide');
        }

        if (fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesPaste'] !== undefined) {
            if (self.buffer.length === 0) {
                fm.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.add('qm-btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesPaste'].classList.remove('qm-btn-hide');
            }
        }

        //запоминаем позицию прокрутки в директории
        if (el.listFiles !== '' && el.listFiles !== null) {
            el.bufferTopDirectories[el.path] = el.listFiles.scrollTop;
        }

        fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);
        fm.Quantumviewfiles.loadDirectory(fm.data.path);

    });

    Filemanager.events.add(this, 'uploadComplete', function (fm, el) {
        Filemanager.Quantumviewfiles.loadDirectory(null, function () {
            fm.Quantumviewfiles.scrollTopFilesCheck(Filemanager.Qantumupload.filesLists);

            let filesAll = el.element.querySelectorAll('.field-list-files .file-item');
            let find = false;
            let element;

            for (let i = 0; i < filesAll.length; i++) {
                if (filesAll[i].querySelector('input').checked) {
                    self.priorityMetaFile = filesAll[i];
                    fm.Quantumviewfiles.selectFile(filesAll[i], true);
                }
            }

            let countSelected = self.getCountSelected();

            if (countSelected > 1) {
                self.showMetaCountFile(countSelected);
            }

            if (countSelected) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('qm-btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('qm-btn-hide');
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });

    Filemanager.events.add(this, 'updateScope', function (fm, el) {
        fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);
    });

    Filemanager.events.add(this, 'afterReloadTypeViewFiles', function (fm, el) {
        if (
            fm.Quantumviewfiles.lastTypeViewFiles === 'list-grid' ||
            fm.Quantumviewfiles.lastTypeViewFiles === 'list-table'
        ) {
            let images = fm.Quantumviewfiles.element.querySelectorAll('.lazyLoad');

            if (self.lazyload === undefined || self.lazyload === null) {
                self.lazyload = new LazyLoad(images);
            } else {
                self.lazyload.clearTurn();
                self.lazyload.changeImages(images);
                self.lazyload.init();
            }
        }
    });


    Filemanager.events.add(this, 'updateContextMenuArea', function (fm, el) {
        fm.Quantumviewfiles.contextMenu.clear();

        for (let i = 0; i < fm.Quantumviewfiles.menuItemsArea.length; i++) {
            fm.Quantumviewfiles.contextMenu.add(new ContextualItem(fm.Quantumviewfiles.menuItemsArea[i]));
        }

    });


    Filemanager.events.add(this, 'hotKeysResolve', function (fm, el, key) {
        key = key.toLowerCase();
        let chain = self.hotKeyPrev + '+' + key;
        let os = QuantumUtils.getOS();
        let action = '';

        if (self.hotKeys[os] === undefined || self.hotKeys[os] === null) {
            return;
        }

        for (let action_hotkey in self.hotKeys[os]) {
            if (
                self.hotKeys[os][action_hotkey].indexOf(chain) !== -1 ||
                self.hotKeys[os][action_hotkey].indexOf(key) !== -1
            ) {
                action = action_hotkey;
            }
        }

        self.hotKeyPrev = key;
        action = action.charAt(0).toUpperCase() + action.slice(1);
        self.trigger('hotKeys' + action);

        if (action === 'Reload') {
            return false;
        }

        return true;
    });


    Filemanager.events.add(this, 'hotKeysDelete', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesDelete');
    });

    Filemanager.events.add(this, 'hotKeysCopy', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesCopy');
    });

    Filemanager.events.add(this, 'hotKeysCut', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesCut');
    });

    Filemanager.events.add(this, 'hotKeysPaste', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesPaste');
    });

    Filemanager.events.add(this, 'hotKeysRename', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesRename');
    });

    Filemanager.events.add(this, 'hotKeysBack', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesBack');
    });

    Filemanager.events.add(this, 'hotKeysReload', function (fm, el) {
        fm.Quantumtoolbar.executeButton('viewfilesReloadPaths');
    });

};