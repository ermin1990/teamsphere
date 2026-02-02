<img
    src="{{ $placeholder }}"
    data-src="{{ $src }}"
    alt="{{ $alt }}"
    class="lazy {{ $class }}"
    loading="lazy"
    onload="this.src = this.dataset.src; this.classList.remove('lazy')"
/>