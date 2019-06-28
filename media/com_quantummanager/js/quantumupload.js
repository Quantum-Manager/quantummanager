/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Qantumupload = function(Filemanager, UploadElement, options) {

    this.dropAreaAll = [];
    this.dropAreaAll = [];
    this.inputFileAll = [];
    this.dropArea = '';
    this.progressBar = '';
    this.uploadProgress = [];
    this.countFiles = 0;
    this.uploadI = 0;
    this.filesLists = [];
    this.errorsWrap = '';
    this.errorsHtml = '';
    this.maxsize = options.maxsize;
    this.exs = '';

    this.init = function () {

        let self = this;
        let interval;
        this.progressBar = UploadElement.querySelector(".progress-bar");
        this.inputPath = UploadElement.querySelector(".pathElem");
        this.inputFile = UploadElement.querySelector(".fileElem");
        this.errorsWrap = UploadElement.querySelector(".upload-errors");
        this.dropArea = UploadElement.closest(".quantummanager");
        this.inputFileAll = UploadElement.querySelectorAll(".fileElem");
        this.exs = ['jpg', 'jpeg', 'png', 'gif'];
        this.path = options.directory;
        Filemanager.element.setAttribute('data-drag-drop-title', QuantumuploadLang.dragDrop);

        ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.dropArea.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
            document.body.addEventListener(eventName, function (e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

        ;['dragover', 'dragenter'].forEach(eventName => {
            this.dropArea.addEventListener(eventName, function (e) {
                self.dropArea.classList.add('highlight');
                clearTimeout(interval);
                interval = setTimeout(function () {
                    self.dropArea.classList.remove('highlight');
                }, 400);
            }, false);
        });

        /*;['drop'].forEach(eventName => {
            this.dropArea.addEventListener(eventName, function (e) {
               self.dropArea.classList.remove('highlight');
            }, false);
        });*/

        this.dropArea.addEventListener('drop', function (e) {
            let dt = e.dataTransfer;
            let files = dt.files;
            self.uploadFiles(files);
        }, false);

        for (let i = 0; i < this.inputFileAll.length; i++) {
            this.inputFileAll[i].addEventListener('change', function () {
                self.uploadFiles(this.files);
            }, false);
        }

        let closeError = self.errorsWrap.querySelector('.upload-errors-close');
        closeError.addEventListener('click', function () {
            self.errorsWrap.style.display = "none";
        });


    };


    this.selectFiles = function () {
        for (let i = 0; i < this.inputFileAll.length; i++) {
            this.inputFileAll[i].click();
            break;
        }
    };

    this.initializeProgress = function(numFiles) {
        this.progressBar.style.display = "block";
        this.progressBar.value = 0;
        this.uploadProgress = [];
        this.countFiles = numFiles;

        for (let i = numFiles; i > 0; i--) {
            this.uploadProgress.push(0);
        }
    };

    this.updateProgress = function(fileNumber, percent) {
        this.uploadProgress[fileNumber] = percent;
        let total = this.uploadProgress.reduce((tot, curr) => tot + curr, 0) / this.uploadProgress.length;
        this.progressBar.value = total;
    };

    this.uploadFiles = function(files) {
        files = [...files];
        this.initializeProgress(files.length);
        this.errorsHtml = '';
        this.uploadI = [];
        this.filesLists = [];
        for (let i=0;i<files.length;i++) {

            let file = files[i];

            if((file.size  / 1024 / 1024) > this.maxsize) {

                /*vex.dialog.alert({
                    unsafeMessage: '<b>Файл ' + file.name + ' не должен превышать ' + maxsize + ' мегабайта.</b>'
                });*/

                this.countFiles--;

                if(this.countFiles === 0) {
                    this.progressBar.style.display = "none";
                }

                return false;
            }

            let currExs = file.name.split('.');

            if(currExs.length === 1) {

                /*vex.dialog.alert({
                    unsafeMessage: '<b>Файл ' + file.name + ' должен иметь расширение.</b>'
                });*/

                this.countFiles--;
                return false;
            }


            let self = this;
            let fm = Filemanager;
            let url = "/administrator/index.php?option=com_quantummanager&task=quantumupload.upload&path=" + encodeURIComponent(Filemanager.data.path);
            let xhr = new XMLHttpRequest();
            let formData = new FormData();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener("progress", function (e) {
                self.updateProgress(i, (e.loaded * 100.0 / e.total) || 100);
            });

            xhr.addEventListener('readystatechange', function (e) {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let response = JSON.parse(xhr.response);

                    if(response.name !== undefined) {
                        self.filesLists.push(response.name);
                    }

                    if(response.error !== undefined) {
                        self.errorsHtml += '<div>' + file.name + ': ' + response.error + '</div>';
                    }

                    self.updateProgress(i, 100);
                    self.uploadI.push((i + 1));

                    if(self.countFiles === self.uploadI.length) {
                        self.progressBar.style.display = "none";

                        self.trigger('uploadComplete');

                        if(self.errorsHtml !== '') {
                            self.errorsWrap.querySelector('div').innerHTML =  self.errorsHtml;
                            self.errorsWrap.style.display = "block";
                        }

                    }

                }
                else if (xhr.readyState == 4 && xhr.status != 200) {

                    self.uploadI.push((i + 1));

                    if(self.countFiles === self.uploadI.length) {
                        self.progressBar.style.display = "none";

                        if(self.errorsHtml !== '') {
                            self.errorsWrap.innerHTML =  self.errorsHtml;
                            self.errorsWrap.style.display = "block";
                        }

                    }

                }


            });

            formData.append('file', file);
            xhr.send(formData);
        }

    };

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

    Filemanager.events.add(this, 'updatePath', function (fm, el) {
        el.path = fm.data.path;
    });


};

