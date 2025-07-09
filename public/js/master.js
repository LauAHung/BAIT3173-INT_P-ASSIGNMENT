document.addEventListener("DOMContentLoaded", () => {
    const header = document.querySelector("header");
    const navAnimate = document.querySelector(".nav-animate");
    const links = header.querySelectorAll("nav a");

    window.addEventListener("scroll", () => {
        const isSticky = window.scrollY > header.offsetHeight;
        header.classList.toggle("sticking", isSticky);
    });

    links.forEach(link => {
        link.addEventListener("mouseover", event => {
            const target = event.target;
            const rect = target.getBoundingClientRect();
            const headerRect = header.getBoundingClientRect();

            const top = rect.top - headerRect.top;
            const left = rect.left - headerRect.left;
            const width = rect.width;
            const height = rect.height;

            navAnimate.style.opacity = "1";
            navAnimate.style.width = `${width}px`;
            navAnimate.style.height = `${height}px`;
            navAnimate.style.transform = `translate(${left}px, ${top}px)`;
        });

        link.addEventListener("mouseleave", () => {
            navAnimate.style.opacity = "0";
        });
    });
});
