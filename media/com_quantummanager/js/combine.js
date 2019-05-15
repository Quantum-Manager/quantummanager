/**
 * @package    quantummanager
 *
 * @author     Cymbal <cymbal@delo-design.ru>
 * @copyright  Copyright (C) 2019 "Delo Design". All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       https://delo-design.ru
 */

document.addEventListener('DOMContentLoaded' ,function () {

    setTimeout(function () {
        for(let i=0;i<QuantummanagerLists.length;i++) {
            togglePositions(QuantummanagerLists[i]);
        }
    }, 300);
    
    function togglePositions(fm) {
        let leftToggle = fm.element.querySelector('.quantummanager-left-toggle');
        let rightToggle = fm.element.querySelector('.quantummanager-right-toggle');
        let leftPosition = fm.element.querySelector('.quantummanager-left');
        let rightPosition = fm.element.querySelector('.quantummanager-right');

        if(leftToggle !== null) {
            leftToggle.addEventListener('click', function () {
                if(leftPosition.classList.contains('open')) {
                    leftToggle.style.left = 0;
                    leftPosition.classList.remove('open');
                } else {
                    leftToggle.style.left = leftPosition.offsetWidth + 'px';
                    leftPosition.classList.add('open');
                }
            });
        }

        if(rightToggle !== null) {
            rightToggle.addEventListener('click', function () {
                if(rightPosition.classList.contains('open')) {
                    rightToggle.style.left = 0;
                    rightPosition.classList.remove('open');
                } else {
                    rightToggle.style.left = (-1 * rightPosition.offsetWidth) + 'px';
                    rightPosition.classList.add('open');
                }
            });
        }

    }

});