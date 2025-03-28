<?php
include("php/getDocuments.php");
// Start session to store user data
session_start();

$applicantID = isset($_SESSION['applicantID']) ? $_SESSION['applicantID'] : 001;
$applicantName = isset($_SESSION['applicantName']) ? $_SESSION['applicantName'] : "Ms. Galve-Abad";
$firstChoice = isset($_SESSION['firstChoice']) ? $_SESSION['firstChoice'] : "Bachelor of Science in Information Technology";
$secondChoice = isset($_SESSION['secondChoice']) ? $_SESSION['secondChoice'] : "Bachelor of Science in Civil Engineering";
$applicantType = isset($_SESSION['applicantType']) ? $_SESSION['applicantType'] : "Bachelor-Program";
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
  <link rel="stylesheet" href="css/applicantPage.css">
</head>

<body class="font-[Roboto] h-full flex flex-1 overflow-auto box-border bg-gray-100">

  <?php include "components/navigation/sidebar.php" ?>

  <section class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

    <?php include "components/navigation/header.php" ?>

    <main class="flex flex-col h-full overflow-auto">
      <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">

        <form action="adminPage.php" method="POST" enctype="multipart/form-data">
          <!-- Checklist -->
          <div class="checklist-box">
            <div class="checklist-header">
              <h4>Requirements</h4>
              <img onclick="toggleChecklist()" src="assets/chevron-up.png" alt="Toggle" class="chevron-icon">
            </div>
            <ul class="checklist">
              <?php

              $requirementsSet = $applicantType == "Bachelor-Program" ? file_get_contents("json/bachelorApplicant.json") : file_get_contents("json/graduateApplicant.json");
              $requirements = json_decode($requirementsSet, true);

              foreach ($requirements as $req) { ?>
                <li id="item-<?= $req['documentType'] ?>">
                  <a href="#anchor-<?= $req['documentType'] ?>"><?= str_replace("-", " ", $req['documentType'])  ?></a>
                  <img src="assets/Info-Icon.png" class="icon info">
                  <img src="assets/Check-Icon.png" class="icon check" style="display: none;">
                </li>
              <?php
              }
              ?>
            </ul>
          </div>

          <div class="name-card">
            <h1 class="applicant-name">Welcome, <?= htmlspecialchars($applicantName); ?>!</h1>
            <p>First Choice: <?= htmlspecialchars($firstChoice); ?></p>
            <p>Second Choice: <?= htmlspecialchars($secondChoice); ?></p>
          </div>

          <h2><?= str_replace("-", " ", $applicantType); ?> Requirements</h2>

          <!-- Document Sections -->

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

          <div class="submit-wrapper">
            <button type="button" class="submit-btn" disabled onclick="showConfirm()">Submit</button>
          </div>

        </form>

      </div>
    </main>

    <?php include "components/navigation/footer.php" ?>

    <!--Confirmation Popup-->
    <div class="popup" id="confirmationPopup">
      <img src="assets/confirm.png" alt="Confirm">
      <h2>Submit Documents</h2>
      <p>Are you sure you want to submit?</p>
      <div class="yes-no-buttons">
        <button class="no" onclick="closeConfirm()">No</button>
        <button type="submit" onclick="showPopup()">Yes</button>
      </div>
    </div>
    <!--Submit Popup-->
    <div class="popup" id="successPopup">
      <img src="assets/submit.png" alt="Success">
      <h2>Success!</h2>
      <p>Please wait for further instructions from the registrar.</p>
      <button class="to-application-tracking" type="submit" onclick="submitForm()">Proceed to Application Tracking</button>
    </div>

  </section>

</body>

<script src="javascript/index.js"></script>
<script src="javascript/document-upload.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

</html>