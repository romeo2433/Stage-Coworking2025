<!-- ======= Footer ======= -->
<footer id="footer" class="footer">
  <div class="copyright text-center">
    &copy; 2025 Chambre de Commerce et d’Industrie d’Antananarivo.<br>
    Tous droits réservés. |
    <a href="https://www.cci.mg" target="_blank">cci.mg</a>
  </div>
</footer>

<!-- Bouton retour haut -->
<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>

<!-- JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.querySelector('.toggle-sidebar-btn');
  const sidebar = document.querySelector('.sidebar');
  const footer = document.querySelector('.footer');
  const content = document.querySelector('.main-content');

  if (toggleBtn && sidebar && content && footer) {
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('closed');
      content.classList.toggle('expanded');
      footer.classList.toggle('expanded');
    });
  }
});
</script>

<style>
/* --- FOOTER SIMPLE ET FLUIDE --- */
.footer {
  background: #f8f9fa;
  color: #333;
  text-align: center;
  padding: 10px 0;
  margin-left: 250px; /* aligné avec sidebar ouverte */
  transition: margin-left 0.3s ease;
  
}

.footer.expanded {
  margin-left: 0; /* prend toute la largeur quand la sidebar est fermée */
}



.sidebar.closed {
  transform: translateX(-250px);
}


.main-content.expanded {
  margin-left: 0;
}
</style>
