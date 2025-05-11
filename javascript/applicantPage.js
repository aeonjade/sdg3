const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
const uploadedFiles = {};
const allowedFormats = {
  "CTC-G11": [".pdf"],
  "CTC-G12": [".pdf"],
  "CTC-ID": [".pdf"],
  "Birth-Certificate": [".pdf"],
  "Applicant-Voter-Certificate": [".pdf"],
  "Parent-Voter-Certificate": [".pdf"],
  "ID-Picture": [".jpg", ".png"],
};

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

function openSampleImage(documentType) {
  if (sampleImages[documentType]) {
    // Create modal elements
    const modal = document.createElement("div");
    modal.className =
      "popup fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex flex-col items-center flex-[1] z-50";

    // Create modal content
    modal.innerHTML = `
        <div class="relative w-full max-w-3xl">
          <h2 class="m-0 text-2xl text-white mb-4">${documentType}</h2>
          <img 
            src="${sampleImages[documentType]}" 
            alt="Sample ${documentType}" 
            class="max-w-full max-h-[70vh] object-contain rounded-lg"
          >
          <button 
            class="absolute -top-8 -right-8 bg-[rgb(145,_29,_52)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 px-4 py-2 hover:bg-[#0C5AAD]">
            Close
          </button>
        </div>
      `;

    // Add click handler to close button
    const closeButton = modal.querySelector("button");
    closeButton.addEventListener("click", () => {
      document.body.removeChild(modal);
      document.body.removeChild(overlay);
    });

    // Add dark overlay
    const overlay = document.createElement("div");
    overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-40";
    document.body.appendChild(overlay);

    // Add modal to body
    document.body.appendChild(modal);
  } else {
    alert("Sample image not available.");
  }
}

function createConfirmationModal(title, message, onConfirm) {
  const modal = document.createElement("div");
  modal.className =
    "popup fixed top-2/4 left-2/4 -translate-x-1/2 -translate-y-1/2 bg-[linear-gradient(to_bottom,_#b57ee4,_#a56ee0)] px-16 py-14 text-center rounded-3xl [box-shadow:0_0px_10px_rgba(0,_0,_0,_0.2)] flex flex-col items-center flex-[1] z-50";

  modal.innerHTML = `
      <img class="w-16 mb-3" src="assets/confirm.png" alt="Confirm">
      <h2 class="m-0 text-2xl text-white">${title}</h2>
      <p class="text-sm text-white">${message}</p>
      <div class="yes-no-buttons space-x-4 my-8 mx-8">
        <button class="no bg-[rgb(145,_29,_52)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 flex-1 px-8 py-3 ml-2 hover:bg-[#0C5AAD]">No</button>
        <button class="yes bg-[rgb(45,_174,_40)] border-[black] text-[white] cursor-pointer border-spacing-1 border-[solid] rounded-xl text-base font-bold transition duration-300 flex-1 px-8 py-3 mr-4 hover:bg-[#0C5AAD]">Yes</button>
      </div>
    `;

  const overlay = document.createElement("div");
  overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-40";

  document.body.appendChild(overlay);
  document.body.appendChild(modal);

  modal.querySelector(".no").addEventListener("click", () => {
    document.body.removeChild(modal);
    document.body.removeChild(overlay);
  });

  modal.querySelector(".yes").addEventListener("click", () => {
    onConfirm();
    document.body.removeChild(modal);
    document.body.removeChild(overlay);
  });
}

function isValidFile(id, file) {
  const extension = "." + file.name.split(".").pop().toLowerCase();
  return allowedFormats[id]?.includes(extension);
}

function removeFile(id) {
  const preview = document.getElementById("preview-" + id);
  const container = preview?.closest(".upload-container");

  if (!container) {
    console.error("Upload container not found");
    return;
  }

  const applicantID = container.dataset.applicantId;
  const rawId = id.replace("input-", ""); // Clean the ID

  const formData = new FormData();
  formData.append("documentType", rawId);
  formData.append("applicantID", applicantID);

  fetch("php/removeDocument.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Remove preview
        preview.remove();

        // Create new input and button
        const input = document.createElement("input");
        input.type = "file";
        input.name = rawId;
        input.id = "input-" + rawId;
        input.className = "file-input hidden";
        input.accept = allowedFormats[rawId]?.join(",") || "";
        input.setAttribute("data-applicant-id", applicantID);

        const button = document.createElement("button");
        button.type = "button";
        button.id = "button-" + rawId;
        button.className =
          "upload-btn block bg-[#7213D0] border-2 border-[solid] border-[black] text-[white] rounded-xl text-base font-bold px-16 py-1 mx-0 my-4 hover:bg-[white] hover:text-[black] hover:cursor-pointer hover:[transition:0.3s]";
        button.textContent = "Upload";

        // Add elements to container
        container.appendChild(input);
        container.appendChild(button);

        // Add event listeners
        button.addEventListener("click", () => {
          input.click();
        });

        input.addEventListener("change", function () {
          const file = this.files[0];
          if (!file) return;

          // Validate file format and size
          if (!isValidFile(rawId, file)) {
            const errorMessage = container.querySelector(".error-message");
            errorMessage.textContent = `Invalid file format. Allowed formats: ${allowedFormats[
              rawId
            ].join(", ")}`;
            errorMessage.classList.remove("hidden");
            return;
          }

          if (file.size > MAX_FILE_SIZE) {
            const errorMessage = container.querySelector(".error-message");
            errorMessage.textContent = `File size exceeds limit of ${
              MAX_FILE_SIZE / 1024 / 1024
            }MB`;
            errorMessage.classList.remove("hidden");
            return;
          }

          // Create FormData
          const formData = new FormData();
          formData.append("file", file);
          formData.append("applicantID", applicantID);
          formData.append("documentType", rawId);
          formData.append("documentName", file.name);

          // Upload file
          fetch("php/uploadDocument.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Create new preview
                const preview = document.createElement("div");
                preview.id = `preview-${rawId}`;
                preview.className =
                  "file-preview flex justify-between gap-[15px] border-[2px] border-[solid] border-[black] p-[10px] rounded-[15px] mt-[10px] min-w-[300px] max-w-[750px]";
                preview.innerHTML = `
              <span class="file-name font-bold underline break-all">${data.filename.replace(/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/, '')}</span>
              <div class="file-actions flex items-center gap-3">
                <img src="assets/Download-Icon.png" class="document-requirements-icon w-5 h-5 cursor-pointer" alt="Download" title="Download" data-filename="${data.filename}">
                <span class="view-text text-[blue] cursor-pointer" data-filename="${data.filename}">View</span>
                <span class="remove-text text-[red] cursor-pointer" data-id="${rawId}">Remove</span>
              </div>
            `;

                // Remove input and button
                input.remove();
                button.remove();

                // Add preview
                container.appendChild(preview);

                // Update status icon
                const statusIcon = document.getElementById(`status-${rawId}`);
                if (statusIcon) {
                  statusIcon.src = "assets/Check-Icon.png";
                }

                // Add event listeners to new preview
                attachPreviewListeners(preview, rawId);
                // Check if all files are uploaded
                checkAllFilesUploaded();
              }
            })
            .catch((error) => {
              console.error("Upload error:", error);
              const errorMessage = container.querySelector(".error-message");
              errorMessage.textContent =
                "Error uploading file. Please try again.";
              errorMessage.classList.remove("hidden");
            });
        });
        // Reset checklist icon
        const statusIcon = document.getElementById(`status-${rawId}`);
        if (statusIcon) {
          statusIcon.src = "assets/Info-Icon.png";
        }
        // Check if all files are uploaded
        checkAllFilesUploaded();
      }
    })
    .catch((error) => {
      console.error("Error removing file:", error);
    });
}

// Helper function to attach preview listeners
function attachPreviewListeners(preview, id) {
  const viewText = preview.querySelector(".view-text");
  const downloadIcon = preview.querySelector(".document-requirements-icon");
  const removeText = preview.querySelector(".remove-text");

  if (viewText) {
    viewText.addEventListener("click", function () {
      const filename = this.dataset.filename;
      window.open(`documents/${filename}`, "_blank");
    });
  }

  if (downloadIcon) {
    downloadIcon.addEventListener("click", function () {
      const filename = this.dataset.filename;
      const link = document.createElement("a");
      link.href = `documents/${filename}`;
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
  }

  if (removeText) {
    removeText.addEventListener("click", function () {
      removeFile(id);
    });
  }
}

function checkAllFilesUploaded() {
  const statusIcons = document.querySelectorAll('[id^="status-"]');
  const submitBtn = document.querySelector(".submit-btn");
  let allUploaded = true;

  statusIcons.forEach((icon) => {
    if (icon.src.includes("Info-Icon.png")) {
      allUploaded = false;
    }
  });

  if (allUploaded) {
    submitBtn.classList.remove("cursor-not-allowed", "text-gray-500");
    submitBtn.classList.add(
      "bg-[#7213D0]",
      "text-white",
      "hover:bg-white",
      "hover:text-black"
    );
    submitBtn.disabled = false;
  } else {
    submitBtn.classList.add("cursor-not-allowed", "text-gray-500");
    submitBtn.classList.remove(
      "bg-[#7213D0]",
      "text-white",
      "hover:bg-white",
      "hover:text-black"
    );
    submitBtn.disabled = true;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const chevronIcon = document.getElementById("chevron-icon");
  const checklistContent = document.getElementById("checklistContent");
  const checklistBox = document.getElementById("checklist-box");

  let isExpanded = true;

  chevronIcon.addEventListener("click", () => {
    isExpanded = !isExpanded;

    if (!isExpanded) {
      checklistContent.style.opacity = "0";
      checklistContent.style.height = "0";
      checklistContent.style.overflow = "hidden";
      chevronIcon.style.transform = "rotate(180deg)";
      checklistBox.style.padding = "20px 25px";
    } else {
      checklistContent.style.opacity = "1";
      checklistContent.style.height = "auto";
      checklistContent.style.overflow = "visible";
      chevronIcon.style.transform = "rotate(0deg)";
      checklistBox.style.padding = "20px 25px";
    }
  });

  // Update your removeFile function to use the confirmation modal
  const originalRemoveFile = window.removeFile;
  window.removeFile = function (id) {
    createConfirmationModal(
      "Remove Document",
      "Are you sure you want to remove this document?",
      () => originalRemoveFile(id)
    );
  };

  // Get applicantID from data attribute in body tag
  const applicantID = document.body.dataset.applicantId;

  // Add event listeners for existing document previews
  document.querySelectorAll(".file-preview").forEach((preview) => {
    const id = preview.id.replace("preview-", "");
    const viewText = preview.querySelector(".view-text");
    const downloadIcon = preview.querySelector(".document-requirements-icon");
    const removeText = preview.querySelector(".remove-text");
    const filename = preview.querySelector(".file-name").textContent;

    // Set up view handler
    if (viewText) {
      viewText.addEventListener("click", function () {
        window.open(`documents/${filename}`, "_blank");
      });
    }

    // Set up download handler
    if (downloadIcon) {
      downloadIcon.addEventListener("click", function () {
        const link = document.createElement("a");
        link.href = `documents/${filename}`;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      });
    }

    // Set up remove handler
    if (removeText) {
      removeText.addEventListener("click", function () {
        removeFile(id);
      });
    }
  });

  document.querySelectorAll(".file-input").forEach((input) => {
    input.addEventListener("change", function handleFileChange() {
      const id = this.id.replace("input-", "");
      const file = this.files[0];
      const container = this.closest(".upload-container");
      const errorMessage = container.querySelector(".error-message");
      const uploadBtn = container.querySelector(".upload-btn");

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
        }

        if (file.size > MAX_FILE_SIZE) {
          errorMessage.textContent = `File size exceeds limit of ${
            MAX_FILE_SIZE / 1024 / 1024
          }MB`;
          errorMessage.classList.remove("hidden");
          return;
        }

        // Create FormData
        const formData = new FormData();
        formData.append("file", file);
        formData.append("applicantID", applicantID); // Use the applicantID from session
        formData.append("documentType", id);
        formData.append("documentName", file.name);

        // Upload file
        fetch("php/uploadDocument.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Create preview element
              const preview = document.createElement("div");
              preview.id = `preview-${id}`;
              preview.className =
                "file-preview flex justify-between gap-[15px] border-[2px] border-[solid] border-[black] p-[10px] rounded-[15px] mt-[10px] min-w-[300px] max-w-[750px]";
              preview.innerHTML = `
              <span class="file-name font-bold underline break-all">${data.filename.replace(/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}/, '')}</span>
              <div class="file-actions flex items-center gap-3">
                <img src="assets/Download-Icon.png" class="document-requirements-icon w-5 h-5 cursor-pointer" alt="Download" title="Download" data-filename="${data.filename}">
                <span class="view-text text-[blue] cursor-pointer" data-filename="${data.filename}">View</span>
                <span class="remove-text text-[red] cursor-pointer" onclick="removeFile('${id}')">Remove</span>
              </div>
            `;

              // Hide upload button and input
              uploadBtn.classList.add("hidden");
              input.classList.add("hidden");

              // Add preview to container
              container.appendChild(preview);

              // Update checklist icon
              const statusIcon = document.getElementById(`status-${id}`);
              if (statusIcon) {
                statusIcon.src = "assets/Check-Icon.png";
              }

              // Store file reference
              uploadedFiles[id] = file;

              // Set up view handler
              const viewText = preview.querySelector(".view-text");
              viewText.addEventListener("click", function () {
                const filename = this.dataset.filename;
                window.open(`documents/${filename}`, "_blank");
              });

              // Set up download handler
              const downloadIcon = preview.querySelector(
                ".document-requirements-icon"
              );
              downloadIcon.addEventListener("click", function () {
                const filename = this.dataset.filename;
                const link = document.createElement("a");
                link.href = `documents/${filename}`;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
              });
              // Check if all files are uploaded
              checkAllFilesUploaded();
            }
          })
          .catch((error) => {
            console.error("Upload error:", error);
            errorMessage.textContent =
              "Error uploading file. Please try again.";
            errorMessage.classList.remove("hidden");
          });
      }
    });
  });

  // Add click handlers for upload buttons
  document.querySelectorAll(".upload-btn").forEach((button) => {
    button.addEventListener("click", function () {
      const id = this.id.replace("button-", "");
      const input = document.getElementById(`input-${id}`);
      if (input) input.click();
    });
  });

  const submitBtn = document.querySelector(".submit-btn");
  const confirmationPopup = document.getElementById("confirmationPopup");
  const successPopup = document.getElementById("successPopup");

  // Initial check
  checkAllFilesUploaded();

  // Add submit button click handler
  submitBtn.addEventListener("click", function () {
    if (!this.disabled) {
      confirmationPopup.classList.remove("hidden");
    }
  });

  // Add confirmation popup handlers
  const noBtn = confirmationPopup.querySelector(".no");
  const yesBtn = confirmationPopup.querySelector('[type="submit"]');

  noBtn.addEventListener("click", function () {
    confirmationPopup.classList.add("hidden");
  });

  yesBtn.addEventListener("click", function (e) {
    e.preventDefault();
    confirmationPopup.classList.add("hidden");
    successPopup.classList.remove("hidden");
  });

  // Add tracking button handler
  const trackingBtn = successPopup.querySelector(".to-application-tracking");
  trackingBtn.addEventListener("click", function () {
    window.location.href = "application-tracking.php";
  });
});
