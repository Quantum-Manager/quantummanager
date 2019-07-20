/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Quantumcropperjs = function(Filemanager, QuantumCropperjsElement, options) {

    let self = this;
    this.options = options;
    this.cropperjs = '';
    this.buttons = '';
    this.pathFile = '';
    this.file = '';
    this.nameFile = '';
    this.areaSave = QuantumCropperjsElement.querySelector('.cropper-save');
    this.ImageWidthValue = QuantumCropperjsElement.querySelector('.image-width-value');
    this.ImageHeightValue = QuantumCropperjsElement.querySelector('.image-height-value');
    this.CropWidthValue = QuantumCropperjsElement.querySelector('.crop-width-value');
    this.CropHeightValue = QuantumCropperjsElement.querySelector('.crop-height-value');
    this.canvasSource;
    this.canvasSourceCtx;
    this.image;
    this.imageChange = document.createElement('img');
    this.checkImageResize = QuantumCropperjsElement.querySelector('.image-width-height-ratio');
    this.editorCropperJS = QuantumCropperjsElement.querySelector('.editor .cropperjs');
    this.currentImage;
    this.changeCropperJS = function () {
        let canvasData = self.cropperjs.getCanvasData();
        let canvasCropData = self.cropperjs.getCropBoxData();
        let widthContainer = self.editorCropperJS.offsetWidth;
        let heightContainer = self.editorCropperJS.offsetHeight;
        let canvasWidth = canvasData.width;
        let canvasWidthCrop = canvasData.width;
        let canvasHeightCrop = canvasData.height;
        let canvasHeight = canvasData.height;
        let resizeCanvas = false;
        let scaleFactorCrop = self.defaultCropperJSOptions.aspectRatio;

        if(canvasWidth === undefined && canvasHeight === undefined) {
            return;
        }

        if(canvasWidth > self.currentImage.width) {
            let scaleFactor = self.currentImage.width / canvasWidth;
            canvasWidth = self.currentImage.width;
            canvasHeight = canvasHeight * scaleFactor;
            resizeCanvas = true;
        }

        if(canvasHeight > self.currentImage.height) {
            let scaleFactor = self.currentImage.height / canvasHeight;
            canvasHeight = self.currentImage.width;
            canvasWidth = canvasWidth * scaleFactor;
            resizeCanvas = true;
        }

        self.ImageWidthValue.value = Math.round(self.currentImage.width);
        self.ImageHeightValue.value = Math.round(self.currentImage.height);
        self.canvasSource = document.createElement('canvas');
        self.canvasSource.width = self.ImageWidthValue.value;
        self.canvasSource.height = self.ImageHeightValue.value;
        self.canvasSourceCtx = self.canvasSource.getContext('2d');
        self.canvasSourceCtx.drawImage(self.currentImage, 0, 0, self.canvasSource.width, self.canvasSource.height);

        if(resizeCanvas) {
            let left = self.editorCropperJS.offsetWidth / 2 - canvasWidth / 2;
            let top = self.editorCropperJS.offsetHeight / 2 - canvasHeight / 2;
            canvasData.left = left;
            canvasData.top = top;
            canvasData.width = canvasWidth;
            canvasData.height = canvasHeight;
            canvasCropData.left = left;
            canvasCropData.top = top;
            canvasCropData.width = canvasWidth;
            canvasCropData.height = canvasHeight;
            self.cropperjs.setCropBoxData(canvasCropData);
            self.cropperjs.setCanvasData(canvasData);
        }
    };
    this.defaultCropperJSOptions =  {
        responsive: false,
        viewMode: 1,
        background: true,
        aspectRatio: NaN,
        autoCropArea: 1,
        ready: function(event) {
            self.changeCropperJS();
        },
        crop: function(event) {
            self.CropWidthValue.value = Math.round(parseFloat(event.detail.width));
            self.CropHeightValue.value = Math.round(parseFloat(event.detail.height));
            self.CropWidthValue.setAttribute('data-old', parseFloat(event.detail.width));
            self.CropHeightValue.setAttribute('data-old', parseFloat(event.detail.height));
        }
    };

    this.init = function () {
        let self = this;
        self.areaSave.style.display = 'none';

        self.ImageWidthValue.addEventListener('change', function () {
            let editor = QuantumCropperjsElement.querySelector('.editor .cropperjs');
            let width = parseInt(this.value);
            let height = self.ImageHeightValue.value;
            let scaleFactor = 1;
            this.value = width;

            if(self.checkImageResize.checked) {
                scaleFactor = width / self.image.width;
                height = self.image.height * scaleFactor;
            }

            self.canvasSourceCtx.clearRect(0, 0, self.canvasSource.width, self.canvasSource.height);
            self.canvasSource.width = width;
            self.canvasSource.height = height;
            self.canvasSourceCtx.drawImage(self.image, 0, 0, width, height);
            self.imageChange.setAttribute('src', self.canvasSourceCtx.canvas.toDataURL());
            self.cropperjs.destroy();
            editor.innerHTML = '';
            self.currentImage = self.imageChange;
            editor.appendChild(self.imageChange);
            self.cropperjs = new Cropper(self.imageChange, self.defaultCropperJSOptions);
        });
        self.ImageHeightValue.addEventListener('change', function () {
            let editor = QuantumCropperjsElement.querySelector('.editor .cropperjs');
            let width = self.ImageWidthValue.value;
            let height = parseInt(this.value);
            let scaleFactor = 1;
            this.value = width;

            if(self.checkImageResize.checked) {
                scaleFactor = height / self.image.height;
                width = self.image.width * scaleFactor;
            }

            self.canvasSourceCtx.clearRect(0, 0, self.canvasSource.width, self.canvasSource.height);
            self.canvasSource.width = width;
            self.canvasSource.height = height;
            self.canvasSourceCtx.drawImage(self.image, 0, 0, width, height);
            self.imageChange.setAttribute('src', self.canvasSourceCtx.canvas.toDataURL());
            self.cropperjs.destroy();
            editor.innerHTML = '';
            self.currentImage = self.imageChange;
            editor.appendChild(self.imageChange);
            self.cropperjs = new Cropper(self.imageChange, self.defaultCropperJSOptions);
            self.changeCropperJS();
        });

        Filemanager.Quantumtoolbar.buttonAdd('cropperjsEdit', 'center', 'file-actions', 'btn-edit btn-hide hidden-label', QuantumviewfilesLang.buttonEdit, 'quantummanager-icon-edit', {}, function (ev) {
            self.startCropperjs();
            Filemanager.Quantumtoolbar.trigger('buttonCropperjsEdit');
            ev.preventDefault();
        });

        QuantumCropperjsElement.querySelector('.btn-save').addEventListener('click', function (event) {
            let name = QuantumCropperjsElement.querySelector('.quantumcropperjs-name-file').value;
            let exs = QuantumCropperjsElement.querySelector('.quantumcropperjs-name-exs').value;
            let result = self.cropperjs.getCroppedCanvas();
            let blob = '';

            if(result === null) {
                return;
            }

            self.areaSave.style.display = 'block';

            if(exs === 'jpg' || exs === 'jpeg') {
                blob = result.toDataURL("image/jpeg", 1);
            }

            if(exs === 'png') {
                blob = result.toDataURL("image/png");
            }

            if(exs === 'webp') {
                blob = result.toDataURL("image/webp", 1);
            }

            QuantumUtils.ajaxFile('/administrator/index.php', {
                    'option': 'com_quantummanager',
                    'task': 'quantumconverter.save',
                    'path': Filemanager.data.path,
                    'name': name,
                    'exs': exs
                },
                QuantumUtils.dataURItoBlob(blob),
                {},
                function (response) {
                    Filemanager.events.trigger('reloadPaths', Filemanager);
                    self.cropperjs.destroy();
                    QuantumCropperjsElement.classList.remove('active');
                    self.areaSave.style.display = 'none';
                }, 
                function (response) {
                    self.areaSave.style.display = 'none';
                }
            );

            event.preventDefault();
        });

        QuantumCropperjsElement.querySelector('.btn-close').addEventListener('click', function (event) {
            self.cropperjs.destroy();
            QuantumCropperjsElement.classList.remove('active');
            event.preventDefault();
        });

        QuantumCropperjsElement.querySelector('.buttons-methods').addEventListener('click', function (event) {
            let e = event || window.event;
            let target = e.target || e.srcElement;
            let result;
            let cropped;
            let input;
            let data;

            if (!self.cropperjs) {
                return;
            }

            while (target !== this) {
                if (target.getAttribute('data-method')) {
                    break;
                }

                target = target.parentNode;
            }

            if (target === this || target.disabled || target.className.indexOf('disabled') > -1) {
                return;
            }

            data = {
                method: target.getAttribute('data-method'),
                target: target.getAttribute('data-target'),
                option: target.getAttribute('data-option') || undefined,
                secondOption: target.getAttribute('data-second-option') || undefined
            };

            cropped = self.cropperjs;

            if (data.method) {
                if (typeof data.target !== 'undefined') {
                    input = document.querySelector(data.target);

                    if (!target.hasAttribute('data-option') && data.target && input) {
                        try {
                            data.option = JSON.parse(input.value);
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                switch (data.method) {
                    case 'rotate':
                        if (cropped && options.viewMode > 0) {
                            self.cropperjs.clear();
                        }

                        break;

                    case 'getCroppedCanvas':
                        try {
                            data.option = JSON.parse(data.option);
                        } catch (e) {
                            console.log(e.message);
                        }

                        if (uploadedImageType === 'image/jpeg') {
                            if (!data.option) {
                                data.option = {};
                            }

                            data.option.fillColor = '#fff';
                        }

                        break;
                }

                result = self.cropperjs[data.method](data.option, data.secondOption);

                switch (data.method) {
                    case 'rotate':
                        if (cropped && options.viewMode > 0) {
                            self.cropperjs.crop();
                        }

                        break;

                    case 'scaleX':
                    case 'scaleY':
                        target.setAttribute('data-option', -data.option);
                        break;

                    case 'destroy':
                        self.cropperjs = null;

                        if (uploadedImageURL) {
                            URL.revokeObjectURL(uploadedImageURL);
                            uploadedImageURL = '';
                            image.src = originalImageURL;
                        }

                        break;
                }

                if (typeof result === 'object' && result !== self.cropperjs && input) {
                    try {
                        input.value = JSON.stringify(result);
                    } catch (e) {
                        console.log(e.message);
                    }
                }
            }
        });

        QuantumCropperjsElement.querySelector('.change-ratio').addEventListener('change', function (event) {
            if (!self.cropperjs) {
                return;
            }

            self.defaultCropperJSOptions.aspectRatio = parseFloat(this.value);
            self.cropperjs.destroy();
            self.cropperjs = new Cropper(self.currentImage, self.defaultCropperJSOptions);
        });
    };

    this.startCropperjs = function () {
        let image = document.createElement('img');
        let editor = QuantumCropperjsElement.querySelector('.editor .cropperjs');
        let fileSource;
        let exs;
        let name;

        if(self.file === '') {
            fileSource = self.nameFile;
            name = self.nameFile.split('.');
            exs = name.pop();
            name = name.join('.');
        } else {
            fileSource = self.file.getAttribute('data-file');
            exs = self.file.getAttribute('data-exs');
            name = self.file.getAttribute('data-name');
        }

        if(['png', 'jpg', 'jpeg'].indexOf(exs) === -1) {
            return;
        }

        jQuery.get("/administrator/index.php?option=com_quantummanager&task=quantumviewfiles.getParsePath&path=" + encodeURIComponent(Filemanager.data.path) + '&v=' + QuantumUtils.randomInteger(111111, 999999)).done(function (response) {
            response = JSON.parse(response);

            if(response.path === undefined) {
                return;
            }

            image.setAttribute('src', '/' + response.path + '/' + fileSource + '?' + QuantumUtils.randomInteger(111111, 999999));
            self.image = image;
            editor.innerHTML = '';
            self.currentImage = image;
            editor.append(image);
            self.cropperjs = new Cropper(image, self.defaultCropperJSOptions);
            QuantumCropperjsElement.classList.add('active');
            QuantumCropperjsElement.querySelector('.quantumcropperjs-name-file').value = name;
            QuantumCropperjsElement.querySelector('.quantumcropperjs-name-exs').value = exs;
        });

    };

    this.trim = function (c) {
        let ctx = c.getContext('2d'),
            copy = document.createElement('canvas').getContext('2d'),
            pixels = ctx.getImageData(0, 0, c.width, c.height),
            l = pixels.data.length,
            i,
            bound = {
                top: null,
                left: null,
                right: null,
                bottom: null
            },
            x, y;

        for (i = 0; i < l; i += 4) {
            if (pixels.data[i+3] !== 0) {
                x = (i / 4) % c.width;
                y = ~~((i / 4) / c.width);

                if (bound.top === null) {
                    bound.top = y;
                }

                if (bound.left === null) {
                    bound.left = x;
                } else if (x < bound.left) {
                    bound.left = x;
                }

                if (bound.right === null) {
                    bound.right = x;
                } else if (bound.right < x) {
                    bound.right = x;
                }

                if (bound.bottom === null) {
                    bound.bottom = y;
                } else if (bound.bottom < y) {
                    bound.bottom = y;
                }
            }
        }

        let trimHeight = bound.bottom - bound.top,
            trimWidth = bound.right - bound.left,
            trimmed = ctx.getImageData(bound.left, bound.top, trimWidth, trimHeight);

        copy.canvas.width = trimWidth;
        copy.canvas.height = trimHeight;
        copy.putImageData(trimmed, 0, 0);

        if((c.width - copy.canvas.width < 4) && (c.height - copy.canvas.height < 3)) {
            return c;
        } else {
            return copy.canvas;
        }
    };

    Filemanager.events.add(this, 'clickFile', function (fm, el) {
        let tmpCheck = Filemanager.Quantumviewfiles.file.querySelector('.import-files-check-file');
        let file = Filemanager.Quantumviewfiles.file;
        let exs = file.getAttribute('data-exs').toLocaleLowerCase();
        el.file = file;

        if(!tmpCheck.checked) {
            fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.add('btn-hide');
            return;
        }

        if(['png', 'jpg', 'jpeg'].indexOf(exs) === -1) {
            return;
        }

        fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.remove('btn-hide');
    });

    Filemanager.events.add(this, 'updatePath', function (fm, el, target) {
        fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.add('btn-hide');
    });

    Filemanager.events.add(this, 'buttonViewfilesDelete', function (fm, el, target) {
        fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.add('btn-hide');
    });

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

    QuantumEventsDispatcher.add(this, 'uploadComplete', function (fm, el) {
        setTimeout(function () {
            if(Filemanager.Qantumupload.filesLists.length > 0) {
                let nameFile = Filemanager.Qantumupload.filesLists[0];
                let exs = nameFile.split('.').pop().toLocaleLowerCase();
                Filemanager.Quantumcropperjs.nameFile = nameFile;
                Filemanager.Quantumcropperjs.file = '';

                if(['png', 'jpg', 'jpeg'].indexOf(exs) === -1) {
                    return;
                }

                fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.remove('btn-hide');
            }
        }, 400);
    });

    QuantumEventsDispatcher.add(this, 'addContextMenuFile', function (fm, el) {
        return [
            {
                fileExs: ['png', 'jpg', 'jpeg'],
                type: 'normal',
                label: QuantumviewfilesLang.buttonEdit,
                tip: '',
                icon: '/media/com_quantummanager/images/contextmenu/edit.svg',
                onClick: function() {
                    let file = Filemanager.Quantumviewfiles.fileContext;
                    let exs = file.getAttribute('data-exs').toLocaleLowerCase();
                    Filemanager.Quantumcropperjs.file = file;
                    Filemanager.Quantumcropperjs.startCropperjs();
                }
            }
        ];
    });

};