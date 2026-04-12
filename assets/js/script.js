            // JavaScript to toggle the menu on mobile view
            const menuIcon = document.getElementById('menu-icon');
            const navLinks = document.querySelector('.nav-links');

            menuIcon.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });

        //nabvar scroll
        // Listen for scroll events
        window.onscroll = function() { changeNavbarBackground() };

        function changeNavbarBackground() {
            if (window.scrollY > 50) { // When you scroll down 50px or more
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        }
        