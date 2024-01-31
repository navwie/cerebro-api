import './bootstrap';



const switchInterface = () => {
    fetch('/switch-interface', {
        method: 'GET'
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            console.error('Switch interface error');
        }
    })
    .catch(error => console.error('Request error:', error));
}

window.switchInterface = switchInterface;