
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
