<?php
foreach ($requirements as $req) { ?>
    <div class="mt-0 mx-10 mb-9" id="anchor-<?= $req['documentType'] ?>">
        <h4 class="m-0 text-xl font-medium pb-2"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
        <?php foreach ($req['subtitles'] as $subtitle) { ?>
            <h5>• <?= $subtitle ?></h5>
        <?php
        } ?>
        <h5>• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>
        <div class="mt-4 mx-6 mb-0">
            <input type="file" name="<?= $req['documentType'] ?>" id="<?= $req['documentType'] ?>" class="hidden" accept="<?= $req['requiredFormat'] ?>">
            <button type="button" class="block bg-[#7213D0] border-2 border-[solid] border-[black] text-[white] rounded-xl text-base font-bold px-16 py-1 mx-0 my-4 hover:bg-[white] hover:text-[black] hover:cursor-pointer hover:[transition:0.3s]" onclick="triggerUpload('<?= $req['documentType'] ?>')">Upload</button>
            <div class="hidden flex justify-between gap-4 border-3 border-[solid] border-[black] p-3 rounded-2xl mt-3 min-w-80 max-w-3xl" id="preview-<?= $req['documentType'] ?>">
                <span class="font-bold underline break-all"></span>
                <div class="flex items-center gap-3">
                    <img src="assets/Download-Icon.png" class="w-5 h-5 cursor-pointer" alt="Download" title="Download">
                    <span class="text-[blue] cursor-pointer">View</span>
                    <span class="text-[red] cursor-pointer" onclick="removeFile('<?= $req['documentType'] ?>')">Remove</span>
                </div>
            </div>
            <p class="text-[red] font-bold hidden"></p>
        </div>
    </div>
<?php
}
?>