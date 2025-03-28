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

<body class="font-[Roboto] h-full flex flex-1 overflow-auto box-border bg-gray-100">

  <?php include("navigation/sidebar.php") ?>

  <section class="flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

    <?php include("navigation/header.php") ?>

    <main class="flex flex-col h-full overflow-auto">
      <div class="bg-white border border-solid border-black rounded-xl rounded-tr-none rounded-br-none m-3 px-6 py-5 overflow-auto">

        <!-- Write your module code in this box -->

      </div>
    </main>

    <?php include("navigation/footer.php") ?>

  </section>

</body>

<script src="javascript/index.js"></script>

</html>