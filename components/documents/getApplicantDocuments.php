<?php
foreach ($requirements as $req) {
  $hasDocument = false;
  foreach ($documents as $doc) {
    if ($doc['documentType'] == $req['documentType']) {
      $hasDocument = true;
      break;
    }
  } ?>

  <!-- main div.inner-box .document-requirements -->
  <div class="document-requirements mt-0 mx-10 mb-9" id="anchor-<?= $req['documentType'] ?>">
    <!-- main div.inner-box h4 -->
    <h4 class="ml-0 text-xl font-semibold pb-3"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
    <?php foreach ($req['subtitles'] as $subtitle) { ?>
      <h5 class="ml-5 text-sm text-gray-500 font-medium">• <?= $subtitle ?></h5>
    <?php } ?>
    <!-- main div.inner-box h5-->
    <h5 class="ml-5 text-sm text-[red] font-medium">• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>

    <!-- .upload-container -->
    <div class="upload-container mt-4 mx-6 mb-0" data-applicant-id="<?= $applicantID ?>">
      <?php if (!$hasDocument) { ?>
        <!-- Show input and button if no document exists -->
        <input type="file" name="<?= $req['documentType'] ?>" id="input-<?= $req['documentType'] ?>"
          class="file-input hidden"
          accept="<?= $req['requiredFormat'] ?>">
        <button type="button"
          class="upload-btn block bg-[#7213D0] border-2 border-[solid] border-[black] text-[white] rounded-xl text-base font-bold px-16 py-1 mx-0 my-4 hover:bg-[white] hover:text-[black] hover:cursor-pointer hover:[transition:0.3s]"
          id="button-<?= $req['documentType'] ?>">Upload</button>
      <?php } else { ?>
        <!-- Show file preview if document exists -->
        <div id="preview-<?= $req['documentType'] ?>" class="file-preview flex flex-col gap-2">
          <div class="flex justify-between gap-[15px] border-[2px] border-[solid] border-[black] p-[10px] rounded-[15px] mt-[10px] min-w-[300px] max-w-[750px]">
            <span class="file-name font-bold underline break-all"><?= preg_replace('/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/', '', $doc['documentName']) ?></span>
            <div class="flex items-center gap-3">
              <img src="assets/Download-Icon.png" class="document-requirements-icon w-5 h-5 cursor-pointer" alt="Download" title="Download" data-filename="<?= $doc['documentName'] ?>">
              <span class="view-text text-[blue] cursor-pointer" data-filename="<?= $doc['documentName'] ?>">View</span>
              <?php if ($doc['documentStatus'] !== 'Approved'): ?>
                <span class="remove-text text-[red] cursor-pointer" data-id="<?= $req['documentType'] ?>">Remove</span>
              <?php endif; ?>
            </div>
          </div>
          <?php if ($doc['documentStatus'] === 'Rejected' && !empty($doc['rejectReason'])): ?>
            <p class="text-[red] font-medium ml-2">Reason for rejection: <?= htmlspecialchars($doc['rejectReason']) ?></p>
          <?php endif; ?>
          <?php if ($doc['documentStatus'] === 'Approved'): ?>
            <p class="text-[green] font-medium ml-2">Status: Approved</p>
          <?php endif; ?>
        </div>
      <?php } ?>
      <p class="error-message text-[red] font-bold hidden"></p>
    </div>
  </div>
<?php } ?>