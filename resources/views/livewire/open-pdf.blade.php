<!-- livewire/open-pdf.blade.php -->

<script>
    window.open('{{ $pdfUrl }}', '_blank');
    window.history.back(); // Opcional: redirigir atrás después de abrir el PDF
</script>