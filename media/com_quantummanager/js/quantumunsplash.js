/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Quantumunsplash = function(Filemanager, QuantumUnsplashElement, options) {

    this.options = options;
    this.filename = '';
    this.currentPage = 0;
    this.totalPage = 0;
    this.searchStr = '';
    self.masnry = '';
    this.areaSave = QuantumUnsplashElement.querySelector('.quantumunsplash-save');
    this.inputSearch = QuantumUnsplashElement.querySelector('.quantumunsplash-module-header input');
    this.pageWrap = QuantumUnsplashElement.querySelector('.quantumunsplash-module-load-page');
    this.pageButton = QuantumUnsplashElement.querySelector('.quantumunsplash-module-load-page button');
    this.closeButton = QuantumUnsplashElement.querySelector('.quantumunsplash-module-close');

    this.init = function () {
        let self = this;

        self.areaSave.style.display = 'none';

        Filemanager.Quantumtoolbar.buttonAdd('unsplashSearch', 'right', 'file-other', 'btn-unsplash-search hidden-label', QuantumunsplashLang.button, 'quantummanager-icon-unsplash', {}, function (ev) {
            QuantumUnsplashElement.classList.add('active');
            let tmpSearchStr = '';

            /*if(localStorage !== undefined) {
                tmpSearchStr = localStorage.getItem('quantumunsplashLastStr');

                if(tmpSearchStr === null) {
                    tmpSearchStr = '';
                }
            }*/

            self.inputSearch.value = tmpSearchStr;
            self.inputSearch.focus();
            self.search(tmpSearchStr);
            ev.preventDefault();
        });

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
           localStorage.setItem('quantumunsplashLastStr', self.searchStr);
        }

        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.search&q=" + encodeURIComponent(str) + '&page=' + encodeURIComponent(page)).done(function (response) {
            response = JSON.parse(response);
            self.currentPage = parseInt(page);
            self.totalPage = parseInt(response.totalPage);

            let html = '';
            let container = QuantumUnsplashElement.querySelector('.quantumunsplash-module-search');

            if(page === 1) {
                container.innerHTML = '';
                self.masnry = new Masonry('.quantumunsplash-module-search', {
                    itemSelector: '.grid-item',
                    percentPosition: true
                });
            }

            let maxLoaded = response.results.length;
            let currentLoaded = 0;

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
                like.innerHTML = "<span class='quantummanager-icon quantummanager-icon-like-inverse'></span>";
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
                self.masnry.appended(elem);

                elem.addEventListener('click', function (ev) {

                    if(ev.target.tagName === 'SPAN') {
                        return;
                    }

                    let element = this;
                    self.filename = element.getAttribute('data-id') + '.jpg';

                    self.areaSave.style.display = 'block';

                    jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.downloadTrigger&id=" + encodeURIComponent(element.getAttribute('data-id')));

                    jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumunsplash.download&path=" + encodeURIComponent(Filemanager.data.path)
                        + '&file=' + encodeURIComponent(element.getAttribute('data-url'))
                        + '&id=' + encodeURIComponent(element.getAttribute('data-id'))
                    ).done(function (response) {
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
    }

};

