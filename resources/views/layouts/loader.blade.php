@include('layouts.loader')
<!-- resources/views/layouts/loader.blade.php -->
<div id="loader" style="
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(255,255,255,0.8);
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script>
    const loader = document.getElementById('loader');
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            loader.style.display = 'flex';
        });
    });
    window.addEventListener('pageshow', () => {
        loader.style.display = 'none';
    });
</script>
