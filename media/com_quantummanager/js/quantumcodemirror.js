/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

window.Quantumcodemirror = function(Filemanager, QuantumcodemirrorElement, options) {

    this.options = options;
    this.codemirror = '';
    this.buttons = '';

    this.init = function () {
        let self = this;

        Filemanager.Quantumtoolbar.buttonAdd('codemirrorEdit', 'center', 'file-actions', 'btn-edit btn-hide hidden-label', QuantumviewfilesLang.buttonEdit, 'quantummanager-icon-edit', {}, function (ev) {

            QuantumcodemirrorElement.classList.add('active');
            let image = document.createElement('img');
            let editor = QuantumcodemirrorElement.querySelector('.editor');
            let editorTextArea = document.createElement('textarea');
            editor.append(editorTextArea);
            self.codemirror = CodeMirror.fromTextArea(editorTextArea, {
                lineNumbers: true,
                indentWithTabs: true,
                mode:  "javascript",
                theme: "elegant"
            });

            Filemanager.Quantumtoolbar.trigger('buttonCropperjsEdit');
            ev.preventDefault();
        });


    };

    Filemanager.events.add(this, 'clickFile', function (fm, el) {
        let tmpCheck = Filemanager.Quantumviewfiles.file.querySelector('.import-files-check-file');
        let nameFile = Filemanager.Quantumviewfiles.file.querySelector('.file-name').innerHTML;
        let exs = nameFile.split('.').pop().toLocaleLowerCase();
        el.nameFile = nameFile;

        if(!tmpCheck.checked) {
            fm.Quantumtoolbar.buttonsList['codemirrorEdit'].classList.add('btn-hide');
            return;
        }

        console.log(exs);
        if(['txt', 'svg', 'css', 'js', 'less', 'sass', 'html'].indexOf(exs) === -1) {
            return;
        }

        fm.Quantumtoolbar.buttonsList['codemirrorEdit'].classList.remove('btn-hide');
    });


    this.trigger = function(event) {
        Filemanager.events.trigger(event, Filemanager);
    };

};