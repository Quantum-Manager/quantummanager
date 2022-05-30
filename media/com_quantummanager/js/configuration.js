/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

document.addEventListener('DOMContentLoaded' ,function () {
    let quantumAll = document.querySelectorAll('.quantummanager');

    let intervalLoadQM = setInterval(function() {
        if(window.QuantummanagerLists === undefined || window.QuantummanagerLists === null) {
            return;
        }

        clearInterval(intervalLoadQM);

        for (let i = 0; i < quantumAll.length; i++) {
            let fmIndex = parseInt(quantumAll[i].getAttribute('data-index'));
            fmIndex = parseInt(quantumAll[i].getAttribute('data-index'));
            QuantummanagerLists[fmIndex].Quantumtoolbar.buttonAdd('conf', 'right', 'file-other', 'hidden-label', QuantumtoolbarLang.buttonSettings , 'quantummanager-icon-settings', {}, function (ev) {
                location.href = '/administrator/index.php?option=com_config&view=component&component=com_quantummanager';
            });
        }
    }, 10);

});