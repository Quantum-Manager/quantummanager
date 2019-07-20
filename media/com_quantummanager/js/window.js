/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

document.addEventListener('DOMContentLoaded' ,function () {
    let quantumAll = document.querySelectorAll('.quantummanager');

    for (let i = 0; i < quantumAll.length; i++) {
        let fmIndex = parseInt(quantumAll[i].getAttribute('data-index'));
        setTimeout(function () {
            fmIndex = parseInt(quantumAll[i].getAttribute('data-index'));

            QuantummanagerLists[fmIndex].Quantumtoolbar.buttonAdd('windowClose', 'right', 'window-actions', 'btn-close hidden-label', QuantumwindowLang.buttonClose, 'quantummanager-icon-close', {}, function (ev) {
                location.href = '/administrator/index.php?option=com_quantummanager';
            });

        }, 300);
    }
});