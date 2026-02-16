<style>
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 99999;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .loader {
      --color: #7373d4ff;
      --size: 70px;
      width: var(--size);
      height: var(--size);
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 5px;
    }
    
    .loader span {
      width: 100%;
      height: 100%;
      background-color: var(--color);
      animation: keyframes-blink 0.6s alternate infinite linear;
    }
    
    .loader span:nth-child(1) {
      animation-delay: 0ms;
    }
    
    .loader span:nth-child(2) {
      animation-delay: 200ms;
    }
    
    .loader span:nth-child(3) {
      animation-delay: 300ms;
    }
    
    .loader span:nth-child(4) {
      animation-delay: 400ms;
    }
    
    .loader span:nth-child(5) {
      animation-delay: 500ms;
    }
    
    .loader span:nth-child(6) {
      animation-delay: 600ms;
    }
    
    @keyframes keyframes-blink {
      0% {
        opacity: 0.3;
        transform: scale(0.5) rotate(5deg);
      }
  
      50% {
        opacity: 1;
        transform: scale(1);
      }
    }
    
</style>

<div id="page-loader">

<div class="loader">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
  <span></span>
  <span></span>
</div>
</div>

<script>
(function() {
    const loader = document.getElementById('page-loader');
    
    window.addEventListener('load', function() {
        setTimeout(function() {
            loader.classList.add('hidden');
        }, 200);
    });
    
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        
        if (target && target.href && !target.classList.contains('no-loader')) {
            const href = target.getAttribute('href');

            if (href && 
                href !== '#' && 
                !href.startsWith('javascript:') && 
                !href.startsWith('mailto:') && 
                !href.startsWith('tel:') &&
                !target.hasAttribute('download') &&
                target.target !== '_blank') {
                
                loader.classList.remove('hidden');
            }
        }
    });
    
    document.addEventListener('submit', function(e) {
        const form = e.target;
        
        if (!form.classList.contains('no-loader')) {
            loader.classList.remove('hidden');
        }
    });
    
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            loader.classList.add('hidden');
        }
    });
})();
</script>