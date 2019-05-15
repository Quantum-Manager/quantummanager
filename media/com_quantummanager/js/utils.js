/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
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
        for (var p in obj) {
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
    }

};