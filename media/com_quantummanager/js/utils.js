/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.QuantumUtils = {

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

    serialize: function(obj) {
        let str = [];
        for (let p in obj) {
            if (obj.hasOwnProperty(p)) {
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            }
        }
        return str.join("&");
    },

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

    randomInteger: function (min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);
        rand = Math.round(rand);
        return rand;
    },

    insertAfter: function (elem, refElem) {
        let parent = refElem.parentNode;
        let next = refElem.nextSibling;
        if (next) {
            return parent.insertBefore(elem, next);
        } else {
            return parent.appendChild(elem);
        }
    },

    bytesToSize: function(bytes) {
        bytes = parseInt(bytes);
        let sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        if (bytes == 0) {
            return '0 Byte';
        }

        let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },

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

    confirm: function(q, callback) {
        JSAlert.confirm(q).then(function(result) {
            if (!result) {
                return;
            }
            callback(result);
        });
    },

    prompt: function (q, defaultValue, callback) {
        JSAlert.prompt(q, defaultValue).then(function(result) {
            if (!result) {
                return;
            }
            callback(result);
        });
    },

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

    getPopUpSize: function(el) {
        let size = this.windowSize();
        size.width = (size.width/100*90);
        size.height = (size.height/100*90);
        return size;
    },

    openInNewTab: function(url) {
        let win = window.open(url, '_blank');
        win.focus();
    },

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
    }

};