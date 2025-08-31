
<script>
    window.addEventListener('updated', function(e) {
        Toast.fire({
            icon: "success",
            title: e.detail.message,
            position: "bottom-end",
        });
    })
    window.addEventListener('logs-null', function(e) {
        Toast.fire({
            icon: "error",
            title: e.detail.message,
            position: "bottom-end",
        });
    })

    document.addEventListener('alpine:init', () => {
        Alpine.directive('focus', (el, {
            value
        }) => {
            if (value) {
                el.focus();
            }
        });
    });
</script>
