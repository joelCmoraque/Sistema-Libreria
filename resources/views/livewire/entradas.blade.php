<div class="wrapper w-full md:max-w-5xl mx-auto pt-20 px-4">
    <section class="pt-4">
        {{ $this->table }}
    </section>

</div>

<script>
    document.addEventListener('open-pdf', event => {
        window.open(event.detail.url, '_blank');
    });
</script>