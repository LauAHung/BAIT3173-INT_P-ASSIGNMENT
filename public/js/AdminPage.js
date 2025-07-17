  document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector('.sidebar');

    sidebar.addEventListener('mouseenter', () => {
      sidebar.classList.add('sidebar-expanded');
    });

    sidebar.addEventListener('mouseleave', () => {
      sidebar.classList.remove('sidebar-expanded');
    });
  });