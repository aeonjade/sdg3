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

      <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto h-full">
        <h2 class="text-2xl font-bold mb-4">Applicant List</h2>
        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-4 py-2 border text-left">Name</th>
              <th class="px-4 py-2 border text-left">Type</th>
              <th class="px-4 py-2 border text-left">First Choice</th>
              <th class="px-4 py-2 border text-left">Second Choice</th>
              <th class="px-4 py-2 border text-left">Requirements Status</th>
              <th class="px-4 py-2 border text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            include("php/getApplicants.php");
            $allApplicants = getApplicants();
            foreach ($allApplicants as $app) {
              echo "<tr class='hover:bg-gray-100'>";
              echo "<td class='px-4 py-2 border'>{$app['applicantName']}</td>";
              echo "<td class='px-4 py-2 border'>{$app['applicantType']}</td>";
              echo "<td class='px-4 py-2 border'>{$app['firstChoice']}</td>";
              echo "<td class='px-4 py-2 border'>{$app['secondChoice']}</td>";
              echo "<td class='px-4 py-2 border'>" . ucfirst($app['requirementsStatus']) . "</td>";
              echo "<td class='px-4 py-2 border'>
                      <a href='admin-documents.php?applicantID={$app['applicantID']}' class='bg-purple-700 text-white px-3 py-1 rounded hover:bg-purple-900 transition'>View</a>
                    </td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

    </main>
    <?php include "components/navigation/footer.php" ?>
  </section>

  <script src="javascript/adminPage.js"></script>
</body>

</html>