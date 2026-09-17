const navToggle = document.getElementById("navToggle");
const navLinks = document.getElementById("navLinks");


// =========================================================
// MOBILE NAVIGATION
// =========================================================

navToggle.addEventListener("click", () => {

    const isOpen = navLinks.classList.toggle("active");

    navToggle.setAttribute(
        "aria-expanded",
        isOpen
    );

});


// Tutup menu setelah link diklik

navLinks.querySelectorAll("a").forEach((link) => {

    link.addEventListener("click", () => {

        navLinks.classList.remove("active");

        navToggle.setAttribute(
            "aria-expanded",
            "false"
        );

    });

});


// =========================================================
// ACTIVE NAVIGATION
// =========================================================

const sections = document.querySelectorAll(
    "main section[id]"
);

const navItems = document.querySelectorAll(
    ".nav-links a"
);


const setActiveLink = () => {

    let current = "";

    sections.forEach((section) => {

        const sectionTop =
            section.offsetTop - 140;

        const sectionBottom =
            sectionTop + section.offsetHeight;

        if (
            window.scrollY >= sectionTop &&
            window.scrollY < sectionBottom
        ) {
            current = section.getAttribute("id");
        }

    });


    navItems.forEach((item) => {

        item.classList.remove("active");

        if (
            item.getAttribute("href") ===
            `#${current}`
        ) {

            item.classList.add("active");

        }

    });

};


// Jalankan ketika halaman di-scroll

window.addEventListener(
    "scroll",
    setActiveLink
);


// Jalankan saat halaman pertama kali dibuka

setActiveLink();


// =========================================================
// LIGHTBOX SERTIFIKAT
// =========================================================

const certLightbox = document.getElementById("certLightbox");
const certLightboxImg = document.getElementById("certLightboxImg");
const certLightboxCaption = document.getElementById(
    "certLightboxCaption"
);

const certButtons = document.querySelectorAll(
    ".certificate-view[data-cert-src]"
);


const openCertLightbox = (src, title) => {

    certLightboxImg.src = src;
    certLightboxImg.alt = title;
    certLightboxCaption.textContent = title;

    certLightbox.hidden = false;

    document.body.style.overflow = "hidden";

};


const closeCertLightbox = () => {

    certLightbox.hidden = true;

    certLightboxImg.src = "";

    document.body.style.overflow = "";

};


// Buka lightbox saat tombol "Lihat sertifikat" diklik

certButtons.forEach((button) => {

    button.addEventListener("click", () => {

        const src = button.getAttribute(
            "data-cert-src"
        );

        const title = button.getAttribute(
            "data-cert-title"
        );

        openCertLightbox(src, title);

    });

});


// Tutup lightbox lewat tombol close atau klik backdrop

document
    .querySelectorAll("[data-cert-close]")
    .forEach((el) => {

        el.addEventListener(
            "click",
            closeCertLightbox
        );

    });


// Tutup lightbox dengan tombol Escape

document.addEventListener("keydown", (event) => {

    if (
        event.key === "Escape" &&
        !certLightbox.hidden
    ) {
        closeCertLightbox();
    }

});