// Define maximum file size (e.g., 5MB)
const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes

// Store uploaded files for view/download use
const uploadedFiles = {};

function triggerUpload(id) {
  document.getElementById(id).click();
}

function removeFile(id) {
  const preview = document.getElementById("preview-" + id);

  const container = preview.closest(".upload-container");
  if (!container) {
    console.error("Upload container not found");
    return;
  }

  // Backend dynamic file remove
  const formData = new FormData();
  formData.append("documentType", id);

  fetch("php/removeDocument.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then(() => {

      
      // Hide preview
      preview.remove(); // Remove instead of hiding

      // Show upload button
      const uploadBtn = container.querySelector(".upload-btn");
      if (uploadBtn) {
        uploadBtn.classList.remove("hidden");
      }

      // Reset checklist icon
      const statusIcon = document.getElementById(`status-${id}`);
      if (statusIcon) {
        statusIcon.src = "assets/Info-Icon.png";
      }

      // Clear from memory
      delete uploadedFiles[id];

      // Re-check if all files are uploaded
      checkAllFilesUploaded();
    })
    .catch((error) => {
      console.error("Error removing file:", error);
    });
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

document.querySelectorAll(".file-input").forEach((input) => {
  input.addEventListener("change", function () {
    const id = this.id;
    const file = this.files[0];
    const container = input.closest(".upload-container");
    const applicantID = container.dataset.applicantId; // Get applicantID from data attribute
    const uploadBtn = container.querySelector(".upload-btn");
    const errorMessage = container.querySelector(".error-message");

    // Clear previous error
    errorMessage.textContent = "";
    errorMessage.classList.add("hidden");

    if (file) {
      // Validate file format and size
      if (!isValidFile(id, file)) {
        errorMessage.textContent = `Invalid file format. Allowed formats: ${allowedFormats[
          id
        ].join(", ")}`;
        errorMessage.classList.remove("hidden");
        return;
      } else if (file.size > MAX_FILE_SIZE) {
        errorMessage.textContent = `File size exceeds the limit of ${
          MAX_FILE_SIZE / 1024 / 1024
        } MB.`;
        errorMessage.classList.remove("hidden");
        return;
      }

      // Create FormData for upload
      let formData = new FormData();
      formData.append("file", file);
      formData.append("applicantID", applicantID); // Replace with actual applicant ID
      formData.append("documentName", file.name);
      formData.append("documentType", id);

      // Upload file
      fetch("php/uploadDocument.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json()) // Change to json() to parse the response
        .then((data) => {
          // Create preview element if it doesn't exist
          let preview = document.getElementById("preview-" + id);
          if (!preview) {
            preview = document.createElement("div");
            preview.id = "preview-" + id;
            preview.className =
              "file-preview flex justify-between gap-[15px] border-[2px] border-[solid] border-[black] p-[10px] rounded-[15px] mt-[10px] min-w-[300px] max-w-[750px]";
            preview.innerHTML = `
            <span class="file-name font-bold underline break-all"></span>
            <div class="file-actions flex items-center gap-3">
              <img src="assets/Download-Icon.png" class="document-requirements-icon w-5 h-5 cursor-pointer" alt="Download" title="Download">
              <span class="view-text text-[blue] cursor-pointer">View</span>
              <span class="remove-text text-[red] cursor-pointer" onclick="removeFile('${id}')">Remove</span>
            </div>
          `;
            container.appendChild(preview);
          }

          // Update preview
          preview.querySelector(".file-name").textContent = file.name;
          preview.classList.remove("hidden");

          // Store file path from server response
          const filePath = data.filePath;

          // Set up view handler to open document from server
          const viewText = preview.querySelector(".view-text");
          if (viewText) {
            viewText.onclick = () => {
              window.open(`documents/${data.filename}`, '_blank');
            };
          }

          // Hide upload button
          if (uploadBtn) {
            uploadBtn.classList.add("hidden");
          }

          // Update checklist icons
          const statusIcon = document.getElementById(`status-${id}`);
          if (statusIcon) {
            statusIcon.src = "assets/Check-Icon.png";
          }

          // Store file for preview/download
          uploadedFiles[id] = file;

          // Set up download handlers
          const downloadIcon = preview.querySelector(
            ".document-requirements-icon"
          );

          if (viewText) {
            viewText.onclick = () => {
              const url = URL.createObjectURL(file);
              window.open(url, "_blank");
              URL.revokeObjectURL(url);
            };
          }

          if (downloadIcon) {
            downloadIcon.onclick = () => {
              const url = URL.createObjectURL(file);
              const link = document.createElement("a");
              link.href = url;
              link.download = file.name;
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);
              URL.revokeObjectURL(url);
            };
          }

          // Check if all files are uploaded
          checkAllFilesUploaded();
        })
        .catch((error) => {
          console.error("Upload error:", error);
          errorMessage.textContent = "Error uploading file. Please try again.";
          errorMessage.classList.remove("hidden");
        });
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

// Function to open the sample image in a modal - Checklist
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
    modalContent.style.padding = "10px";
    modalContent.style.borderRadius = "8px";
    modalContent.style.maxWidth = "70%";
    modalContent.style.maxHeight = "70%";
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
    closeButton.style.top = "10px";
    closeButton.style.right = "10px";
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

//Add eventlistener dun sa mga tinanggal ko yung onClick
document.addEventListener("DOMContentLoaded", function () {
  // Toggle Checklist Icon
  const chevronIcon = document.getElementById("chevron-icon");
  const checklistContent = document.getElementById("checklistContent");

  if (chevronIcon) {
    chevronIcon.addEventListener("click", () => {
      checklistContent.classList.toggle("hidden");
      chevronIcon.classList.toggle("rotate-180");
    });
  }

  // Submit Button
  const submitBtn = document.querySelector(".submit-btn");
  if (submitBtn) {
    submitBtn.addEventListener("click", showConfirm);
  }

  // Confirmation Popup Buttons
  const noBtn = document.querySelector(".no");
  if (noBtn) {
    noBtn.addEventListener("click", closeConfirm);
  }

  const yesBtn = document.querySelector(
    "#confirmationPopup button[type='submit']"
  );
  if (yesBtn) {
    yesBtn.addEventListener("click", showPopup);
  }

  // Success Popup Button
  const proceedBtn = document.querySelector(".to-application-tracking");
  if (proceedBtn) {
    proceedBtn.addEventListener("click", submitForm);
  }
});

function submitForm() {
  document.querySelector("form").submit();
}

function showConfirm() {
  document.getElementById("confirmationPopup").style.display = "flex";
  document.querySelector(".inner-box")?.classList.add("overflow-hidden");
  document.querySelector("main")?.classList.add("blur-background"); //pag wala yung ?, di gagana yung blur bg
}

function closeConfirm() {
  document.getElementById("confirmationPopup").style.display = "none";
  document.querySelector(".inner-box")?.classList.remove("overflow-hidden");
  document.querySelector("main")?.classList.remove("blur-background");
}

function showPopup() {
  document.getElementById("confirmationPopup").style.display = "none";
  document.getElementById("successPopup").style.display = "flex";
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle upload buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('upload-btn')) {
            const id = e.target.dataset.id;
            triggerUpload(id);
        }
    });

    // Handle view buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('view-text')) {
            const preview = e.target.closest('.file-preview');
            const id = preview.id.replace('preview-', '');
            const file = uploadedFiles[id];
            if (file) {
                const url = URL.createObjectURL(file);
                window.open(url, '_blank');
                URL.revokeObjectURL(url);
            }
        }
    });

    // Handle download buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('document-requirements-icon')) {
            const preview = e.target.closest('.file-preview');
            const id = preview.id.replace('preview-', '');
            const file = uploadedFiles[id];
            if (file) {
                const url = URL.createObjectURL(file);
                const link = document.createElement('a');
                link.href = url;
                link.download = file.name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            }
        }
    });
});
