/* ---- particles.js config ---- */

particlesJS("particles-js", {
    particles: {
      number: {
        value: 380,
        density: {
          enable: true,
          value_area: 800,
        },
      },
      color: {
        value: "#13E8E9",
      },
      shape: {
        type: "circle",
        stroke: {
          width: 0,
          color: "#000000",
        },
        polygon: {
          nb_sides: 5,
        },
        image: {
          src: "img/github.svg",
          width: 100,
          height: 100,
        },
      },
      opacity: {
        value: 0.5,
        random: false,
        anim: {
          enable: false,
          speed: 1,
          opacity_min: 0.1,
          sync: false,
        },
      },
      size: {
        value: 3,
        random: true,
        anim: {
          enable: false,
          speed: 40,
          size_min: 0.1,
          sync: false,
        },
      },
      line_linked: {
        enable: true,
        distance: 150,
        color: "#ffffff",
        opacity: 0.4,
        width: 1,
      },
      move: {
        enable: true,
        speed: 2,
        direction: "none",
        random: false,
        straight: false,
        out_mode: "out",
        bounce: false,
        attract: {
          enable: false,
          rotateX: 600,
          rotateY: 1200,
        },
      },
    },
    interactivity: {
      detect_on: "canvas",
      events: {
        onhover: {
          enable: true,
          mode: "grab",
        },
        onclick: {
          enable: true,
          mode: "push",
        },
        resize: true,
      },
      modes: {
        grab: {
          distance: 140,
          line_linked: {
            opacity: 1,
          },
        },
        bubble: {
          distance: 400,
          size: 40,
          duration: 2,
          opacity: 8,
          speed: 3,
        },
        repulse: {
          distance: 200,
          duration: 0.4,
        },
        push: {
          particles_nb: 4,
        },
        remove: {
          particles_nb: 2,
        },
      },
    },
    retina_detect: true,
  });
  /* ---- particles.js config ---- */
  
  
  document.getElementById("contactForm").addEventListener("submit", function (e) {
    e.preventDefault();
  
    // Form validation
    const name = this.name.value.trim();
    const email = this.email.value.trim();
    const message = this.message.value.trim();
    const alertBox = document.getElementById("formAlert");
  
    // Clear previous alerts
    alertBox.style.display = "none";
    alertBox.className = "alert";
  
    // Validate email format
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      showAlert("Please enter a valid email address", "danger");
      return;
    }
  
    // Basic validation
    if (!name || !message) {
      showAlert("Please fill all required fields", "danger");
      return;
    }
  
    // Use FormSubmit.co
    const formData = new FormData(this);
    const formAction = "https://formsubmit.co/ajax/harveyelvis24@gmail.com";
  
    fetch(formAction, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          showAlert("Message sent successfully!", "success");
          this.reset();
        } else {
          showAlert("Failed to send message. Please try again.", "danger");
        }
      })
      .catch((error) => {
        showAlert("Error: " + error.message, "danger");
      });
  
    function showAlert(message, type) {
      alertBox.textContent = message;
      alertBox.classList.add(`alert-${type}`);
      alertBox.style.display = "block";
  
      // Auto-hide after 5 seconds
      setTimeout(() => {
        alertBox.style.display = "none";
      }, 5000);
    }
  });
  document.addEventListener('DOMContentLoaded', function() {
    const brandimgs = document.querySelector('.brandimgs');
    
    // Add loaded class after short delay to trigger animations
    setTimeout(() => {
        brandimgs.classList.add('loaded');
    }, 500);
    
    // Optional: Add hover effects
    const images = document.querySelectorAll('.image-wrapper');
    images.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.zIndex = '10';
            this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.3)';
        });
        img.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = '2';
            this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
        });
    });
});