<?php
// Start session to store user data
session_start();

$applicantID = isset($_SESSION['applicantID']) ? $_SESSION['applicantID'] : 1;
$applicantName = isset($_SESSION['applicantName']) ? $_SESSION['applicantName'] : "Ms. Galve-Abad";
$firstChoice = isset($_SESSION['firstChoice']) ? $_SESSION['firstChoice'] : "Bachelor of Science in Information Technology";
$secondChoice = isset($_SESSION['secondChoice']) ? $_SESSION['secondChoice'] : "Bachelor of Science in Civil Engineering";
$applicantType = isset($_SESSION['applicantType']) ? $_SESSION['applicantType'] : "Bachelor-Program";

// Check uploaded documents
include("php/getDocuments.php");
$documents = getDocuments('applicantID = ?', [$applicantID]);
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

  <?php include "components/navigation/sidebar.php" ?>

  <section class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

    <?php include "components/navigation/header.php" ?>

    <main class="flex flex-col h-full overflow-auto">
      <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">

        <!-- Checklist -->
        <div id="checklist-box" class="sticky top-0 right-0 float-right bg-[#7a20e0] text-[white] px-[25px] py-[20px] mt-0 mr-0 mb-[15px] ml-[15px] rounded-[8px] border-[1px] border-[solid] border-[black] text-[14px] w-max [transition:height_0.3s_ease,_padding_0.3s_ease] overflow-hidden">
          <!-- .checklist-header-->
          <div class="flex justify-between items-center gap-x-5 cursor-pointer" id="checklist-header">
            <!-- main div.inner-box .checklist-header h4-->
            <h4 class="pb-0 text-lg font-bold">Requirements</h4>
            <img id="chevron-icon" src="assets/chevron-up.png" class="w-[18px] h-[18px] filter brightness-0 invert rounded-[5px] p-[2px] [transition:transform_0.3s_ease] cursor-pointer hover:[transition:0.3s] hover:bg-[rgba(0,_0,_0,_0.3)]">
          </div>
          <ul class="list-none pl-0 m-0 transition-all duration-300 max-h-[500px] opacity-100 overflow-hidden" id="checklistContent">
            <?php
            $requirementsSet = $applicantType == "Bachelor-Program" ? file_get_contents("json/bachelorApplicant.json") : file_get_contents("json/graduateApplicant.json");
            $requirements = json_decode($requirementsSet, true);
            foreach ($requirements as $req) { ?>
              <li class="flex justify-between items-center mx-0 my-[6px] whitespace-nowrap" id="item-<?= $req['documentType'] ?>">
                <a class="no-underline text-[white] hover:underline" href="#" onclick="openSampleImage('<?= $req['documentType'] ?>'); return false;"><?= str_replace("-", " ", $req['documentType']) ?></a>
                <?php
                $hasDocument = false;
                $documentStatus = 'Not Uploaded';
                foreach ($documents as $doc) {
                  if ($doc['documentType'] == $req['documentType']) {
                    $hasDocument = true;
                    $documentStatus = $doc['documentStatus'];
                    break;
                  }
                }
                // Update the icon based on status
                if ($hasDocument) {
                  switch ($documentStatus) {
                    case 'Approved':
                      $statusIcon = 'Check-Icon.png';
                      break;
                    case 'Rejected':
                      $statusIcon = 'Wrong-Icon.png';
                      break;
                    case 'Pending':
                      $statusIcon = 'Info-Icon.png';
                      break;
                    default:
                      $statusIcon = 'Info-Icon.png';
                  }
                } else {
                  $statusIcon = 'Info-Icon.png';
                  $documentStatus = 'Not Uploaded';
                }
                ?>
                <img id="status-<?= $req['documentType'] ?>"
                  src="assets/<?= $statusIcon ?>"
                  class="w-[16px] h-[16px] ml-[35px]"
                  title="<?= $documentStatus ?>">
              <?php
            }
              ?>
          </ul>
        </div>

        <div class="ml-5">
          <!-- .name-card h1.applicant-name-->
          <h1 class="text-3xl font-extrabold mx-0 my-4">Welcome, <?= htmlspecialchars($applicantName); ?>!</h1>
          <p class="my-2.5 text-base">First Choice: <?= htmlspecialchars($firstChoice); ?></p>
          <p class="my-2.5 text-base">Second Choice: <?= htmlspecialchars($secondChoice); ?></p>
        </div>

        <h2 class="ml-5 text-2xl font-semibold my-4"><?= str_replace("-", " ", $applicantType); ?> Requirements</h2>

        <!-- The next php include is the documents part of the page -->

        <?php include "components/documents/getApplicantDocuments.php" ?>

        <!-- .submit-wrapper-->
        <div class="submit-wrapper flex justify-end">
          <!-- .submit-btn-->
          <button type="button" class="submit-btn cursor-not-allowed text-gray-500 border-2 border-[solid] border-[black] rounded-xl text-base font-bold px-7 py-3 mx-0 my-8 [transition:0.3s]" disabled>Submit</>
        </div>

      </div>
    </main>

    <?php include "components/navigation/footer.php" ?>

    <!-- Confirmation Popup -->
    <div class="popup hidden flex fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex-col items-center flex-[1]" id="confirmationPopup">
      <img class="w-16 mb-3" src="assets/confirm.png" alt="Confirm">
      <h2 class="m-0 text-2xl text-white">Submit Documents</h2>
      <p class="text-sm text-white">Are you sure you want to submit?</p>
      <div class="yes-no-buttons space-x-4 my-8 mx-8">
        <button class="no bg-[rgb(145,_29,_52)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 flex-1 px-8 py-3 ml-2 hover:bg-[#0C5AAD]">No</button>
        <button class="bg-[rgb(45,_174,_40)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 flex-1 px-8 py-3 mr-4 hover:bg-[#0C5AAD]" type="submit">Yes</button>
      </div>
    </div>

    <!-- Submit Popup -->
    <div class="popup hidden flex fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex-col items-center flex-[1]" id="successPopup">
      <img class="w-16 mb-3" src="assets/submit.png" alt="Success">
      <h2 class="m-0 text-2xl text-white">Success!</h2>
      <p class="text-sm text-white">Please wait for further instructions from the registrar.</p>
      <button class="to-application-tracking bg-[rgb(45,_174,_40)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 flex-1 px-8 py-3 m-8 hover:bg-[#0C5AAD]" type="submit">Proceed to Application Tracking</button>
    </div>

  </section>

</body>

<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="javascript/applicantPage.js"></script>
<script src="javascript/index.js"></script>

</html>