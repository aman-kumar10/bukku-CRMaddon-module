<div class="sync-modal" id="syncModal">
  <div class="syncmodal-body">
    <div class="icon-wrapper">
      <i class="fa fa-spinner"></i>
      <i class="fas fa-user"></i>
    </div>
    <p  class="modal-msg">Are you sure you want to sync the user?</p>
    <input type="hidden" id="sync-user-id">
    <input type="hidden" id="sync-user-name">
    <div>
      <a class="btn btn-info yes-btn">Yes</a>
      <a class="btn btn-danger cancel-btn">Cancel</a>
    </div>
  </div>
</div>  

<div class="sync-modal" id="invoiceSyncModal">
  <div class="syncmodal-body">
    <div class="icon-wrapper">
      <i class="fa fa-spinner"></i>
      <i class="fas fa-file-invoice"></i>
    </div>
    <p  class="modal-msg">Are you sure you want to sync the invoice?</p>
    <input type="hidden" id="sync-invocie-id">
    <input type="hidden" id="sync-invocie-name">
    <div>
      <a class="btn btn-info yes-btn">Yes</a>
      <a class="btn btn-danger cancel-btn">Cancel</a>
    </div>
  </div>
</div>  

<div class="complete-modal" id="completeModal">
  <div class="completemodal-body">
    <div class="icon-wrapper">
      <i class="fa fa-check-circle"></i>
    </div>
    <p class="modal-msg"><b id="sync-userName"></b> have been successfully synced.</span></p>
    <input type="hidden" id="complete-user-id">
    <div>
      <a class="btn btn-primary ok-btn">Ok</a>
    </div>
  </div>
</div>  