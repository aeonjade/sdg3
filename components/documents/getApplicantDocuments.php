<?php
foreach ($requirements as $req) { ?>
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
    <div class="upload-container mt-4 mx-6 mb-0">
      <!-- .file-input -->
      <input type="file" name="<?= $req['documentType'] ?>" id="<?= $req['documentType'] ?>" class="file-input hidden" accept="<?= $req['requiredFormat'] ?>">
      <!-- .upload-btn -->
      <button type="button" class="upload-btn block bg-[#7213D0] border-2 border-[solid] border-[black] text-[white] rounded-xl text-base font-bold px-16 py-1 mx-0 my-4 hover:bg-[white] hover:text-[black] hover:cursor-pointer hover:[transition:0.3s]" onclick="triggerUpload('<?= $req['documentType'] ?>')">Upload</button>

      <!-- .file-preview -->
      <div class="file-preview flex hidden justify-between gap-[15px] border-[2px] border-[solid] border-[black] p-[10px] rounded-[15px] mt-[10px] min-w-[300px] max-w-[750px]" id="preview-<?= $req['documentType'] ?>">
        <!-- .file-preview .file-name -->
        <span class="file-name font-bold underline break-all"></span>
        <!-- .file-actions -->
        <div class="file-actions flex items-center gap-3">
          <!-- .document-requirements-icon -->
          <img src="assets/Download-Icon.png" class="document-requirements-icon w-5 h-5 cursor-pointer" alt="Download" title="Download">
          <!-- .view-text -->
          <span class="view-text text-[blue] cursor-pointer">View</span>
          <!-- .remove-text -->
          <span class="remove-text text-[red] cursor-pointer" onclick="removeFile('<?= $req['documentType'] ?>')">Remove</span>
        </div>
      </div>
      <!-- .error-message -->
      <p class="error-message text-[red] font-bold hidden"></p>
    </div>
  </div>
<?php } ?>