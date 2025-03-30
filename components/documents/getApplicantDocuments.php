<?php
foreach ($requirements as $req) { ?>
    <div class="document-requirements" id="anchor-<?= $req['documentType'] ?>">
        <h4><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
        <?php foreach ($req['subtitles'] as $subtitle) { ?>
            <h5>• <?= $subtitle ?></h5>
        <?php
        } ?>
        <h5>• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>
        <div class="upload-container">
            <input type="file" name="<?= $req['documentType'] ?>" id="<?= $req['documentType'] ?>" class="file-input" accept="<?= $req['requiredFormat'] ?>">
            <button type="button" class="upload-btn" onclick="triggerUpload('<?= $req['documentType'] ?>')">Upload</button>
            <div class="file-preview hidden" id="preview-<?= $req['documentType'] ?>">
                <span class="file-name"></span>
                <div class="file-actions">
                    <img src="assets/Download-Icon.png" class="document-requirements-icon" alt="Download" title="Download">
                    <span class="view-text">View</span>
                    <span class="remove-text" onclick="removeFile('<?= $req['documentType'] ?>')">Remove</span>
                </div>
            </div>
            <p class="error-message"></p>
        </div>
    </div>
<?php
}
?>