<?php
$uploadDir = 'documents/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

foreach ($requirements as $req) {
    $docType = $req['documentType'];
    $inputName = $docType;
    $fileUploaded = isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === 0;
    $fileName = $fileUploaded ? basename($_FILES[$inputName]['name']) : null;

    if ($fileUploaded) {
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath);
    }
?>

    <div class="mb-8 mx-10" id="anchor-<?= $docType ?>">
        <h4 class="m-0 text-xl font-medium pb-2"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
        <h5 class="ml-2 text-gray-500 text-sm font-medium">• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>

        <div class="mt-4 mx-6">
            <?php if ($fileUploaded): ?>
                <div class="flex items-center gap-4 mt-2">
                    <!-- File preview -->
                    <div class="flex justify-between items-center gap-4 border-2 border-black p-2 rounded-xl min-w-80 max-w-3xl flex-1">
                        <span class="font-bold underline break-all">
                            <a href="<?= $targetPath ?>" target="_blank"><?= $fileName ?></a>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-blue-500 cursor-pointer" onclick="window.open('<?= $targetPath ?>', '_blank')">View</span>
                            <img class="w-5 h-5 cursor-pointer document-requirements-icon" src="assets/Download-Icon.png" alt="Download" title="Download"
                                 onclick="downloadSampleFile('<?= $targetPath ?>')">
                        </div>
                    </div>

                    <!-- Actions + icons -->
                    <div class="flex items-center gap-2" id="actions-<?= $req['documentType'] ?>" data-status="pending">
                        <!-- Action buttons -->
                        <span class="text-red-500 font-bold cursor-pointer reject-text"
                              onclick="showRejectPopup('<?= $req['documentType'] ?>', '<?= $fileName ?>')">Reject</span>
                        <span class="text-green-500 font-bold cursor-pointer approve-text"
                              onclick="handleDecision('<?= $req['documentType'] ?>', 'approve')">Approve</span>

                        <!-- Status icons -->
                        <img src="assets/Wrong-Icon.png" class="reject-icon hidden w-5 h-5" id="reject-icon-<?= $req['documentType'] ?>" alt="Rejected">
                        <img src="assets/Check-Icon.png" class="approve-icon hidden w-5 h-5" id="approve-icon-<?= $req['documentType'] ?>" alt="Approved">
                    </div>
                </div>
            <?php else: ?>
                <p class="text-red-600 font-bold">No file uploaded.</p>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>

<!-- Submit Button -->
<div class="flex justify-end">
<button type="button" id="submitBtn" class="bg-[#c7acee] border-2 border-[#6a11cb] text-[#6c6c6c] font-bold px-8 py-2 rounded-none transition-all cursor-not-allowed" disabled>Submit</button>

</div>
