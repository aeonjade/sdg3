<?php
session_start();

$applicantName = $_SESSION['applicantName'] ?? "Ms. Galve-Abad";
$firstChoice = $_SESSION['firstChoice'] ?? "Bachelor of Science in Information Technology";
$secondChoice = $_SESSION['secondChoice'] ?? "Bachelor of Science in Civil Engineering";
$applicantType = $_SESSION['applicantType'] ?? "Bachelor-Program";

/*This shit is for JSON because if you don't include JSON file first, the admin page will have an error. Damn what an english speaking*/
$requirementsSet = $applicantType == "Bachelor-Program"
  ? file_get_contents("json/bachelorApplicant.json")
  : file_get_contents("json/graduateApplicant.json");

$requirements = json_decode($requirementsSet, true);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Document Requirements</title>
  <script src="https://cdn.tailwindcss.com"></script>
  
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
  </style>
  <!--<link rel="stylesheet" href="css/adminPage.css">-->
</head>

<body class= "font-roboto">

  <?php include("components/navigation/sidebar.php") ?>

  <section>
    <header class= "flex justify-between items-center p-4 bg-gray-100">
      <div class="header-text">
        <h1 class="text-2xl font-bold">Document Requirements</h1>
      </div>
      <div class="flex gap-4">
        <img src="assets/Phone-Icon.png" alt="Phone" class= "w-6 h-6">
        <img src="assets/Notification-Icon.png" alt="Notifications" class= "w-6 h-6">
        <img src="assets/Profile-Icon.png" alt="Profile" class= "w-6 h-6">
      </div>
    </header>

    <main class= "p-6">
      <div class="inner-box">
        <!-- Checklist -->
        <div class="sticky top-0 right-0 float-right bg-purple-700 text-white px-6 py-5 rounded-lg border-2 border-black text-sm w-max transition-all overflow-hidden" id="checklistBox">
          <div class="flex justify-between items-center mb-3">
            <h4 class="m-0 text-xl pb-3">Checklist</h4>
            <img onclick="toggleChecklist()" src="assets/chevron-up.png" alt="Toggle" class="w-4 h-4 filter brightness-0 invert [transition:transform_0.3s_ease]">
          </div>
          <ul class="list-none pl-0 m-0 transition-opacity">
            <?php foreach ($requirements as $req) {
              $docType = $req['documentType'];
              $isUploaded = isset($_FILES[$docType]) && $_FILES[$docType]['error'] === 0;
            ?>
             <li class="flex justify-between items-center my-2 whitespace-nowrap" id="item-<?= $docType ?>">
             <a class="text-white no-underline hover:underline" href="#anchor-<?= $docType ?>">• <?= str_replace("-", " ", $docType) ?></a>
                <?php if ($isUploaded): ?>
                  <img src="assets/Check-Icon.png" class="w-4 h-4 ml-8">
                <?php else: ?>
                  <img src="assets/Wrong-Icon.png" class="w-4 h-4 ml-8">
                <?php endif; ?>
              </li>
            <?php } ?>
          </ul>

        </div>

        <!-- Applicant Info -->
        <div class="ml-5">
        <h1 class="text-3xl font-bold my-4"><?= htmlspecialchars($applicantName); ?>'s Application</h1>
          <p>First Choice: <?= htmlspecialchars($firstChoice); ?></p>
          <p>Second Choice: <?= htmlspecialchars($secondChoice); ?></p>
        </div>

        <!-- Document Set Title -->
        <h2 class="ml-5 text-xl font-semibold my-4"><?= str_replace("-", " ", $applicantType); ?> Requirements</h2>

        <!-- Document Sections -->
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
            <h4 class="m-0 text-xl pb-2"><?= $req['documentID'] ?>. <?= $req['documentName'] ?></h4>
            <h5 class="mx-5 my-0 px-0 py-1 text-sm text-[gray]">• Must be uploaded in the following format: <?= $req['requiredFormat'] ?></h5>

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

        <!--Submit Popup-->
        <div class="popup" id="successPopup" style="display: none;">
          <img class="w-12 mb-3" src="assets/check-icon.png" alt="Success" >
          <h2 class="m-0 text-2xl text-[#fff]">Success</h2>
          <p class="text-sm text-[#f5f5f5]">Please wait for further instructions from the registrar</p>
          <button class="bg-[#4CAF50] text-[white] border-[none] px-4 py-2 mt-4 cursor-pointer text-base rounded-md hover:bg-[#45a049]" type="submit">Back to Document Upload</button>
        </div>

        <!-- Reject Message Pop-up -->
        <div class="text-[black] fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[white] p-4 border-2 border-[solid] border-[#ccc] rounded-xl [box-shadow:0_2px_10px_rgba(0,_0,_0,_0.2)]" id="rejectPopup" style="display: none;">
          <h2 style="color: black; padding-bottom:5px ;">Reject Message</h2>
          <textarea id="rejectMessageInput" rows="4" style="width: 100%; resize: none;"></textarea>
          <div style="margin-top: 5px; display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="saveRejectMessage()" style="background: limegreen; color: white; padding: 6px 14px; border: none; border-radius: 4px;">Save</button>
            <button onclick="cancelReject()" style="border: 1px solid gray; padding: 6px 14px; background: white; color:black;">Cancel</button>
          </div>
        </div>
    </main>
  </section>

  <script>

    let namefile;


    let currentRejectId = null;
    function showRejectPopup(id, filename) {
      namefile = filename;
      currentRejectId = id; // Save the ID for use in save
      const popup = document.getElementById('rejectPopup');
      if (popup) {
        popup.style.display = 'block';
      }
    }

    function cancelReject() {
      const popup = document.getElementById('rejectPopup');
      if (popup) {
        popup.style.display = 'none';
      }
    }


  function saveRejectMessage() {
  const message = document.getElementById('rejectMessageInput').value;
  console.log("Rejected with message:", message);


  let formData = new FormData();
  formData.append("fileName", namefile);
  formData.append("rejectReason", message);


  fetch("php/updateRejectMessage.php", {
    method: "POST",
    body: formData,
  }).then((response) => response.text());





  // Hide popup
  cancelReject();

  // Hide approve/reject text
  const approveText = document.querySelector(`#actions-${currentRejectId} .approve-text`);
  const rejectText = document.querySelector(`#actions-${currentRejectId} .reject-text`);
  if (approveText) approveText.style.display = "none";
  if (rejectText) rejectText.style.display = "none";

  // Show reject icon, hide approve icon
  const rejectIcon = document.getElementById(`reject-icon-${currentRejectId}`);
  const approveIcon = document.getElementById(`approve-icon-${currentRejectId}`);
  if (rejectIcon) rejectIcon.style.display = "inline";
  if (approveIcon) approveIcon.style.display = "none";
  }


  function downloadSampleFile(filePath) {
    const link = document.createElement("a");
    link.href = filePath;
    link.download = filePath.split("/").pop();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }

  function toggleChecklist() {
    const checklist = document.getElementById("checklistBox");
    checklist.classList.toggle("minimized");
  }

  /**Approve or reject */
  function handleDecision(id, action) {
    const approveText = document.querySelector(`#actions-${id} .approve-text`);
    const rejectText = document.querySelector(`#actions-${id} .reject-text`);
    const approveIcon = document.getElementById(`approve-icon-${id}`);
    const rejectIcon = document.getElementById(`reject-icon-${id}`);

    if (approveText) approveText.style.display = "none";
    if (rejectText) rejectText.style.display = "none";

    if (action === "approve") {
        if (approveIcon) approveIcon.classList.remove("hidden");
        if (rejectIcon) rejectIcon.classList.add("hidden");
    } else {
        if (rejectIcon) rejectIcon.classList.remove("hidden");
        if (approveIcon) approveIcon.classList.add("hidden");
    }
  }

  const submitBtn = document.getElementById('submitBtn');
  const successPopup = document.getElementById('successPopup');

  submitBtn.addEventListener('click', function() {
    if (!submitBtn.disabled) {
      successPopup.style.display = 'block';
    }
  });


    
  </script>
  <script src="javascript/adminPage.js"></script>
</body>

</html>