const navToggle = document.getElementById('navToggle');
const navLinks = document.querySelector('.nav__links');

navToggle.addEventListener('click', () => {
  navLinks.classList.toggle('open');
});

navLinks.querySelectorAll('a').forEach((link) => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('open');
  });
});

const sections = document.querySelectorAll('main section[id]');
const navItems = document.querySelectorAll('.nav__links a');

const setActiveLink = () => {
  let current = '';
  sections.forEach((section) => {
    const top = section.offsetTop - 100;
    if (window.scrollY >= top) {
      current = section.getAttribute('id');
    }
  });

  navItems.forEach((item) => {
    item.style.color = item.getAttribute('href') === `#${current}` ? 'var(--teal)' : '';
  });
};

window.addEventListener('scroll', setActiveLink);
setActiveLink();