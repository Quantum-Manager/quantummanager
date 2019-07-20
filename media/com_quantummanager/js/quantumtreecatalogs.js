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

    this.loadDirectory = function (path, callback) {

        let self = this;

        if (path === null || path === undefined) {
            path = this.path;
        } else {
            this.path = path;
        }


        if (Filemanager.data.path === undefined || Filemanager.data.path !== path) {
            //Filemanager.data.path = path;
        }

        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumtreecatalogs.getDirectories&path=" + encodeURIComponent(path) + '&root=' + encodeURIComponent(self.options.directory)).done(function (response) {

            response = JSON.parse(response);
            if(response.directories !== undefined) {
                let html = "<ul class=\"tree-ul\">" + self.directoriesPrepare(response.directories, 0) + "</ul>";
                QuantumTreeCatalogsElement.querySelector('.tree-scroll').innerHTML = html;
            }

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

            let tmpCaret = QuantumTreeCatalogsElement.querySelector('.root').closest('li').querySelector('.tree-caret');

            if(tmpCaret !== null) {
                tmpCaret.click();
            }

            Filemanager.Quantumtreecatalogs.directoryScroll(Filemanager.data.path);

        });
    };

    this.treePathsDblclick = function (element, qte) {
        element.closest('li').querySelector('.tree-caret').click();
    };

    this.treePathsClick = function (element, qte) {

        let pathFind = [];
        let currLi = element.closest('li');
        let maxI = 500;
        let currI = 0;
        while(true) {

            if(currI > maxI) {
                break;
            }

            if(currLi.querySelector('.tree-path').classList.contains('root')) {
                pathFind.push(currLi.querySelector('.tree-path').innerHTML);
                Filemanager.data.path = pathFind.reverse().join('/');

                if(localStorage !== undefined) {
                    localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
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
        if(directories.subpath !== undefined && directories.subpath.length > 0) {
            let html = "<li><span class=\"tree-caret\"></span> <span class='tree-path " + ((level === 0) ? "root" : "" ) + " '>" + directories.path + "</span> <ul class='tree-nested'>";
            for(let i=0;i<directories.subpath.length;i++) {
                html += this.directoriesPrepare(directories.subpath[i], level + 1);
            }
            html += "</ul></li>";
            return html;
        } else {
            return "<li><span class='tree-path " + ((level === 0) ? "root" : "" ) + "'>" + directories.path + "</span></li>";
        }
    };


    this.directoryScroll = function (pathSource) {
        let self = this;
        let li = QuantumTreeCatalogsElement.querySelector('.root').closest('li');
        let pathFind = li.querySelector('.tree-path').innerHTML;

        if(li === null) {
            return;
        }

        let findPathInLists = function (li, pathParent) {
            let nestedLi = li.querySelectorAll('.tree-nested > li');
            for(let i=0;i<nestedLi.length;i++) {
                let currPathFind = pathParent + '/' + nestedLi[i].querySelector('.tree-path').innerHTML;
                if(currPathFind === pathSource) {
                    let lastLi = nestedLi[i];
                    let top = 0;
                    let deleteDirertory = document.createElement('div');
                    let deleteDirectoryIcon = document.createElement('span');
                    deleteDirertory.setAttribute('class', 'tree-delete');
                    deleteDirectoryIcon.setAttribute('class', 'quantummanager-icon quantummanager-icon-delete');
                    deleteDirertory.append(deleteDirectoryIcon);

                    if(self.active !== '') {
                        let deleteActive = self.active.querySelector('.tree-delete');
                        if(deleteActive !== null) {
                            self.active.querySelector('.tree-delete').remove();
                        }
                        self.active.classList.remove('active');
                    }

                    self.active = nestedLi[i];
                    self.active.classList.add('active');
                    QuantumUtils.insertAfter(deleteDirertory,  self.active.querySelector('.tree-path'));

                    deleteDirertory.addEventListener('click', function (ev) {
                        let deleteNamePath = this.closest('li').querySelector('.tree-path').innerHTML;
                        let selfThis = this;

                        QuantumUtils.confirm(QuantumtreecatalogsLang.confirmDelete + ' ' + deleteNamePath + '?', function (result) {
                            let files = [];
                            let pathDelete = Filemanager.data.path.split('/');
                            pathDelete.pop();
                            files.push(selfThis.closest('li').querySelector('.tree-path').innerHTML);
                            jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.delete&path=" + encodeURIComponent(pathDelete.join('/')) + '&list=' + encodeURIComponent(JSON.stringify(files))).done(function (response) {
                                Filemanager.data.path = pathDelete.join('/');

                                if(localStorage !== undefined) {
                                    localStorage.setItem('quantummanagerLastDir', Filemanager.data.path);
                                }

                                Filemanager.events.trigger('reloadPaths', Filemanager);
                            });
                        });

                        ev.preventDefault();
                    });

                    while(true) {
                        let carret = lastLi.querySelector('.tree-caret');
                        if(carret !== null) {
                            if(!carret.classList.contains('tree-caret-down')) {
                                carret.click();
                            }
                        }
                        top += lastLi.offsetTop;
                        lastLi = lastLi.closest('ul').closest('li');

                        if(lastLi.querySelector('.tree-path').classList.contains('root')) {
                            break;
                        }

                    }

                    //QuantumTreeCatalogsElement.querySelector('.tree-scroll').scrollTop = top - 25;

                } else {
                    findPathInLists(nestedLi[i], currPathFind)
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

                self.active.classList.remove('active');
            }

            self.active = li;
            self.active.classList.add('active');

            QuantumTreeCatalogsElement.querySelector('.tree-scroll').scrollTop = 0;
        } else {
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
        fm.Quantumtreecatalogs.loadDirectory();
    });

    QuantumEventsDispatcher.add(this, 'reloadPaths', function (fm, el) {
        fm.Quantumtreecatalogs.loadDirectory();
    });

};