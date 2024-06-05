// JavaScript for modal
function showModal(message) {
    var modal = document.getElementById("myModal");
    var span = document.getElementsByClassName("close")[0];
    document.getElementById("modal-message").textContent = message;
    modal.style.display = "block";
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

// Check if there's a message in the URL parameters
document.addEventListener('DOMContentLoaded', (event) => {
    const params = new URLSearchParams(window.location.search);
    if (params.has('message')) {
        showModal(params.get('message'));
    }
});
