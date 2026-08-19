function existElement(idElement) {
    return document.getElementById(idElement) !== null;
}

function handleProfile(){
    const profilePicInput = document.getElementById('profile-pic-input');
    const profilePic = document.getElementById('profile-pic');

    profilePicInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                profilePic.src = e.target.result;
            }

            reader.readAsDataURL(file);
        }
    });
}

function toggleinputsShow(){
    const container = document.getElementById('profile-details');
    container.addEventListener('click', (e) => {
        if(e.target.classList.contains('edit-button')){
            const inputsContainer = e.target.closest('div').nextElementSibling;
            const elements = Array.from(inputsContainer.querySelectorAll('input, select, span'));
            elements.forEach((element) => {
                element.classList.toggle('opacity')
            });
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    if(existElement('profile-details')){
        toggleinputsShow();
        handleProfile();
    }
});
