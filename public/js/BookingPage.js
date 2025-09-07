document.addEventListener('DOMContentLoaded', function() {
        const ongoingTab = document.getElementById('ongoing-tab');
        const pastTab = document.getElementById('past-tab');
        const ongoingContent = document.getElementById('ongoing-content');
        const pastContent = document.getElementById('past-content');
        const refundTab = document.getElementById('refunded-tab');
        const refundContent = document.getElementById('refunded-content');

        ongoingTab.addEventListener('click', function() {
            ongoingTab.classList.add('active');
            pastTab.classList.remove('active');
            refundTab.classList.remove('active');
            ongoingContent.style.display = '';
            pastContent.style.display = 'none';
            refundContent.style.display = 'none';
        });
        pastTab.addEventListener('click', function() {
            pastTab.classList.add('active');
            ongoingTab.classList.remove('active');
            refundTab.classList.remove('active');
            pastContent.style.display = '';
            ongoingContent.style.display = 'none';
            refundContent.style.display = 'none';
        });
        refundTab.addEventListener('click', function() {
            refundTab.classList.add('active');
            ongoingTab.classList.remove('active');
            pastTab.classList.remove('active');
            refundContent.style.display = '';
            ongoingContent.style.display = 'none';
            pastContent.style.display = 'none';
        });
    });