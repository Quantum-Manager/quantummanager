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
    getFullUrl: function(url, root) {
        let prefix = '';

        if(root === null || root === undefined) {
            if(QuantumSettings.urlBase !== undefined) {
                prefix = QuantumSettings.urlBase;
            }
        }
        else {
            if(QuantumSettings.urlFull !== undefined) {
                prefix = QuantumSettings.urlFull;
            }
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

        for(let current in data) {
            formData.append(current, data[current]);
        }

        for(let current in headers) {
            xhr.setRequestHeader(current, headers[current]);
        }

        xhr.open(typeRequest, url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(callbackSuccess !== null) {
                    callbackSuccess(xhr.response);
                }
            }
            else if (xhr.readyState == 4 && xhr.status != 200) {
                if(callbackFail !== null) {
                    callbackFail(xhr.response);
                }
            }
        };
        xhr.send(formData);
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
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                if(callbackSuccess !== null) {
                    callbackSuccess(xhr.response);
                }
            }
            else if (xhr.readyState == 4 && xhr.status !== 200) {
                if(callbackFail !== null) {
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
    serialize: function(obj) {
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
    dataURItoBlob: function(dataURI) {
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

        return new Blob([ia], {type:mimeString});
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
    bytesToSize: function(bytes) {
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
    fromUnixTimeToDate: function(unix_timestamp) {
        unix_timestamp = parseInt(unix_timestamp);
        let date = new Date(unix_timestamp * 1000);
        let hours = date.getHours();
        let minutes = date.getMinutes();
        let seconds = date.getSeconds();
        let day = date.getDate();
        let month = date.getMonth() + 1;
        let year = date.getFullYear();

        if(hours < 10) {
            hours = "0" + hours;
        }

        if(minutes < 10) {
            minutes = "0" + minutes;
        }

        if(day < 10) {
            day = "0" + day;
        }

        if(month < 10) {
            month = "0" + month;
        }

        if(year < 10) {
            year = "0" + year;
        }

        let formattedTime = day + "-" +  month + "-" +  year + ' ' + hours + ':' + minutes;
        return formattedTime;
    },

    /**
     *
     * @param time
     * @returns {string}
     */
    toHHMMSS: function (time) {
        let sec_num = parseInt(time, 10);
        let hours   = Math.floor(sec_num / 3600);
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
    fallbackCopyTextToClipboard: function(text) {
        let textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            let successful = document.execCommand('copy');
            let msg = successful ? 'successful' : 'unsuccessful';
            console.log('Fallback: Copying text command was ' + msg);
        } catch (err) {
            console.error('Fallback: Oops, unable to copy', err);
        }

        document.body.removeChild(textArea);
    },

    /**
     *
     * @param m
     * @param buttons
     */
    alert: function(m, buttons) {
        let alert = JSAlert.alert(m);

        if(typeof buttons === 'object') {
            for(let i=0;i<buttons.length;i++) {
                alert.addButton(buttons[i].name).then(function() {
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
    confirm: function(q, callback) {
        JSAlert.confirm(q).then(function(result) {
            if (!result) {
                return;
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
        JSAlert.prompt(q, defaultValue).then(function(result) {
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

    windowSize: function() {
        let myWidth = 0,
            myHeight = 0,
            size = { width: 0, height: 0 };
        if (typeof(window.innerWidth) == 'number') {
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
     * @param fm
     * @param header
     * @param body
     * @param footer
     * @param classForModal
     */
    modal: function(fm, header, body, footer, classForModal) {
        if(classForModal === null) {
            classForModal = '';
        }

        let modal = this.createElement('div', {'class': 'quatummanagermodal-wrap ' + classForModal})
            .addChild('div', {'class': 'quatummanagermodal-container'})
                .add('button', {
                    'class': 'btn quatummanagermodal-close',
                    'events': [
                        ['click', function (ev) {
                            this.closest('.quatummanagermodal-wrap').remove();
                        }]
                    ]}, QuantumLang.close)
                .add('div', {'class': 'quatummanagermodal-header'}, header)
                .addChild('div', {'class': 'quatummanagermodal-body-wrap'})
                    .add('div', {'class': 'quatummanagermodal-body'}, body)
                    .getParent()
                .getParent();
        fm.element.append(modal.build());
    },

    /**
     *
     * @param el
     * @returns {{width: number, height: number}}
     */
    getPopUpSize: function(el) {
        let size = this.windowSize();
        size.width = (size.width/100*90);
        size.height = (size.height/100*90);
        return size;
    },

    /**
     *
     * @param url
     */
    openInNewTab: function(url) {
        let win = window.open(url, '_blank');
        win.focus();
    },

    /**
     *
     * @param element
     */
    replaceImgToSvg: function(element) {
        jQuery(element + ' img.svg').each(function(){
            let $img = jQuery(this);
            let imgID = $img.attr('id');
            let imgClass = $img.attr('class');
            let imgURL = $img.attr('src');

            jQuery.get(imgURL, function(data) {
                // Get the SVG tag, ignore the rest
                var $svg = jQuery(data).find('svg');

                // Add replaced image's ID to the new SVG
                if(typeof imgID !== 'undefined') {
                    $svg = $svg.attr('id', imgID);
                }
                // Add replaced image's classes to the new SVG
                if(typeof imgClass !== 'undefined') {
                    $svg = $svg.attr('class', imgClass+' replaced-svg');
                }

                // Remove any invalid XML tags as per http://validator.w3.org
                $svg = $svg.removeAttr('xmlns:a');

                // Replace image with new SVG
                $img.replaceWith($svg);

            }, 'xml');

        });
    },

    compilePath: function(scope, path, callbackSuccess, callbackFail) {
        jQuery.get(QuantumUtils.getFullUrl("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(path) + '&scope=' + scope + '&v=' + QuantumUtils.randomInteger(111111, 999999))).done(function (response) {
            response = JSON.parse(response);

            if(response.path !== undefined) {
                if(typeof callbackSuccess === 'function') {
                    callbackSuccess(response, scope, path);
                }
            } else {
                if(typeof callbackFail === 'function') {
                    callbackFail(response, scope, path);
                }
            }

        });
    },

    /**
     *
     * @param options
     */
    notify: function(options) {

        if(window.Toastify === null || window.Toastify === undefined) {
            return;
        }

        let optionsMerge = {
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
            backgroundColor: "linear-gradient(135deg, #78abde, #5477f5)",
            // Avatar
            avatar: "",
            // Additional classes for the toast
            classes: "",
            // Prevents dismissing of toast on hover
            stopOnFocus: true,
            callback: function () {},
        };

        if(options.fm !== undefined) {
            optionsMerge.selector = '.quantummanager[data-index="' + options.fm.id + '"]';
        }

        for(let k in options) {
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
    createElement: function(tag, attr, innerHtml) {
        let self = this;
        let element = document.createElement(tag);

        for(keyAttr in attr) {
            if(keyAttr === 'events') {
                let eventsLength = attr[keyAttr].length;
                for(let i=0;i<eventsLength;i++) {
                    element.addEventListener(attr[keyAttr][i][0], attr[keyAttr][i][1]);
                }
                continue;
            }

            element.setAttribute(keyAttr, attr[keyAttr]);
        }

        if(innerHtml !== undefined && innerHtml !== null) {

            if(typeof innerHtml === 'function') {
                element.innerHTML = innerHtml();
            }

            if(typeof innerHtml === 'string') {
                element.innerHTML = innerHtml;
            }

            if(typeof innerHtml === 'object') {
                element.append(innerHtml);
            }

        }

        return {
            el: element,
            parent: undefined,
            child: [],
            getParent: function() {
                return this.parent;
            },
            setParent: function(parent) {
                this.parent = parent;
                return this;
            },
            add: function (tag, attr, innerHtml) {
                this.child.push(self.createElement(tag, attr, innerHtml).setParent(this));
                return this;
            },
            addChild: function (tag, attr, innerHtml) {
                this.child.push(self.createElement(tag, attr, innerHtml).setParent(this));
                return this.child[ this.child.length - 1];
            },
            build: function () {
                var buildElement = this.el;

                if(this.child.length > 0) {

                    for(var i=0;i<this.child.length;i++) {
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
    copyInBuffer: function(text) {

        if(window.ClipboardJS === null || window.ClipboardJS === undefined) {
            return;
        }

        let button = document.createElement('button');
        button.setAttribute('data-clipboard-text', text);
        new ClipboardJS(button);
        button.click();
    },

    /**
     *
     * @param event_name
     * @param el
     */
    triggerElementEvent: function(event_name, el) {
        let event;
        if(document.createEvent) {
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