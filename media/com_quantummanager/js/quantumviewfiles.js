/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Quantumviewfiles = function(Filemanager, ViewfilesElement, options) {

    this.element = ViewfilesElement;
    this.options = options;
    this.viewMeta = ViewfilesElement.querySelector('.view-wrap .meta-file');
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
                }

                Filemanager.data.path = directory;
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesBack');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesUp', 'left', 'navigations', 'btn-up hidden-label', QuantumviewfilesLang.buttonUp, 'quantummanager-icon-up', {}, function (ev) {
                let currDirectories = Filemanager.data.path.split('/');
                if (currDirectories.length > 1) {
                    currDirectories.pop();
                    Filemanager.data.path = currDirectories.join('/');
                    self.trigger('updatePath');
                }
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesUp');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesGrid', 'center', 'list-view', 'btn-grid hidden-label', '', 'quantummanager-icon-grid', {'data-tooltip': QuantumviewfilesLang.changeGridViews}, function (ev) {
                Filemanager.Quantumviewfiles.ListviewToGrid();
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesGrid');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesTable', 'center', 'list-view', 'btn-table hidden-label', '', 'quantummanager-icon-table', {}, function (ev) {
                Filemanager.Quantumviewfiles.ListviewToTable();
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesTable');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesCreateDirectory', 'center', 'file-actions', 'btn-create-directory hidden-label', QuantumviewfilesLang.buttonCreateDirectory, 'quantummanager-icon-directory', {}, function (ev) {
                let nameDirectory;
                nameDirectory = prompt(QuantumviewfilesLang.directoryName, '');

                if(nameDirectory !== null) {

                    if(nameDirectory !== '') {
                        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.createDirectory&path=" + encodeURIComponent(Filemanager.data.path) + '&name=' + encodeURIComponent(nameDirectory)).done(function (response) {
                            Filemanager.events.trigger('reloadPaths', Filemanager);
                        });
                    } else {
                        alert(QuantumviewfilesLang.directoryNameRequired);
                    }

                } else {
                    alert(QuantumviewfilesLang.directoryNameRequired);
                }

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

                jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(Filemanager.data.path) + '&list=' + encodeURIComponent(JSON.stringify(files))).done(function (response) {
                    Filemanager.events.trigger('reloadPaths', Filemanager);
                });

                Filemanager.Quantumtoolbar.trigger('buttonViewfilesDelete');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesReloadPaths', 'center', 'file-other', 'btn-reload', '', 'quantummanager-icon-reload', {}, function (ev) {
                Filemanager.events.trigger('reloadPaths', Filemanager);
                Filemanager.Quantumtoolbar.trigger('buttonViewfilesReloadPaths');
                ev.preventDefault();
            });

            Filemanager.Quantumtoolbar.buttonAdd('viewfilesUploadFile', 'center', 'file-other', 'btn-upload-file hidden-label', QuantumviewfilesLang.buttonUpload, 'quantummanager-icon-upload', {}, function (ev) {
                Filemanager.Qantumupload.selectFiles();
                ev.preventDefault();
            });

        }

        searchByName.addEventListener('keyup', function () {
            self.searchByName(this.value);
        });

    };

    this.loadDirectory = function (path, callback) {

        let self = this;

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

        if(self.viewMeta !== null) {
            self.viewMeta.classList.add('hidden');
        }

        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getFiles&path=" + encodeURIComponent(path) + '&v=' + QuantumUtils.randomInteger(111111, 999999)).done(function (response) {
            response = JSON.parse(response);

            if(response.error !== undefined) {
                Filemanager.data.path = 'root';
                self.trigger('updatePath');
                return;
            }

            let htmlfilesAndDirectories = '<div class="field-list-files"><div class="list">';
            let files = response.files;
            let directories = response.directories;

            if(!parseInt(self.options.onlyfiles)) {
                for (let i = 0; i < directories.length; i++) {
                    htmlfilesAndDirectories += "<div class='directory-item'><div class='directory'><div class='directory-icon'><span></span></div><div class='directory-name'>" + directories[i] + "</div></div></div>";
                }
            }

            for(let i = 0;i<files.length;i++) {
                let type = files[i]['exs'];
                htmlfilesAndDirectories += "<div class='file-item' data-size='" + files[i]['size'] + "' data-name='" + files[i]['name'] + "' data-exs='" + files[i]['exs'] + "' data-fileP='" + files[i]['fileP'] + "' data-dateC='" + files[i]['dateC'] + "' data-dateM='" + files[i]['dateM'] + "' data-file='" + files[i]['file'] + "'><input type=\"checkbox\" class=\"import-files-check-file\"><div class='file'><div class='context-menu-open'><span></span></div><div class='file-exs icon-file-" + type + "'><div class='av-folderlist-label'></div></div><div class='file-name'>" + files[i]['file'] + "</div></div></div>" ;
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

            for(let i=0;i<filesAll.length;i++) {
                filesAll[i].addEventListener('click', function () {
                    let element = this;
                    timer = setTimeout(function() {
                        if (!prevent) {
                            self.fileClick(element, self);
                        }
                        prevent = false;
                    }, delay);
                });

                filesAll[i].addEventListener('dblclick', function () {
                    let element = this;
                    clearTimeout(timer);
                    prevent = true;
                    self.fileDblclick(element, self);
                });
            }

            if(!parseInt(self.options.onlyfiles)) {
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
                }
            }

            if(self.searchNameValue !== '')
            {
                self.searchByName(self.searchNameValue);
            }

            self.buildBreadcrumbs();

            if(callback !== undefined) {
                callback();
            }

        });

    };

    this.fileClick = function (element, qvf) {
        let self = this;
        let tmpInput = element.closest('.file-item').querySelector('.import-files-check-file');
        tmpInput.checked = !tmpInput.checked;
        qvf.file = element;
        qvf.trigger('clickFile', element);

        if(tmpInput.checked) {
            self.showMetaFile(element);
        } else {
            self.hideMetaFile();
        }

    };

    this.fileDblclick = function (element, qvf) {
        qvf.trigger('dblclickFile', element);
    };

    this.preoloader = function () {
        ViewfilesElement.querySelector('.view').innerHTML = "<div class=\"loader\">" +  QuantumviewfilesLang.loading + "<span></span><span></span><span></span><span></span></div>";
    };

    this.hideMetaFile = function () {
        let self = this;

        if(self.options.metafile === '1') {
            self.viewMeta.classList.add('hidden');
        }

    };

    this.showMetaFile = function (element) {
        let self = this;

        if(self.options.metafile === '1') {
            jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getMetaFile&path=" + encodeURIComponent(self.path) + '&name=' + encodeURIComponent(element.getAttribute('data-file'))).done(function (response) {
                response = JSON.parse(response);
                if(response.global !== undefined || response.find !== undefined) {
                    self.viewMeta.classList.remove('hidden');

                    let html = '';

                    if(response.global !== undefined) {
                        html += '<table><tbody>';
                        for (let i in response.global) {
                            html += '<tr><td>' + response.global[i].key + '</td><td>' + response.global[i].value + '</td></tr>';
                        }
                        html += '</tbody></table>';
                    }

                    if(response.find !== undefined) {
                        if(Object.keys(response.find).length > 0) {
                            html += '<span class="show-all-tags">' + QuantumviewfilesLang.metaFileShow + '</span>';
                            html += '<table class="meta-find meta-hidden"><tbody>';
                            for (let i in response.find) {
                                html += '<tr><td>' + response.find[i].key + '</td><td>' + response.find[i].value + '</td></tr>';
                            }
                            html += '</tbody></table>';
                        }
                    }

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

                } else {
                    self.viewMeta.classList.add('hidden');
                }
            });
        }
    };

    this.searchByName = function (search) {
        this.searchNameValue = search;
        let filesAll = ViewfilesElement.querySelectorAll('.field-list-files .file-item');
        let directoryAll = ViewfilesElement.querySelectorAll('.field-list-files .directory-item');

        if(search === '') {
            for (let i=0;i<filesAll.length;i++) {
                filesAll[i].style.display = 'block';
            }
            for (let i=0;i<directoryAll.length;i++) {
                directoryAll[i].style.display = 'block';
            }
        } else {
            for (let i=0;i<filesAll.length;i++) {
                let nameFile = filesAll[i].querySelector('.file-name').innerHTML;
                if(nameFile.indexOf(search) !== -1) {
                    filesAll[i].style.display = 'block';
                } else {
                    filesAll[i].style.display = 'none';
                }
            }
            for (let i=0;i<directoryAll.length;i++) {
                let nameDirectory = directoryAll[i].querySelector('.directory-name').innerHTML;
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
        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=" + encodeURIComponent(this.options.directory))
            .done(function (response) {
                response = JSON.parse(response);

                self.breadcrumbsLists = response.directories;

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

        for(let i=0;i<currPaths.length;i++) {
            if(i === 0) {
                pathAtr = currPaths[i];
                html += "<li class='clPath root' data-path='" + pathAtr + "'><span>" + currPaths[i] + "</span></li>";
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

    this.ListviewToGrid = function () {
        this.lastTypeViewFiles = 'list-grid';
        this.reloadTypeViewFiles();
    };

    this.ListviewToTable = function () {
        this.lastTypeViewFiles = 'list-table';
        this.reloadTypeViewFiles();
    };

    this.reloadTypeViewFiles = function(path) {
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
            let gridColumn = 'list-grid-1-5';
            let gridColumnCache = '';
            if(localStorage !== undefined) {
                gridColumnCache = localStorage.getItem('quantummanagerLastTypeViewFilesGrid');
            }

            if (gridColumnCache !== null) {
                gridColumn = gridColumnCache;
            }

            if(viewFiles.classList.contains('list-grid-1-5')) {
                gridColumn = 'list-grid-1-4';
            }

            if(viewFiles.classList.contains('list-grid-1-4')) {
                gridColumn = 'list-grid-1-3';
            }

            if(viewFiles.classList.contains('list-grid-1-3')) {
                gridColumn = 'list-grid-1-5';
            }

            if(localStorage !== undefined) {
                localStorage.setItem('quantummanagerLastTypeViewFilesGrid', gridColumn);
            }

            viewFiles.setAttribute('class', 'list list-grid ' + gridColumn);

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
                if(exsImage.indexOf(fileExs.toLowerCase()) !== -1) {

                    let file;
                    let image = document.createElement('img');

                    if(fileP.indexOf('index.php') === -1) {
                        file = path.replace('root', path) + '/' + fileP;

                        file = "/images/com_quantummanager/cache/" + file + '?' + QuantumUtils.randomInteger(111111, 999999);

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
                    let exsAvailable = ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'mp3', 'ogg', 'flac', 'pdf', 'zip', 'txt', 'html', 'css', 'js', 'webp'];
                    if(exsAvailable.indexOf(fileExs) !== -1) {
                        let file = "/media/com_quantummanager/images/icons/" + fileExs + ".svg";
                        filePreview.style.backgroundImage = "url(" + file + ")";
                        filePreview.classList.add('file-icons');
                    } else {
                        let file = "/media/com_quantummanager/images/icons/other.svg";
                        filePreview.style.backgroundImage = "url(" + file + ")";
                        filePreview.classList.add('file-icons');
                    }
                }

            }

            if(this.lastTypeViewFiles === 'list-table') {
                let filePreview = filesAll[i].querySelector('.file-exs');
                filePreview.style.backgroundImage = "";
                filePreview.innerHTML = '';
            }

        }

        this.trigger('afterReloadTypeViewFiles', this);

    };

    this.trigger = function(event, target) {
        Filemanager.events.trigger(event, Filemanager, target);
    };

    Filemanager.events.add(this, 'clickFile', function (fm, el) {
        let filesAll = el.element.querySelectorAll('.field-list-files .file-item');
        let find = false;

        for(let i=0;i<filesAll.length;i++) {
            if (filesAll[i].querySelector('input').checked) {
                find = true;
            }
        }

        if(find) {
            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('btn-hide');
            }
        } else {
            if(fm.Quantumtoolbar !== undefined && fm.Quantumtoolbar.buttonsList['viewfilesDelete'] !== undefined) {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
            }
        }
    });

    Filemanager.events.add(this, 'updatePath', function (fm, el) {

        //вырубаем кнопки для выделенного
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
                    element = filesAll[i];
                    find = true;
                }
            }

            if(find) {
                fm.Quantumviewfiles.showMetaFile(element);
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });

    Filemanager.events.add(this, 'afterReloadTypeViewFiles', function (fm, el) {
        if(fm.Quantumviewfiles.lastTypeViewFiles === 'list-grid') {
            let images = fm.Quantumviewfiles.element.querySelectorAll('.lazyLoad');
            new LazyLoad(images);
        }

    });

};