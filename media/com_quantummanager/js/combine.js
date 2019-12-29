/**
 * @package    quantummanager
 * @author     Dmitry Tsymbal <cymbal@delo-design.ru>
 * @copyright  Copyright © 2019 Delo Design & NorrNext. All rights reserved.
 * @license    GNU General Public License version 3 or later; see license.txt
 * @link       https://www.norrnext.com
 */

let loadQuantum = setInterval(function () {

    if(QuantumEventsDispatcher === undefined) {
        return;
    } else {
        QuantumEventsDispatcher.add('afterMainInit', function (fm) {

            togglePositions(fm);

            let width = window.innerWidth;
            if(width === 0) {
                if(parent.document !== undefined && parent.document.body.clientWidth > 0) {
                    width = parent.document.body.clientWidth;
                }
            }

            if(width > 800) {
                let splitSizesContainer = [25, 75];

                if(localStorage !== undefined) {
                    if(localStorage.getItem('quantummanagerSplitSizesC') !== null) {
                        splitSizesContainer = JSON.parse(localStorage.getItem('quantummanagerSplitSizesC'));
                    }
                }

                Split(['.quantummanager-left', '.quantummanager-center'], {
                    sizes: splitSizesContainer,
                    elementStyle: (dimension, size, gutterSize) => ({
                        'flex-basis': `calc(${size}% - ${gutterSize}px)`,
                    }),
                    gutterStyle: (dimension, gutterSize) => ({
                        'flex-basis':  `${gutterSize}px`,
                    }),
                    onDragEnd: function(sizes) {
                        localStorage.setItem('quantummanagerSplitSizesC', JSON.stringify(sizes))
                    },
                });
            }


            let splitSizesContainerToolbar = [25, 60];

            if(localStorage !== undefined) {
                if(localStorage.getItem('quantummanagerSplitSizesT') !== null) {
                    splitSizesContainerToolbar = JSON.parse(localStorage.getItem('quantummanagerSplitSizesT'));
                }
            }

            Split(['.quantumtoolbar-module .left', '.quantumtoolbar-module .center',], {
                sizes: splitSizesContainerToolbar,
                elementStyle: (dimension, size, gutterSize) => ({
                    'flex-basis': `calc(${size}% - ${gutterSize}px)`,
                }),
                gutterStyle: (dimension, gutterSize) => ({
                    'flex-basis':  `${gutterSize}px`,
                }),
                onDragEnd: function(sizes) {
                    localStorage.setItem('quantummanagerSplitSizesT', JSON.stringify(sizes))
                },
            });

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
        console.log(QuantumEventsDispatcher);
        clearInterval(loadQuantum);
    }



}, 1);
