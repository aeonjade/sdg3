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
      <!-- .checklist-box minimized -->  
      <div id="checklist-box" class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">
        <!-- Checklist -->
        <div class="sticky top-0 right-0 float-right bg-purple-700 text-white px-6 py-5 rounded-lg border-2 border-black text-sm w-max transition-all overflow-hidden" id="checklistBox">
          <!-- .checklist header -->
          <div class="flex justify-between items-center mb-3">
            <h4 class="m-0 text-xl pb-3">Checklist</h4>
            <img src="assets/chevron-up.png" alt="Toggle" id="chevron-icon" class="w-4 h-4 filter brightness-0 invert cursor-pointer transform transition-transform duration-300"/>
          </div>
          <!-- .checklist -->
          <ul class="list-none pl-0 m-0 transition-opacity hidden" id="checklistContent">
            <?php foreach ($requirements as $req) {
              $docType = $req['documentType'];
              $isUploaded = isset($_FILES[$docType]) && $_FILES[$docType]['error'] === 0;
            ?>
              <li class="flex justify-between items-center my-2 whitespace-nowrap" id="item-<?= $docType ?>">
                <a class="text-white no-underline hover:underline cursor-pointer" href="#" onclick="openSampleImage('<?= $docType ?>'); return false;">• <?= str_replace("-", " ", $docType) ?></a>
                <?php if ($isUploaded): ?>
                  <!-- .icon check -->
                  <img src="assets/Check-Icon.png" class="w-4 h-4 ml-8">
                <?php else: ?>
                  <!-- . icon wrong -->
                  <img src="assets/Wrong-Icon.png" class="w-4 h-4 ml-8">
                <?php endif; ?>
              </li>
            <?php } ?>
          </ul>
        </div>

        <!-- Applicant Info -->
         <!-- .name-card -->
        <div class="ml-5">
          <!-- applicant-name -->
          <h1 class="text-3xl font-bold my-4"><?= htmlspecialchars($applicantName); ?>'s Application</h1>
          <p>First Choice: <?= htmlspecialchars($firstChoice); ?></p>
          <p>Second Choice: <?= htmlspecialchars($secondChoice); ?></p>
        </div>

        <!-- Document Set Title -->
         <!-- set -->
        <h2 class="ml-5 text-xl font-semibold my-4"><?= str_replace("-", " ", $applicantType); ?> Requirements</h2>

        <!-- Document Sections -->
        <?php include "components/documents/getAdminDocuments.php" ?>

        <!--Submit Popup-->
        <!-- .popup, h2, p, button -->
        <!--Submit Popup-->
        <div id="successPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
          <div class="bg-gradient-to-b from-purple-500 to-purple-700 p-8 rounded-2xl text-center shadow-xl text-white w-[90%] max-w-md">
            <img class="w-12 h-12 mx-auto mb-4" src="assets/check-icon.png" alt="Success Icon">
            <h2 class="text-2xl font-bold mb-2">Success</h2>
            <p class="text-sm mb-6">Please wait for further instructions from the registrar</p>
            <button class="bg-green-500 px-6 py-2 rounded-md text-white font-semibold hover:bg-green-600 transition">Back to Document Upload</button>
          </div>
        </div>
        
        <!-- Reject Message Pop-up -->
        <div id="rejectPopup" class="fixed inset-0 z-50 flex items-center justify-center hidden">
          <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-300 w-[350px]">
            <h2 class="text-lg font-medium mb-2">Reject Message</h2>
            <textarea id="rejectMessageInput" rows="4" class="w-full p-2 border rounded resize-none"></textarea>
            <div class="flex justify-end gap-2 mt-3">
              <button id="saveRejectBtn" class="bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600">Save</button>
              <button id="cancelRejectBtn" class="border px-4 py-1 rounded">Cancel</button>
            </div>
          </div>
        </div>


    </main>
    <?php //include "components/navigation/footer.php" ?>
  </section>

  <script src="javascript/adminPage.js"></script>
</body>

</html>