<?php
$uploadDir = 'documents/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

foreach ($requirements as $req) {
    $docType = $req['documentType'];
    $inputName = $docType; // this should match <input name="...">
    $fileUploaded = isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === 0;
    $fileName = $fileUploaded ? basename($_FILES[$inputName]['name']) : null;

    // Save uploaded file (optional)
    if ($fileUploaded) {
        $targetPath = $uploadDir . $fileName;
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetPath);
    }
?>

    <div class="mb-8 mx-10" id="anchor-<?= $docType ?>">
        <h4 class="m-0 text-xl font-medium pb-2"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>

        <div class="mt-4 mx-6">
            <?php if ($fileUploaded): ?>
                <div class="flex items-center gap-4 mt-2">
                    <div class="flex justify-between items-center gap-4 border-2 border-black p-2 rounded-xl min-w-80 max-w-3xl flex-1">
                        <span class="font-bold underline break-all">
                            <a href="<?= $targetPath ?>" target="_blank"><?= $fileName ?></a>
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-blue-500 cursor-pointer" onclick="window.open('<?= $targetPath ?>', '_blank')">View</span>
                            <img class="w-5 h-5 cursor-pointer" src="assets/Download-Icon.png" class="document-requirements-icon" alt="Download" title="Download"
                                onclick="downloadSampleFile('<?= $targetPath ?>')">
                        </div>
                    </div>

                    <div class="flex gap-2" id="actions-<?= $req['documentType'] ?>">
                        <span class="text-red-500 font-bold cursor-pointer" onclick="showRejectPopup('<?= $req['documentType'] ?>', '<?= $fileName ?>')">Reject</span>
                        <span class="text-green-500 font-bold cursor-pointer" onclick="handleDecision('<?= $req['documentType'] ?>', 'approve')">Approve</span>

                        <img src="assets/Wrong-Icon.png" class="reject-icon hidden" id="reject-icon-<?= $req['documentType'] ?>" alt="Rejected">
                        <img src="assets/Check-Icon.png" class="approve-icon hidden" id="approve-icon-<?= $req['documentType'] ?>" alt="Approved">
                    </div>
                </div>
            <?php else: ?>
                <p style="color: red; font-weight: bold;">No file uploaded.</p>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>

<div class="flex justify-end">
    <button type="button" class="bg-[#c7acee] border-2 border-[solid] border-[#6a11cb] text-[#6c6c6c] font-bold px-8 py-2 rounded-none cursor-not-allowed [transition:0.3s_ease]" id="submitBtn">Submit</button>
</div>