window.addEventListener("scroll", function () {
  const nav = document.querySelector(".breadcrumb");
  if (window.scrollY > 50) {
    nav.classList.add("scrolled");
  } else {
    nav.classList.remove("scrolled");
  }
});

// Hamburger menu toggle functionality
document.addEventListener('DOMContentLoaded', () => {
  // Create hamburger menu element
  const hamburgerMenu = document.createElement('div');
  hamburgerMenu.className = 'hamburger-menu';
  hamburgerMenu.innerHTML = '<div></div><div></div><div></div>';

  // Append to nav-links container
  const navLinks = document.querySelector('.nav-links');
  if (navLinks) {
    navLinks.appendChild(hamburgerMenu);

    hamburgerMenu.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      hamburgerMenu.classList.toggle('active');
    });
  }
});


let count = 0;
const counter = document.getElementById("counter");
const digits = counter.querySelectorAll("span");

function updateCounter(number) {
  const str = number.toString().padStart(digits.length, "0");
  digits.forEach((digit, i) => {
    digit.textContent = str[i];
  });
}

setInterval(() => {
  count++;
  updateCounter(count);
}, 5000);

updateCounter(count);
