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


const counter = document.getElementById("counter");
const digits = counter.querySelectorAll("span");

function updateCounter(number) {
  const str = number.toString().padStart(digits.length, "0");
  digits.forEach((digit, i) => {
    digit.textContent = str[i];
  });
}

updateCounter(totalVisits);

document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form[action="contact_submit.php"]');
  if (form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const notification = document.getElementById('contact-notification');
      const errorDiv = document.getElementById('contact-error');

      // Hide previous messages
      notification.style.display = 'none';
      errorDiv.style.display = 'none';

      try {
        const response = await fetch('contact_submit.php', {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const result = await response.json();
        if (result.success) {
          notification.style.display = 'block';
          this.reset();
          setTimeout(() => notification.style.display = 'none', 3000);
        } else {
          errorDiv.textContent = result.message;
          errorDiv.style.display = 'block';
          setTimeout(() => errorDiv.style.display = 'none', 3000);
        }
      } catch (error) {
        errorDiv.textContent = 'Error sending message. Please try again.';
        errorDiv.style.display = 'block';
        setTimeout(() => errorDiv.style.display = 'none', 3000);
      }
    });
  }
});
