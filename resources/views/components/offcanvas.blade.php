<div x-data="{ show: @entangle('show') }" x-init="$watch('show', value => show = value)" class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEnd" aria-labelledby="offcanvasEndLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title" id="offcanvasEndLabel">End offcanvas</h2>
        <button type="button" class="btn-close text-reset" x-on:click="show = false" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab assumenda ea est, eum exercitationem fugiat illum itaque laboriosam magni necessitatibus, nemo nisi numquam quae reiciendis repellat sit soluta unde. Aut!
        </div>
        <div class="mt-3">
            <button class="btn btn-primary" type="button" x-on:click="show = false">
                Close offcanvas
            </button>
        </div>
    </div>
</div>
