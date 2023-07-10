document.addEventListener("DOMContentLoaded", function(){
    // make it as accordion for smaller screens
    if (window.innerWidth < 992) {
    
      // close all inner dropdowns when parent is closed
      document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown){
        everydropdown.addEventListener('hidden.bs.dropdown', function () {
          // after dropdown is hidden, then find all submenus
            this.querySelectorAll('.submenu').forEach(function(everysubmenu){
              // hide every submenu as well
              everysubmenu.style.display = 'none';
            });
        })
      });
    
      document.querySelectorAll('.dropdown-menu a').forEach(function(element){
        element.addEventListener('click', function (e) {
            let nextEl = this.nextElementSibling;
            if(nextEl && nextEl.classList.contains('submenu')) {	
              // prevent opening link if link needs to open dropdown
              e.preventDefault();
              if(nextEl.style.display == 'block'){
                nextEl.style.display = 'none';
              } else {
                nextEl.style.display = 'block';
              }
    
            }
        });
      })
    }
    // end if innerWidth
    }); 
    // DOMContentLoaded  end



    document.addEventListener('DOMContentLoaded', function() {
      // Activer la barre de recherche "collection" par défaut
      const defaultButton = document.getElementById('button1');
      const defaultSearchBar = document.getElementById('searchBar1');
  
      defaultButton.classList.add('active');
      defaultSearchBar.style.display = 'block';

      const searchBars = document.getElementsByClassName('search-bar');
    for (var i = 1; i < searchBars.length; i++) {
      searchBars[i].style.display = 'none';
    }
    });

    function toggleSearchBar(searchBarId, buttonId) {
      const searchBar = document.getElementById(searchBarId);
      const button = document.getElementById(buttonId);

      // Réinitialiser l'état des boutons et masquer toutes les barres de recherche
      const buttons = document.getElementsByClassName('buttonsearch');
      const searchBars = document.getElementsByClassName('search-bar');
      for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('active');
        searchBars[i].style.display = 'none';
      }

      // Afficher la barre de recherche appropriée et mettre en surbrillance le bouton
      searchBar.style.display = 'block';
      button.classList.add('active');

    }


    
    document.addEventListener('DOMContentLoaded', function() {
      const dropdown = document.querySelector('.dropdown');
      
      dropdown.addEventListener('mouseenter', function() {
        dropdown.classList.add('open');
      });
      
      dropdown.addEventListener('mouseleave', function() {
        dropdown.classList.remove('open');
      });
    });
   
  






    function toggleSearchBar(searchBarId, buttonId) {
      var searchBar = document.getElementById(searchBarId);
      var button = document.getElementById(buttonId);
  
      // Afficher la barre de recherche en haut à droite
      searchBar.style.display = "block";
  
      // Cacher toutes les autres barres de recherche
      var searchBars = document.getElementsByClassName("search-bar");
      for (var i = 0; i < searchBars.length; i++) {
        if (searchBars[i].id !== searchBarId) {
          searchBars[i].style.display = "none";
        }
      }
  
      // Mettre à jour le texte du bouton principal avec le texte du bouton sélectionné
      var dropdownButton = document.getElementById("dropdownMenuButton");
      dropdownButton.innerHTML = button.innerHTML;
    }
