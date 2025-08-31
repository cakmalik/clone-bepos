 <div class="modal modal-blur fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-{{ $size ?? '' }}" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="{{ Str::slug($title) }}-title">{{ $title }}</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div class="modal-body">
                     {{ $body }}
                 </div>
             </div>
             <div class="modal-footer">
                 <div class="btn-group">
                     {{-- <button type="button" class="btn me-auto" data-bs-dismiss="modal">Tutup</button> --}}
                 </div>
                 {{ $footer }}
             </div>
         </div>
     </div>
 </div>
