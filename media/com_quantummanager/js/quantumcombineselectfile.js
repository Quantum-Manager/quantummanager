/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

document.addEventListener('DOMContentLoaded' ,function () {
    let quantumcombineselectfileAll = document.querySelectorAll('.quantumcombineselectfile');

    for(let i=0;i<quantumcombineselectfileAll.length;i++) {
        let buttonChange = quantumcombineselectfileAll[i].querySelector('.preview-file');
        let preveiwFile = quantumcombineselectfileAll[i].querySelector('.preview-file .image');
        let inputFile = quantumcombineselectfileAll[i].querySelector('.input-file');
        let fmIndex = parseInt(quantumcombineselectfileAll[i].getAttribute('data-index'));
        let paths = inputFile.value.split('/');
        let nameFile = paths.pop();
        let currPath = paths.join('/');
        let div = document.createElement('div');
        div.style.backgroundImage = 'url("/' + inputFile.value + '")';
        preveiwFile.innerHTML = '';
        preveiwFile.appendChild(div);

        setTimeout(function () {
            fmIndex = parseInt(quantumcombineselectfileAll[i].getAttribute('data-index'));
            QuantummanagerLists[fmIndex].Quantumviewfiles.loadDirectory(currPath);
        }, 300);

        buttonChange.addEventListener('click', function (ev) {
            let quantumcombineselectfile = this.closest('.quantummanager');
            let fmIndex = parseInt(quantumcombineselectfile.getAttribute('data-index'));
            let inputFile = quantumcombineselectfile.querySelector('.input-file');
            let paths = inputFile.value.split('/');
            let nameFile = paths.pop();
            quantumcombineselectfileAll[i].classList.add('change-file');

            setTimeout(function () {
                console.log(nameFile);
                QuantummanagerLists[fmIndex].Quantumviewfiles.scrollTopFilesCheck(nameFile);
            }, 400);

            ev.preventDefault();
        });
    }

    QuantumEventsDispatcher.add('clickFile', function (fm) {
        let inputFile = fm.element.querySelector('.input-file');
        let preveiwFile = fm.element.querySelector('.preview-file .image');
        let file = fm.Quantumviewfiles.file;
        let name = fm.Quantumviewfiles.file.querySelector('.file-name').innerHTML;
        let div = document.createElement('div');
        div.style.backgroundImage = 'url("/' + fm.Quantumviewfiles.path + '/' + name + '")';
        fm.Quantumviewfiles.file.querySelector('input').checked = false;
        inputFile.value = fm.Quantumviewfiles.path + '/' + name;
        preveiwFile.innerHTML = '';
        preveiwFile.appendChild(div);
        fm.element.classList.remove('change-file');
    });

});