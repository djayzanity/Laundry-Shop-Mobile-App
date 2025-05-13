// Initialize Swiper
new Swiper('.card-wrapper', {
  loop: true,
  spaceBetween: 30,
  pagination: {
    el: '.swiper-pagination',
    clickable: true,
    dynamicBullets: true
  },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  breakpoints: {
    0: {
      slidesPerView: 1
    },
    768: {
      slidesPerView: 2
    },
    1024: {
      slidesPerView: 3
    }
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');
  const orderNumberElement = document.querySelector('.order-info h3');
  const orderPriceElement = document.querySelector('.order-price h2');
  
  // Dynamic update for order details
  const orderNumber = '212334'; 
  const orderPrice = '₱300'; 
  
  orderNumberElement.textContent = `Order number: ${orderNumber}`;
  orderPriceElement.textContent = orderPrice;
  
  // Smooth transition for navigation
  const activateNavItem = (href) => {
    navItems.forEach(nav => {
      nav.classList.remove('active');
      if (nav.getAttribute('href') === href) {
        nav.classList.add('active');
      }
    });
  };

  const currentPage = window.location.href;
  activateNavItem(currentPage);

  navItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent default link navigation
      const targetUrl = item.getAttribute('href');

      // Set the active item in localStorage
      localStorage.setItem('activeNavItem', targetUrl);
      
      // Smooth transition
      document.body.classList.add('fade-out');
      setTimeout(() => {
        window.location.href = targetUrl;
      }, 500);
    });
  });

  window.onload = () => {
    document.body.classList.remove('fade-out');
  };
});

document.addEventListener('DOMContentLoaded', () => {
  const navItems = document.querySelectorAll('.nav-item');
  const orderNumberElement = document.querySelector('.order-info h3');
  const orderPriceElement = document.querySelector('.order-price h2');
  
  // Dynamic update for order details
  const orderNumber = '212334'; 
  const orderPrice = '₱300'; 
  
  orderNumberElement.textContent = `Order number: ${orderNumber}`;
  orderPriceElement.textContent = orderPrice;
  
  // Smooth transition for navigation
  const activateNavItem = (href) => {
    navItems.forEach(nav => {
      nav.classList.remove('active');
      if (nav.getAttribute('href') === href) {
        nav.classList.add('active');
      }
    });
  };

  const currentPage = window.location.href;
  activateNavItem(currentPage);

  navItems.forEach(item => {
    item.addEventListener('click', (e) => {
      e.preventDefault(); // Prevent default link navigation
      const targetUrl = item.getAttribute('href');

      // Set the active item in localStorage
      localStorage.setItem('activeNavItem', targetUrl);
      
      // Smooth transition
      document.body.classList.add('fade-out');
      setTimeout(() => {
        window.location.href = targetUrl;
      }, 500);
    });
  });

  window.onload = () => {
    document.body.classList.remove('fade-out');
  };
});
