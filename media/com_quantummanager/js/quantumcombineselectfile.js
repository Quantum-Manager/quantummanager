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
            fmIndex = parseInt(quantumcombineselectfileAll[i].querySelector('.quantummanager').getAttribute('data-index'));
            QuantummanagerLists[fmIndex].Quantumviewfiles.loadDirectory(currPath);
            QuantummanagerLists[fmIndex].element.style.display = 'none';
        }, 300);

        buttonChange.addEventListener('click', function (ev) {
            let quantumcombineselectfile = this.closest('.quantumcombineselectfile');
            let fmIndex = parseInt(quantumcombineselectfile.querySelector('.quantummanager').getAttribute('data-index'));
            let inputFile = quantumcombineselectfile.querySelector('.input-file');
            let paths = inputFile.value.split('/');
            let nameFile = paths.pop();
            quantumcombineselectfileAll[i].classList.add('change-file');
            quantumcombineselectfile.querySelector('.quantummanager').style.display = 'block';

            setTimeout(function () {
                QuantummanagerLists[fmIndex].Quantumviewfiles.scrollTopFilesCheck(nameFile);
            }, 400);

            ev.preventDefault();
        });
    }

    QuantumEventsDispatcher.add('clickFile', function (fm) {
        let quantumcombineselectfile = fm.element.closest('.quantumcombineselectfile');
        let inputFile = quantumcombineselectfile.querySelector('.input-file');
        let preveiwFile = quantumcombineselectfile.querySelector('.preview-file .image');
        let file = fm.Quantumviewfiles.file;
        let name = fm.Quantumviewfiles.file.querySelector('.file-name').innerHTML;
        let div = document.createElement('div');
        div.style.backgroundImage = 'url("/' + fm.Quantumviewfiles.path + '/' + name + '")';
        fm.Quantumviewfiles.file.querySelector('input').checked = false;
        inputFile.value = fm.Quantumviewfiles.path + '/' + name;
        preveiwFile.innerHTML = '';
        preveiwFile.appendChild(div);
        quantumcombineselectfile.classList.remove('change-file');
        fm.element.style.display = 'none';
    });

});