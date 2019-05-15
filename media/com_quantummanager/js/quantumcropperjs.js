/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

window.Quantumcropperjs = function(Filemanager, QuantumCropperjsElement, options) {

    this.options = options;
    this.cropperjs = '';
    this.buttons = '';
    this.pathFile = '';
    this.nameFile = '';

    this.init = function () {
        let self = this;

        Filemanager.Quantumtoolbar.buttonAdd('cropperjsEdit', 'left', 'btn-edit btn-hide hidden-label', QuantumviewfilesLang.buttonEdit, 'quantummanager-icon-edit', {}, function (ev) {

            let image = document.createElement('img');
            let editor = QuantumCropperjsElement.querySelector('.editor');
            let nameFile = self.nameFile;
            let name = nameFile.split('.');
            let exs = name.pop().toLocaleLowerCase();

            if(['png', 'jpg', 'jpeg', 'gif', 'bmp'].indexOf(exs) === -1) {
                return;
            }

            image.setAttribute('src', '/' + Filemanager.data.path + '/' + nameFile + '?' + QuantumUtils.randomInteger(111111, 999999));
            editor.innerHTML = '';
            editor.append(image);
            self.cropperjs = new Cropper(image, {responsive: false});
            QuantumCropperjsElement.classList.add('active');
            QuantumCropperjsElement.querySelector('.quantumcropperjs-name-file').value = name;
            QuantumCropperjsElement.querySelector('.quantumcropperjs-name-exs').value = exs;

            Filemanager.Quantumtoolbar.trigger('buttonCropperjsEdit');
            ev.preventDefault();
        });

        QuantumCropperjsElement.querySelector('.btn-save').addEventListener('click', function (event) {
            let name = QuantumCropperjsElement.querySelector('.quantumcropperjs-name-file').value;
            let exs = QuantumCropperjsElement.querySelector('.quantumcropperjs-name-exs').value;
            let result = self.cropperjs.getCroppedCanvas();
            let blob = '';

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
                }, 
                function (response) {
                    console.log('fail');
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
        QuantumCropperjsElement.querySelector('.buttons-toggles').addEventListener('change', function (event) {
            let e = event || window.event;
            let target = e.target || e.srcElement;
            let cropBoxData;
            let canvasData;
            let isCheckbox;
            let isRadio;
            let image = QuantumCropperjsElement.querySelector('.editor img');

            console.log(self);

            if (!self.cropperjs) {
                return;
            }

            if (target.tagName.toLowerCase() === 'label') {
                target = target.querySelector('input');
            }

            isCheckbox = target.type === 'checkbox';
            isRadio = target.type === 'radio';

            if (isCheckbox || isRadio) {
                if (isCheckbox) {
                    options[target.name] = target.checked;
                    cropBoxData = cropper.getCropBoxData();
                    canvasData = cropper.getCanvasData();

                    options.ready = function () {
                        console.log('ready');
                        cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
                    };
                } else {
                    options[target.name] = target.value;
                    options.ready = function () {
                        console.log('ready');
                    };
                }
                self.cropperjs.destroy();
                self.cropperjs = new Cropper(image, options);
            }
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
        let nameFile = Filemanager.Quantumviewfiles.file.querySelector('.file-name').innerHTML;
        let exs = nameFile.split('.').pop().toLocaleLowerCase();
        el.nameFile = nameFile;

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

    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

    QuantumEventsDispatcher.add(this, 'uploadComplete', function (fm, el) {
        setTimeout(function () {
            if(Filemanager.Qantumupload.filesLists.length > 0) {
                let nameFile = Filemanager.Qantumupload.filesLists[0];
                let exs = nameFile.split('.').pop().toLocaleLowerCase();
                Filemanager.Quantumcropperjs.nameFile = nameFile;

                if(['png', 'jpg', 'jpeg'].indexOf(exs) === -1) {
                    return;
                }

                fm.Quantumtoolbar.buttonsList['cropperjsEdit'].classList.remove('btn-hide');
            }
        }, 400);
    });

};