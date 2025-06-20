<?php
session_start();

// Get applicantID from GET parameter
$applicantID = isset($_GET['applicantID']) ? intval($_GET['applicantID']) : 0;
if ($applicantID <= 0) {
  die("Invalid applicant ID.");
}

// Get applicant details
include("php/getApplicants.php");
$applicant = getApplicants('applicantID = ?', [$applicantID])[0];

$applicantID = $_SESSION['applicantID'] = $applicant['applicantID'];
$applicantName = $_SESSION['applicantName'] = $applicant['applicantName'];
$firstChoice = $_SESSION['firstChoice'] = $applicant['firstChoice'];
$secondChoice = $_SESSION['secondChoice'] = $applicant['secondChoice'];
$applicantType = $_SESSION['applicantType'] = $applicant['applicantType'];
$requirementsStatus = $_SESSION['requirementsStatus'] = $applicant['requirementsStatus'];

if ($requirementsStatus == 'submitted') {
  /*This shit is for JSON because if you don't include JSON file first, the admin page will have an error.
Damn what an english speaking*/
  $requirementsSet = $applicantType == "Bachelor-Program"
    ? file_get_contents("json/bachelorApplicant.json")
    : file_get_contents("json/graduateApplicant.json");

  $requirements = json_decode($requirementsSet, true);

  // Check uploaded documents
  include("php/getDocuments.php");
  $documents = getDocuments('applicantID = ?', [$applicantID]);
}
?>

<!DOCTYPE html>
<html lang="en" class="font-[Roboto] h-full flex flex-1 overflow-auto box-border">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Requirements</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
  </style>
</head>

<body class="font-[Roboto] h-full flex flex-1 overflow-auto box-border bg-gray-100" data-applicant-id="<?= $applicantID ?>">

  <?php include("components/navigation/sidebar.php") ?>

  <section class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

    <?php include "components/navigation/header.php" ?>

    <main class="flex flex-col h-full overflow-auto">
      <?php
      if ($requirementsStatus == 'accomplished') {
      ?>
        <div class="flex flex-col bg-white border border-solid border-black rounded-3xl justify-center m-auto py-12 px-14 items-center gap-y-2">
          <img class="w-16 mb-3" src="assets/submit.png" alt="Success">
          <div class="flex flex-col gap-y-6 items-center">
            <h1 class="text-5xl font-bold">Success!</h1>
            <p>The document requirements of this applicant have been approved.</p>
            <button class="font-semibold bg-[rgb(45,_174,_40)] border-[black] text-[white] hover:bg-[#0C5AAD] transition duration-300 cursor-pointer border-spacing-1 border-[solid] rounded-2xl px-6 py-2 w-full" onclick="toApplicantList()">Go Back to Applicant List</button>
          </div>
        </div>
      <?php
      } else if ($requirementsStatus == 'pending' || $requirementsStatus == 'incomplete') {
      ?>
        <div class="flex flex-col bg-white border border-solid border-black rounded-3xl justify-center m-auto py-12 px-14 items-center gap-y-2">
          <img class="w-16 mb-3" src="assets/confirm.png" alt="Info">
          <div class="flex flex-col gap-y-6 items-center">
            <h1 class="text-5xl font-bold">Oops!</h1>
            <p>This applicant has not submitted the required documents yet.</p>
            <button class="font-semibold bg-[#0C5AAD] border-[black] text-[white] hover:bg-[rgb(45,_174,_40)] transition duration-300 cursor-pointer border-spacing-1 border-[solid] rounded-2xl px-6 py-2 w-full" onclick="toApplicantList()">Go Back to Applicant List</button>
          </div>
        </div>
      <?php
      } else {
      ?>
        <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">
          <!-- Checklist -->
          <div id="checklist-box" class="sticky top-0 right-0 float-right bg-[#7a20e0] text-[white] px-[25px] py-[20px] mt-0 mr-0 mb-[15px] ml-[15px] rounded-[8px] border-[1px] border-[solid] border-[black] text-[14px] w-max [transition:height_0.3s_ease,_padding_0.3s_ease] overflow-hidden">
            <div class="flex justify-between items-center gap-x-5 cursor-pointer" id="checklist-header">
              <h4 class="pb-0 text-lg font-bold">Checklist</h4>
              <img id="chevron-icon"
                src="assets/chevron-up.png"
                alt="Toggle"
                class="w-[18px] h-[18px] filter brightness-0 invert rounded-[5px] p-[2px] transition-transform duration-300 hover:bg-[rgba(0,_0,_0,_0.3)]" />
            </div>
            <ul class="list-none pl-0 m-0 transition-all duration-300 max-h-[500px] opacity-100 overflow-hidden" id="checklistContent">
              <?php
              foreach ($requirements as $req) {
                $docType = $req['documentType'];
                $isUploaded = false;

                // Check if document exists in uploaded documents
                foreach ($documents as $doc) {
                  if ($doc['documentType'] === $docType) {
                    $isUploaded = true;
                    break;
                  }
                }
              ?>
                <li class="flex justify-between items-center mx-0 my-[6px] whitespace-nowrap" id="item-<?= $docType ?>">
                  <a class="no-underline text-[white] hover:underline"
                    href="#anchor-<?= $docType ?>"
                    onclick="event.preventDefault(); document.getElementById('anchor-<?= $docType ?>').scrollIntoView({behavior: 'smooth'});">
                    <?= str_replace("-", " ", $docType) ?>
                  </a>
                  <img src="assets/
                <?php
                if (!$isUploaded) {
                  echo 'Wrong-Icon.png';
                } else {
                  $status = 'Pending';
                  foreach ($documents as $doc) {
                    if ($doc['documentType'] === $docType) {
                      $status = $doc['documentStatus'];
                      break;
                    }
                  }
                  if ($status === 'Approved') echo 'Check-Icon.png';
                  else if ($status === 'Rejected') echo 'Wrong-Icon.png';
                  else echo 'Info-Icon.png';
                } ?>"
                    class="w-4 h-4 ml-8"
                    alt="<?= $isUploaded ? $status : 'Not uploaded' ?>">
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
          <h2 class="ml-5 text-2xl font-semibold my-4"><?= str_replace("-", " ", $applicantType); ?> Requirements</h2>

          <!-- Document Sections -->
          <?php include "components/documents/getAdminDocuments.php" ?>

          <!--Submit Popup-->
          <!-- .popup, h2, p, button -->
          <!--Submit Popup-->
          <div id="successPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-gradient-to-b from-purple-500 to-purple-700 p-8 rounded-2xl text-center shadow-xl text-white w-[90%] max-w-md">
              <img class="w-12 h-12 mx-auto mb-4" src="assets/check-icon.png" alt="Success Icon">
              <h2 class="text-2xl font-bold mb-2">Success!</h2>
              <button class="bg-green-500 px-6 py-2 rounded-md text-white font-semibold hover:bg-green-600 transition">Back to Applicants List</button>
            </div>
          </div>

          <!-- Reject Message Pop-up -->
          <div id="rejectPopup" class="fixed inset-0 z-50 flex items-center justify-center hidden">
            <div class="bg-white p-4 rounded-xl shadow-lg border border-gray-300 w-[350px]">
              <h2 class="text-lg font-medium mb-2">Reject Message</h2>
              <textarea id="rejectMessageInput" rows="4" class="w-full p-2 border rounded resize-none"></textarea>
              <div class="flex justify-end gap-2 mt-3">
                <button id="cancelRejectBtn" class="border px-4 py-1 rounded">Cancel</button>
                <button id="saveRejectBtn" class="bg-green-500 text-white px-4 py-1 rounded hover:bg-green-600">Save</button>
              </div>
            </div>
          </div>
        </div>
      <?php
      }
      ?>
    </main>
    <?php include "components/navigation/footer.php" ?>
  </section>

  <script src="javascript/adminPage.js"></script>
</body>

</html>