document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector('.sidebar');
  sidebar.addEventListener('mouseenter', () => {
    sidebar.classList.add('sidebar-expanded');
  });
  sidebar.addEventListener('mouseleave', () => {
    sidebar.classList.remove('sidebar-expanded');
  });
});

document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('theme-toggle-btn');
    const icon = btn.querySelector('i');
    const mainLayout = document.querySelector('.main-layout');
    btn.addEventListener('click', function() {
        document.body.classList.toggle('light-mode');
        if (mainLayout) mainLayout.classList.toggle('light-mode');
        if(document.body.classList.contains('light-mode')) {
            icon.classList.add('active');
            icon.classList.remove('fa-lightbulb');
            icon.classList.add('fa-regular', 'fa-lightbulb');
        } else {
            icon.classList.remove('active');
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-lightbulb');
        }
    });
});