/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumpexels = function(Filemanager, QuantumPexelsElement, options) {

    this.options = options;
    this.element = QuantumPexelsElement;
    this.filename = '';
    this.currentPage = 0;
    this.totalPage = 0;
    this.searchStr = '';
    this.masonry = '';
    this.loadPage = false;
    this.searchWrap = QuantumPexelsElement.querySelector('.quantumpexels-module-container-search-wrap .quantumpexels-module-container-search');
    this.searchGrid = QuantumPexelsElement.querySelector('.quantumpexels-module-container-search-wrap .quantumpexels-module-container-search .quantumpexels-module-search');
    this.areaSave = QuantumPexelsElement.querySelector('.quantumpexels-save');
    this.inputSearch = QuantumPexelsElement.querySelector('.quantumpexels-module-header input');
    this.pageWrap = QuantumPexelsElement.querySelector('.quantumpexels-module-load-page');
    this.pageButton = QuantumPexelsElement.querySelector('.quantumpexels-module-load-page button');
    this.closeButton = QuantumPexelsElement.querySelector('.quantumpexels-module-close');

    this.init = function () {
        let self = this;

        self.areaSave.style.display = 'none';


        let buttonPhotostock = Filemanager.Quantumtoolbar.buttonAdd(
            'photostock',
            'center',
            'file-other',
            'btn-more',
            QuantumpexelsLang.photostock,
            '',
            {},
            function (ev) {}).parentElement;

        Filemanager.Quantumtoolbar.buttonAdd(
            'pexelsSearch',
            'right',
            'file-other',
            'btn-pexels-search hidden-label',
            QuantumpexelsLang.button,
            'quantummanager-icon-pexels',
            {},
            function (ev) {
                QuantumPexelsElement.classList.add('active');

                if(self.inputSearch.value === '')
                {
                    self.search('');
                }

                self.inputSearch.focus();
                ev.preventDefault();
            },
            buttonPhotostock
        );

        self.closeButton.addEventListener('click', function () {
            QuantumPexelsElement.classList.remove('active');
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

        self.searchWrap.addEventListener('scroll', function () {
            if((self.searchGrid.offsetHeight - (self.searchWrap.scrollTop + self.searchWrap.offsetHeight)) < 400) {
                self.search(self.searchStr, self.currentPage + 1);
            }
        });


        let filterFieldsLi = self.element.querySelectorAll('.filter-field li');
        for(let i=0;i<filterFieldsLi.length;i++) {
            filterFieldsLi[i].addEventListener('click', function () {
                let field = this.closest('.filter-field');
                field.setAttribute('data-value', this.getAttribute('data-value'));
                field.querySelector('.quantummanager-dropdown-title').innerHTML = this.innerHTML;

                if(field.hasAttribute('data-disabled')) {
                    return;
                }

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

        if(page !== 1) {
            if(this.loadPage) {
                return;
            }
        }

        if(localStorage !== undefined) {
           localStorage.setItem('quantumpexelsLastStr', self.searchStr);
        }

        let fieldsForRequest = '';
        let filterFields = self.element.querySelectorAll('.filter-field');
        for (let i=0;i<filterFields.length;i++) {
            if(filterFields[i].hasAttribute('data-disabled')) {
                continue;
            }

            fieldsForRequest += '&' + filterFields[i].getAttribute('data-name') + '=' + encodeURIComponent(filterFields[i].getAttribute('data-value'));
        }


        this.loadPage = true;

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumpexels.search&q=" + encodeURIComponent(str) + '&page=' + encodeURIComponent(page) + fieldsForRequest + '&v=' + QuantumUtils.randomInteger(1111111, 9999999))).done(function (response) {
            response = JSON.parse(response);
            self.currentPage = parseInt(page);
            self.totalPage = parseInt(response.totalPage);
            self.loadPage = false;

            let html = '';
            let container = QuantumPexelsElement.querySelector('.quantumpexels-module-search');

            if(page === 1) {
                container.innerHTML = '';
                self.masonry = new Masonry('.quantumpexels-module-search', {
                    itemSelector: '.grid-item',
                    percentPosition: true
                });
            }

            let maxLoaded = response.results.length;
            let currentLoaded = 0;

            if(response.results.length === 0) {
                if(page === 1) {
                    container.innerHTML = '';
                    let elem = document.createElement('div');
                    elem.setAttribute('class', 'grid-item');
                    elem.innerHTML = QuantumpexelsLang.notFound;
                    container.appendChild(elem);
                    self.masonry.appended(elem);
                } else {
                    self.loadPage = true;
                }

                return;
            }

            for(let i=0;i<response.results.length;i++) {

                let elem = document.createElement('div');
                elem.setAttribute('class', 'grid-item');
                elem.setAttribute('data-id', response.results[i]['id']);
                elem.setAttribute('data-tiny', response.results[i]['src']['tiny']);
                elem.setAttribute('data-landscape', response.results[i]['src']['landscape']);
                elem.setAttribute('data-portrait', response.results[i]['src']['portrait']);
                elem.setAttribute('data-small', response.results[i]['src']['small']);
                elem.setAttribute('data-medium', response.results[i]['src']['medium']);
                elem.setAttribute('data-large', response.results[i]['src']['large']);
                elem.setAttribute('data-large2x', response.results[i]['src']['large2x']);
                elem.setAttribute('data-original', response.results[i]['src']['original']);

                let metaWrap = document.createElement('div');
                metaWrap.setAttribute('class', 'pexels-meta-wrap');

                let meta = document.createElement('div');
                meta.setAttribute('class', 'pexels-meta');

                /*let like = document.createElement('div');
                like.setAttribute('class', 'pexels-like');
                like.innerHTML = "<img class='svg' src='/media/com_quantummanager/images/icons/action/favorite-heart-button.svg'></img>";
                like.innerHTML += "<span class='pexels-like-count'>" + response.results[i]['likes'] + "</span>";
                meta.appendChild(like);*/

                let user = document.createElement('div');
                user.setAttribute('class', 'pexels-user');
                //user.innerHTML = "<div class='pexels-user-avatar' style='background-image: url(" + response.photos[i]['profile_image']['medium'] + ")'></div>";
                user.innerHTML += "<a target='_blank' href='" + response.results[i]['photographer_url'] + "?utm_source=" + encodeURIComponent('Quantum Manager') + "&utm_medium=referral'><span class='pexels-user-name'>" + response.results[i]['photographer'] + "</span></a>";
                meta.appendChild(user);

                metaWrap.appendChild(meta);

                let image = document.createElement('img');
                image.setAttribute('src', response.results[i]['src']['medium']);
                image.onload = function() {
                    currentLoaded++;
                };

                elem.append(image);
                elem.append(metaWrap);

                container.appendChild(elem);
                self.masonry.appended(elem);

                QuantumUtils.replaceImgToSvg('.quantumpexels-module-search');

                elem.addEventListener('click', function (ev) {

                    if(ev.target.tagName === 'SPAN') {
                        return;
                    }

                    let element = this;
                    let fileDownload = element.getAttribute('data-original');
                    let filtersSize = self.element.querySelector('.filter-field[data-name=size]');
                    self.areaSave.style.display = 'block';

                    if(filtersSize !== null && filtersSize !== undefined) {
                        let selectSize = filtersSize.getAttribute('data-value');
                        if(element.getAttribute('data-' + selectSize) !== '') {
                            fileDownload = element.getAttribute('data-' + selectSize);
                        }
                    }

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumpexels.download&path=" + encodeURIComponent(Filemanager.data.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope)
                        + '&file=' + encodeURIComponent(fileDownload)
                        + '&id=' + encodeURIComponent(element.getAttribute('data-id'))
                    )).done(function (response) {
                        response = JSON.parse(response);

                        if(response.name !== undefined) {
                            self.filename = response.name;
                        }

                        QuantumPexelsElement.classList.remove('active');
                        self.areaSave.style.display = 'none';
                        Filemanager.events.trigger('pexelsComplete', Filemanager);
                    }).fail(function () {
                        QuantumPexelsElement.classList.remove('active');
                        self.areaSave.style.display = 'none';
                    });

                    ev.preventDefault();
                });

            }

            let intervalLayout = setInterval(function () {

                self.masonry.layout();

                if(currentLoaded === maxLoaded) {
                    self.masonry.layout();
                    clearInterval(intervalLayout)
                }
            }, 150);


            if(parseInt(response.totalPage) > 1 && (self.currentPage !== self.totalPage)) {
                self.pageWrap.classList.add('active');
            } else {
                self.pageWrap.classList.remove('active');
            }


        });
    }


    Filemanager.events.add(this, 'pexelsComplete', function (fm, el) {
        Filemanager.Quantumviewfiles.loadDirectory(null, function () {
            fm.Quantumviewfiles.scrollTopFilesCheck(Filemanager.Quantumpexels.filename);

            let filesAll = fm.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
            let find = false;
            let element;

            for(let i=0;i<filesAll.length;i++) {
                if (Filemanager.Quantumpexels.filename === filesAll[i].getAttribute('data-file')) {
                    fm.Quantumviewfiles.selectFile(filesAll[i], true);
                    find = true;
                }
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });


};

