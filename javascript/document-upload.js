// Define maximum file size (e.g., 5MB)
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes

// Store uploaded files for view/download use
const uploadedFiles = {};

function triggerUpload(id) {
  document.getElementById(id).click();
}

function removeFile(id) {
  const input = document.getElementById(id);
  const preview = document.getElementById("preview-" + id);
  const container = input.closest(".upload-container");
  const uploadBtn = container.querySelector(".upload-btn");
  const errorMessage = container.querySelector(".error-message");

  // fetch remove
  let formData = new FormData();
  formData.append("documentType", id);

  fetch("php/removeDocument.php", {
    method: "POST",
    body: formData,
  }).then((response) => response.text());

  // Reset file input
  input.value = "";
  preview.classList.add("hidden");

  // Show upload button again (using opacity method)
  uploadBtn.style.display = "block";
  uploadBtn.style.pointerEvents = "auto";
  uploadBtn.style.height = "";

  // Hide error message
  errorMessage.classList.add("hidden");

  // Reset checklist icons
  const checklistIcon = document.querySelector(`#item-${id} .info`);
  const checkIcon = document.querySelector(`#item-${id} .check`);
  if (checklistIcon && checkIcon) {
    checklistIcon.style.display = "inline";
    checkIcon.style.display = "none";
  }

  // Remove from memory
  delete uploadedFiles[id];

  // Check if all files are uploaded
  checkAllFilesUploaded();
}

function checkAllFilesUploaded() {
  const checkIcons = document.querySelectorAll(".check");
  const submitBtn = document.querySelector(".submit-btn");
  const allUploaded = Array.from(checkIcons).every(
    (icon) => icon.style.display === "inline"
  );
  submitBtn.disabled = !allUploaded;
  if (allUploaded) {
    submitBtn.classList.add("enabled");
  } else {
    submitBtn.classList.remove("enabled");
  }
}

const allowedFormats = {
  "CTC-G11": [".pdf"],
  "CTC-G12": [".pdf"],
  "CTC-ID": [".pdf"],
  "Birth-Certificate": [".pdf"],
  "Applicant-Voter-Certificate": [".pdf"],
  "Parent-Voter-Certificate": [".pdf"],
  "ID-Picture": [".jpg", ".png"],
};

function isValidFile(id, file) {
  const extension = file.name.split(".").pop().toLowerCase();
  return allowedFormats[id].includes("." + extension);
}

function toggleChecklist() {
  const checklistBox = document.querySelector(".checklist-box");
  const chevronIcon = document.querySelector(".chevron-icon");

  checklistBox.classList.toggle("minimized");

  // Rotate the chevron icon
  if (checklistBox.classList.contains("minimized")) {
    chevronIcon.style.transform = "rotate(180deg)";
  } else {
    chevronIcon.style.transform = "rotate(0deg)";
  }
}

document.querySelectorAll(".file-input").forEach((input) => {
  input.addEventListener("change", function () {
    const id = this.id;
    const file = this.files[0];
    const preview = document.getElementById("preview-" + id);
    const container = input.closest(".upload-container");
    const uploadBtn = container.querySelector(".upload-btn");
    const errorMessage = container.querySelector(".error-message");

    // Clear previous error
    errorMessage.textContent = "";
    errorMessage.classList.remove("visible");

    if (file) {
      if (!isValidFile(id, file)) {
        errorMessage.textContent = `Invalid file format. Allowed formats: ${allowedFormats[
          id
        ].join(", ")}`;
        errorMessage.style.display = "block";
        return;
      } else if (file.size > MAX_FILE_SIZE) {
        errorMessage.textContent = `File size exceeds the limit of ${
          MAX_FILE_SIZE / 1024 / 1024
        } MB.`;
        errorMessage.style.display = "block";
        return;
      }

      // fetch upload
      let formData = new FormData();
      formData.append("file", file);
      formData.append("applicantID", 1);
      formData.append("documentName", file.name);
      formData.append("documentType", id);

      fetch("php/uploadDocument.php", {
        method: "POST",
        body: formData,
      }).then((response) => response.text());

      // Show filename and preview
      preview.querySelector(".file-name").textContent = file.name;
      preview.classList.remove("hidden");

      // Hide upload button using opacity
      uploadBtn.style.display = "none";
      uploadBtn.style.pointerEvents = "none";
      uploadBtn.style.height = "0";

      // Update checklist icons
      const checklistIcon = document.querySelector(`#item-${id} .info`);
      const checkIcon = document.querySelector(`#item-${id} .check`);
      if (checklistIcon && checkIcon) {
        checklistIcon.style.display = "none";
        checkIcon.style.display = "inline";
      }

      // Save file for later use (view/download)
      uploadedFiles[id] = file;

      // Set up download
      const downloadIcon = preview.querySelector(".document-requirements-icon");
      downloadIcon.onclick = () => {
        const fileToDownload = uploadedFiles[id];
        const url = URL.createObjectURL(fileToDownload);
        const link = document.createElement("a");
        link.href = url;
        link.download = fileToDownload.name;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      };

      // Set up view
      const viewText = preview.querySelector(".view-text");
      viewText.onclick = () => {
        const fileToView = uploadedFiles[id];
        const url = URL.createObjectURL(fileToView);
        window.open(url, "_blank");
      };

      // Check if all files are uploaded
      checkAllFilesUploaded();
    }
  });
});

function handleFileChange(id, documentName) {
  const input = document.getElementById(id);
  if (!input) return;

  const file = input.files[0];
  const preview = document.getElementById("preview-" + id);
  const container = input.closest(".upload-container");
  const uploadBtn = container.querySelector(".upload-btn");

  // Show filename and preview
  preview.querySelector(".file-name").textContent = documentName;
  preview.classList.remove("hidden");

  // Hide upload button using opacity
  uploadBtn.style.display = "none";
  uploadBtn.style.pointerEvents = "none";
  uploadBtn.style.height = "0";

  // Update checklist icons
  const checklistIcon = document.querySelector(`#item-${id} .info`);
  const checkIcon = document.querySelector(`#item-${id} .check`);
  if (checklistIcon && checkIcon) {
    checklistIcon.style.display = "none";
    checkIcon.style.display = "inline";
  }

  // Save file for later use (view/download)
  uploadedFiles[id] = file;

  // Set up download
  const downloadIcon = preview.querySelector(".document-requirements-icon");
  downloadIcon.onclick = () => {
    const fileToDownload = uploadedFiles[id];
    const url = URL.createObjectURL(fileToDownload);
    const link = document.createElement("a");
    link.href = url;
    link.download = fileToDownload.name;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  };

  // Set up view
  const viewText = preview.querySelector(".view-text");
  viewText.onclick = () => {
    const fileToView = uploadedFiles[id];
    const url = URL.createObjectURL(fileToView);
    window.open(url, "_blank");
  };

  // Check if all files are uploaded
  checkAllFilesUploaded();
}

// Define sample image paths (Ensure images exist in the "samples" folder)
const sampleImages = {
  "CTC-G11": "assets/samples/CTC-G11.jpg",
  "CTC-G12": "assets/samples/CTC-G12.jpg",
  "CTC-ID": "assets/samples/CTC-ID.jpg",
  "Birth-Certificate": "assets/samples/Birth-Certificate.jpg",
  "Applicant-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "Parent-Voter-Certificate": "assets/samples/Parent-Voter-Certificate.jpg",
  "ID-Picture": "assets/samples/ID-Picture.jpg",
};

// Function to open the sample image in a modal
function openSampleImage(documentType) {
  if (sampleImages[documentType]) {
    // Create modal elements
    const modal = document.createElement("div");
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";

    const modalContent = document.createElement("div");
    modalContent.style.backgroundColor = "white";
    modalContent.style.padding = "10px"; // reduce padding
    modalContent.style.borderRadius = "8px";
    modalContent.style.maxWidth = "70%"; // reduce max width
    modalContent.style.maxHeight = "70%"; // reduce max height
    modalContent.style.overflow = "auto";
    modalContent.style.position = "relative";

    const img = document.createElement("img");
    img.src = sampleImages[documentType];
    img.alt = `Sample ${documentType}`;
    img.style.maxWidth = "90%";
    img.style.maxHeight = "90%";
    img.style.display = "block";
    img.style.margin = "0 auto";

    const closeButton = document.createElement("button");
    closeButton.textContent = "X";
    closeButton.style.position = "fixed";
    closeButton.style.top = "10px"; // reduce top spacing
    closeButton.style.right = "10px"; // reduce right spacing
    closeButton.style.background = "transparent";
    closeButton.style.color = "white";
    closeButton.style.border = "none";
    closeButton.style.fontSize = "20px";
    closeButton.style.cursor = "pointer";
    closeButton.style.zIndex = "1001";

    closeButton.addEventListener("click", () => {
      document.body.removeChild(modal);
    });

    // Append elements to modal content
    modalContent.appendChild(img);

    // Append modal content to modal
    modal.appendChild(modalContent);
    modal.appendChild(closeButton);

    // Append modal to body
    document.body.appendChild(modal);
  } else {
    alert("Sample image not available.");
  }
}

// Attach event listeners to requirement titles
document.addEventListener("DOMContentLoaded", function () {
  const requirementLinks = document.querySelectorAll(".checklist li a");
  requirementLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault(); // Prevent default link behavior
      const documentType = this.parentElement.id.replace("item-", ""); // Extract document type
      openSampleImage(documentType); // Open image in new tab
    });
  });
});

function submitForm() {
  // Actually submit the form
  document.querySelector("form").submit();
}

function showConfirm() {
  document.getElementById("confirmationPopup").style.display = "flex";
  document.querySelector(".inner-box").style.overflow = "hidden";
  document.querySelector("main").classList.add("blur-background");
}
function closeConfirm() {
  document.getElementById("confirmationPopup").style.display = "none";
  document.querySelector(".inner-box").style.overflow = "auto";
  document.querySelector("main").classList.remove("blur-background");
}

function showPopup() {
  document.getElementById("confirmationPopup").style.display = "none";
  document.getElementById("successPopup").style.display = "flex";
}
