// public/assets/js/app.js
document.addEventListener('DOMContentLoaded', () => {
  const q = document.querySelector('#tableSearch');
  if (q) {
    q.addEventListener('input', e => {
      const val = e.target.value.toLowerCase();
      document.querySelectorAll('table tbody tr').forEach(tr => {
        tr.style.display = tr.innerText.toLowerCase().includes(val) ? '' : 'none';
      });
    });
  }
});
<script>
    const toggleBtn = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme');

    if (currentTheme === 'dark') 
        document.body.classList.add('dark-mode');
        toggleBtn.textContent = "🌙 Dark Mode";
    

    toggleBtn.addEventListener'click', () =
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) 
            toggleBtn.textContent = "🌙 Dark Mode";
            localStorage.setItem('theme', 'dark');
         else 
            toggleBtn.textContent = "🌞 Light Mode";
            localStorage.setItem('theme', 'light');
        
    ;
</script>
