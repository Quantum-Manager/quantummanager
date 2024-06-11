/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Qantumupload = function (Filemanager, UploadElement, options) {

    let self = this;
    this.options = options;
    this.dropAreaAll = [];
    this.inputFileAll = [];
    this.dropArea = '';
    this.dropAreaInput = '';
    this.progressBar = '';
    this.uploadProgress = [];
    this.countFiles = 0;
    this.uploadI = 0;
    this.filesLists = [];
    this.errors = [];
    this.maxsize = options.maxsize;
    this.maxsizeServer = parseInt(options.maxsizeServer);
    this.exs = '';

    this.init = function () {

        let self = this;
        let interval;
        this.progressBar = UploadElement.querySelector(".progress-bar");
        this.inputPath = UploadElement.querySelector(".pathElem");
        this.inputFile = UploadElement.querySelector(".fileElem");
        this.errorsWrap = UploadElement.querySelector(".upload-errors");
        this.dropArea = UploadElement.closest(".quantummanager");
        this.dropAreaInput = UploadElement.querySelector('.drop-area');
        this.inputFileAll = UploadElement.querySelectorAll(".fileElem");
        this.exs = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        this.path = options.directory;
        this.scope = options.scope;
        Filemanager.element.setAttribute('data-drag-drop-title', QuantumuploadLang.dragDrop);

        if (
            (Filemanager.data.scope === undefined ||
                Filemanager.data.scope === '') &&
            (this.scope !== undefined && this.scope !== null)
        ) {
            Filemanager.data.scope = this.scope;
        }

        if (
            (Filemanager.data.path === undefined ||
                Filemanager.data.path === '') &&
            (this.path !== undefined && this.path !== null)

        ) {
            Filemanager.data.path = this.path;
        }

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

        this.dropAreaInput.addEventListener('click', function (ev) {
            if (ev.target.tagName.toLowerCase() !== 'label') {
                for (let i = 0; i < self.inputFileAll.length; i++) {
                    self.inputFileAll[i].click();
                    break;
                }
            }
        });

    };


    this.selectFiles = function () {
        for (let i = 0; i < this.inputFileAll.length; i++) {
            this.inputFileAll[i].click();
            break;
        }
    };

    this.initializeProgress = function (numFiles) {
        this.progressBar.style.display = "block";
        this.progressBar.value = 0;
        this.uploadProgress = [];
        this.countFiles = numFiles;

        for (let i = numFiles; i > 0; i--) {
            this.uploadProgress.push(0);
        }
    };

    this.updateProgress = function (fileNumber, percent) {
        this.uploadProgress[fileNumber] = percent;
        let total = this.uploadProgress.reduce((tot, curr) => tot + curr, 0) / this.uploadProgress.length;
        this.progressBar.value = total;
    };

    this.uploadFiles = function (files) {

        if (files === null || files.length === 0) {
            return;
        }

        files = [...files];
        this.initializeProgress(files.length);
        this.errors = [];
        this.uploadI = [];
        this.filesLists = [];
        for (let i = 0; i < files.length; i++) {

            let file = files[i];

            if (file.size > this.maxsizeServer) {
                QuantumUtils.notify({
                    type: 'danger',
                    text: QuantumuploadLang.file + file.name + QuantumuploadLang.maxsize + (Math.round(this.maxsizeServer / 1024 / 1024)) + QuantumuploadLang.megabyte
                });

                this.countFiles--;

                if (this.countFiles === 0) {
                    this.progressBar.style.display = "none";
                }

                continue;
            }

            if ((file.size / 1024 / 1024) > this.maxsize) {
                QuantumUtils.notify({
                    type: 'danger',
                    text: QuantumuploadLang.file + file.name + QuantumuploadLang.maxsize + this.maxsize + QuantumuploadLang.megabyte
                });

                this.countFiles--;

                if (this.countFiles === 0) {
                    this.progressBar.style.display = "none";
                }

                continue;
            }

            let currExs = file.name.split('.');

            if (currExs.length === 1) {
                QuantumUtils.alert(QuantumuploadLang.file + file.name + QuantumuploadLang.exs);
                this.countFiles--;

                continue;
            }


            let self = this;
            let fm = Filemanager;
            let url = QuantumUtils.getFullUrl("index.php?option=com_quantummanager&task=quantumupload.upload&path=" + encodeURIComponent(Filemanager.data.path) + "&scope=" + encodeURIComponent(Filemanager.data.scope));
            let xhr = new XMLHttpRequest();
            let formData = new FormData();
            xhr.open('POST', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener("progress", function (e) {
                self.updateProgress(i, (e.loaded * 100.0 / e.total) || 100);
            });

            xhr.addEventListener('readystatechange', function (e) {
                if (xhr.readyState == 4 && xhr.status == 200) {

                    try {
                        let response = JSON.parse(xhr.response);

                        if (response.name !== undefined) {
                            self.filesLists.push(response.name);
                        }

                        if (response.error !== undefined) {
                            self.errors.push(file.name + ': ' + QuantumUtils.htmlspecialcharsDecode(response.error, 'ENT_QUOTES'));
                        }

                    } catch (e) {
                    }


                    self.updateProgress(i, 100);
                    self.uploadI.push((i + 1));

                    if (self.countFiles === self.uploadI.length) {
                        self.progressBar.style.display = "none";

                        self.trigger('uploadComplete');

                        if (self.errors.length > 0) {
                            for (let k in self.errors) {
                                QuantumUtils.notify({
                                    type: 'danger',
                                    text: self.errors[k]
                                });
                            }
                        }

                        self.trigger('uploadAfter');

                    }

                } else if (xhr.readyState === 4 && xhr.status !== 200) {

                    self.uploadI.push((i + 1));

                    if (self.countFiles === self.uploadI.length) {
                        self.progressBar.style.display = "none";

                        if (self.errors.length > 0) {
                            for (let k in self.errors) {
                                QuantumUtils.notify({
                                    type: 'danger',
                                    text: self.errors[k]
                                });
                            }
                        }

                        self.trigger('uploadAfter');

                    }

                }


            });

            formData.append('file', file);
            xhr.send(formData);
        }

    };

    this.trigger = function (event) {
        Filemanager.events.trigger(event, Filemanager);
    };

    Filemanager.events.add(this, 'updatePath', function (fm, el) {
        el.path = fm.data.path;
    });

    Filemanager.events.add(this, 'uploadAfter', function (fm, el) {
        for (let i = 0; i < fm.Qantumupload.inputFileAll.length; i++) {
            fm.Qantumupload.inputFileAll[i].value = '';
        }
    });

};

