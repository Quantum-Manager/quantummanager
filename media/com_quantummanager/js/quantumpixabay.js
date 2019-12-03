/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumpixabay = function(Filemanager, QuantumPixbayElement, options) {

    this.options = options;
    this.element = QuantumPixbayElement;
    this.filename = '';
    this.currentPage = 0;
    this.totalPage = 0;
    this.searchStr = '';
    self.masnry = '';
    this.areaSave = QuantumPixbayElement.querySelector('.quantumpixabay-save');
    this.inputSearch = QuantumPixbayElement.querySelector('.quantumpixabay-module-header input');
    this.pageWrap = QuantumPixbayElement.querySelector('.quantumpixabay-module-load-page');
    this.pageButton = QuantumPixbayElement.querySelector('.quantumpixabay-module-load-page button');
    this.closeButton = QuantumPixbayElement.querySelector('.quantumpixabay-module-close');

    this.init = function () {
        let self = this;

        self.areaSave.style.display = 'none';

        Filemanager.Quantumtoolbar.buttonAdd('pixabaySearch', 'right', 'file-other', 'btn-pixabay-search hidden-label', QuantumpixabayLang.button, 'quantummanager-icon-pixabay', {}, function (ev) {
            QuantumPixbayElement.classList.add('active');
            let tmpSearchStr = '';
            self.inputSearch.value = tmpSearchStr;
            self.inputSearch.focus();
            self.search(tmpSearchStr);
            ev.preventDefault();
        });

        self.closeButton.addEventListener('click', function () {
            QuantumPixbayElement.classList.remove('active');
        });

        self.pageButton.addEventListener('click', function () {
            self.search(self.searchStr, self.currentPage + 1);
        });

        self.inputSearch.addEventListener('change', function () {
            self.search(this.value, 1);
        });

        self.inputSearch.addEventListener('focus', function(){
            let that = this;
            setTimeout(function() {
                that.selectionStart = that.selectionEnd = 10000;
            }, 0);
        });

        let filterFieldsLi = self.element.querySelectorAll('.filter-field li');
        for(let i=0;i<filterFieldsLi.length;i++) {
            filterFieldsLi[i].addEventListener('click', function () {
                let field = this.closest('.filter-field');
                field.setAttribute('data-value', this.getAttribute('data-value'));
                field.querySelector('.quantummanager-dropdown-title').innerHTML = this.innerHTML;
                self.search(self.searchStr);
            });
        }
    };

    this.search = function (str, page) {
        let self = this;
        self.searchStr = str;
        self.pageWrap.classList.remove('active');

        if(str === null || str === undefined) {
            str = '';
        }

        if(page === null || page === undefined) {
            page = 1;
        }

        if(localStorage !== undefined) {
           localStorage.setItem('quantumpixabayLastStr', self.searchStr);
        }

        let fieldsForRequest = '';
        let filterFields = self.element.querySelectorAll('.filter-field');
        for (let i=0;i<filterFields.length;i++) {
            fieldsForRequest += '&' + filterFields[i].getAttribute('data-name') + '=' + encodeURIComponent(filterFields[i].getAttribute('data-value'));
        }

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumpixabay.search&q=" + encodeURIComponent(str) + '&page=' + encodeURIComponent(page) + fieldsForRequest)).done(function (response) {
            response = JSON.parse(response);
            self.currentPage = parseInt(page);
            self.totalPage = parseInt(response.totalPage);

            let html = '';
            let container = QuantumPixbayElement.querySelector('.quantumpixabay-module-search');

            if(page === 1) {
                container.innerHTML = '';
                self.masnry = new Masonry('.quantumpixabay-module-search', {
                    itemSelector: '.grid-item',
                    percentPosition: true
                });
            }

            let maxLoaded = response.results.length;
            let currentLoaded = 0;

            if(response.results.length === 0) {
                container.innerHTML = '';
                let elem = document.createElement('div');
                elem.setAttribute('class', 'grid-item');
                elem.innerHTML = QuantumpixabayLang.notFound;
                container.appendChild(elem);
                self.masnry.appended(elem);

                return;
            }

            for(let i=0;i<response.results.length;i++) {

                let dataUrl = '';
                let elem = document.createElement('div');

                if(response.results[i].vectorURL !== undefined) {
                    dataUrl = response.results[i].vectorURL;
                } else {
                    dataUrl = response.results[i].imageURL;
                }

                elem.setAttribute('class', 'grid-item');
                elem.setAttribute('data-url', dataUrl);
                elem.setAttribute('data-id', response.results[i]['id']);

                let metaWrap = document.createElement('div');
                metaWrap.setAttribute('class', 'pixabay-meta-wrap');

                let meta = document.createElement('div');
                meta.setAttribute('class', 'pixabay-meta');

                let like = document.createElement('div');
                like.setAttribute('class', 'pixabay-like');
                like.innerHTML = "<img class='svg' src='/media/com_quantummanager/images/icons/action/favorite-heart-button.svg'></img>";
                like.innerHTML += "<span class='pixabay-like-count'>" + response.results[i]['likes'] + "</span>";
                meta.appendChild(like);

                let user = document.createElement('div');
                user.setAttribute('class', 'pixabay-user');
                user.innerHTML = "<div class='pixabay-user-avatar' style='background-image: url(" + response.results[i]['userImageURL'] + ")'></div>";
                user.innerHTML += "<span class='pixabay-user-name'>" + response.results[i]['user'] + "</span>";
                meta.appendChild(user);

                metaWrap.appendChild(meta);

                let image = document.createElement('img');
                image.setAttribute('src', response.results[i]['webformatURL']);
                image.onload = function() {
                    currentLoaded++;
                };

                image.onerror = function() {
                    currentLoaded++;
                };

                elem.append(image);
                elem.append(metaWrap);

                container.appendChild(elem);
                self.masnry.appended(elem);

                QuantumUtils.replaceImgToSvg('.quantumpixabay-module-search');

                elem.addEventListener('click', function (ev) {

                    if(ev.target.tagName === 'SPAN') {
                        return;
                    }

                    let element = this;
                    self.areaSave.style.display = 'block';

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumpixabay.download&path=" + encodeURIComponent(Filemanager.data.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope)
                        + '&file=' + encodeURIComponent(element.getAttribute('data-url'))
                        + '&id=' + encodeURIComponent(element.getAttribute('data-id'))
                    )).done(function (response) {
                        response = JSON.parse(response);

                        if(response.name !== undefined) {
                            self.filename = response.name;
                        }

                        QuantumPixbayElement.classList.remove('active');
                        self.areaSave.style.display = 'none';
                        Filemanager.events.trigger('pixabayComplete', Filemanager);
                    }).fail(function () {
                        QuantumPixbayElement.classList.remove('active');
                        self.areaSave.style.display = 'none';
                    });

                    ev.preventDefault();
                });

            }

            self.masnry.layout();

            let intervalLayout = setInterval(function () {

                if(currentLoaded === maxLoaded) {
                    self.masnry.layout();
                    clearInterval(intervalLayout)
                }
            }, 100);


            if(parseInt(response.totalPage) > 1 && (self.currentPage !== self.totalPage)) {
                self.pageWrap.classList.add('active');
            } else {
                self.pageWrap.classList.remove('active');
            }


        });
    };

    Filemanager.events.add(this, 'pixabayComplete', function (fm, el) {
        Filemanager.Quantumviewfiles.loadDirectory(null, function () {
            fm.Quantumviewfiles.scrollTopFilesCheck(Filemanager.Quantumunsplash.filename);

            let filesAll = fm.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
            let find = false;
            let element;

            for(let i=0;i<filesAll.length;i++) {
                if (Filemanager.Quantumpixabay.filename === filesAll[i].getAttribute('data-file')) {
                    fm.Quantumviewfiles.selectFile(filesAll[i]);
                    find = true;
                }
            }

            if(find) {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.remove('btn-hide');
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.remove('btn-hide');
            } else {
                fm.Quantumtoolbar.buttonsList['viewfilesWatermark'].classList.add('btn-hide');
                fm.Quantumtoolbar.buttonsList['viewfilesDelete'].classList.add('btn-hide');
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });

};

