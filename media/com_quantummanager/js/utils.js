/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.QuantumUtils = {

    /**
     *
     * @param url
     * @param root
     * @returns {string}
     */
    getFullUrl: function (url, root) {
        let prefix = '';

        // its script
        if(url.indexOf('.php') !== -1) {
            if (root === null || root === undefined) {
                if (QuantumSettings.urlBase !== undefined) {
                    prefix = QuantumSettings.urlBase;
                }
            } else {
                if (QuantumSettings.urlFull !== undefined) {
                    prefix = QuantumSettings.urlFull;
                }
            }
        } else {
            // its assets
            prefix = QuantumSettings.urlMedia;
        }

        if(
            prefix.slice(-1) !== '/' &&
            url.slice(0, 1) !== '/'
        ) {
            return prefix + '/' + url;
        }

        return prefix + url;
    },

    /**
     *
     * @param typeRequest
     * @param url
     * @param data
     * @param headers
     * @param callbackSuccess
     * @param callbackFail
     */
    ajax: function (typeRequest, url, data, headers, callbackSuccess, callbackFail) {
        let xhr = new XMLHttpRequest();
        let formData = new FormData();

        for (let current in data) {
            formData.append(current, data[current]);
        }

        for (let current in headers) {
            xhr.setRequestHeader(current, headers[current]);
        }

        xhr.open(typeRequest, url, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (callbackSuccess !== null) {
                    callbackSuccess(xhr.response);
                }
            } else if (xhr.readyState == 4 && xhr.status != 200) {
                if (callbackFail !== null) {
                    callbackFail(xhr.response);
                }
            }
        };
        xhr.send(formData);
    },


    ajaxGet: function (url, data) {
        let self = this,
            request = new XMLHttpRequest();

        if (data !== undefined && data !== null) {
            url += '&' + Object.keys(data).map(function (key) {
                return key + '=' + data[key];
            }).join('&');
        }

        request.open('GET', url);
        return self.ajaxRequest(request);
    },


    ajaxPost: function (url, data) {
        let self = this,
            request = new XMLHttpRequest(),
            formData = new FormData();

        if (data !== undefined && data !== null) {
            for (let key in data) {
                formData.append(key, data[key]);
            }
        }


        request.open('POST', url);
        let ajax = self.ajaxRequest(request, false);
        request.send(formData);

        return ajax
    },


    ajaxRequest: function (request, send = true) {
        let ajax = new function () {
            return this;
        };
        ajax.request = request;
        ajax.request.onreadystatechange = function () {
            if (this.readyState === 4) {
                if (this.status === 200) {

                    // если возвращается html страница с содержанием form-login, то значит авторизации уже нет
                    // не по коду статусу определяется, потому что джумла не отдает правильный код http на авторизацию
                    if(
                        this.responseText.indexOf('<html') !== -1 &&
                        this.responseText.indexOf('form-login') !== -1
                    ) {
                        location.reload()
                    }

                    if (ajax.done !== undefined) {
                        ajax.done(this.responseText, this);
                    }
                } else {
                    if (ajax.fail !== undefined) {
                        ajax.fail(this);
                    }
                }

            }
        };

        if (send) {
            ajax.request.send();
        }

        let ajax_proxy = new Proxy(ajax, {
            get: function (target_original, prop, receiver) {
                let F = function (...args) {
                }
                return new Proxy(F, {
                    apply: function (target, thisArg, argumentsList) {
                        target_original[prop] = argumentsList[0];
                        return ajax_proxy;
                    }
                });
            },
            set(target, prop, val) {
                target[prop] = val;
                return true;
            }
        });

        return ajax_proxy;
    },


    /**
     *
     * @param url
     * @param dataGET
     * @param blob
     * @param headers
     * @param callbackSuccess
     * @param callbackFail
     */
    ajaxFile: function (url, dataGET, blob, headers, callbackSuccess, callbackFail) {
        let xhr = new XMLHttpRequest();
        let formData = new FormData();
        formData.append("file", blob);
        xhr.open('POST', url + '?' + this.serialize(dataGET), true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if (callbackSuccess !== null) {
                    callbackSuccess(xhr.response);
                }
            } else if (xhr.readyState == 4 && xhr.status !== 200) {
                if (callbackFail !== null) {
                    callbackFail(xhr.response);
                }
            }
        };
        xhr.send(formData);
    },

    /**
     *
     * @param obj
     * @returns {string}
     */
    serialize: function (obj) {
        let str = [];
        for (let p in obj) {
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        }
        return str.join("&");
    },

    /**
     *
     * @param dataURI
     * @returns {Blob}
     */
    dataURItoBlob: function (dataURI) {
        let byteString;
        if (dataURI.split(',')[0].indexOf('base64') >= 0) {
            byteString = atob(dataURI.split(',')[1]);
        } else {
            byteString = unescape(dataURI.split(',')[1]);
        }

        let mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
        let ia = new Uint8Array(byteString.length);
        for (let i = 0; i < byteString.length; i++) {
            ia[i] = byteString.charCodeAt(i);
        }

        return new Blob([ia], {type: mimeString});
    },

    /**
     *
     * @param min
     * @param max
     * @returns {number}
     */
    randomInteger: function (min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);
        rand = Math.round(rand);
        return rand;
    },

    /**
     *
     * @param elem
     * @param refElem
     * @returns {*}
     */
    insertAfter: function (elem, refElem) {
        let parent = refElem.parentNode;
        let next = refElem.nextSibling;
        if (next) {
            return parent.insertBefore(elem, next);
        } else {
            return parent.appendChild(elem);
        }
    },

    /**
     *
     * @param bytes
     * @returns {string}
     */
    bytesToSize: function (bytes) {
        bytes = parseInt(bytes);
        let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        if (bytes == 0) {
            return '0 Byte';
        }

        let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },

    /**
     *
     * @param unix_timestamp
     * @returns {string}
     */
    fromUnixTimeToDate: function (unix_timestamp) {
        unix_timestamp = parseInt(unix_timestamp);
        let date = new Date(unix_timestamp * 1000);
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let seconds = date.getSeconds();
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();

        if (hours < 10) {
            hours = "0" + hours;
        }

        if (minutes < 10) {
            minutes = "0" + minutes;
        }

        if (day < 10) {
            day = "0" + day;
        }

        if (month < 10) {
            month = "0" + month;
        }

        if (year < 10) {
            year = "0" + year;
        }

        let formattedTime = day + "-" + month + "-" + year + ' ' + hours + ':' + minutes;
        return formattedTime;
    },

    /**
     *
     * @param time
     * @returns {string}
     */
    toHHMMSS: function (time) {
        let sec_num = parseInt(time, 10);
        let hours = Math.floor(sec_num / 3600);
        let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        let seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours < 10) {
            hours = "0" + hours;
        }

        if (minutes < 10) {
            minutes = "0" + minutes;
        }

        if (seconds < 10) {
            seconds = "0" + seconds;
        }

        return hours + ':' + minutes + ':' + seconds;
    },

    /**
     *
     * @param text
     */
    fallbackCopyTextToClipboard: function (text) {
        let textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            let successful = document.execCommand('copy');
            let msg = successful ? 'successful' : 'unsuccessful';
        } catch (err) {
        }

        document.body.removeChild(textArea);
    },

    /**
     *
     * @param m
     * @param buttons
     */
    alert: function (m, buttons) {
        let alert = JSAlert.alert(m);

        if (typeof buttons === 'object') {
            for (let i = 0; i < buttons.length; i++) {
                alert.addButton(buttons[i].name).then(function () {
                    buttons[i].callback();
                });
            }
        }

        return alert;
    },

    /**
     *
     * @param q
     * @param callback
     */
    confirm: function (q, callback, callback_check) {
        JSAlert.confirm(q).then(function (result) {
            if (!result) {

                if(typeof callback_check === 'function') {
                    if(!callback_check()) {
                        return;
                    }
                } else {
                    return;
                }

            }

            callback(result);
        });
    },

    /**
     *
     * @param q
     * @param defaultValue
     * @param callback
     */
    prompt: function (q, defaultValue, callback) {
        JSAlert.prompt(q, defaultValue).then(function (result) {
            if (!result) {
                return;
            }
            callback(result);
        });
    },

    /**
     *
     * @param name
     * @param url
     * @returns {Window}
     */
    windowOpen: function (name, url) {
        let winSize = this.windowSize();
        let size = this.getPopUpSize();
        let centerWidth = (winSize.width - size.width) / 2;
        let centerHeight = (winSize.height - size.height) / 2;
        return window.open(url,
            name,
            'width=' + size.width +
            ',height=' + size.height +
            ',left=' + centerWidth +
            ',top=' + centerHeight
        );
    },

    windowSize: function () {
        let myWidth = 0,
            myHeight = 0,
            size = {width: 0, height: 0};
        if (typeof (window.innerWidth) == 'number') {
            myWidth = window.innerWidth;
            myHeight = window.innerHeight;
        } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            myWidth = document.documentElement.clientWidth;
            myHeight = document.documentElement.clientHeight;
        } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
            myWidth = document.body.clientWidth;
            myHeight = document.body.clientHeight;
        }
        size.width = myWidth;
        size.height = myHeight;

        return size;
    },

    /**
     *
     * @param options
     */
    modal: function (options) {

        if (options.fm === undefined) {
            return false;
        }

        if (options.classForModal === undefined) {
            options.classForModal = '';
        }

        if (options.header === undefined) {
            options.header = '';
        }

        if (options.body === undefined) {
            options.body = '';
        }

        if (options.footer === undefined) {
            options.footer = '';
        }

        if (options.close === undefined) {
            options.close = true;
        }

        let modal = this.createElement('div', {'class': 'quatummanagermodal-wrap ' + options.classForModal})
            .addChild('div', {'class': 'quatummanagermodal-container'});

        if (options.close) {
            modal = modal.add('button', {
                'class': 'qm-btn quatummanagermodal-close',
                'events': [
                    ['click', function (ev) {
                        this.closest('.quatummanagermodal-wrap').remove();
                    }]
                ]
            }, QuantumLang.close);
        }

        modal = modal.add('div', {'class': 'quatummanagermodal-header'}, options.header)
            .addChild('div', {'class': 'quatummanagermodal-body-wrap'})
            .add('div', {'class': 'quatummanagermodal-body'}, options.body)
            .getParent()
            .getParent();

        options.fm.element.append(modal.build());


        let modalClass = function (modal) {
            let self = this;
            self.modal = modal;
            self.modal_html = modal.build();

            this.show = function () {
                self.modal_html.classList.remove('quatummanagermodal-hide');
            }

            this.hide = function () {
                self.modal_html.classList.add('quatummanagermodal-hide');
            }

            this.destroy = function () {
                self.modal_html.remove();
            }

        }

        return (new modalClass(modal));
    },

    /**
     *
     * @param el
     * @returns {{width: number, height: number}}
     */
    getPopUpSize: function (el) {
        let size = this.windowSize();
        size.width = (size.width / 100 * 90);
        size.height = (size.height / 100 * 90);
        return size;
    },

    /**
     *
     * @param url
     */
    openInNewTab: function (url) {
        let win = window.open(url, '_blank');
        win.focus();
    },

    /**
     *
     * @param element
     */
    replaceImgToSvg: function (element) {
        let elements = document.querySelector(element).querySelectorAll('img.svg');
        for (let i = 0; i < elements.length; i++) {

            let imgID = elements[i].getAttribute('id'),
                imgClass = elements[i].getAttribute('class'),
                imgURL = elements[i].getAttribute('src');

            QuantumUtils.ajaxGet(imgURL).done(function (data) {
                let svg = document.createElement('div');
                svg.innerHTML = data.trim();
                svg = svg.querySelector('svg')

                if (typeof imgID !== 'undefined') {
                    svg.setAttribute('id', imgID);
                }

                if (typeof imgClass !== 'undefined') {
                    svg.setAttribute('class', imgClass + ' replaced-svg');
                }

                // Remove any invalid XML tags as per http://validator.w3.org
                svg.removeAttribute('xmlns:a');
                elements[i].replaceWith(svg);

            });


        }

    },

    compilePath: function (scope, path, callbackSuccess, callbackFail) {
        QuantumUtils.ajaxGet(QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(path) + '&scope=' + scope + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
            response = JSON.parse(response);

            if (response.path !== undefined) {
                if (typeof callbackSuccess === 'function') {
                    callbackSuccess(response, scope, path);
                }
            } else {
                if (typeof callbackFail === 'function') {
                    callbackFail(response, scope, path);
                }
            }

        });
    },

    /**
     *
     * @param options
     */
    notify: function (options) {

        if (window.Toastify === null || window.Toastify === undefined) {
            return;
        }

        let optionsMerge = {
            selector: '.quantummanager',
            // Text for notify
            text: '',
            // Duration notification
            duration: 3000,
            // On-click destination
            //destination: '',
            // Open destination in new window
            newWindow: false,
            // Show toast close icon
            close: true,
            // Toast position - top or bottom
            gravity: 'bottom',
            // Toast position - left, right, or center
            position: 'right',
            // Background color
            backgroundColor: "",
            // Avatar
            avatar: "",
            // Additional classes for the toast
            className: "",
            // Prevents dismissing of toast on hover
            stopOnFocus: true,
            callback: function () {},
        };

        if (options.fm !== undefined) {
            optionsMerge.selector = '.quantummanager[data-index="' + options.fm.id + '"]';
        }

        for (let k in options) {

            if(k === 'type') {
                optionsMerge.className += ' toastify-' + options[k];
                continue;
            }

            optionsMerge[k] = options[k];
        }

        let notify = Toastify(optionsMerge);
        notify.showToast();
        return notify;
    },

    /**
     * Создание вложенных DOM элементов
     *
     * @param tag
     * @param attr
     * @param innerHtml
     * @returns {{add: add, build: build, el: *, child: []}}
     */
    createElement: function (tag, attr, innerHtml) {
        let self = this;
        let element = document.createElement(tag);

        for (keyAttr in attr) {
            if (keyAttr === 'events') {
                let eventsLength = attr[keyAttr].length;
                for (let i = 0; i < eventsLength; i++) {
                    element.addEventListener(attr[keyAttr][i][0], attr[keyAttr][i][1]);
                }
                continue;
            }

            element.setAttribute(keyAttr, attr[keyAttr]);
        }

        if (innerHtml !== undefined && innerHtml !== null) {

            if (typeof innerHtml === 'function') {
                element.innerHTML = innerHtml();
            }

            if (typeof innerHtml === 'string') {
                element.innerHTML = innerHtml;
            }

            if (typeof innerHtml === 'object') {
                element.append(innerHtml);
            }

        }

        return {
            el: element,
            parent: undefined,
            child: [],
            getParent: function () {
                return this.parent;
            },
            setParent: function (parent) {
                this.parent = parent;
                return this;
            },
            add: function (tag, attr, innerHtml) {
                this.child.push(self.createElement(tag, attr, innerHtml).setParent(this));
                return this;
            },
            addChild: function (tag, attr, innerHtml) {
                this.child.push(self.createElement(tag, attr, innerHtml).setParent(this));
                return this.child[this.child.length - 1];
            },
            build: function () {
                let buildElement = this.el;

                if (this.child.length > 0) {

                    for (let i = 0; i < this.child.length; i++) {
                        buildElement.appendChild(this.child[i].build());
                    }

                    return buildElement;
                } else {
                    return buildElement;
                }

            },
        }
    },

    /**
     *
     * @param name
     * @returns {string}
     */
    getUrlParameter: function (name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        let results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    },

    /**
     *
     * @param text
     * @returns {string}
     */
    escapeHtml: function (text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    },

    /**
     *
     * @param text
     */
    copyInBuffer: function (text) {

        if (window.ClipboardJS === null || window.ClipboardJS === undefined) {
            return;
        }

        let button = document.createElement('button');
        button.setAttribute('data-clipboard-text', text);
        new ClipboardJS(button);
        button.click();
    },


    getPlatform: function () {
        let unknown = '-';

        // screen
        let screenSize = '';
        if (screen.width) {
            let width = (screen.width) ? screen.width : '';
            let height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }

        // browser
        let nVer = navigator.appVersion;
        let nAgt = navigator.userAgent;
        let browser = navigator.appName;
        let version = '' + parseFloat(navigator.appVersion);
        let majorVersion = parseInt(navigator.appVersion, 10);
        let nameOffset, verOffset, ix;

        // Opera
        if ((verOffset = nAgt.indexOf('Opera')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 6);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Opera Next
        if ((verOffset = nAgt.indexOf('OPR')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 4);
        }
        // Legacy Edge
        else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
            browser = 'Microsoft Legacy Edge';
            version = nAgt.substring(verOffset + 5);
        }
        // Edge (Chromium)
        else if ((verOffset = nAgt.indexOf('Edg')) != -1) {
            browser = 'Microsoft Edge';
            version = nAgt.substring(verOffset + 4);
        }
        // MSIE
        else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(verOffset + 5);
        }
        // Chrome
        else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
            browser = 'Chrome';
            version = nAgt.substring(verOffset + 7);
        }
        // Safari
        else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
            browser = 'Safari';
            version = nAgt.substring(verOffset + 7);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        // Firefox
        else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
            browser = 'Firefox';
            version = nAgt.substring(verOffset + 8);
        }
        // MSIE 11+
        else if (nAgt.indexOf('Trident/') != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(nAgt.indexOf('rv:') + 3);
        }
        // Other browsers
        else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
            browser = nAgt.substring(nameOffset, verOffset);
            version = nAgt.substring(verOffset + 1);
            if (browser.toLowerCase() == browser.toUpperCase()) {
                browser = navigator.appName;
            }
        }
        // trim the version string
        if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);

        majorVersion = parseInt('' + version, 10);
        if (isNaN(majorVersion)) {
            version = '' + parseFloat(navigator.appVersion);
            majorVersion = parseInt(navigator.appVersion, 10);
        }

        // mobile version
        let mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);

        // cookie
        let cookieEnabled = (navigator.cookieEnabled) ? true : false;

        if (typeof navigator.cookieEnabled == 'undefined' && !cookieEnabled) {
            document.cookie = 'testcookie';
            cookieEnabled = (document.cookie.indexOf('testcookie') != -1) ? true : false;
        }

        // system
        let os = unknown;
        let clientStrings = [
            {s: 'Windows 10', r: /(Windows 10.0|Windows NT 10.0)/},
            {s: 'Windows 8.1', r: /(Windows 8.1|Windows NT 6.3)/},
            {s: 'Windows 8', r: /(Windows 8|Windows NT 6.2)/},
            {s: 'Windows 7', r: /(Windows 7|Windows NT 6.1)/},
            {s: 'Windows Vista', r: /Windows NT 6.0/},
            {s: 'Windows Server 2003', r: /Windows NT 5.2/},
            {s: 'Windows XP', r: /(Windows NT 5.1|Windows XP)/},
            {s: 'Windows 2000', r: /(Windows NT 5.0|Windows 2000)/},
            {s: 'Windows ME', r: /(Win 9x 4.90|Windows ME)/},
            {s: 'Windows 98', r: /(Windows 98|Win98)/},
            {s: 'Windows 95', r: /(Windows 95|Win95|Windows_95)/},
            {s: 'Windows NT 4.0', r: /(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
            {s: 'Windows CE', r: /Windows CE/},
            {s: 'Windows 3.11', r: /Win16/},
            {s: 'Android', r: /Android/},
            {s: 'Open BSD', r: /OpenBSD/},
            {s: 'Sun OS', r: /SunOS/},
            {s: 'Chrome OS', r: /CrOS/},
            {s: 'Linux', r: /(Linux|X11(?!.*CrOS))/},
            {s: 'iOS', r: /(iPhone|iPad|iPod)/},
            {s: 'Mac OS', r: /Mac OS X/},
            {s: 'Mac OS', r: /(Mac OS|MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
            {s: 'QNX', r: /QNX/},
            {s: 'UNIX', r: /UNIX/},
            {s: 'BeOS', r: /BeOS/},
            {s: 'OS/2', r: /OS\/2/},
            {s: 'Search Bot', r: /(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
        ];
        for (let id in clientStrings) {
            let cs = clientStrings[id];
            if (cs.r.test(nAgt)) {
                os = cs.s;
                break;
            }
        }

        let osVersion = unknown;

        if (/Windows/.test(os)) {
            osVersion = /Windows (.*)/.exec(os)[1];
            os = 'Windows';
        }

        switch (os) {
            case 'Mac OS':
            case 'Mac OS X':
            case 'Android':
                osVersion = /(?:Android|Mac OS|Mac OS X|MacPPC|MacIntel|Mac_PowerPC|Macintosh) ([\.\_\d]+)/.exec(nAgt)[1];
                break;

            case 'iOS':
                osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
                break;
        }

        // flash (you'll need to include swfobject)
        /* script src="//ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js" */
        let flashVersion = 'no check';
        if (typeof swfobject != 'undefined') {
            let fv = swfobject.getFlashPlayerVersion();
            if (fv.major > 0) {
                flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
            } else {
                flashVersion = unknown;
            }
        }

        return {
            screen: screenSize,
            browser: browser,
            browserVersion: version,
            browserMajorVersion: majorVersion,
            mobile: mobile,
            os: os,
            osVersion: osVersion,
            cookies: cookieEnabled,
            flashVersion: flashVersion
        }
    },


    getOS: function() {
        return this.getPlatform().os;
    },


    /**
     *
     * @param event_name
     * @param el
     */
    triggerElementEvent: function (event_name, el) {
        let event;
        if (document.createEvent) {
            event = document.createEvent("HTMLEvents");
            event.initEvent(event_name, true, true);
            event.eventName = event_name;
            el.dispatchEvent(event);
        } else {
            event = document.createEventObject();
            event.eventName = event_name;
            event.eventType = event_name;
            el.fireEvent("on" + event.eventType, event);
        }
    },

    /**
     *
     * @param string
     * @param quoteStyle
     * @returns {void | string | *}
     */
    htmlspecialcharsDecode: function (string, quoteStyle) {
        let optTemp = 0,
            i = 0,
            noquotes = false;

        if (typeof quoteStyle === 'undefined') {
            quoteStyle = 2
        }

        string = string.toString()
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
        let OPTS = {
            'ENT_NOQUOTES': 0,
            'ENT_HTML_QUOTE_SINGLE': 1,
            'ENT_HTML_QUOTE_DOUBLE': 2,
            'ENT_COMPAT': 2,
            'ENT_QUOTES': 3,
            'ENT_IGNORE': 4
        };

        if (quoteStyle === 0) {
            noquotes = true;
        }

        if (typeof quoteStyle !== 'number') {
            // Allow for a single string or an array of string flags
            quoteStyle = [].concat(quoteStyle)
            for (i = 0; i < quoteStyle.length; i++) {
                // Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
                if (OPTS[quoteStyle[i]] === 0) {
                    noquotes = true
                } else if (OPTS[quoteStyle[i]]) {
                    optTemp = optTemp | OPTS[quoteStyle[i]]
                }
            }
            quoteStyle = optTemp
        }
        if (quoteStyle & OPTS.ENT_HTML_QUOTE_SINGLE) {
            string = string.replace(/&#0*39;/g, "'")
        }
        if (!noquotes) {
            string = string.replace(/&quot;/g, '"')
        }
        string = string.replace(/&amp;/g, '&')

        return string
    }

};