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
    }

};