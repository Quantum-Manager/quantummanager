/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumtreecatalogs = function(Filemanager, QuantumTreeCatalogsElement, options) {

    this.options = options;
    this.active = '';

    this.init = function() {
        this.path = this.options.directory;
        this.loadDirectory();
    };

    this.loadDirectory = function (path, callback, reload) {

        let self = this;

        if (path === null || path === undefined) {
            path = this.path;
        } else {
            this.path = path;
        }

        if (reload === null || reload === undefined) {
            reload = false;
        }

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=" + encodeURIComponent(path) + '&root=' + encodeURIComponent(self.options.directory))).done(function (response) {

            response = JSON.parse(response);
            if(response.directories === undefined) {
                return false;
            }

            if(reload) {
                QuantumTreeCatalogsElement.querySelector('.tree-scroll').innerHTML = '';
            }

            for(var i=0;i<response.directories.length;i++) {

                let html = "<ul class=\"tree-ul root-scope\" data-scope='" + response.directories[i]['scopeid'] + "'>" + self.directoriesPrepare(response.directories[i], 0) + "</ul>";

                QuantumTreeCatalogsElement.querySelector('.tree-scroll').innerHTML += html;

                let toggler = QuantumTreeCatalogsElement.querySelectorAll(".tree-caret");
                let treePaths = QuantumTreeCatalogsElement.querySelectorAll(".tree-path");
                let timer = 0;
                let delay = 200;
                let prevent = false;


                for (let i=0;i<toggler.length;i++) {
                    toggler[i].addEventListener("click", function() {
                        this.parentElement.querySelector(".tree-nested").classList.toggle("active");
                        this.classList.toggle("tree-caret-down");
                    });
                }

                for (let i=0;i<treePaths.length;i++) {
                    treePaths[i].addEventListener("click", function(ev) {
                        let element = this;
                        timer = setTimeout(function() {
                            if (!prevent) {
                                self.treePathsClick(element, self);
                            }
                            prevent = false;
                        }, delay);
                    });
                }

                for (let i=0;i<treePaths.length;i++) {
                    treePaths[i].addEventListener("dblclick", function(ev) {
                        let element = this;
                        clearTimeout(timer);
                        prevent = true;
                        self.treePathsDblclick(element, self);
                    });
                }

                let tmpRoot = QuantumTreeCatalogsElement.querySelector('[data-scope="' + Filemanager.data.scope + '"] .root');

                if(tmpRoot !== null) {
                    let tmpCaret = tmpRoot.closest('li').querySelector('.tree-caret');

                    if(tmpCaret !== null) {
                        tmpCaret.click();
                    }

                }

            }

            Filemanager.Quantumtreecatalogs.directoryScroll(Filemanager.data.path);

        });
    };

    this.treePathsDblclick = function (element, qte) {
        let carret = element.closest('li').querySelector('.tree-caret');
        if(carret !== null) {
            carret.click();
        }
    };

    this.treePathsClick = function (element, qte) {

        let scope = element.closest('.root-scope').getAttribute('data-scope');
        let pathFind = [];
        let currLi = element.closest('li');
        let maxI = 500;
        let currI = 0;
        while(true) {

            if(currI > maxI) {
                break;
            }

            if(currLi.querySelector('.tree-path').classList.contains('root')) {
                pathFind.push(currLi.querySelector('.tree-path').getAttribute('data-path'));
                Filemanager.data.path = pathFind.reverse().join('/');

                if(Filemanager.data.scope !== scope) {
                    Filemanager.data.scope = scope;
                    qte.trigger('updateScope');
                }


                if(localStorage !== undefined) {
                    localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
                    localStorage.setItem('quantummanagerScope', Filemanager.data.scope);
                }

                qte.trigger('clickTreeDirectory', this);
                qte.trigger('updatePath');

                break;
            } else {
                pathFind.push(currLi.querySelector('.tree-path').innerHTML);
                currLi = currLi.closest('ul').closest('li');
            }

            currI++;
        }

    };

    this.directoriesPrepare = function (directories, level) {
        let title = '';
        if(directories.title !== undefined) {
            title = directories.title;
        } else {
            title = directories.path;
        }

        if(directories.subpath !== undefined && directories.subpath.length > 0) {
            let html = "<li><span class=\"tree-caret\"></span> <span data-path='" + directories.path + "' class='tree-path " + ((level === 0) ? "root" : "" ) + " " + (parseInt(directories.is_empty) === 0 ? 'empty' : '') + " '>" + title + "</span> <ul class='tree-nested'>";
            for(let i=0;i<directories.subpath.length;i++) {
                html += this.directoriesPrepare(directories.subpath[i], level + 1);
            }
            html += "</ul></li>";
            return html;
        } else {
            return "<li><span data-path='" + directories.path + "' class='tree-path " + ((level === 0) ? "root" : "" ) + " " + (parseInt(directories.is_empty) === 0 ? 'empty' : '') + "'>" + title + "</span></li>";
        }
    };


    this.directoryScroll = function (pathSource) {
        let self = this;
        let li = QuantumTreeCatalogsElement.querySelector('[data-scope="' + Filemanager.data.scope + '"] .root');
        if(li === null) {
            return;
        }

        li = li.closest('li');
        let pathFind = li.querySelector('.tree-path').getAttribute('data-path');
        let findDirectory = false;

        let findPathInLists = function (li, pathParent) {

            if(findDirectory) {
                return;
            }

            let nestedUl = li.querySelectorAll('.tree-nested');
            for(let j=0;j<nestedUl.length;j++) {
                let nestedLi = nestedUl[j].children;
                for(let i=0;i<nestedLi.length;i++) {
                    let currPathFind = pathParent + '/' + nestedLi[i].querySelector('.tree-path').innerHTML;
                    if(currPathFind === pathSource) {
                        findDirectory = true;
                        let lastLi = nestedLi[i];
                        let top = 0;
                        let deleteDirertory = document.createElement('div');
                        let deleteDirectoryIcon = document.createElement('span');
                        let editDirertory = document.createElement('div');
                        let editDirectoryIcon = document.createElement('span');
                        deleteDirertory.setAttribute('class', 'tree-delete');
                        deleteDirectoryIcon.setAttribute('class', 'quantummanager-icon quantummanager-icon-delete');
                        deleteDirertory.append(deleteDirectoryIcon);

                        editDirertory.setAttribute('class', 'tree-edit');
                        editDirectoryIcon.setAttribute('class', 'quantummanager-icon quantummanager-icon-edit');
                        editDirertory.append(editDirectoryIcon);

                        if(self.active !== '') {
                            let deleteActive = self.active.querySelector('.tree-delete');
                            if(deleteActive !== null) {
                                self.active.querySelector('.tree-delete').remove();
                            }

                            let editActive = self.active.querySelector('.tree-edit');
                            if(editActive !== null) {
                                self.active.querySelector('.tree-edit').remove();
                            }

                            self.active.classList.remove('active');
                        }

                        self.active = nestedLi[i];
                        self.active.classList.add('active');
                        QuantumUtils.insertAfter(editDirertory,  self.active.querySelector('.tree-path'));
                        QuantumUtils.insertAfter(deleteDirertory,  self.active.querySelector('.tree-path'));

                        deleteDirertory.addEventListener('click', function (ev) {
                            let deleteNamePath = this.closest('li').querySelector('.tree-path').innerHTML;
                            let selfThis = this;

                            QuantumUtils.confirm(QuantumUtils.htmlspecialcharsDecode(QuantumtreecatalogsLang.confirmDelete, 'ENT_QUOTES') + ' ' + deleteNamePath + '?', function (result) {
                                let files = [];
                                let pathDelete = Filemanager.data.path.split('/');
                                pathDelete.pop();
                                files.push(selfThis.closest('li').querySelector('.tree-path').innerHTML);
                                jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(pathDelete.join('/')) + '&list=' + encodeURIComponent(JSON.stringify(files)) + '&scope=' + encodeURIComponent(Filemanager.data.scope))).done(function (response) {
                                    Filemanager.data.path = pathDelete.join('/');

                                    if(localStorage !== undefined) {
                                        localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
                                    }

                                    Filemanager.events.trigger('reloadPaths', Filemanager);
                                });
                            });

                            ev.preventDefault();
                        });

                        editDirertory.addEventListener('click', function (ev) {
                            let name = this.closest('li').querySelector('.tree-path').innerHTML;
                            let pathEdit = Filemanager.data.path.split('/');
                            pathEdit.pop();
                            pathEdit = pathEdit.join('/');

                            QuantumUtils.prompt(QuantumtreecatalogsLang.fileName, name , function (result) {
                                jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.renameDirectory&path=" + encodeURIComponent(pathEdit) + '&oldName=' + encodeURIComponent(name) + '&name='+ encodeURIComponent(result) + '&scope=' + encodeURIComponent(Filemanager.data.scope) + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
                                    response = JSON.parse(response);
                                    if(response.status === undefined) {
                                        return;
                                    }

                                    if(response.status === 'ok') {

                                        pathEdit += '/' + result;
                                        if(localStorage !== undefined) {
                                            localStorage.setItem('quantummanagerLastDir', pathEdit);
                                        }

                                        Filemanager.data.path = pathEdit;
                                        Filemanager.events.trigger('reloadPaths', Filemanager);
                                    }
                                });
                            });
                        });

                        //top = lastLi.closest('.root-scope').offsetTop;

                        while(true) {

                            if(lastLi === null) {
                                break;
                            }

                            let carret = lastLi.querySelector('.tree-caret');
                            if(carret !== null) {
                                if(!carret.classList.contains('tree-caret-down')) {
                                    carret.click();
                                }
                            }

                            top += lastLi.offsetTop;
                            lastLi = lastLi.closest('ul').closest('li');

                        }

                        QuantumTreeCatalogsElement.querySelector('.tree-scroll').scrollTop = top - 25;
                        return;
                    } else {
                        findPathInLists(nestedLi[i], currPathFind)
                    }
                }
            }
        };

        //если рут, если нет, запускаем поиск
        if(pathFind === pathSource) {

            if(self.active !== '') {
                let deleteActive = self.active.querySelector('.tree-delete');
                if(deleteActive !== null) {
                    self.active.querySelector('.tree-delete').remove();
                }


                let editActive = self.active.querySelector('.tree-edit');
                if(editActive !== null) {
                    self.active.querySelector('.tree-edit').remove();
                }

                self.active.classList.remove('active');
            }

            self.active = li;
            self.active.classList.add('active');

            let carret = self.active.querySelector('.tree-caret');
            if(carret !== null) {
                if(!carret.classList.contains('tree-caret-down')) {
                    carret.click();
                }
            }

            QuantumTreeCatalogsElement.querySelector('.tree-scroll').scrollTop = self.active.closest('.root-scope').offsetTop - 25;

        } else {
            findDirectory = false;
            findPathInLists(li, pathFind);
        }

    };

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

    QuantumEventsDispatcher.add(this, 'updatePath', function (fm, el) {
        fm.Quantumtreecatalogs.directoryScroll(fm.data.path);
    });

    QuantumEventsDispatcher.add(this, 'uploadComplete', function (fm, el) {
        fm.Quantumtreecatalogs.loadDirectory(this.path, function () {}, true);
    });

    QuantumEventsDispatcher.add(this, 'reloadPaths', function (fm, el) {
        fm.Quantumtreecatalogs.loadDirectory(this.path, function () {}, true);
    });

};