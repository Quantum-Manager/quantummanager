/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumviewfiles = function(Filemanager, ViewfilesElement, options) {

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
    this.bufferTopDirectories = {};
    this.listFiles = '';
    this.history = [];
    this.breadcrumbsLists = [];
    this.breadcrumbsWaitLoad = false;
    this.searchNameValue = '';
    this.cacheMetaPath = '';
    this.cacheMeta = '';
    this.contextMenu = '';
    this.menuItemsArea = [
        {
            type: 'normal',
            label: QuantumviewfilesLang.contextReload,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/refresh-button.svg'),
            onClick: function(){
                Filemanager.events.trigger('reloadPaths', Filemanager);
            }
        }
    ];
    this.directoryContext = '';
    this.menuItemsDirectories = [
        {
            writeable: 1,
            type: 'normal',
            label: QuantumviewfilesLang.contextRename,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/create-new-pencil-button.svg'),
            onClick: function() {
                let name = self.directoryContext.querySelector('.directory-name').innerHTML;

                QuantumUtils.prompt(QuantumviewfilesLang.fileName, name , function (result) {
                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.renameDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&oldName=' + encodeURIComponent(name) + '&name='+ encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                        response = JSON.parse(response);
                        if(response.status === undefined) {
                            return;
                        }

                        if(response.status === 'ok') {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        }
                    });
                });

            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumviewfilesLang.contextDelete,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/rubbish-bin-delete-button.svg'),
            onClick: function() {
                let nameDirectory = self.directoryContext.querySelector('.directory-name').innerHTML;

                QuantumUtils.confirm(QuantumtreecatalogsLang.confirmDelete + ' ' + nameDirectory + '?', function (result) {
                    let directories = [];
                    directories.push(nameDirectory);
                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&list=' + encodeURIComponent(JSON.stringify(directories)))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                });

            }
        },
    ];
    this.fileContext = '';
    this.menuItemsFile = [
        {
            writeable: 0,
            type: 'normal',
            label: QuantumviewfilesLang.contextPreviewFile,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/visibility-button.svg'),
            onClick: function(){
                jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                    response = JSON.parse(response);
                    if(response.path === undefined) {
                        return;
                    }

                    QuantumUtils.windowOpen("previewFile", response.path + '/' + self.fileContext.getAttribute('data-file'));
                });
            }
        },
        {
            writeable: 1,
            type: 'normal',
            label: QuantumviewfilesLang.contextRename,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/create-new-pencil-button.svg'),
            onClick: function() {

                QuantumUtils.prompt(QuantumviewfilesLang.fileName, self.fileContext.getAttribute('data-name'), function (result) {
                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.renameFile&path=" + encodeURIComponent(Filemanager.data.path) + '&file=' + encodeURIComponent(self.fileContext.getAttribute('data-file')) + '&name='+ encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                        response = JSON.parse(response);
                        if(response.status === undefined) {
                            return;
                        }

                        if(response.status === 'ok') {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        }
                    });
                });

            }
        },
        {
            writeable: 0,
            type: 'normal',
            label: QuantumviewfilesLang.contextCopyLink,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/link-button.svg'),
            onClick: function(){
                jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                    response = JSON.parse(response);
                    if(response.path === undefined) {
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
            label: QuantumviewfilesLang.contextDelete,
            tip: '',
            icon: QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/rubbish-bin-delete-button.svg'),
            onClick: function() {
                let files = [],
                    nameFile = self.fileContext.getAttribute('data-file');
                files.push(self.fileContext.getAttribute('data-file'));

                QuantumUtils.confirm(QuantumviewfilesLang.contextDelete + ' ' + nameFile + '?', function (result) {
                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&list=' + encodeURIComponent(JSON.stringify(files)))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                });


            }
        },
    ];

    this.init = function() {
        let self = this;
        this.path = this.options.directory;
        let openLastDir = localStorage.getItem('quantummanagerLastDir');
        let openLastDirHash = localStorage.getItem('quantummanagerLastDirHash');
        if(openLastDir !== null)
        {
            if(openLastDirHash === this.options.hash) {
                this.path = openLastDir;
            }
        }

        this.loadDirectory();
        let searchByName = ViewfilesElement.querySelector('.filter-search input');

        if(!parseInt(self.options.onlyfiles)) {

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesBack', 'left', 'navigations', 'btn-back hidden-label', QuantumviewfilesLang.buttonBack, 'quantummanager-icon-back', {}, function (ev) {

                let directory;

                if(self.history.length > 0) {
                    directory = self.history[self.history.length - 1];
                    self.history.splice(self.history.length - 2, 2);
                    self.loadDirectory(directory);
                    Filemanager.data.path = directory;
                }


                Filemanager.Quantumtoolbar.trigger('buttonViewfilesBack');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesUp', 'left', 'navigations', 'btn-up hidden-label', QuantumviewfilesLang.buttonUp, 'quantummanager-icon-up', {}, function (ev) {

                if(Filemanager.data.path === undefined)
                {
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

            let buttonGrid = Filemanager.Quantumtoolbar.buttonAdd('viewfilesGrid', 'center', 'list-view', 'btn-grid hidden-label', '', 'quantummanager-icon-grid', {'data-tooltip': QuantumviewfilesLang.changeGridViews}, function (ev) {
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
            ];

            for(let i=0;i<buttonsGrid.length;i++) {
                Filemanager.Quantumtoolbar.buttonAdd('viewfilesGrid-' + i, 'center', 'list-view', 'btn-grid', buttonsGrid[i], '', {}, function (ev) {
                    Filemanager.Quantumviewfiles.gridColumnSet = 'list-grid-1-' + buttonsGrid[i];
                    Filemanager.Quantumviewfiles.ListviewToGrid();
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesGrid');
                    ev.preventDefault();
                }, buttonGrid);
            }


            Filemanager.Quantumtoolbar.buttonAdd('viewfilesTable', 'center', 'list-view', 'btn-table hidden-label', '', 'quantummanager-icon-table', {}, function (ev) {
                Filemanager.Quantumviewfiles.ListviewToTable();
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesTable');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesCreateDirectory', 'center', 'file-actions', 'btn-create-directory hidden-label', QuantumviewfilesLang.buttonCreateDirectory, 'quantummanager-icon-directory', {}, function (ev) {
                QuantumUtils.prompt(QuantumviewfilesLang.directoryName, '', function (nameDirectory) {
                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.createDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&name=' + encodeURIComponent(nameDirectory)  + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });
                });
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesCreateDirectory');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesDelete', 'center', 'file-actions', 'btn-delete btn-hide hidden-label', QuantumviewfilesLang.buttonDelete, 'quantummanager-icon-delete', {}, function (ev) {

                let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
                let files = [];

                for(let i=0;i<filesAll.length;i++) {
                    if (filesAll[i].querySelector('input').checked) {
                        files.push(filesAll[i].getAttribute('data-file'));
                    }
                }

                jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&list=' + encodeURIComponent(JSON.stringify(files)) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                    Filemanager.events.trigger('reloadPaths', Filemanager);
                });

                Filemanager.Quantumviewfiles.showMetaDirectory(true);
                Filemanager.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesDelete');
                ev.preventDefault();
            });

            if(self.options.watermark === '1') {
                Filemanager.Quantumtoolbar.buttonAdd('viewfilesWatermark', 'center', 'file-actions', 'btn-delete btn-hide hidden-label', QuantumviewfilesLang.buttonWatermark, 'quantummanager-icon-watermark', {}, function (ev) {

                    let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
                    let files = [];

                    for(let i=0;i<filesAll.length;i++) {
                        if (filesAll[i].querySelector('input').checked) {
                            files.push(filesAll[i].getAttribute('data-file'));
                        }
                    }

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.watermark&path=" + encodeURIComponent(Filemanager.data.path) + '&list=' + encodeURIComponent(JSON.stringify(files)) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                        Filemanager.events.trigger('reloadPaths', Filemanager);
                    });

                    Filemanager.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
                    Filemanager.Quantumtoolbar.trigger('buttonViewfilesWatermark');
                    ev.preventDefault();
                });
            }

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesReloadPaths', 'center', 'file-other', 'btn-reload', '', 'quantummanager-icon-reload', {}, function (ev) {
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

            for(let i=0;i<self.menuItemsArea.length;i++) {
                self.contextMenu.add(new ContextualItem(self.menuItemsArea[i]));
            }

            self.contextMenu.show(ev);
        });

    };

    this.initAfter = function () {
        if(Filemanager.Qantumupload !== undefined)
        {
            if(Filemanager.Qantumupload.options.dropAreaHidden === '1') {
                Filemanager.Quantumtoolbar.buttonAdd('viewfilesUploadFile', 'center', 'file-other', 'btn-upload-file hidden-label', QuantumviewfilesLang.buttonUpload, 'quantummanager-icon-upload', {}, function (ev) {
                    Filemanager.Qantumupload.selectFiles();
                    ev.preventDefault();
                });
            }
        }

        Filemanager.Quantumtoolbar.buttonAdd('viewfilesHelp', 'right', 'file-other', 'btn-back hidden-label', QuantumviewfilesLang.help, 'quantummanager-icon-info', {}, function (ev) {
            QuantumUtils.alert('<div class="quantummanager-about"><div class="text">' + QuantumviewfilesLang.helpText + '</div><div class="copyright">' + QuantumviewfilesLang.helpCopyright + '</div><div class="copyright-images">' + QuantumviewfilesLang.helpCopyrightImages + '</div><div class="love">' + QuantumviewfilesLang.helpLove + ' <img src="' + QuantumUtils.getFullUrl('/media/com_quantummanager/images/icons/action/favorite-heart-button.svg')+ '" class="svg" /></div>', [
                {
                    name: QuantumviewfilesLang.helpButtonProductPage,
                    callback: function () {
                        QuantumUtils.openInNewTab('https://www.norrnext.com/quantum-manager');
                    }
                },
                {
                    name: QuantumviewfilesLang.helpButtonDocumentation,
                    callback: function () {
                        QuantumUtils.openInNewTab('https://www.norrnext.com/docs/joomla-extensions/quantum-manager');
                    }
                },
                {
                    name: QuantumviewfilesLang.helpButtonSupport,
                    callback: function () {
                        QuantumUtils.openInNewTab('https://www.norrnext.com/forum/quantum-manager');
                    }
                },
                {
                    name: QuantumviewfilesLang.helpButtonReview,
                    callback: function () {
                        QuantumUtils.openInNewTab('https://extensions.joomla.org/extension/quantum-manager/');
                    }
                }
            ]);
            setTimeout(function () {
                QuantumUtils.replaceImgToSvg('.quantummanager-about .love');
            }, 100);
        });

    };

    this.loadDirectory = function (path, callback) {

        let self = this;
        let scope = Filemanager.data.scope;

        if(path === null || path === undefined) {
            path = this.path;
        } else {
            this.path = path;
        }

        if (Filemanager.data.path === undefined || Filemanager.data.path !== path) {
            Filemanager.data.path = path;
        }

        ViewfilesElement.querySelector('.view').innerHTML = '';
        this.preoloader();

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getFiles&path=" + encodeURIComponent(path) + '&scope=' + encodeURIComponent(scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
            response = JSON.parse(response);

            if(response.error !== undefined) {
                Filemanager.data.path = self.options.directory;
                self.trigger('updatePath');
                return;
            }

            let htmlfilesAndDirectories = '<div class="field-list-files"><div class="list">';
            let files = response.files;
            let directories = response.directories;

            if(!parseInt(self.options.onlyfiles)) {
                for (let i = 0; i < directories.length; i++) {
                    let addClass = '';
                    if(directories[i]['is_writable'] === 0) {
                        addClass = 'lock ';
                    }

                    if(directories[i]['is_empty'] === 0) {
                        addClass += 'empty';
                    }

                    htmlfilesAndDirectories += "<div class='directory-item " + addClass + "' data-iswritable='" + directories[i]['is_writable'] + "'><div class='directory'><div class='directory-icon'><span></span></div><div class='directory-name'>" + directories[i]['name'] + "</div></div></div>";
                }
            }

            for(let i = 0;i<files.length;i++) {
                let type = files[i]['exs'];
                let addClass = '';
                if(files[i]['is_writable'] === 0) {
                    addClass = 'lock';
                }

                htmlfilesAndDirectories += "<div class='file-item " + addClass + "' data-iswritable='" + files[i]['is_writable'] + "' data-size='" + files[i]['size'] + "' data-name='" + files[i]['name'] + "' data-exs='" + files[i]['exs'] + "' data-fileP='" + files[i]['fileP'] + "' data-dateC='" + files[i]['dateC'] + "' data-dateM='" + files[i]['dateM'] + "' data-file='" + files[i]['file'] + "'><input type=\"checkbox\" class=\"import-files-check-file\"><div class='file'><div class='context-menu-open'><span></span></div><div class='file-exs icon-file-" + type + "'><div class='av-folderlist-label'></div></div><div class='file-name'>" + files[i]['file'] + "</div></div></div>" ;
            }

            htmlfilesAndDirectories += "</div></div>";

            if(files.length === 0 && directories.length === 0) {
                htmlfilesAndDirectories = "<div class='empty'><div>" + QuantumviewfilesLang.empty + "</div></div>";
            }

            ViewfilesElement.querySelector('.view-wrap .view').innerHTML = '';
            ViewfilesElement.querySelector('.view-wrap .view').innerHTML = htmlfilesAndDirectories;
            self.reloadTypeViewFiles(path);
            self.listFiles = ViewfilesElement.querySelector('.field-list-files');

            if(self.bufferTopDirectories[path] !== undefined) {
                if(self.listFiles !== null && self.listFiles.scrollTop !== undefined) {
                    self.listFiles.scrollTop = self.bufferTopDirectories[path];
                }
            }

            let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
            let directoriesAll = ViewfilesElement.querySelectorAll('.field-list-files .directory-item');
            let timer = 0;
            let delay = 200;
            let prevent = false;
            self.fileMenu =  new Contextual({
                items: []
            });
            self.fileMenu.hide();

            for(let i=0;i<filesAll.length;i++) {
                let inputCheck = filesAll[i].querySelector('.import-files-check-file');

                filesAll[i].addEventListener('dblclick', function () {
                    let element = this;
                    clearTimeout(timer);
                    prevent = true;
                    self.fileDblclick(element, self);
                });

                filesAll[i].addEventListener('contextmenu',function(ev) {
                    self.fileContext = filesAll[i];
                    self.contextMenu.clear();
                    let exs = self.fileContext.getAttribute('data-exs').toLocaleLowerCase();
                    let writeable = parseInt(self.fileContext.getAttribute('data-iswritable'));
                    let tmpContextMenu = [];
                    let tmpContextMenuSource = self.menuItemsFile;
                    let addContextMenu = self.trigger('addContextMenuFile');
                    let tmpAddContextMenu = [];

                    if(addContextMenu !== undefined && (typeof addContextMenu === 'object')) {

                        for(let i = 0; i < addContextMenu.length;i++) {

                            if(addContextMenu[i][0].fileExs.indexOf(exs) === -1) {
                                continue;
                            }

                            if(writeable !== addContextMenu[i][0].writeable) {
                                continue;
                            }

                            tmpAddContextMenu = tmpAddContextMenu.concat(addContextMenu[i]);
                        }

                    }

                    for(let i = 0; i < tmpContextMenuSource.length;i++) {

                        if((writeable === 0) && (tmpContextMenuSource[i].writeable === 1)) {
                            continue;
                        }

                        tmpAddContextMenu = tmpAddContextMenu.concat(tmpContextMenuSource[i]);
                    }

                    tmpContextMenu = tmpAddContextMenu.concat(tmpContextMenu);

                    for(let i=0;i<tmpContextMenu.length;i++) {
                        self.contextMenu.add(new ContextualItem(tmpContextMenu[i]));
                    }

                    self.contextMenu.show(ev);
                    ev.preventDefault();
                });
            }

            if(!parseInt(self.options.onlyfiles)) {
                self.directoryMenu =  new Contextual({
                    items: []
                });
                self.contextMenu.hide();

                for(let i=0;i<directoriesAll.length;i++) {
                    directoriesAll[i].addEventListener('click', function () {
                        let directory = this.querySelector('.directory-name').innerHTML;
                        Filemanager.data.path = self.path + '/' + directory;

                        if(localStorage !== undefined) {
                            localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
                            localStorage.setItem('quantummanagerLastDirHash', self.options.hash);
                        }

                        self.directory = this;
                        self.trigger('updatePath');
                        self.trigger('clickDirectory');
                    });

                    directoriesAll[i].addEventListener('contextmenu',function(ev) {
                        self.directoryContext = directoriesAll[i];
                        self.contextMenu.clear();
                        let writeable = parseInt(self.directoryContext.getAttribute('data-iswritable'));
                        let tmpContextMenu = [];
                        let tmpContextMenuSource = self.menuItemsDirectories;
                        let addContextMenu = self.trigger('addContextMenuDirectory');
                        let tmpAddContextMenu = [];

                        for(let i = 0; i < tmpContextMenuSource.length;i++) {

                            if((writeable === 0) && (tmpContextMenuSource[i].writeable === 1)) {
                                continue;
                            }

                            tmpContextMenu = tmpContextMenu.concat(tmpContextMenuSource[i]);
                        }

                        if(addContextMenu !== undefined && (typeof addContextMenu === 'object')) {

                            for(let i = 0; i < addContextMenu.length;i++) {
                                tmpAddContextMenu = tmpAddContextMenu.concat(addContextMenu[i]);
                            }

                        }

                        tmpContextMenu = tmpAddContextMenu.concat(tmpContextMenu);

                        for(let i=0;i<tmpContextMenu.length;i++) {
                            self.contextMenu.add(new ContextualItem(tmpContextMenu[i]));
                        }

                        if(tmpContextMenu.length > 0) {
                            self.contextMenu.show(ev);
                        }

                        ev.preventDefault();
                    });
                }
            }

            if(self.searchNameValue !== '') {
                self.searchByName(self.searchNameValue);
            }

            self.buildBreadcrumbs();
            self.showMetaDirectory(true);
            self.metaFileLoad = {};

            if(filesAll.length > 0) {
                self.ds = undefined;
                self.dsP;
                self.dsElemet;
                self.dsCount = 0;

                self.ds = new DragSelect({
                    selectables: ViewfilesElement.querySelectorAll('.field-list-files .file-item'),
                    area: ViewfilesElement.querySelector('.field-list-files .list'),
                    customStyles: false,
                    multiSelectKeys: ['ctrlKey', 'shiftKey', 'metaKey'],
                    multiSelectMode: false,
                    autoScrollSpeed: 3,
                    onDragStart: function (ev) {
                        if (!ev.target.classList.contains('ds-selectable')) {
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

                        if(elements.length) {
                            self.file = elements[0];
                        } else {
                            self.file = undefined;
                        }


                        let countSelected = self.getCountSelected();

                        if (countSelected) {

                            if(countSelected > 1) {
                                self.showMetaCountFile(countSelected);
                            } else {
                                self.showMetaFile(self.file);
                            }

                        } else {
                            self.metaReset = true;
                            self.showMetaDirectory();
                        }

                        self.trigger('clickFile', self.file);
                    }
                });
            }

            if(self.path.split('/').length === 1) {
                Filemanager.Quantumtoolbar.buttonsList['viewfilesUp'].setAttribute('disabled', 'disabled');
            } else {
                Filemanager.Quantumtoolbar.buttonsList['viewfilesUp'].removeAttribute('disabled');
            }

            if(callback !== undefined) {
                callback();
            }

        });

    };

    this.getCountSelected = function () {

        if(this.ds !== undefined) {
            return this.ds.getSelection().length;
        }

        return 0;
    };

    this.selectFile = function (element, triggerFlag) {
        let self = this;
        let tmpInput = element.closest('.file-item').querySelector('.import-files-check-file');
        tmpInput.checked = true;
        self.ds.addSelection(element);
        let countSelected = self.getCountSelected();

        if(countSelected > 1) {
            self.showMetaCountFile(countSelected);
        } else {
            self.showMetaFile(element);
        }

        if(triggerFlag !== null && triggerFlag === true) {
            self.file = element;
            self.trigger('clickFile', self);
        }

    };

    this.unSelectFile = function (element, qvf) {

        let self = this;
        let countSelected = self.getCountSelected();
        let tmpInput = element.closest('.file-item').querySelector('.import-files-check-file');
        tmpInput.checked = false;

        if (countSelected) {

            if(countSelected > 1) {
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
        qvf.trigger('dblclickFile', element);
    };

    this.preoloader = function () {
        ViewfilesElement.querySelector('.view').innerHTML = "<div class=\"loader\">" +  QuantumviewfilesLang.loading + "<span></span><span></span><span></span><span></span></div>";
    };

    this.hideMeta = function () {
        let self = this;

        if(self.options.metafile === '1') {
            self.viewMeta.classList.add('hidden');
        }

    };

    this.showMetaFile = function (element) {
        let self = this;
        let tmpLength = 0;

        if(self.options.metafile === '1') {

            let url = QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getMetaFile&path=" + encodeURIComponent(self.path) + '&name=' + encodeURIComponent(element.getAttribute('data-file')) + '&scope=' + Filemanager.data.scope);

            if(self.metaFileLoad[url] === undefined) {
                self.metaFileLoad[url] = 1;
            } else {
                self.metaFileLoad[url]++;
            }


            jQuery.get(url).done(function (response) {

                self.metaFileLoad[url]--;

                if(self.metaFileLoad[url] < 0) {
                    self.metaFileLoad[url] = 0;
                }

                for(let i in self.metaFileLoad) {
                    if(!isNaN(self.metaFileLoad[i])) {
                        tmpLength += self.metaFileLoad[i];
                    }
                }

                if(self.getCountSelected() > 1) {
                    return;
                }

                response = JSON.parse(response);
                if(response.global !== undefined || response.find !== undefined) {
                    self.viewMeta.classList.remove('hidden');

                    let html = '<div>';

                    if(response.preview !== undefined) {
                        if(response.preview.link === 'none') {
                            html += '<div class="meta-preview">' + self.generateIconFile() + '</div>';
                        } else {
                            html += '<div class="meta-preview meta-preview-open"><img src="' + response.preview.link + '" /></div>';
                        }
                    }

                    if(response.global !== undefined) {
                        html += '<div class="meta-table">';
                        for (let i in response.global) {

                            if(response.global[i].key === undefined) {
                                continue;
                            }

                            html += '<div><div>' + response.global[i].key + '</div><div>' + response.global[i].value + '</div></div>';
                        }
                        html += '</div>';
                    }

                    if(response.find !== undefined) {
                        if(Object.keys(response.find).length > 0) {
                            html += '<span class="show-all-tags">' + QuantumviewfilesLang.metaFileShow + '</span>';
                            html += '<div class="meta-table meta-find meta-hidden">';
                            for (let i in response.find) {

                                if(response.find[i].key === undefined) {
                                    continue;
                                }

                                html += '<div><div>' + response.find[i].key + '</div><div>' + response.find[i].value + '</div></div>';
                            }
                            html += '</div>';
                        }
                    }

                    html += '</div>';
                    self.viewMeta.querySelector('.meta-file-list').innerHTML = html;

                    let previewOpen =  self.viewMeta.querySelector('.meta-preview-open');
                    let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');

                    if(previewOpen !== null) {
                        let previewOpenImg = previewOpen.querySelector('img');
                        previewOpenImg.addEventListener('click', function () {

                            jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&host=on&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                                response = JSON.parse(response);
                                if(response.path === undefined) {
                                    return;
                                }

                                QuantumUtils.windowOpen("previewFile", response.path + '/' + self.file.getAttribute('data-file'));
                            });

                        });
                    }

                    if(buttonToggleTags !== null) {
                        let metaFind = self.viewMeta.querySelector('.meta-find');
                        buttonToggleTags.addEventListener('click', function () {
                            if(this.classList.contains('active')) {
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

                } else {
                    self.viewMeta.classList.add('hidden');
                }

                if(tmpLength === 0) {
                    let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
                    for(let i=0;i<filesAll.length;i++) {
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

        if(self.options.metafile === '1') {

            if(!cacheReset && self.getCountSelected() >= 1) {
                return;
            }

            if(cacheReset === null || cacheReset === undefined || cacheReset === false) {
                if(self.path === self.cacheMetaPath)
                {
                    self.viewMeta.querySelector('.meta-file-list').innerHTML =  self.cacheMeta;
                    let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');

                    if(buttonToggleTags !== null) {
                        let metaFind = self.viewMeta.querySelector('.meta-find');
                        buttonToggleTags.addEventListener('click', function () {
                            if(this.classList.contains('active')) {
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

            if(self.metaLoadDirCurrent === (Filemanager.data.scope + '/' +self.path)) {
               return;
            }

            self.metaLoadDirCurrent = Filemanager.data.scope + '/' +self.path;

            jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getMetaFile&path=" + encodeURIComponent(self.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope))).done(function (response) {

                response = JSON.parse(response);
                if(response.global !== undefined || response.find !== undefined) {
                    self.viewMeta.classList.remove('hidden');

                    let html = '<div>';

                    if(response.preview !== undefined) {
                        html += '<div class="meta-preview meta-preview-folder"><img src="' + response.preview.link + '" /></div>';
                    }

                    if(response.global !== undefined) {
                        html += '<div class="meta-table">';
                        for (let i in response.global) {

                            if(response.global[i].key === undefined) {
                                continue;
                            }

                            html += '<div><div>' + response.global[i].key + '</div><div>' + response.global[i].value + '</div></div>';
                        }
                        html += '</tbody></table>';
                    }

                    if(response.find !== undefined) {
                        if(Object.keys(response.find).length > 0) {
                            html += '<span class="show-all-tags">' + QuantumviewfilesLang.metaFileShow + '</span>';
                            html += '<div class="meta-find meta-hidden">';
                            for (let i in response.find) {

                                if(response.find[i].key === undefined) {
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

                    if(self.getCountSelected() === 0) {
                        self.viewMeta.querySelector('.meta-file-list').innerHTML = html;
                        let buttonToggleTags = self.viewMeta.querySelector('.show-all-tags');

                        if(buttonToggleTags !== null) {
                            let metaFind = self.viewMeta.querySelector('.meta-find');
                            buttonToggleTags.addEventListener('click', function () {
                                if(this.classList.contains('active')) {
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

        if(self.options.metafile === '1') {

            let filesAll = self.ds.getSelection();
            let size = 0;
            let imgs = '';
            let exsImage = ['jpg', 'png', 'svg', 'jpeg', 'gif'];

            for (let i = filesAll.length - 1; i >= 0; i--) {
                let input = filesAll[i].querySelector('.import-files-check-file');
                if (input.checked) {
                    let dataExs = filesAll[i].getAttribute('data-exs').toLocaleLowerCase();
                    size += parseInt(filesAll[i].getAttribute('data-size'));

                    if (exsImage.indexOf(dataExs) !== -1) {
                        imgs += "<img src='" + filesAll[i].getAttribute('data-filep') + "&path=" + encodeURIComponent(Filemanager.data.path) + "' />"
                    } else {
                        //imgs += "<img src='/media/com_quantummanager/images/icons/files/" + dataExs + ".svg' />"
                        imgs += self.generateIconFile(dataExs);
                    }

                }
            }

            let html = '<div>';
            html += '<div class="meta-preview meta-preview-album">' + imgs + '</div>';
            html += '<div class="meta-table">';
            html += '<div><div>' + QuantumviewfilesLang.metaSelectCount + '</div><div>' + countElement + '</div></div>';
            html += '<div><div>' + QuantumviewfilesLang.metaSelectSize + '</div><div>' + QuantumUtils.bytesToSize(size) + '</div></div>';
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

        if(search === '') {
            for (let i=0;i<filesAll.length;i++) {
                filesAll[i].style.display = 'flex';
            }
            for (let i=0;i<directoryAll.length;i++) {
                directoryAll[i].style.display = 'block';
            }
        } else {
            for (let i=0;i<filesAll.length;i++) {
                let nameFile = filesAll[i].querySelector('.file-name').innerHTML.toLowerCase();
                if(nameFile.indexOf(search) !== -1) {
                    filesAll[i].style.display = 'flex';
                } else {
                    filesAll[i].style.display = 'none';
                }
            }
            for (let i=0;i<directoryAll.length;i++) {
                let nameDirectory = directoryAll[i].querySelector('.directory-name').innerHTML.toLowerCase();
                if(nameDirectory.indexOf(search) !== -1) {
                    directoryAll[i].style.display = 'block';
                } else {
                    directoryAll[i].style.display = 'none';
                }
            }
        }
    };

    this.initBreadcrumbs = function (callback) {
        let self = this;
        let fm = Filemanager;
        let scope = fm.data.scope;

        //jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=" + encodeURIComponent(this.options.directory) + "&scope=" + encodeURIComponent(scope) + '&root=' + encodeURIComponent(this.options.directory)))
        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=root&scope=" + encodeURIComponent(scope) + '&root=root'))
            .done(function (response) {
                response = JSON.parse(response);
                self.breadcrumbsLists = response.directories[0];

                if(callback !== undefined) {
                    callback(self, fm)
                }

                self.trigger('afterInitBreadcrumbs', self);
        }).fail(function () {
            self.breadcrumbsWaitLoad = false;
        });
    };

    this.buildBreadcrumbs = function (el, fm) {

        let self = this;

        if(el !== undefined) {
            self = el;
        }

        if(fm === undefined) {
            fm = Filemanager;
        }

        if(self.breadcrumbsLists === undefined || self.breadcrumbsLists.length === 0) {
            self.initBreadcrumbs(self.buildBreadcrumbs);
            return;
        }

        let currPaths = self.path.split('/');
        let htmlBreadcrumbs = ViewfilesElement.querySelector('.breadcumbs');
        let html = "<ul class='breadcumbs-ul'>";
        let lastElement;
        let clPaths = [];
        let pathAtr = '';
        let title = '';

        for(let i=0;i<currPaths.length;i++) {
            if(i === 0) {
                title = currPaths[i];

                if(self.breadcrumbsLists.title !== undefined) {
                    title = self.breadcrumbsLists.title;
                }

                pathAtr = currPaths[i];
                html += "<li class='clPath root' data-path='" + pathAtr + "'><span>" + title + "</span></li>";
                lastElement = self.breadcrumbsLists.subpath;

                if(self.breadcrumbsLists.subpath.length > 0) {
                    html += '<li class="carret dropdown"><span></span><div class="dropdown-content"><ul>';
                    for (let j=0;j<self.breadcrumbsLists.subpath.length;j++) {
                        html += "<li><span class='clPath' data-path='" + pathAtr + "/" + self.breadcrumbsLists.subpath[j].path + "'>" + self.breadcrumbsLists.subpath[j].path + "</span></li>";
                    }
                    html += '</ul></div></li>';
                }

            } else {
                let otherPaths = [];
                let findPath = '';
                let findElement = lastElement;
                for (let j=0;j<lastElement.length;j++) {
                    if(lastElement[j].path === currPaths[i]) {
                        findPath += currPaths[i];
                        findElement = lastElement[j].subpath
                    } else {
                        otherPaths.push(lastElement[j].path);
                    }
                }

                lastElement = findElement;

                if(otherPaths.length > 0) {
                    html += '<li class="dropdown"><span class="clPath" data-path="' + pathAtr + '/' + findPath + '">' + findPath + '</span><div class="dropdown-content"><ul>';
                } else {
                    html += '<li><span class="clPath" data-path="' + pathAtr  + '/' + findPath + '">' + findPath + '</span>';
                }

                for (let j=0;j<otherPaths.length;j++) {
                    html += "<li><span class='clPath' data-path='" + pathAtr + "/" + otherPaths[j] + "'>" + otherPaths[j] + "</span></li>";
                }

                if(otherPaths.length > 0) {
                    html += "</ul></div></li>";
                } else {
                    html += "</li>";
                }


                if(lastElement.length > 0) {
                    html += '<li class="carret dropdown"><span></span><div class="dropdown-content"><ul>';
                    for (let j=0;j<lastElement.length;j++) {
                        html += "<li><span class='clPath' data-path='" + pathAtr + "/" + findPath + '/' + lastElement[j].path + "'>" + lastElement[j].path + "</span></li>";
                    }
                    html += '</ul></div></li>';
                }

                pathAtr += '/' + findPath;

            }
        }

        html += "</ul>";
        htmlBreadcrumbs.innerHTML = html;
        clPaths = htmlBreadcrumbs.querySelectorAll('.clPath');

        for(let i=0;i<clPaths.length;i++) {
            clPaths[i].addEventListener('click', function (ev) {
                fm.data.path = this.getAttribute('data-path');

                if(localStorage !== undefined) {
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
        for(let i=0;i<filesAll.length;i++) {
            let input = filesAll[i].querySelector('.import-files-check-file');
            let nameFile = filesAll[i].querySelector('.file-name').innerHTML;

            if(typeof files === 'string') {
                if(files === nameFile) {
                    if(!input.checked) {
                        input.checked = !input.checked;
                    }
                    setTimeout(function () {
                        self.listFiles.scrollTop = filesAll[i].getBoundingClientRect().top - 460;
                    }, 300)
                } else {
                    input.checked = false;
                }
            }

            if(typeof files === 'object') {

                if(files.indexOf(nameFile) !== -1) {
                    if(!input.checked) {
                        input.checked = !input.checked;
                    }

                    if(!find) {
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
        if(self.ds !== undefined) {
            let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');

            self.ds.clearSelection();
            for (let i=0;i<filesAll.length;i++) {
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

    this.reloadTypeViewFiles = function(path) {
        let self = this;
        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
        let viewFiles = ViewfilesElement.querySelector('.field-list-files .list');

        if(this.lastTypeViewFiles === '') {
            this.lastTypeViewFiles = 'list-grid';
            let currLastTypeViewFiles = localStorage.getItem('quantummanagerLastTypeViewFiles');
            if(currLastTypeViewFiles !== null) {
                this.lastTypeViewFiles = currLastTypeViewFiles;
            }
        }

        if(path === undefined || path === null) {
            path = this.path;
        }

        if(viewFiles === null || viewFiles === undefined) {
            return;
        }

        if(this.lastTypeViewFiles === 'list-grid') {
            this.gridColumn = 'list-grid-1-6';
            let gridColumnCache = '';

            if(this.gridColumnSet === undefined || this.gridColumnSet === '') {
                if(localStorage !== undefined) {
                    gridColumnCache = localStorage.getItem('quantummanagerLastTypeViewFilesGrid');
                }

                if (gridColumnCache !== null) {
                    this.gridColumn = gridColumnCache;
                }

                if(viewFiles.classList.contains('list-grid-1-6')) {
                    this.gridColumn = 'list-grid-1-5';
                }

                if(viewFiles.classList.contains('list-grid-1-5')) {
                    this.gridColumn = 'list-grid-1-4';
                }

                if(viewFiles.classList.contains('list-grid-1-4')) {
                    this.gridColumn = 'list-grid-1-3';
                }

                if(viewFiles.classList.contains('list-grid-1-3')) {
                    this.gridColumn = 'list-grid-1-2';
                }

                if(viewFiles.classList.contains('list-grid-1-2')) {
                    this.gridColumn = 'list-grid-1-6';
                }
            } else {
                this.gridColumn = this.gridColumnSet;
                this.gridColumnSet = '';
            }


            if(localStorage !== undefined) {
                localStorage.setItem('quantummanagerLastTypeViewFilesGrid', this.gridColumn);
            }

            viewFiles.setAttribute('class', 'list list-grid ' + this.gridColumn);

        }

        if(this.lastTypeViewFiles === 'list-table') {
            viewFiles.setAttribute('class', 'list list-table');
        }

        if(localStorage !== undefined) {
            localStorage.setItem('quantummanagerLastTypeViewFiles', this.lastTypeViewFiles);
        }


        for(let i=0;i<filesAll.length;i++) {

            if(this.path !== path) {
                break;
            }

            if(this.lastTypeViewFiles === 'list-grid') {
                let fileP = filesAll[i].getAttribute('data-fileP');
                let fileName = filesAll[i].getAttribute('data-name');
                let fileExs = filesAll[i].getAttribute('data-exs');
                let filePreview = filesAll[i].querySelector('.file-exs');
                let exsImage = ['jpg', 'png', 'svg', 'jpeg', 'gif'];
                let fields = filesAll[i].querySelector('.fields');

                if(fields !== null) {
                    fields.remove();
                }

                if(exsImage.indexOf(fileExs.toLowerCase()) !== -1) {

                    let file;
                    let image = document.createElement('img');

                    if(fileP.indexOf('index.php') === -1) {
                        file = path.replace('root', path) + '/' + fileP;

                        file = QuantumUtils.getFullUrl("/cache/com_quantummanager/" + file + '?' + QuantumUtils.randomInteger(111111, 999999));

                        if(fileExs.toLowerCase() === 'svg') {
                            file = "/" + path.replace('root', path) + '/' + fileP + '?' + QuantumUtils.randomInteger(111111, 999999);
                        }

                    } else {
                        file = fileP + '&path=' + path + '&v=' + QuantumUtils.randomInteger(111111, 999999);
                    }

                    image.setAttribute('class', 'lazyLoad');
                    image.setAttribute('data-src', file);
                    filePreview.innerHTML = '';
                    filePreview.append(image);
                } else {
                    filePreview.innerHTML = self.generateIconFile(fileExs);
                    filePreview.classList.add('file-icons');
                }

            }

            if(this.lastTypeViewFiles === 'list-table') {
                let htmlFields = '';
                let filePreview = filesAll[i].querySelector('.file-exs');
                let checkFields = filesAll[i].querySelector('.fields');
                filePreview.style.backgroundImage = "";
                filePreview.innerHTML = '';

                if(checkFields === null) {
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

    this.trigger = function(event, target) {
        return Filemanager.events.trigger(event, Filemanager, target);
    };

    Filemanager.events.add(this, 'clickFile', function (fm, el) {
        let file = fm.Quantumviewfiles.file;
        let exs = '';

        if(file === undefined) {

            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
            }

        } else {
            exs = file.getAttribute('data-exs').toLocaleLowerCase();
        }

        let filesAll = el.element.querySelectorAll('.field-list-files .file-item');
        let find = false;

        for(let i=0;i<filesAll.length;i++) {
            if (filesAll[i].querySelector('input').checked) {
                find = true;
            }
        }

        if(['png', 'jpg', 'jpeg'].indexOf(exs) !== -1) {
            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.remove('btn-hide');
            }
        }

        if(find) {
            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('btn-hide');
            }
        } else {
            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
            }

            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
            }
        }
    });

    Filemanager.events.add(this, 'updatePath', function (fm, el) {

        //вырубаем кнопки для выделенного

        if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
        }

        if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
        }

        //запоминаем позицию прокрутки в директории
        if(el.listFiles !== '' && el.listFiles !== null) {
            el.bufferTopDirectories[el.path] = el.listFiles.scrollTop;
        }

        //добавление в историю
        el.history.push(el.path);

        //переключаем путь и открываем папку
        el.path = fm.data.path;
        el.loadDirectory(el.path);

    });

    Filemanager.events.add(this, 'afterInitBreadcrumbs', function (fm, el) {
        if(localStorage !== undefined) {

            let findPath = function (pathSearch, pathParent, find, level) {
                if(level === 0) {
                    if(find === pathSearch.path) {
                        Filemanager.data.path = find;

                        if(el.path !== find) {
                            el.path = find;
                            el.trigger('updatePath');
                        }

                        return;
                    } else {
                        findPath(pathSearch.subpath, pathSearch.path, find, level + 1);
                        return;
                    }
                }

                for (let i=0;i<pathSearch.length;i++) {
                    if(find === (pathParent + '/' + pathSearch[i].path)) {
                        Filemanager.data.path = find;

                        if(el.path !== find) {
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

        if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesWatermark'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
        }

        if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
            fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
        }

        //запоминаем позицию прокрутки в директории
        if(el.listFiles !== '' && el.listFiles !== null) {
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

            for(let i=0;i<filesAll.length;i++) {
                if (filesAll[i].querySelector('input').checked) {
                    self.priorityMetaFile = filesAll[i];
                    fm.Quantumviewfiles.selectFile(filesAll[i], true);

                }
            }

            let countSelected = self.getCountSelected();

            if(countSelected > 1) {
                self.showMetaCountFile(countSelected);
            }

            if(countSelected) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });

    Filemanager.events.add(this, 'updateScope', function (fm, el) {
        fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);
    });

    Filemanager.events.add(this, 'afterReloadTypeViewFiles', function (fm, el) {
        if(fm.Quantumviewfiles.lastTypeViewFiles === 'list-grid') {
            let images = fm.Quantumviewfiles.element.querySelectorAll('.lazyLoad');
            new LazyLoad(images);
        }
    });

};