<?php
$filesDir = 'documents/';
$files = scandir($filesDir);
$files = array_diff($files, array('.', '..'));

foreach ($requirements as $req) {
    $docType = $req['documentType'];
    $fileUploaded = false;
    $targetPath = '';
    $fileName = '';

    // Check if document exists for this requirement
    foreach ($documents as $doc) {
        if ($doc['documentType'] === $docType) {
            foreach ($files as $file) {
                if ($file === $doc['documentName']) {
                    $fileUploaded = true;
                    $targetPath = $filesDir . $file;
                    $fileName = $file;
                    $trimmedName = preg_replace('/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/', '', $file);
                    $documentStatus = $doc['documentStatus'] ?? 'Pending';
                    $rejectReason = $doc['rejectReason'] ?? '';
                    break;
                }
            }
            break;
        }
    } ?>
    <div class="mb-8 mx-10" id="anchor-<?= $docType ?>">
        <h4 class="m-0 text-xl font-medium pb-2"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
        <h5 class="ml-5 text-gray-500 text-sm font-medium">• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>

        <div class="mt-4 mx-6 document-container">
            <?php if ($fileUploaded): ?>
                <div class="flex items-center gap-2 mt-2">
                    <!-- File preview container with max width -->
                    <div class="flex flex-1 max-w-[800px]">
                        <div class="flex justify-between items-center gap-2 border-2 border-black p-2 rounded-xl w-full">
                            <span class="font-bold underline break-all">
                                <a href="<?= htmlspecialchars($targetPath) ?>" target="_blank"><?= htmlspecialchars($trimmedName) ?></a>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-blue-500 cursor-pointer" onclick="window.open('<?= htmlspecialchars($targetPath) ?>', '_blank')">View</span>
                                <img class="w-5 h-5 cursor-pointer document-requirements-icon"
                                    src="assets/Download-Icon.png"
                                    alt="Download"
                                    title="Download"
                                    onclick="downloadSampleFile('<?= htmlspecialchars($targetPath) ?>')">
                            </div>
                        </div>
                    </div>

                    <!-- Actions + icons with fixed position -->
                    <div class="flex items-center gap-2 ml-2" id="actions-<?= $docType ?>" data-status="<?= strtolower($documentStatus) ?>">
                        <?php if ($documentStatus === 'Pending'): ?>
                            <span class="text-red-500 font-bold cursor-pointer whitespace-nowrap reject-text"
                                onclick="showRejectPopup('<?= $docType ?>', '<?= htmlspecialchars($fileName) ?>')">Reject</span>
                            <span class="text-green-500 font-bold cursor-pointer whitespace-nowrap approve-text"
                                onclick="handleDecision('<?= $docType ?>', 'approve')">Approve</span>
                        <?php endif; ?>

                        <img src="assets/Wrong-Icon.png" class="reject-icon <?= $documentStatus === 'Rejected' ? '' : 'hidden' ?> w-5 h-5" id="reject-icon-<?= $docType ?>" alt="Rejected">
                        <img src="assets/Check-Icon.png" class="approve-icon <?= $documentStatus === 'Approved' ? '' : 'hidden' ?> w-5 h-5" id="approve-icon-<?= $docType ?>" alt="Approved">
                    </div>
                </div>
                <?php if ($documentStatus === 'Rejected' && !empty($rejectReason)): ?>
                    <p class="text-[red] font-medium mt-4 ml-2">Reason for rejection: <?= htmlspecialchars($rejectReason) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-red-600 font-bold">No file uploaded.</p>
            <?php endif; ?>
        </div>
    </div>
<?php } ?>

<!-- Action Buttons -->
<div class="flex justify-end gap-4 mx-10">
    <button type="button" id="downloadAllBtn"
        class="border-2 border-solid border-black rounded-xl text-base font-bold px-7 py-3 mx-0 my-8 transition duration-300 bg-[#0C5AAD] text-white hover:bg-white hover:text-black">
        Download All
    </button>
    <button type="button" id="submitBtn"
        class="border-2 border-solid border-black rounded-xl text-base font-bold px-7 py-3 mx-0 my-8 transition duration-300 cursor-not-allowed text-[gray]"
        disabled>
        Submit
    </button>
</div>