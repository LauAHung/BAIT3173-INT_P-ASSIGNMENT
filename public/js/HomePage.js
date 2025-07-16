
function handleTrainTypeChange() {
    var returnTrain = document.getElementById('return-train');
    var oneWayTrain = document.getElementById('one-way-train');

    var toggleSwitch = document.querySelector('.toggle-switch');

    if (returnTrain.checked) {
        toggleSwitch.style.transform = 'translateX(0)';
        returnTrain.nextElementSibling.style.color = 'aliceblue';
        oneWayTrain.nextElementSibling.style.color = 'rgba(88, 88, 88, 1)';
    } else if (oneWayTrain.checked) {
        toggleSwitch.style.transform = 'translateX(100%)';
        oneWayTrain.nextElementSibling.style.color = 'aliceblue';
        returnTrain.nextElementSibling.style.color = 'rgba(88, 88, 88, 1)';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const banners = document.querySelectorAll('.banner-block');
    const body = document.body;

    function setBodyBgFromBanner(banner) {
        const bg = banner.style.backgroundImage;

        body.style.backgroundImage = bg;
        body.style.backgroundSize = 'cover';
        body.style.backgroundPosition = 'center';
        body.style.transition = 'background 0.7s cubic-bezier(.68,-0.55,.27,1.55)';
    }

    function onScroll() {
        let found = false;
        banners.forEach(banner => {
            const rect = banner.getBoundingClientRect();
            if (rect.top < window.innerHeight/2 && rect.bottom > window.innerHeight/2 && !found) {
                setBodyBgFromBanner(banner);
                found = true;
            }
        });
        if (!found) {
            body.style.backgroundImage = '';
            body.style.background = 'linear-gradient(120deg, #181c22 0%, #23272f 100%)';
        }
    }

    window.addEventListener('scroll', onScroll);
    onScroll();
});


