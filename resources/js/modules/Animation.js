import Utility from './Utility.js';

export default class Animation extends Utility {

    constructor(element) {
        super();
        this.element = element;
        this.start = performance.now();
    }

    animate() {
        const elapsed = performance.now() - this.start;
        this.element.style.transform = `translateX(${elapsed * 0.5}px)`;
    }

    moveProgressBar(step){
        const currentWidth = this.currentElementWidth(this.element);
        this.element.style.width = `${currentWidth+step}%`;
    }

    moveProgressBarTo(width){
        this.element.style.width = `${width}%`;
    }

    fadeUpElement(element, pixels, duration = 0.6){
        setTimeout(() => {
            element.style.transition = `all ease-in ${duration}s`;
            element.style.transform = `translateY(${pixels}px)`;
            element.style.opacity = '0';
        }, 100);
    }

    fadeInUpElement(element, pixels, duration = 0.6){
        setTimeout(() => {
            element.style.transition = `all ease-in ${duration}s`;
            element.style.transform = `translateY(${pixels}px)`;
            element.style.opacity = '1';            
        }, 100);
    }

    fadeInLeftElement(element, pixels, duration = 0.6){
        setTimeout(() => {
            element.style.transition = `all ease-in ${duration}s`;
            element.style.transform = `translateX(${pixels}px)`;
            element.style.opacity = '1';            
        }, 100);
    }

    fadeOutLeftElement(element, pixels, duration = 0.6){
        setTimeout(() => {
            element.style.transition = `all ease-in ${duration}s`;
            element.style.transform = `translateX(${pixels}px)`;
            element.style.opacity = '0';
        }, 100);
    }

    fadeInElement(element){
        element.style.transition = 'opacity 0.6s';
        element.style.opacity = '1';
    }

    fadeDropdown(element) {
        element.style.transition = 'opacity 0.3s, max-height 0.3s';
        element.style.overflow = 'hidden';
        element.style.opacity = 0;
        element.style.maxHeight = '0px';
    
        if (element.hidden) {
            element.hidden = false;
            requestAnimationFrame(() => {
                element.style.opacity = 1;
                element.style.maxHeight = element.scrollHeight + 'px';
            });
        } else {
            element.style.opacity = 0;
            element.style.maxHeight = '0px';
            setTimeout(() => {
                element.hidden = true;
            }, 300);
        }
    }

    //move down the element
    moveElementDown(element, pixels = 200) {
        element.style.transition = 'transform 0.2s ease-in';
        element.style.transform = `translateY(${pixels}px)`;
    }

    //move up the element
    moveElementUp(element, pixels = 200) {
        element.style.transition = 'transform 0.2s ease-in';
        element.style.transform = `translateY(-${pixels}px)`;
    }

    //move left the element
    moveElementLeft(element, pixels = 200) {
        element.style.transition = 'transform 0.2s ease-in';
        element.style.transform = `translateX(-${pixels}px)`;
    }

    //move right the element
    moveElementRight(element, pixels = 200) {
        element.style.transition = 'transform 0.2s ease-in';
        element.style.transform = `translateX(${pixels}px)`;
    }

    autoHeightElement(element = undefined, pixels = undefined) {
        if (element) {
            this.element = element;
        }
    
        this.element.style.maxHeight = `${this.element.scrollHeight}px`;
        this.element.style.transition = 'max-height 0.6s ease-in';
    
        this.element.offsetHeight;

        setTimeout(() => {
            this.element.style.maxHeight = !pixels ? `${pixels}px` : `${this.element.scrollHeight}px`;    
        }, 100);
    }

    buttonLoader(btnElement){
        btnElement.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        btnElement.disabled = true;
    }

    normalizeButton(btnElement, originalText){
        btnElement.innerHTML = originalText;
        btnElement.disabled = false;
    }

    dragElement(container){
        let draggedItem = null;

        document.querySelectorAll('.draggable').forEach(item => {
            //Dragstart event
            item.addEventListener('dragstart', (e) => {
                draggedItem = e.currentTarget;
                setTimeout(() => {
                    item.style.opacity = '0.2';
                }, 0);
            });

            //Dragend event
            item.addEventListener('dragend', (e) => {
                setTimeout(() => {
                    draggedItem.style.opacity = '1';
                    draggedItem = null;    
                }, 0);
            });

            //Dragover event
            item.addEventListener('dragover', (e) => {
                e.preventDefault();
            });

            //Dragleave event
            item.addEventListener('dragleave', (e) => {
                item.classList.remove('over');
            });

            //Dragenter event
            item.addEventListener('dragenter', (e) => {
                e.preventDefault();
                item.classList.add('over');
            });

            //Drop event
            item.addEventListener('drop', (e) => {
                item.classList.remove('over');

                if(draggedItem !== item){
                    let allItems = [...container.querySelectorAll('.draggable')];
                    let draggedIndex = allItems.indexOf(draggedItem);
                    let droppedIndex = allItems.indexOf(item);

                    if (draggedIndex < droppedIndex) {
                        item.after(draggedItem);
                    } else {
                        item.before(draggedItem);
                    }
                }
            });
        });
    }
}