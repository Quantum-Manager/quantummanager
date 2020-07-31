/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumunsplash = function(Filemanager, QuantumUnsplashElement, options) {

    this.options = options;
    this.filename = '';
    this.currentPage = 0;
    this.totalPage = 0;
    this.searchStr = '';
    this.masonry = '';
    this.loadPage = false;
    this.searchWrap = QuantumUnsplashElement.querySelector('.quantumunsplash-module-container-search-wrap .quantumunsplash-module-container-search');
    this.searchGrid = QuantumUnsplashElement.querySelector('.quantumunsplash-module-container-search-wrap .quantumunsplash-module-container-search .quantumunsplash-module-search');
    this.areaSave = QuantumUnsplashElement.querySelector('.quantumunsplash-save');
    this.inputSearch = QuantumUnsplashElement.querySelector('.quantumunsplash-module-header input');
    this.pageWrap = QuantumUnsplashElement.querySelector('.quantumunsplash-module-load-page');
    this.pageButton = QuantumUnsplashElement.querySelector('.quantumunsplash-module-load-page button');
    this.closeButton = QuantumUnsplashElement.querySelector('.quantumunsplash-module-close');

    this.init = function () {
        let self = this;

        self.areaSave.style.display = 'none';

        let buttonPhotostock = Filemanager.Quantumtoolbar.buttonAdd(
            'photostock',
            'center',
            'file-other',
            'btn-more',
            QuantumunsplashLang.photostock,
            '',
            {},
            function (ev) {}).parentElement;

        Filemanager.Quantumtoolbar.buttonAdd(
            'unsplashSearch',
            'right',
            'file-other',
            'btn-unsplash-search hidden-label',
            QuantumunsplashLang.button,
            'quantummanager-icon-unsplash-short',
            {},
            function (ev) {
                QuantumUnsplashElement.classList.add('active');

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
            QuantumUnsplashElement.classList.remove('active');
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
           localStorage.setItem('quantumunsplashLastStr', self.searchStr);
        }

        this.loadPage = true;

        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.search&q=" + encodeURIComponent(str) + '&page=' + encodeURIComponent(page))).done(function (response) {
            response = JSON.parse(response);
            self.currentPage = parseInt(page);
            self.totalPage = parseInt(response.totalPage);
            self.loadPage = false;

            let html = '';
            let container = QuantumUnsplashElement.querySelector('.quantumunsplash-module-search');

            if(page === 1) {
                container.innerHTML = '';
                self.masonry = new Masonry('.quantumunsplash-module-search', {
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
                    elem.innerHTML = QuantumunsplashLang.notFound;
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
                elem.setAttribute('data-url', response.results[i]['urls']['raw']);
                elem.setAttribute('data-id', response.results[i]['id']);

                let metaWrap = document.createElement('div');
                metaWrap.setAttribute('class', 'unsplash-meta-wrap');

                let meta = document.createElement('div');
                meta.setAttribute('class', 'unsplash-meta');

                let like = document.createElement('div');
                like.setAttribute('class', 'unsplash-like');
                like.innerHTML = "<img class='svg' src='/media/com_quantummanager/images/icons/action/favorite-heart-button.svg'></img>";
                like.innerHTML += "<span class='unsplash-like-count'>" + response.results[i]['likes'] + "</span>";
                meta.appendChild(like);

                let user = document.createElement('div');
                user.setAttribute('class', 'unsplash-user');
                user.innerHTML = "<div class='unsplash-user-avatar' style='background-image: url(" + response.results[i]['user']['profile_image']['medium'] + ")'></div>";
                user.innerHTML += "<a target='_blank' href='https://unsplash.com/@" + response.results[i]['user']['username'] + "?utm_source=" + encodeURIComponent('Quantum Manager') + "&utm_medium=referral'><span class='unsplash-user-name'>" + response.results[i]['user']['last_name'] + " " + response.results[i]['user']['first_name'] + "</span></a>";
                meta.appendChild(user);

                metaWrap.appendChild(meta);

                let image = document.createElement('img');
                image.setAttribute('src', response.results[i]['urls']['small']);
                image.onload = function() {
                    currentLoaded++;
                };

                elem.append(image);
                elem.append(metaWrap);

                container.appendChild(elem);
                self.masonry.appended(elem);

                QuantumUtils.replaceImgToSvg('.quantumunsplash-module-search');

                elem.addEventListener('click', function (ev) {

                    if(ev.target.tagName === 'SPAN') {
                        return;
                    }

                    let element = this;
                    self.areaSave.style.display = 'block';

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.downloadTrigger&id=" + encodeURIComponent(element.getAttribute('data-id'))));

                    jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.download&path=" + encodeURIComponent(Filemanager.data.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope)
                        + '&file=' + encodeURIComponent(element.getAttribute('data-url'))
                        + '&id=' + encodeURIComponent(element.getAttribute('data-id'))
                    )).done(function (response) {
                        response = JSON.parse(response);

                        if(response.name !== undefined) {
                            self.filename = response.name;
                        }

                        QuantumUnsplashElement.classList.remove('active');
                        self.areaSave.style.display = 'none';
                        Filemanager.events.trigger('unsplashComplete', Filemanager);
                    }).fail(function () {
                        QuantumUnsplashElement.classList.remove('active');
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


    Filemanager.events.add(this, 'unsplashComplete', function (fm, el) {
        Filemanager.Quantumviewfiles.loadDirectory(null, function () {
            fm.Quantumviewfiles.scrollTopFilesCheck(Filemanager.Quantumunsplash.filename);

            let filesAll = fm.Quantumviewfiles.element.querySelectorAll('.field-list-files .file-item');
            let find = false;
            let element;

            for(let i=0;i<filesAll.length;i++) {
                if (Filemanager.Quantumunsplash.filename === filesAll[i].getAttribute('data-file')) {
                    fm.Quantumviewfiles.selectFile(filesAll[i], true);
                    find = true;
                }
            }

            fm.Quantumviewfiles.initBreadcrumbs(fm.Quantumviewfiles.buildBreadcrumbs);

        });
    });


};

