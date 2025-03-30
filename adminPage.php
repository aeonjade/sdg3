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
<html lang="en" class="font-[Roboto] h-full flex flex-1 overflow-auto box-border">

<head>
  <meta charset="UTF-8">
  <title>Document Requirements</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
  </style>
  <!--<link rel="stylesheet" href="css/adminPage.css">-->
</head>

<body class="font-[Roboto] h-full flex flex-1 overflow-auto box-border bg-gray-100">

  <?php include("components/navigation/sidebar.php") ?>

  <section class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

    <?php include "components/navigation/header.php" ?>

    <main class="flex flex-col h-full overflow-auto">
      <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">
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
        <?php include "components/documents/getAdminDocuments.php" ?>

        <!--Submit Popup-->
        <div class="popup" id="successPopup" style="display: none;">
          <img class="w-12 mb-3" src="assets/check-icon.png" alt="Success">
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
    <?php include "components/navigation/footer.php" ?>
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